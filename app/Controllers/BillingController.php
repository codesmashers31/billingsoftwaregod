<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Account;
use App\Models\Payment;

class BillingController extends Controller {
    public function pos(Request $request): void {
        $this->requirePermission('billing.pos');
        $prodModel = new Product();
        $catModel = new Category();
        $custModel = new Customer();
        $accModel = new Account();

        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $initialProducts = $prodModel->searchForPos('', null, 24);
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'type ASC, name ASC');

        $this->render('billing.pos', [
            'pageTitle' => 'Sacred POS Billing Terminal',
            'categories' => $categories,
            'products' => $initialProducts,
            'accounts' => $accounts
        ], 'app');
    }

    public function searchProducts(Request $request): void {
        $q = (string)$request->input('q', '');
        $categoryId = $request->input('category_id') ? (int)$request->input('category_id') : null;

        $prodModel = new Product();
        $products = $prodModel->searchForPos($q, $categoryId, 30);
        Response::json(['data' => $products]);
    }

    public function scanBarcode(Request $request): void {
        $code = trim((string)$request->input('code', ''));
        if (empty($code)) {
            Response::error('Barcode is empty.');
        }

        $prodModel = new Product();
        $product = $prodModel->findByBarcodeOrSku($code);

        if (!$product) {
            Response::error("No product found matching barcode/SKU: {$code}");
        }

        Response::json(['success' => true, 'product' => $product]);
    }

    public function invoices(Request $request): void {
        $this->requirePermission('billing.view');
        $invModel = new Invoice();

        $page = (int)$request->input('page', 1);
        $filters = [
            'search' => (string)$request->input('search', ''),
            'status' => (string)$request->input('status', ''),
            'payment_status' => (string)$request->input('payment_status', ''),
            'date_from' => (string)$request->input('date_from', ''),
            'date_to' => (string)$request->input('date_to', ''),
        ];

        $result = $invModel->getPaginatedList($page, 15, $filters);

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('billing.invoices', [
            'pageTitle' => 'Sales Invoices & Billing History',
            'invoices' => $result['data'],
            'pagination' => $result,
            'filters' => $filters
        ]);
    }

    public function checkout(Request $request): void {
        $this->requirePermission('billing.create');
        $customerId = (int)$request->input('customer_id', 1);
        $paymentMethod = $request->input('payment_method', 'cash');
        $accountId = (int)$request->input('account_id', 1); // default cash account
        $overallDiscountPercent = (float)$request->input('overall_discount_percent', 0);
        $paidAmountInput = (float)$request->input('paid_amount', 0);
        $notes = trim($request->input('notes', ''));
        $cartItems = $request->input('items', []);

        if (empty($cartItems) || !is_array($cartItems)) {
            Response::error('Billing cart is empty. Please add products.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $invModel = new Invoice();
            $prodModel = new Product();
            $invTxModel = new Inventory();
            $custModel = new Customer();
            $accModel = new Account();
            $payModel = new Payment();

            $customer = $custModel->find($customerId);
            if (!$customer) {
                throw new \Exception('Invalid customer selected.');
            }

            $invoiceNo = $invModel->generateInvoiceNo();
            $today = date('Y-m-d');
            $nowTime = date('H:i:s');

            $subtotal = 0.00;
            $itemDiscountTotal = 0.00;
            $taxableAmount = 0.00;
            $taxTotal = 0.00;
            $validatedItems = [];

            foreach ($cartItems as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 1);
                $unitPrice = (float)($item['unit_price'] ?? 0);
                $itemDiscountPercent = (float)($item['discount_percent'] ?? 0);

                if ($productId <= 0 || $qty <= 0) continue;

                $prod = $prodModel->find($productId);
                if (!$prod) {
                    throw new \Exception("Product #{$productId} not found.");
                }

                if ($prod['current_stock'] < $qty) {
                    throw new \Exception("Insufficient stock for '{$prod['name']}'. Available: {$prod['current_stock']}, Requested: {$qty}");
                }

                $gross = $qty * $unitPrice;
                $discAmt = ($gross * $itemDiscountPercent) / 100;
                $netItem = $gross - $discAmt;
                $taxPercent = (float)($prod['gst_percent'] ?? 12);
                $taxAmt = ($netItem * $taxPercent) / 100;
                $itemTotal = $netItem + $taxAmt;

                $subtotal += $gross;
                $itemDiscountTotal += $discAmt;
                $taxTotal += $taxAmt;

                $validatedItems[] = [
                    'product_id' => $productId,
                    'product_name' => $prod['name'],
                    'product_code' => $prod['code'],
                    'god_name' => $prod['god_name'],
                    'material' => $prod['material'],
                    'hsn_code' => $prod['hsn_code'] ?: '9701',
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $itemDiscountPercent,
                    'discount_amount' => $discAmt,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmt,
                    'total_amount' => $itemTotal
                ];
            }

            if (empty($validatedItems)) {
                throw new \Exception('Cart items validation failed.');
            }

            // Overall discount calculation
            $overallDiscountAmount = (($subtotal - $itemDiscountTotal) * $overallDiscountPercent) / 100;
            $rawTotal = ($subtotal - $itemDiscountTotal - $overallDiscountAmount) + $taxTotal;

            // Round-off
            $grandTotal = round($rawTotal);
            $roundOff = round($grandTotal - $rawTotal, 2);

            // GST split (Intra-state CGST 6% + SGST 6% vs Inter-state IGST 12%)
            $isInterstate = ($customer['state'] !== 'Tamil Nadu' && !empty($customer['state']));
            if ($isInterstate) {
                $cgst = 0.00;
                $sgst = 0.00;
                $igst = $taxTotal;
            } else {
                $cgst = round($taxTotal / 2, 2);
                $sgst = round($taxTotal / 2, 2);
                $igst = 0.00;
            }

            // Payment calculation
            $paidAmount = min($grandTotal, max(0, $paidAmountInput));
            if ($paymentMethod === 'credit') {
                $paidAmount = 0.00;
            } elseif ($paidAmount == 0 && $paymentMethod !== 'credit') {
                $paidAmount = $grandTotal; // Default full payment if not credit
            }
            $dueAmount = max(0, $grandTotal - $paidAmount);
            $paymentStatus = $dueAmount == 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            // 1. Create Invoice Header
            $invoiceId = $invModel->create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $customerId,
                'user_id' => Auth::id() ?: 1,
                'invoice_date' => $today,
                'invoice_time' => $nowTime,
                'subtotal' => $subtotal,
                'item_discount_total' => $itemDiscountTotal,
                'overall_discount_percent' => $overallDiscountPercent,
                'overall_discount_amount' => $overallDiscountAmount,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_amount' => $taxTotal,
                'round_off' => $roundOff,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'payment_status' => $paymentStatus,
                'notes' => $notes
            ]);

            // 2. Insert Invoice Items & Decrement Inventory
            foreach ($validatedItems as $v) {
                $db->query("INSERT INTO `invoice_items` (`invoice_id`, `product_id`, `product_name`, `product_code`, `god_name`, `material`, `hsn_code`, `quantity`, `unit_price`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `total_amount`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                    $invoiceId, $v['product_id'], $v['product_name'], $v['product_code'], $v['god_name'], $v['material'], $v['hsn_code'],
                    $v['quantity'], $v['unit_price'], $v['discount_percent'], $v['discount_amount'], $v['tax_percent'], $v['tax_amount'], $v['total_amount']
                ]);

                // Reduce stock with audit ledger
                $invTxModel->adjustStock(
                    $v['product_id'],
                    -$v['quantity'],
                    'sale',
                    'invoice',
                    (int)$invoiceId,
                    $invoiceNo,
                    "Sold in Invoice #{$invoiceNo}",
                    Auth::id() ?: 1
                );
            }

            // 3. Financial Bookkeeping & Payments
            if ($paidAmount > 0) {
                $paymentNo = $payModel->generatePaymentNo();
                $payModel->create([
                    'payment_no' => $paymentNo,
                    'payment_type' => 'customer_payment',
                    'account_id' => $accountId,
                    'customer_id' => $customerId,
                    'invoice_id' => (int)$invoiceId,
                    'amount' => $paidAmount,
                    'payment_method' => in_array($paymentMethod, ['cash', 'card', 'upi', 'bank_transfer']) ? $paymentMethod : 'cash',
                    'transaction_reference' => "Bill {$invoiceNo}",
                    'payment_date' => $today,
                    'notes' => "POS Sales receipt for {$invoiceNo}",
                    'user_id' => Auth::id() ?: 1
                ]);

                // Record accounting ledger entry
                $accModel->recordTransaction(
                    $accountId,
                    'debit', // Cash/Bank receipt increases asset
                    $paidAmount,
                    'invoice',
                    (int)$invoiceId,
                    $invoiceNo,
                    "Sales receipt {$invoiceNo}",
                    $today,
                    Auth::id() ?: 1
                );
            }

            // 4. Update Customer Outstanding Balance if credit/due
            if ($dueAmount > 0) {
                $newCustomerOutstanding = (float)$customer['outstanding_balance'] + $dueAmount;
                $custModel->update($customerId, ['outstanding_balance' => $newCustomerOutstanding]);
            }

            $db->commit();
            $this->logActivity('create', 'billing', (int)$invoiceId, $invoiceNo, "Generated invoice {$invoiceNo} for " . formatCurrency($grandTotal));

            Response::success('Invoice completed successfully!', [
                'invoice_id' => $invoiceId,
                'invoice_no' => $invoiceNo,
                'grand_total' => $grandTotal,
                'print_a4_url' => url("billing/invoice/{$invoiceId}/print?type=a4"),
                'print_thermal_url' => url("billing/invoice/{$invoiceId}/print?type=thermal"),
                'view_url' => url("billing/invoice/{$invoiceId}")
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function viewInvoice(Request $request, array $params): void {
        $this->requirePermission('billing.view');
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();

        $invoice = $invModel->getWithDetails($id);
        if (!$invoice) {
            Session::flash('error', 'Invoice not found.');
            Response::redirect('/billing/invoices');
        }

        $items = $invModel->getItems($id);

        $this->render('billing.invoice-view', [
            'pageTitle' => "Invoice: {$invoice['invoice_no']}",
            'invoice' => $invoice,
            'items' => $items
        ]);
    }

    public function printInvoice(Request $request, array $params): void {
        $this->requirePermission('billing.print');
        $id = (int)($params['id'] ?? 0);
        $type = $request->input('type', 'a4'); // a4 or thermal
        $invModel = new Invoice();

        $invoice = $invModel->getWithDetails($id);
        if (!$invoice) {
            die('Invoice not found.');
        }

        $items = $invModel->getItems($id);

        if ($type === 'thermal') {
            $this->render('billing.print-thermal', [
                'invoice' => $invoice,
                'items' => $items
            ], 'print');
        } else {
            $this->render('billing.print-a4', [
                'invoice' => $invoice,
                'items' => $items
            ], 'print');
        }
    }

    public function cancel(Request $request, array $params): void {
        $this->requirePermission('billing.cancel');
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();
        $invoice = $invModel->find($id);

        if (!$invoice) {
            Response::error('Invoice not found.');
        }

        if ($invoice['status'] === 'cancelled') {
            Response::error('Invoice is already cancelled.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $items = $invModel->getItems($id);
            $invTxModel = new Inventory();

            // Restock items
            foreach ($items as $item) {
                $invTxModel->adjustStock(
                    (int)$item['product_id'],
                    (int)$item['quantity'],
                    'stock_in',
                    'invoice_cancellation',
                    $id,
                    $invoice['invoice_no'],
                    "Restocked on Invoice {$invoice['invoice_no']} cancellation",
                    Auth::id() ?: 1
                );
            }

            // Cancel invoice
            $invModel->update($id, ['status' => 'cancelled']);

            $db->commit();
            $this->logActivity('cancel', 'billing', $id, $invoice['invoice_no'], "Cancelled invoice {$invoice['invoice_no']}");
            Response::success('Invoice has been cancelled and items restocked.');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }
}
