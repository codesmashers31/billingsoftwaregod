<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\SalesReturn;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Payment;

class SalesReturnController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('billing.returns');
        $returnModel = new SalesReturn();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');

        $conditions = ["1=1"];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(sr.return_no LIKE ? OR i.invoice_no LIKE ? OR c.name LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * 15);

        $db = Database::getInstance();
        $countSql = "SELECT COUNT(*) as total FROM `sales_returns` sr 
                     JOIN `invoices` i ON sr.invoice_id = i.id 
                     JOIN `customers` c ON sr.customer_id = c.id 
                     WHERE {$whereClause}";
        $totalRes = $db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT sr.*, i.invoice_no, c.name as customer_name, c.mobile as customer_mobile, u.name as creator_name 
                    FROM `sales_returns` sr 
                    JOIN `invoices` i ON sr.invoice_id = i.id 
                    JOIN `customers` c ON sr.customer_id = c.id 
                    JOIN `users` u ON sr.created_by = u.id 
                    WHERE {$whereClause} 
                    ORDER BY sr.id DESC 
                    LIMIT 15 OFFSET {$offset}";
        $rows = $db->query($dataSql, $params);

        $pagination = [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => 15,
            'total_pages' => (int)ceil($total / 15)
        ];

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($pagination);
        }

        $this->render('sales-returns.index', [
            'pageTitle' => 'Sales Returns & Replacement Management',
            'returns' => $rows,
            'pagination' => $pagination,
            'search' => $search
        ]);
    }

    public function create(Request $request): void {
        $this->requirePermission('billing.returns');
        $accModel = new Account();
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'name ASC');

        $this->render('sales-returns.create', [
            'pageTitle' => 'Process Statue Return / Credit Note',
            'accounts' => $accounts
        ]);
    }

    public function lookupInvoice(Request $request): void {
        $invoiceNo = trim((string)$request->input('invoice_no', ''));
        $invModel = new Invoice();
        $invoice = $invModel->findBy('invoice_no', $invoiceNo);

        if (!$invoice) {
            Response::error('Invoice not found with number: ' . $invoiceNo);
        }

        $custModel = new Customer();
        $customer = $custModel->find((int)$invoice['customer_id']);
        $items = $invModel->getItems((int)$invoice['id']);

        Response::json([
            'success' => true,
            'invoice' => $invoice,
            'customer' => $customer,
            'items' => $items
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('billing.returns');
        $invoiceId = (int)$request->input('invoice_id');
        $refundMethod = $request->input('refund_method', 'cash');
        $accountId = (int)$request->input('account_id', 1);
        $reason = trim($request->input('reason', ''));
        $items = (array)$request->input('items', []);

        if ($invoiceId <= 0 || empty($items)) {
            Response::error('Please lookup an invoice and select items to return.');
        }

        $invModel = new Invoice();
        $invoice = $invModel->find($invoiceId);
        if (!$invoice) {
            Response::error('Invoice not found.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $returnModel = new SalesReturn();
            $invTxModel = new Inventory();
            $custModel = new Customer();
            $accModel = new Account();
            $payModel = new Payment();

            $returnNo = $returnModel->generateReturnNo();
            $today = date('Y-m-d');
            $totalRefund = 0.00;

            $validatedReturnItems = [];
            foreach ($items as $item) {
                $pId = (int)($item['product_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                $rate = (float)($item['unit_price'] ?? 0);

                if ($pId > 0 && $qty > 0) {
                    $itemRefund = $qty * $rate;
                    $totalRefund += $itemRefund;

                    $validatedReturnItems[] = [
                        'product_id' => $pId,
                        'quantity' => $qty,
                        'unit_price' => $rate,
                        'refund_amount' => $itemRefund
                    ];
                }
            }

            if (empty($validatedReturnItems)) {
                throw new \Exception('No valid return items selected.');
            }

            // 1. Create Return Header
            $returnId = $returnModel->create([
                'return_no' => $returnNo,
                'invoice_id' => $invoiceId,
                'customer_id' => (int)$invoice['customer_id'],
                'return_date' => $today,
                'total_refund_amount' => $totalRefund,
                'refund_method' => $refundMethod,
                'reason' => $reason,
                'status' => 'approved',
                'created_by' => Auth::id() ?: 1
            ]);

            // 2. Insert items and restock
            foreach ($validatedReturnItems as $v) {
                $db->query("INSERT INTO `sales_return_items` (`sales_return_id`, `product_id`, `quantity`, `unit_price`, `refund_amount`) 
                            VALUES (?, ?, ?, ?, ?)", [
                    $returnId, $v['product_id'], $v['quantity'], $v['unit_price'], $v['refund_amount']
                ]);

                // Restore stock
                $invTxModel->adjustStock(
                    $v['product_id'],
                    $v['quantity'],
                    'sales_return',
                    'sales_return',
                    (int)$returnId,
                    $returnNo,
                    "Restocked from return {$returnNo} for invoice {$invoice['invoice_no']}",
                    Auth::id() ?: 1
                );
            }

            // 3. Financial refund bookkeeping
            if ($refundMethod === 'credit_note') {
                // Deduct customer outstanding if positive or create credit
                $cust = $custModel->find((int)$invoice['customer_id']);
                if ($cust) {
                    $newBal = max(0, (float)$cust['outstanding_balance'] - $totalRefund);
                    $custModel->update((int)$invoice['customer_id'], ['outstanding_balance' => $newBal]);
                }
            } else {
                // Cash / Bank payout
                $paymentNo = $payModel->generatePaymentNo();
                $payModel->create([
                    'payment_no' => $paymentNo,
                    'payment_type' => 'refund',
                    'account_id' => $accountId,
                    'customer_id' => (int)$invoice['customer_id'],
                    'invoice_id' => $invoiceId,
                    'amount' => $totalRefund,
                    'payment_method' => in_array($refundMethod, ['cash', 'upi', 'bank_transfer']) ? $refundMethod : 'cash',
                    'transaction_reference' => "Return {$returnNo}",
                    'payment_date' => $today,
                    'notes' => "Refund for returned idol(s) against bill {$invoice['invoice_no']}",
                    'user_id' => Auth::id() ?: 1
                ]);

                // Credit the cash/bank account (reducing asset balance)
                $accModel->recordTransaction(
                    $accountId,
                    'credit',
                    $totalRefund,
                    'sales_return',
                    (int)$returnId,
                    $returnNo,
                    "Sales return payout {$returnNo}",
                    $today,
                    Auth::id() ?: 1
                );
            }

            $db->commit();
            $this->logActivity('create', 'billing', (int)$returnId, $returnNo, "Processed sales return {$returnNo} for " . formatCurrency($totalRefund));

            Response::success('Sales return processed successfully! Stock restored and refund recorded.', [
                'redirect' => url('sales-returns')
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }
}
