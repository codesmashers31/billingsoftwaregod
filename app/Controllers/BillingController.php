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

    public function createInvoice(Request $request): void {
        $this->requirePermission('billing.create');
        $custModel = new Customer();
        $catModel = new Category();
        $accModel = new Account();
        $invModel = new Invoice();

        $nextInvoiceNo = $invModel->generateInvoiceNo();
        $customers = $custModel->where("status = 'active'", [], 'name ASC', 50);
        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'type ASC, name ASC');

        $this->render('billing.invoice-form', [
            'pageTitle' => 'Create Tax / Proforma Invoice',
            'isEdit' => false,
            'invoice' => [
                'invoice_no' => $nextInvoiceNo,
                'invoice_type' => 'tax_invoice',
                'invoice_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+15 days')),
                'payment_terms' => 'Net 15',
                'reference_no' => '',
                'customer_id' => 1,
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => ''
            ],
            'items' => [],
            'customers' => $customers,
            'categories' => $categories,
            'accounts' => $accounts,
            'nextInvoiceNo' => $nextInvoiceNo
        ]);
    }

    public function storeInvoice(Request $request): void {
        $this->requirePermission('billing.create');
        $this->saveOrUpdateInvoice($request, 0);
    }

    public function editInvoice(Request $request, array $params): void {
        $this->requirePermission('billing.edit');
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();
        $custModel = new Customer();
        $catModel = new Category();
        $accModel = new Account();

        $invoice = $invModel->getWithDetails($id);
        if (!$invoice) {
            Session::flash('error', 'Invoice not found.');
            Response::redirect('/billing/invoices');
        }

        $items = $invModel->getItems($id);
        $customers = $custModel->where("status = 'active'", [], 'name ASC', 50);
        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'type ASC, name ASC');

        $this->render('billing.invoice-form', [
            'pageTitle' => "Edit Invoice: {$invoice['invoice_no']}",
            'isEdit' => true,
            'invoice' => $invoice,
            'items' => $items,
            'customers' => $customers,
            'categories' => $categories,
            'accounts' => $accounts,
            'nextInvoiceNo' => $invoice['invoice_no']
        ]);
    }

    public function updateInvoice(Request $request, array $params): void {
        $this->requirePermission('billing.edit');
        $id = (int)($params['id'] ?? 0);
        $this->saveOrUpdateInvoice($request, $id);
    }

    public function apiGetNextInvoiceNo(Request $request): void {
        $invModel = new Invoice();
        Response::json(['invoice_no' => $invModel->generateInvoiceNo()]);
    }

    public function downloadPdf(Request $request, array $params): void {
        $this->requirePermission('billing.view');
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();
        $invoice = $invModel->getWithDetails($id);
        if (!$invoice) {
            die('Invoice not found.');
        }
        $items = $invModel->getItems($id);
        $this->render('billing.print-a4', [
            'invoice' => $invoice,
            'items' => $items,
            'autoPrint' => true
        ], 'print');
    }

    public function addPayment(Request $request, array $params): void {
        $this->requirePermission('billing.create');
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();
        $invoice = $invModel->find($id);
        if (!$invoice) {
            Response::error('Invoice not found.');
        }
        if ($invoice['status'] === 'cancelled') {
            Response::error('Cannot add payment to a cancelled invoice.');
        }

        $amount = (float)$request->input('amount', 0);
        if ($amount <= 0) {
            Response::error('Payment amount must be greater than zero.');
        }

        $due = (float)$invoice['due_amount'];
        if ($amount > $due + 0.01) {
            Response::error("Payment amount (₹{$amount}) cannot exceed due balance (₹{$due}).");
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $newPaid = (float)$invoice['paid_amount'] + $amount;
            $newDue = max(0, (float)$invoice['grand_total'] - $newPaid);
            $payStatus = $newDue <= 0.01 ? 'paid' : 'partial';

            $invModel->update($id, [
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'payment_status' => $payStatus
            ]);

            $payModel = new Payment();
            $paymentMethod = $request->input('payment_method', 'cash');
            $accountId = (int)$request->input('account_id', 1);
            $payNo = $payModel->generatePaymentNo();

            $payModel->create([
                'payment_no' => $payNo,
                'payment_type' => 'customer_payment',
                'account_id' => $accountId,
                'customer_id' => (int)$invoice['customer_id'],
                'invoice_id' => $id,
                'amount' => $amount,
                'payment_method' => in_array($paymentMethod, ['cash', 'card', 'upi', 'bank_transfer']) ? $paymentMethod : 'cash',
                'transaction_reference' => $request->input('transaction_reference', "Bill {$invoice['invoice_no']}"),
                'payment_date' => $request->input('payment_date', date('Y-m-d')),
                'notes' => $request->input('notes', "Payment for {$invoice['invoice_no']}"),
                'user_id' => Auth::id() ?: 1
            ]);

            // Update customer outstanding balance
            $custModel = new Customer();
            $cust = $custModel->find((int)$invoice['customer_id']);
            if ($cust) {
                $newCustBal = max(0, (float)$cust['outstanding_balance'] - $amount);
                $custModel->update((int)$cust['id'], ['outstanding_balance' => $newCustBal]);
            }

            $db->commit();
            $this->logActivity('update', 'billing', $id, $invoice['invoice_no'], "Recorded payment of ₹{$amount} for invoice {$invoice['invoice_no']}");
            Response::success('Payment recorded successfully!', [
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'payment_status' => $payStatus
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    private function saveOrUpdateInvoice(Request $request, int $existingId = 0): void {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $invModel = new Invoice();
            $prodModel = new Product();
            $invTxModel = new Inventory();
            $custModel = new Customer();
            $accModel = new Account();
            $payModel = new Payment();

            // 1. Customer Resolution
            $customerId = (int)$request->input('customer_id', 0);
            $custName = trim($request->input('customer_name', ''));
            $custMobile = trim($request->input('customer_mobile', ''));
            $custEmail = trim($request->input('customer_email', ''));
            $custBilling = trim($request->input('customer_billing_address', ''));
            $custShipping = trim($request->input('customer_shipping_address', '')) ?: $custBilling;
            $custGst = trim($request->input('customer_gst', ''));
            $custState = trim($request->input('customer_state', 'Tamil Nadu')) ?: 'Tamil Nadu';
            $custStateCode = trim($request->input('customer_state_code', '33')) ?: '33';

            if ($customerId > 0) {
                $customer = $custModel->find($customerId);
                if ($customer && !empty($custMobile)) {
                    $custModel->update($customerId, [
                        'name' => $custName ?: $customer['name'],
                        'mobile' => $custMobile,
                        'email' => $custEmail ?: $customer['email'],
                        'address' => $custBilling ?: $customer['address'],
                        'shipping_address' => $custShipping ?: $customer['shipping_address'],
                        'gst_number' => $custGst ?: $customer['gst_number'],
                        'state' => $custState,
                        'state_code' => $custStateCode,
                    ]);
                    $customer = $custModel->find($customerId);
                }
            } else {
                $customer = $custModel->findOrCreate([
                    'name' => $custName ?: 'Direct Customer',
                    'mobile' => $custMobile ?: '9999999999',
                    'email' => $custEmail,
                    'address' => $custBilling,
                    'shipping_address' => $custShipping,
                    'gst_number' => $custGst,
                    'state' => $custState,
                    'state_code' => $custStateCode,
                    'customer_type' => 'retail'
                ]);
                $customerId = (int)$customer['id'];
            }

            if (!$customer) {
                throw new \Exception('Failed to resolve or create customer.');
            }

            // 2. Invoice Basic Details
            $invoiceNo = trim($request->input('invoice_no', ''));
            if (empty($invoiceNo)) {
                $invoiceNo = $invModel->generateInvoiceNo();
            }

            // Validate invoice number uniqueness
            if ($existingId > 0) {
                $dupCheck = $db->query("SELECT id FROM `invoices` WHERE invoice_no = ? AND id != ? LIMIT 1", [$invoiceNo, $existingId]);
            } else {
                $dupCheck = $db->query("SELECT id FROM `invoices` WHERE invoice_no = ? LIMIT 1", [$invoiceNo]);
            }
            if (!empty($dupCheck)) {
                throw new \Exception("Invoice number '{$invoiceNo}' already exists. Please use a unique invoice number.");
            }

            $invoiceType = $request->input('invoice_type', 'tax_invoice');
            if (!in_array($invoiceType, ['tax_invoice', 'proforma_invoice'])) {
                $invoiceType = 'tax_invoice';
            }

            $invoiceDate = $request->input('invoice_date', date('Y-m-d'));
            $dueDate = $request->input('due_date', date('Y-m-d', strtotime('+15 days')));
            $paymentTerms = trim($request->input('payment_terms', 'Net 15'));
            $referenceNo = trim($request->input('reference_no', ''));
            $paymentMethod = $request->input('payment_method', 'cash');
            $accountId = (int)$request->input('account_id', 1);
            $status = $request->input('status', 'completed');
            if (!in_array($status, ['completed', 'draft'])) {
                $status = 'completed';
            }
            $notes = trim($request->input('notes', ''));

            // 3. Process Items & Validate Stock
            $rawItems = $request->input('items', []);
            if (empty($rawItems) || !is_array($rawItems)) {
                throw new \Exception('Please add at least one product to the invoice.');
            }

            // If editing an existing completed invoice, revert previous stock deductions before checking/applying
            $oldInvoice = $existingId > 0 ? $invModel->find($existingId) : null;
            if ($oldInvoice && $oldInvoice['status'] === 'completed') {
                $oldItems = $invModel->getItems($existingId);
                foreach ($oldItems as $oi) {
                    $invTxModel->adjustStock(
                        (int)$oi['product_id'],
                        (int)$oi['quantity'],
                        'stock_in',
                        'invoice_edit_revert',
                        $existingId,
                        $oldInvoice['invoice_no'],
                        "Stock reversal prior to editing invoice #{$oldInvoice['invoice_no']}",
                        Auth::id() ?: 1
                    );
                }
                // Revert previous due balance on customer
                if ((float)$oldInvoice['due_amount'] > 0) {
                    $prevCust = $custModel->find((int)$oldInvoice['customer_id']);
                    if ($prevCust) {
                        $revertedBal = max(0, (float)$prevCust['outstanding_balance'] - (float)$oldInvoice['due_amount']);
                        $custModel->update((int)$prevCust['id'], ['outstanding_balance' => $revertedBal]);
                    }
                }
            }

            $validatedItems = [];
            $subtotal = 0.00;
            $itemDiscountTotal = 0.00;
            $taxTotal = 0.00;

            foreach ($rawItems as $item) {
                $prodId = (int)($item['product_id'] ?? 0);
                $qty = max(1, (int)($item['quantity'] ?? 1));
                $unitPrice = max(0, (float)($item['unit_price'] ?? 0));
                $itemDiscountPercent = max(0, min(100, (float)($item['discount_percent'] ?? 0)));

                if ($prodId <= 0) continue;
                $prod = $prodModel->find($prodId);
                if (!$prod) {
                    throw new \Exception("Product #{$prodId} not found in inventory.");
                }

                // Available stock check for completed/finalized invoices
                if ($status === 'completed' && $prod['current_stock'] < $qty) {
                    throw new \Exception("Insufficient stock for '{$prod['name']}'. Available: {$prod['current_stock']}, Requested: {$qty}. Cannot sell more than available stock.");
                }

                $gross = round($qty * $unitPrice, 2);
                $discAmt = round(($gross * $itemDiscountPercent) / 100, 2);
                $rowTaxable = max(0, $gross - $discAmt);
                $taxPercent = (float)($item['tax_percent'] ?? $prod['gst_percent'] ?? 12);
                $taxAmt = round(($rowTaxable * $taxPercent) / 100, 2);
                $rowTotal = $rowTaxable + $taxAmt;

                $subtotal += $gross;
                $itemDiscountTotal += $discAmt;
                $taxTotal += $taxAmt;

                $validatedItems[] = [
                    'product_id' => $prodId,
                    'product_name' => $prod['name'],
                    'product_code' => $prod['code'] ?: ($prod['sku'] ?: $prod['barcode']),
                    'god_name' => $prod['god_name'] ?? '',
                    'material' => $prod['material'] ?? '',
                    'hsn_code' => $prod['hsn_code'] ?: '9701',
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $itemDiscountPercent,
                    'discount_amount' => $discAmt,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmt,
                    'total_amount' => $rowTotal
                ];
            }

            if (empty($validatedItems)) {
                throw new \Exception('No valid items found in invoice submission.');
            }

            // 4. Calculations: Overall Discount, Tax split (CGST+SGST vs IGST), Additional charges, Round-off
            $overallDiscountPercent = max(0, min(100, (float)$request->input('overall_discount_percent', 0)));
            $overallDiscountAmount = round((($subtotal - $itemDiscountTotal) * $overallDiscountPercent) / 100, 2);
            $additionalCharges = max(0, (float)$request->input('additional_charges', 0));

            $companyState = getSetting('company_state', 'Tamil Nadu');
            $customerState = trim($customer['state'] ?? 'Tamil Nadu');
            $isInterstate = (!empty($customerState) && strcasecmp($customerState, $companyState) !== 0);

            if ($isInterstate) {
                $cgst = 0.00;
                $sgst = 0.00;
                $igst = $taxTotal;
            } else {
                $cgst = round($taxTotal / 2, 2);
                $sgst = round($taxTotal - $cgst, 2);
                $igst = 0.00;
            }

            $rawTotal = ($subtotal - $itemDiscountTotal - $overallDiscountAmount) + $taxTotal + $additionalCharges;
            $grandTotal = round($rawTotal);
            $roundOff = round($grandTotal - $rawTotal, 2);

            $paidAmountInput = (float)$request->input('paid_amount', 0);
            $paidAmount = min($grandTotal, max(0, $paidAmountInput));
            if ($paymentMethod === 'credit') {
                $paidAmount = 0.00;
            } elseif ($paidAmount == 0 && $paymentMethod !== 'credit' && $request->input('paid_amount') === null) {
                $paidAmount = $grandTotal;
            }

            $dueAmount = max(0, $grandTotal - $paidAmount);
            $paymentStatus = $dueAmount <= 0.01 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            $invoiceData = [
                'invoice_no' => $invoiceNo,
                'invoice_type' => $invoiceType,
                'reference_no' => $referenceNo,
                'customer_id' => $customerId,
                'user_id' => Auth::id() ?: 1,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'invoice_time' => date('H:i:s'),
                'payment_terms' => $paymentTerms,
                'subtotal' => $subtotal,
                'item_discount_total' => $itemDiscountTotal,
                'overall_discount_percent' => $overallDiscountPercent,
                'overall_discount_amount' => $overallDiscountAmount,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_amount' => $taxTotal,
                'additional_charges' => $additionalCharges,
                'round_off' => $roundOff,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'notes' => $notes
            ];

            // 5. Save or Update Invoice Record
            if ($existingId > 0) {
                $invoiceId = $existingId;
                $invModel->update($invoiceId, $invoiceData);
                $db->query("DELETE FROM `invoice_items` WHERE invoice_id = ?", [$invoiceId]);
            } else {
                $invoiceId = $invModel->create($invoiceData);
            }

            // 6. Insert Items & Apply Stock Deductions if Completed
            foreach ($validatedItems as $v) {
                $db->query("INSERT INTO `invoice_items` 
                            (`invoice_id`, `product_id`, `product_name`, `product_code`, `god_name`, `material`, `hsn_code`, `quantity`, `unit_price`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `total_amount`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                    $invoiceId, $v['product_id'], $v['product_name'], $v['product_code'], $v['god_name'], $v['material'], $v['hsn_code'],
                    $v['quantity'], $v['unit_price'], $v['discount_percent'], $v['discount_amount'], $v['tax_percent'], $v['tax_amount'], $v['total_amount']
                ]);

                if ($status === 'completed') {
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
            }

            // 7. Payment Bookkeeping & Ledger (if completed)
            if ($status === 'completed' && $paidAmount > 0) {
                $paymentNo = $payModel->generatePaymentNo();
                $payModel->create([
                    'payment_no' => $paymentNo,
                    'payment_type' => 'customer_payment',
                    'account_id' => $accountId,
                    'customer_id' => $customerId,
                    'invoice_id' => (int)$invoiceId,
                    'amount' => $paidAmount,
                    'payment_method' => in_array($paymentMethod, ['cash', 'card', 'upi', 'bank_transfer']) ? $paymentMethod : 'cash',
                    'transaction_reference' => $referenceNo ?: "Bill {$invoiceNo}",
                    'payment_date' => $invoiceDate,
                    'notes' => "Payment for {$invoiceNo}",
                    'user_id' => Auth::id() ?: 1
                ]);

                $accModel->recordTransaction(
                    $accountId,
                    'debit',
                    $paidAmount,
                    'invoice',
                    (int)$invoiceId,
                    $invoiceNo,
                    "Sales receipt {$invoiceNo}",
                    $invoiceDate,
                    Auth::id() ?: 1
                );
            }

            // 8. Update Customer Balance
            if ($status === 'completed' && $dueAmount > 0) {
                $updatedCust = $custModel->find($customerId);
                if ($updatedCust) {
                    $newCustBal = (float)$updatedCust['outstanding_balance'] + $dueAmount;
                    $custModel->update($customerId, ['outstanding_balance' => $newCustBal]);
                }
            }

            $db->commit();
            $actionWord = $existingId > 0 ? 'Updated' : 'Created';
            $this->logActivity('create', 'billing', (int)$invoiceId, $invoiceNo, "{$actionWord} invoice {$invoiceNo} for " . formatCurrency($grandTotal));

            Response::success("Invoice {$actionWord} successfully!", [
                'invoice_id' => $invoiceId,
                'invoice_no' => $invoiceNo,
                'grand_total' => $grandTotal,
                'print_a4_url' => url("billing/invoice/{$invoiceId}/print?type=a4"),
                'print_thermal_url' => url("billing/invoice/{$invoiceId}/print?type=thermal"),
                'pdf_url' => url("billing/invoice/{$invoiceId}/pdf"),
                'view_url' => url("billing/invoice/{$invoiceId}"),
                'invoices_url' => url("billing/invoices")
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
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

            // Restock items only if was previously completed
            if ($invoice['status'] === 'completed') {
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

                // Restore customer outstanding balance if due amount was outstanding
                if ((float)$invoice['due_amount'] > 0) {
                    $custModel = new Customer();
                    $cust = $custModel->find((int)$invoice['customer_id']);
                    if ($cust) {
                        $newCustBal = max(0, (float)$cust['outstanding_balance'] - (float)$invoice['due_amount']);
                        $custModel->update((int)$cust['id'], ['outstanding_balance' => $newCustBal]);
                    }
                }
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
