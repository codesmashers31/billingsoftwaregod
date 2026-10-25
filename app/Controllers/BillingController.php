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
use App\Models\Supplier;
use App\Services\WhatsAppService;

class BillingController extends Controller {
    public function pos(Request $request): void {
        $this->requirePermission('billing.pos');
        $prodModel = new Product();
        $catModel = new Category();
        $custModel = new Customer();
        $accModel = new Account();

        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $initialProducts = $prodModel->searchForPos('', null, 100);
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
        $products = $prodModel->searchForPos($q, $categoryId, 100);
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
        $suppModel = new Supplier();

        $search = trim((string)$request->input('search', ''));
        $paymentStatus = trim((string)$request->input('payment_status', ''));

        $filters = [
            'search' => $search,
            'payment_status' => $paymentStatus,
        ];

        $invoices = $suppModel->getSupplierInvoiceList($search ?: null, $paymentStatus ?: null);

        if ($request->isAjax() && ($request->input('ajax_table') || $request->input('json'))) {
            Response::json([
                'success' => true,
                'data' => $invoices
            ]);
        }

        $this->render('billing.invoices', [
            'pageTitle' => 'Supplier Purchase & Payment Management',
            'invoices' => $invoices,
            'filters' => $filters
        ]);
    }

    public function customerInvoices(Request $request): void {
        $this->requirePermission('billing.view');
        $invModel = new Invoice();

        $page = max(1, (int)$request->input('page', 1));
        $perPage = max(10, min(100, (int)$request->input('per_page', 20)));

        $search = trim((string)$request->input('search', ''));
        $paymentStatus = trim((string)$request->input('payment_status', ''));
        $invoiceType = trim((string)$request->input('invoice_type', ''));
        $paymentMethod = trim((string)$request->input('payment_method', ''));
        $dateFrom = trim((string)$request->input('date_from', ''));
        $dateTo = trim((string)$request->input('date_to', ''));
        $salesMode = trim((string)$request->input('sales_mode', ''));

        $filters = [
            'search' => $search,
            'payment_status' => $paymentStatus,
            'invoice_type' => $invoiceType,
            'payment_method' => $paymentMethod,
            'sales_mode' => $salesMode,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        $paginated = $invModel->getPaginatedList($page, $perPage, $filters);
        $metrics = $invModel->getSummaryMetrics($filters);

        if ($request->isAjax() && ($request->input('ajax_table') || $request->input('json'))) {
            Response::json([
                'success' => true,
                'data' => $paginated['data'],
                'pagination' => [
                    'total' => $paginated['total'],
                    'page' => $paginated['page'],
                    'per_page' => $paginated['per_page'],
                    'total_pages' => $paginated['total_pages'],
                ],
                'metrics' => $metrics
            ]);
            return;
        }

        $this->render('billing.customer-invoices', [
            'pageTitle' => 'Customer Invoice Details & Register',
            'invoices' => $paginated['data'],
            'pagination' => $paginated,
            'filters' => $filters,
            'metrics' => $metrics
        ]);
    }

    public function supplierDetail(Request $request, array $params): void {
        $this->requirePermission('billing.view');
        $id = (int)($params['id'] ?? $request->input('supplier_id', 0));
        $suppModel = new Supplier();
        $detail = $suppModel->getSupplierInvoiceWithDetails($id);
        if (!$detail) {
            Response::error('Supplier not found.');
        }
        Response::json(['success' => true, 'data' => $detail]);
    }

    public function updateSupplierPayment(Request $request): void {
        $this->requirePermission('billing.view');
        $supplierId = (int)$request->input('supplier_id', 0);
        if ($supplierId <= 0) {
            Response::error('Valid Supplier ID is required.');
        }

        $paidAmount = (float)$request->input('paid_amount', 0);
        $additionalAmount = (float)$request->input('additional_amount', 0);
        $status = (string)$request->input('payment_status', 'Unpaid');
        $method = (string)$request->input('payment_method', 'Cash');
        $date = (string)$request->input('payment_date', date('Y-m-d'));
        $notes = trim((string)$request->input('notes', ''));

        try {
            $suppModel = new Supplier();
            $result = $suppModel->updateSupplierPayment(
                $supplierId,
                $paidAmount,
                $status,
                $method,
                $date,
                $notes,
                Auth::id() ?: 1,
                $additionalAmount
            );

            $this->logActivity('update', 'suppliers', $supplierId, $result['supplier_id'], "Updated payment for {$result['supplier_name']}: Status {$result['payment_status']}, Paid ₹{$result['paid_amount']}");

            Response::success('Supplier payment updated successfully!', $result);
        } catch (\Throwable $e) {
            Response::error($e->getMessage());
        }
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

            $custMobileInput = trim((string)$request->input('customer_mobile', ''));
            $custNameInput = trim((string)$request->input('customer_name', ''));
            $cleanDigits = preg_replace('/\D/', '', $custMobileInput);

            if (empty($cleanDigits) || strlen($cleanDigits) < 10) {
                throw new \Exception('Customer Details Required: Please provide a valid 10-digit Customer Mobile Number to complete billing.');
            }

            if (empty($custNameInput)) {
                throw new \Exception('Customer Details Required: Please provide Customer Full Name to complete billing.');
            }

            $existing = $custModel->findBy('mobile', $cleanDigits);
            if (!$existing && $cleanDigits !== $custMobileInput) {
                $existing = $custModel->findBy('mobile', $custMobileInput);
            }

            if ($existing) {
                $customer = $existing;
                $customerId = (int)$customer['id'];
                if (!empty($custNameInput) && $customer['name'] === 'Walk-in Retail Customer') {
                    $custModel->update($customerId, ['name' => $custNameInput]);
                    $customer['name'] = $custNameInput;
                }
            } else {
                $custCode = $custModel->generateCustomerCode();
                $newCustId = $custModel->create([
                    'customer_code' => $custCode,
                    'name' => $custNameInput,
                    'mobile' => $cleanDigits,
                    'customer_type' => 'retail',
                    'status' => 'active'
                ]);
                $customer = $custModel->find($newCustId);
                $customerId = (int)$newCustId;
            }

            $invoiceNo = $invModel->generateInvoiceNo();
            $today = date('Y-m-d');
            $nowTime = date('H:i:s');

            $subtotal = 0.00;
            $itemDiscountTotal = 0.00;
            $taxableAmount = 0.00;
            $taxTotal = 0.00;
            $validatedItems = [];

            // Check for dynamic bill-level GST tax percentage if specified by billing person
            $billGstInput = $request->input('bill_gst_percent', null);
            $hasBillGst = ($billGstInput !== null && $billGstInput !== '' && is_numeric($billGstInput));
            $billGstPercent = $hasBillGst ? max(0, min(100, (float)$billGstInput)) : null;

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
                    $outOfStockNote = "Out-of-stock sale: '{$prod['name']}' (Stock was: {$prod['current_stock']}, Sold: {$qty})";
                    $notes = $notes ? ($notes . " | " . $outOfStockNote) : $outOfStockNote;
                }

                $gross = $qty * $unitPrice;
                $discAmt = ($gross * $itemDiscountPercent) / 100;
                $netItem = $gross - $discAmt;

                // Priority: Bill-level dynamic GST > Item-level dynamic GST > Product default GST > Fallback 12%
                if ($hasBillGst) {
                    $taxPercent = $billGstPercent;
                } elseif (isset($item['gst_percent']) && $item['gst_percent'] !== '' && is_numeric($item['gst_percent'])) {
                    $taxPercent = max(0, min(100, (float)$item['gst_percent']));
                } elseif (isset($item['tax_percent']) && $item['tax_percent'] !== '' && is_numeric($item['tax_percent'])) {
                    $taxPercent = max(0, min(100, (float)$item['tax_percent']));
                } else {
                    $taxPercent = (float)($prod['gst_percent'] ?? 12);
                }

                $taxAmt = round(($netItem * $taxPercent) / 100, 2);
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

            // Overall discount calculation (supporting amount in Rupees or percentage)
            $overallDiscountAmountInput = $request->input('overall_discount_amount', null);
            $netSubtotalForDisc = max(0, $subtotal - $itemDiscountTotal);

            if ($overallDiscountAmountInput !== null && $overallDiscountAmountInput !== '') {
                $overallDiscountAmount = min($netSubtotalForDisc, max(0, (float)$overallDiscountAmountInput));
                $overallDiscountPercent = $netSubtotalForDisc > 0 ? round(($overallDiscountAmount / $netSubtotalForDisc) * 100, 2) : 0.00;
            } else {
                $overallDiscountPercent = max(0, (float)$request->input('overall_discount_percent', 0));
                $overallDiscountAmount = round(($netSubtotalForDisc * $overallDiscountPercent) / 100, 2);
            }

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
            $rawMethods = $request->input('payment_methods', null);
            if ($rawMethods === null) {
                $rawMethods = $request->input('payment_method', 'cash');
            }
            if (is_array($rawMethods)) {
                $methodsList = array_values(array_filter(array_map('strtolower', array_map('trim', $rawMethods))));
            } else {
                $methodsList = array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', (string)$rawMethods)))));
            }
            if (empty($methodsList)) {
                $methodsList = ['cash'];
            }

            if (count($methodsList) > 1) {
                $primaryMethod = 'split';
            } else {
                $primaryMethod = reset($methodsList);
                if (!in_array($primaryMethod, ['cash', 'card', 'upi', 'bank_transfer', 'credit'])) {
                    $primaryMethod = 'cash';
                }
            }

            $rawMethodAmounts = $request->input('payment_amounts', []);
            $cashAmt = is_array($rawMethodAmounts) && isset($rawMethodAmounts['cash']) ? max(0, (float)$rawMethodAmounts['cash']) : 0.0;
            $upiAmt = is_array($rawMethodAmounts) && isset($rawMethodAmounts['upi']) ? max(0, (float)$rawMethodAmounts['upi']) : 0.0;
            $bankAmt = is_array($rawMethodAmounts) && isset($rawMethodAmounts['bank_transfer']) ? max(0, (float)$rawMethodAmounts['bank_transfer']) : 0.0;
            $creditAmt = is_array($rawMethodAmounts) && isset($rawMethodAmounts['credit']) ? max(0, (float)$rawMethodAmounts['credit']) : 0.0;
            $immediatePaid = round($cashAmt + $upiAmt + $bankAmt, 2);

            $hasPaidParam = ($request->input('paid_amount') !== null && $request->input('paid_amount') !== '');
            if (!empty($rawMethodAmounts) && ($immediatePaid > 0 || $creditAmt > 0)) {
                $paidAmount = min($grandTotal, $immediatePaid);
            } elseif ($primaryMethod === 'credit' && !$hasPaidParam) {
                $paidAmount = 0.00;
            } elseif ($hasPaidParam) {
                $paidAmount = min($grandTotal, max(0, (float)$request->input('paid_amount')));
            } else {
                $paidAmount = $grandTotal;
            }

            $dueAmount = max(0, round($grandTotal - $paidAmount, 2));
            $paymentStatus = $dueAmount <= 0.001 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            // Format split payment breakdown note
            $breakdownParts = [];
            if ($cashAmt > 0) $breakdownParts[] = "Cash: ₹" . number_format($cashAmt, 2);
            if ($upiAmt > 0) $breakdownParts[] = "UPI: ₹" . number_format($upiAmt, 2);
            if ($bankAmt > 0) $breakdownParts[] = "Bank: ₹" . number_format($bankAmt, 2);
            if ($creditAmt > 0) $breakdownParts[] = "Credit: ₹" . number_format($creditAmt, 2);
            $breakdownDetail = !empty($breakdownParts) ? implode(', ', $breakdownParts) : (count($methodsList) > 1 ? implode(', ', array_map('ucfirst', $methodsList)) : "");
            
            $invoiceNotes = $notes;
            if (!empty($breakdownDetail)) {
                $invoiceNotes = $notes ? "{$notes} | Payment: [{$breakdownDetail}]" : "Payment: [{$breakdownDetail}]";
            }

            // Sales Channel / Mode (Offline vs Online)
            $rawSalesMode = strtolower(trim((string)$request->input('sales_mode', 'offline')));
            $salesMode = in_array($rawSalesMode, ['offline', 'online']) ? $rawSalesMode : 'offline';

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
                'payment_method' => $primaryMethod,
                'sales_mode' => $salesMode,
                'status' => 'completed',
                'payment_status' => $paymentStatus,
                'notes' => $invoiceNotes
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

                $breakdownParts = [];
                if ($cashAmt > 0) $breakdownParts[] = "Cash: ₹" . number_format($cashAmt, 2);
                if ($upiAmt > 0) $breakdownParts[] = "UPI: ₹" . number_format($upiAmt, 2);
                if ($bankAmt > 0) $breakdownParts[] = "Bank: ₹" . number_format($bankAmt, 2);
                if ($creditAmt > 0) $breakdownParts[] = "Credit: ₹" . number_format($creditAmt, 2);
                $breakdownNotes = !empty($breakdownParts) ? " (" . implode(', ', $breakdownParts) . ")" : (count($methodsList) > 1 ? " (" . implode(', ', array_map('ucfirst', $methodsList)) . ")" : "");

                $payModel->create([
                    'payment_no' => $paymentNo,
                    'payment_type' => 'customer_payment',
                    'account_id' => $accountId,
                    'customer_id' => $customerId,
                    'invoice_id' => (int)$invoiceId,
                    'amount' => $paidAmount,
                    'payment_method' => in_array($primaryMethod, ['cash', 'card', 'upi', 'bank_transfer']) ? $primaryMethod : (in_array('upi', $methodsList) ? 'upi' : 'cash'),
                    'transaction_reference' => "Bill {$invoiceNo}",
                    'payment_date' => $today,
                    'notes' => "POS Sales receipt for {$invoiceNo}{$breakdownNotes}",
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

            // Construct direct itemized bill text (NO PDF link)
            $storeName = getSetting('company_name', 'Sastha Sasti - Divine Heritage');
            $directBillText = "*{$storeName}*\n"
                . "*CASH BILL / SALES INVOICE*\n"
                . "--------------------------------\n"
                . "🧾 *Bill No:* {$invoiceNo}\n"
                . "📅 *Date:* " . date('d-m-Y h:i A') . "\n"
                . "👤 *Customer:* {$customer['name']}\n"
                . "📱 *Mobile:* {$customer['mobile']}\n"
                . "--------------------------------\n"
                . "*ITEMS PURCHASED:*\n";

            foreach ($validatedItems as $v) {
                $directBillText .= "• {$v['product_name']} ({$v['quantity']} x ₹" . number_format($v['unit_price'], 2) . ") = ₹" . number_format($v['total_amount'], 2) . "\n";
            }

            $directBillText .= "--------------------------------\n"
                . "Subtotal: ₹" . number_format($subtotal, 2) . "\n";
            if ($itemDiscountTotal + $overallDiscountAmount > 0) {
                $directBillText .= "Discount: -₹" . number_format($itemDiscountTotal + $overallDiscountAmount, 2) . "\n";
            }
            $directBillText .= "GST Tax: ₹" . number_format($taxTotal, 2) . "\n"
                . "*Total Amount: ₹" . number_format($grandTotal, 2) . "*\n"
                . "Paid: ₹" . number_format($paidAmount, 2) . " (" . ucfirst($primaryMethod) . ")\n"
                . "--------------------------------\n"
                . "Thank you for shopping with us! 🌸\n"
                . "Visit again!";

            // Direct background WhatsApp dispatch if requested
            $receiptDelivery = $request->input('receipt_delivery', 'thermal');
            $waStatus = null;
            if ($receiptDelivery === 'whatsapp' || $receiptDelivery === 'both') {
                $waStatus = WhatsAppService::sendDirectMessage($cleanDigits, $directBillText);
            }

            Response::success('Invoice completed successfully!', [
                'invoice_id' => $invoiceId,
                'invoice_no' => $invoiceNo,
                'customer_name' => $customer['name'] ?? 'Customer',
                'customer_mobile' => $customer['mobile'] ?? '',
                'grand_total' => $grandTotal,
                'print_thermal_url' => url("billing/invoice/{$invoiceId}/print?type=thermal"),
                'view_url' => url("billing/invoice/{$invoiceId}"),
                'whatsapp_status' => $waStatus,
                'whatsapp_sent' => !empty($waStatus['success']),
                'whatsapp_message' => $waStatus['message'] ?? null,
                'direct_bill_text' => $directBillText
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function viewInvoice(Request $request, array $params): void {
        $this->requirePermission('billing.view');
        $id = (int)($params['id'] ?? 0);
        $suppModel = new Supplier();

        $supplierInvoice = $suppModel->getSupplierInvoiceWithDetails($id);
        if ($supplierInvoice) {
            $this->render('billing.invoice-view', [
                'pageTitle' => "Supplier Invoice: {$supplierInvoice['supplier_id']}",
                'supplier' => $supplierInvoice,
                'products' => $supplierInvoice['products']
            ]);
            return;
        }

        $invModel = new Invoice();
        $invoice = $invModel->getWithDetails($id);
        if ($invoice) {
            $items = $invModel->getItems($id);
            $payModel = new Payment();
            $accModel = new Account();
            $payments = $payModel->where('invoice_id = ?', [$id], 'payment_date DESC, id DESC');
            $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'type ASC, name ASC');

            if ($request->isAjax() || $request->input('json')) {
                Response::json([
                    'success' => true,
                    'invoice' => $invoice,
                    'items' => $items,
                    'payments' => $payments,
                    'accounts' => $accounts
                ]);
                return;
            }

            $this->render('billing.invoice-view', [
                'pageTitle' => "Invoice: {$invoice['invoice_no']}",
                'invoice' => $invoice,
                'items' => $items,
                'payments' => $payments,
                'accounts' => $accounts
            ]);
            return;
        }

        Session::flash('error', 'Invoice record not found.');
        Response::redirect('/billing/invoices');
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
                    $notes = $notes ? ($notes . " | Out-of-stock sale: '{$prod['name']}' (Stock was: {$prod['current_stock']}, Sold: {$qty})") : "Out-of-stock sale: '{$prod['name']}' (Stock was: {$prod['current_stock']}, Sold: {$qty})";
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

            $rawSalesMode = strtolower(trim((string)$request->input('sales_mode', 'offline')));
            $salesMode = in_array($rawSalesMode, ['offline', 'online']) ? $rawSalesMode : 'offline';

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
                'sales_mode' => $salesMode,
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

    public function sendWhatsApp(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        $invModel = new Invoice();
        $invoice = $invModel->getWithDetails($id);
        if (!$invoice) {
            Response::error('Invoice not found.');
        }

        $items = $invModel->getItems($id);
        $storeName = getSetting('company_name', 'Sastha Sasti - Divine Heritage');
        $cleanPhone = preg_replace('/\D/', '', (string)($request->input('mobile') ?: $invoice['customer_mobile']));

        if (empty($cleanPhone) || strlen($cleanPhone) < 10) {
            Response::error('Valid 10-digit customer mobile number is required.');
        }

        $directBillText = "*{$storeName}*\n"
            . "*CASH BILL / SALES INVOICE*\n"
            . "--------------------------------\n"
            . "🧾 *Bill No:* {$invoice['invoice_no']}\n"
            . "📅 *Date:* " . date('d-m-Y h:i A', strtotime($invoice['invoice_date'] . ' ' . $invoice['invoice_time'])) . "\n"
            . "👤 *Customer:* {$invoice['customer_name']}\n"
            . "📱 *Mobile:* {$cleanPhone}\n"
            . "--------------------------------\n"
            . "*ITEMS PURCHASED:*\n";

        foreach ($items as $item) {
            $directBillText .= "• {$item['product_name']} ({$item['quantity']} x ₹" . number_format($item['unit_price'], 2) . ") = ₹" . number_format($item['total_amount'], 2) . "\n";
        }

        $directBillText .= "--------------------------------\n"
            . "Subtotal: ₹" . number_format($invoice['subtotal'], 2) . "\n";
        if ((float)$invoice['overall_discount_amount'] + (float)$invoice['item_discount_total'] > 0) {
            $directBillText .= "Discount: -₹" . number_format($invoice['overall_discount_amount'] + $invoice['item_discount_total'], 2) . "\n";
        }
        $directBillText .= "GST Tax: ₹" . number_format($invoice['tax_amount'], 2) . "\n"
            . "*Total Amount: ₹" . number_format($invoice['grand_total'], 2) . "*\n"
            . "Status: Paid\n"
            . "--------------------------------\n"
            . "Thank you for shopping with us! 🌸\n"
            . "Visit again!";

        $res = WhatsAppService::sendDirectMessage($cleanPhone, $directBillText);
        if ($res['success']) {
            Response::success('Direct WhatsApp bill sent to customer successfully!', $res);
        } else {
            Response::error($res['message'] ?? 'Direct WhatsApp API not configured yet.', $res);
        }
    }
}
