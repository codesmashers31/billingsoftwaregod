<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;

class AccountController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('accounts.view');
        $db = Database::getInstance();
        $accModel = new Account();

        $accounts = $accModel->all('type ASC, name ASC');

        // Cash balance
        $resCash = $db->query("SELECT COALESCE(SUM(current_balance), 0) as total FROM `accounts` WHERE type = 'cash' AND status = 'active'");
        $cashTotal = (float)($resCash[0]['total'] ?? 0);

        // Bank balance
        $resBank = $db->query("SELECT COALESCE(SUM(current_balance), 0) as total FROM `accounts` WHERE type = 'bank' AND status = 'active'");
        $bankTotal = (float)($resBank[0]['total'] ?? 0);

        // Customer Receivables
        $resCustRec = $db->query("SELECT COALESCE(SUM(outstanding_balance), 0) as total FROM `customers` WHERE status = 'active'");
        $customerReceivables = (float)($resCustRec[0]['total'] ?? 0);

        // Supplier Payables
        $resSuppPay = $db->query("SELECT COALESCE(SUM(outstanding_balance), 0) as total FROM `suppliers` WHERE status = 'active'");
        $supplierPayables = (float)($resSuppPay[0]['total'] ?? 0);

        // Recent Payments
        $paymentModel = new Payment();
        $recentPayments = $paymentModel->getPaginatedList(1, 10);

        $this->render('accounts.index', [
            'pageTitle' => 'Accounts & Financial Department',
            'accounts' => $accounts,
            'cashTotal' => $cashTotal,
            'bankTotal' => $bankTotal,
            'customerReceivables' => $customerReceivables,
            'supplierPayables' => $supplierPayables,
            'recentPayments' => $recentPayments['data']
        ]);
    }

    public function payments(Request $request): void {
        $this->requirePermission('accounts.payments');
        $payModel = new Payment();
        $accModel = new Account();
        $custModel = new Customer();
        $suppModel = new Supplier();

        $page = (int)$request->input('page', 1);
        $filters = [
            'payment_type' => (string)$request->input('payment_type', ''),
            'account_id' => $request->input('account_id'),
            'date_from' => (string)$request->input('date_from', ''),
            'date_to' => (string)$request->input('date_to', '')
        ];

        $result = $payModel->getPaginatedList($page, 15, $filters);
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'name ASC');
        $customers = $custModel->where("status = 'active'", [], 'name ASC');
        $suppliers = $suppModel->where("status = 'active'", [], 'name ASC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('accounts.payments', [
            'pageTitle' => 'Payment Receipts & Disbursements',
            'payments' => $result['data'],
            'pagination' => $result,
            'accounts' => $accounts,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'filters' => $filters
        ]);
    }

    public function recordCustomerPayment(Request $request): void {
        $this->requirePermission('accounts.payments');
        $customerId = (int)$request->input('customer_id');
        $accountId = (int)$request->input('account_id');
        $amount = (float)$request->input('amount');
        $method = $request->input('payment_method', 'cash');
        $ref = trim($request->input('transaction_reference', ''));
        $date = $request->input('payment_date', date('Y-m-d'));
        $notes = trim($request->input('notes', ''));

        if ($customerId <= 0 || $accountId <= 0 || $amount <= 0) {
            Response::error('Please fill all required payment fields.');
        }

        $custModel = new Customer();
        $customer = $custModel->find($customerId);
        if (!$customer) {
            Response::error('Customer not found.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $payModel = new Payment();
            $accModel = new Account();
            $paymentNo = $payModel->generatePaymentNo();

            // Create payment
            $payModel->create([
                'payment_no' => $paymentNo,
                'payment_type' => 'customer_payment',
                'account_id' => $accountId,
                'customer_id' => $customerId,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_reference' => $ref,
                'payment_date' => $date,
                'notes' => $notes ?: "Collection from {$customer['name']}",
                'user_id' => Auth::id() ?: 1
            ]);

            // Deduct customer outstanding
            $newCustBal = max(0, (float)$customer['outstanding_balance'] - $amount);
            $custModel->update($customerId, ['outstanding_balance' => $newCustBal]);

            // Debit Cash/Bank account (increasing funds)
            $accModel->recordTransaction(
                $accountId,
                'debit',
                $amount,
                'payment',
                null,
                $paymentNo,
                "Customer payment from {$customer['name']}",
                $date,
                Auth::id() ?: 1
            );

            $db->commit();
            $this->logActivity('create', 'accounts', null, $paymentNo, "Recorded customer payment of " . formatCurrency($amount) . " from {$customer['name']}");
            Response::success('Customer payment received and ledger updated!');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function recordSupplierPayment(Request $request): void {
        $this->requirePermission('accounts.payments');
        $supplierId = (int)$request->input('supplier_id');
        $accountId = (int)$request->input('account_id');
        $amount = (float)$request->input('amount');
        $method = $request->input('payment_method', 'bank_transfer');
        $ref = trim($request->input('transaction_reference', ''));
        $date = $request->input('payment_date', date('Y-m-d'));
        $notes = trim($request->input('notes', ''));

        if ($supplierId <= 0 || $accountId <= 0 || $amount <= 0) {
            Response::error('Please fill all required payment fields.');
        }

        $suppModel = new Supplier();
        $supplier = $suppModel->find($supplierId);
        if (!$supplier) {
            Response::error('Supplier not found.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $payModel = new Payment();
            $accModel = new Account();
            $paymentNo = $payModel->generatePaymentNo();

            // Create payment
            $payModel->create([
                'payment_no' => $paymentNo,
                'payment_type' => 'supplier_payment',
                'account_id' => $accountId,
                'supplier_id' => $supplierId,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_reference' => $ref,
                'payment_date' => $date,
                'notes' => $notes ?: "Disbursement to {$supplier['name']}",
                'user_id' => Auth::id() ?: 1
            ]);

            // Deduct supplier payable balance
            $newSuppBal = max(0, (float)$supplier['outstanding_balance'] - $amount);
            $suppModel->update($supplierId, ['outstanding_balance' => $newSuppBal]);

            // Credit Cash/Bank account (reducing funds)
            $accModel->recordTransaction(
                $accountId,
                'credit',
                $amount,
                'payment',
                null,
                $paymentNo,
                "Supplier payment to {$supplier['name']}",
                $date,
                Auth::id() ?: 1
            );

            $db->commit();
            $this->logActivity('create', 'accounts', null, $paymentNo, "Recorded supplier disbursement of " . formatCurrency($amount) . " to {$supplier['name']}");
            Response::success('Supplier payment recorded and ledger updated!');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function ledger(Request $request, array $params): void {
        $this->requirePermission('accounts.view');
        $accountId = (int)($params['id'] ?? 0);
        $accModel = new Account();
        $account = $accModel->find($accountId);

        if (!$account) {
            Response::error('Account not found.');
        }

        $transactions = $accModel->getTransactions($accountId, 50);
        Response::json([
            'success' => true,
            'account' => $account,
            'transactions' => $transactions
        ]);
    }
}
