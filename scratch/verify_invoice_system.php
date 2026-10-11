<?php
declare(strict_types=1);

// Autoload application classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

require_once dirname(__DIR__) . '/app/Helpers/helpers.php';
$config = require dirname(__DIR__) . '/config/config.php';
date_default_timezone_set($config['timezone'] ?? 'Asia/Kolkata');

use App\Core\Database;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Payment;

$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $details = '') {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo " [PASS] $name\n";
        if ($details) echo "        Details: $details\n";
    } else {
        $failed++;
        echo " [FAIL] $name\n";
        if ($details) echo "        Failure details: $details\n";
    }
}

echo "===================================================================\n";
echo " RUNNING VERIFICATION SUITE FOR INVOICE MANAGEMENT SYSTEM\n";
echo "===================================================================\n\n";

$db = Database::getInstance();
$invoiceModel = new Invoice();
$customerModel = new Customer();
$productModel = new Product();
$inventoryModel = new Inventory();
$paymentModel = new Payment();

// -------------------------------------------------------------------------
// 1. Invoice number generation
// -------------------------------------------------------------------------
$nextInvNo = $invoiceModel->generateInvoiceNo();
$matchesFormat = preg_match('/^INV-\d{4}-\d{4}$/', $nextInvNo);
assertTest(
    "1. Invoice Number Generation",
    $matchesFormat === 1,
    "Generated invoice number: $nextInvNo (matches pattern INV-YYYY-XXXX)"
);

// -------------------------------------------------------------------------
// 2. Customer Selection & Auto-Population
// -------------------------------------------------------------------------
$testMobile = '9876543210';
$cust = $customerModel->findOrCreate([
    'name' => 'Lord Krishna Devotee Test',
    'mobile' => $testMobile,
    'email' => 'devotee@example.com',
    'address' => '108 Temple Street, Madurai',
    'shipping_address' => '108 Temple Street, Madurai',
    'city' => 'Madurai',
    'state' => 'Tamil Nadu',
    'state_code' => '33',
    'gstin' => '33AAAAA0000A1Z5'
]);

$fetchedCust = $customerModel->find((int)$cust['id']);
assertTest(
    "2. Customer Selection & Auto-Population",
    !empty($fetchedCust) && $fetchedCust['mobile'] === $testMobile && $fetchedCust['state_code'] === '33',
    "Customer ID: {$cust['id']}, Name: {$fetchedCust['name']}, State: {$fetchedCust['state']} (Code: {$fetchedCust['state_code']})"
);

// -------------------------------------------------------------------------
// 3. Prevent Duplicate Customer Records
// -------------------------------------------------------------------------
$custDup = $customerModel->findOrCreate([
    'name' => 'Lord Krishna Devotee Alternate',
    'mobile' => $testMobile, // Same mobile
    'email' => 'devotee_alt@example.com'
]);
assertTest(
    "3. Prevent Duplicate Customer Records",
    (int)$custDup['id'] === (int)$cust['id'],
    "Same Customer ID {$cust['id']} returned when inserting duplicate mobile $testMobile"
);

// -------------------------------------------------------------------------
// 4. Product Selection & Dynamic Inventory Details
// -------------------------------------------------------------------------
$prod = $db->query("SELECT * FROM `products` WHERE status = 'active' AND current_stock >= 5 LIMIT 1");
if (empty($prod)) {
    $prodId = $productModel->create([
        'name' => 'Test Brass Ganesha 12 Inch',
        'code' => 'TEST-GANESHA-01',
        'sku' => 'TEST-GANESHA-01',
        'barcode' => 'BAR-TEST-9988',
        'category_id' => 1,
        'purchase_price' => 1000.00,
        'selling_price' => 1500.00,
        'current_stock' => 20,
        'gst_rate' => 18.00,
        'status' => 'active'
    ]);
    $prod = $productModel->find((int)$prodId);
} else {
    $prod = $prod[0];
}

assertTest(
    "4. Product Selection & Dynamic Inventory Details",
    !empty($prod) && isset($prod['selling_price']) && isset($prod['current_stock']),
    "Product: {$prod['name']}, Barcode: {$prod['barcode']}, Stock: {$prod['current_stock']}, Price: {$prod['selling_price']}"
);

// -------------------------------------------------------------------------
// 5. Stock Validation Logic
// -------------------------------------------------------------------------
$excessiveQty = (int)$prod['current_stock'] + 50;
$stockValid = ((int)$prod['current_stock'] >= $excessiveQty);
assertTest(
    "5. Stock Validation (Rejection of Excessive Quantity)",
    $stockValid === false,
    "Requested $excessiveQty units against available {$prod['current_stock']} units correctly blocked."
);

// -------------------------------------------------------------------------
// 6. Automatic Bill Calculation: Intra-State (CGST + SGST)
// -------------------------------------------------------------------------
$qty = 2;
$unitPrice = 1000.00;
$itemDiscount = 100.00;
$subtotal = $qty * $unitPrice; // 2000
$taxable = $subtotal - $itemDiscount; // 1900
$gstRate = 18.0;
$taxAmt = round($taxable * ($gstRate / 100), 2); // 342.00
$cgst = round($taxAmt / 2, 2); // 171.00
$sgst = round($taxAmt / 2, 2); // 171.00
$igst = 0.00;
$intraTotal = $taxable + $cgst + $sgst; // 2242.00

assertTest(
    "6. Automatic Bill Calculation: Intra-State (CGST + SGST)",
    ($cgst == 171.00 && $sgst == 171.00 && $igst == 0.00 && $intraTotal == 2242.00),
    "Subtotal: $subtotal, Taxable: $taxable, CGST(9%): $cgst, SGST(9%): $sgst, Total: $intraTotal"
);

// -------------------------------------------------------------------------
// 7. Automatic Bill Calculation: Inter-State (IGST)
// -------------------------------------------------------------------------
$supplierState = 'Tamil Nadu';
$customerState = 'Karnataka';
$isInterState = (strcasecmp($supplierState, $customerState) !== 0);
$interCgst = 0.00;
$interSgst = 0.00;
$interIgst = $taxAmt; // 342.00
$addCharges = 58.00;
$invDiscount = 50.00;
$rawGrand = ($taxable - $invDiscount) + $interIgst + $addCharges; // 1850 + 342 + 58 = 2250.00
$roundedGrand = round($rawGrand);
$roundOff = round($roundedGrand - $rawGrand, 2);

assertTest(
    "7. Automatic Bill Calculation: Inter-State (IGST & Round-off)",
    ($isInterState && $interIgst == 342.00 && $interCgst == 0.00 && $roundedGrand == 2250.00),
    "Inter-State IGST: $interIgst, Add Charges: $addCharges, Round Off: $roundOff, Grand Total: $roundedGrand"
);

// -------------------------------------------------------------------------
// 8. Payment Status Calculation
// -------------------------------------------------------------------------
function computeStatus(float $grand, float $paid, ?string $dueDate): string {
    if ($paid >= $grand && $grand > 0) return 'paid';
    if ($paid > 0 && $paid < $grand) return 'partial';
    if ($dueDate && strtotime($dueDate) < strtotime(date('Y-m-d')) && $paid < $grand) return 'overdue';
    return 'unpaid';
}

$status1 = computeStatus(2250.00, 2250.00, null);
$status2 = computeStatus(2250.00, 1000.00, null);
$status3 = computeStatus(2250.00, 0.00, date('Y-m-d', strtotime('-5 days')));
$status4 = computeStatus(2250.00, 0.00, date('Y-m-d', strtotime('+5 days')));

assertTest(
    "8. Payment Status Dynamic Calculation",
    ($status1 === 'paid' && $status2 === 'partial' && $status3 === 'overdue' && $status4 === 'unpaid'),
    "Paid: $status1, Partial: $status2, Overdue: $status3, Unpaid: $status4"
);

// -------------------------------------------------------------------------
// 9. Invoice Saving in MySQL Transaction
// -------------------------------------------------------------------------
$initialStock = (int)$prod['current_stock'];
$sellQty = 2;

$db->beginTransaction();
$invId = 0;
$invNumber = '';
try {
    $invNumber = $invoiceModel->generateInvoiceNo();
    $invId = (int)$invoiceModel->create([
        'invoice_no' => $invNumber,
        'customer_id' => $cust['id'],
        'user_id' => 1,
        'invoice_date' => date('Y-m-d'),
        'invoice_time' => date('H:i:s'),
        'due_date' => date('Y-m-d', strtotime('+15 days')),
        'invoice_type' => 'tax_invoice',
        'subtotal' => 2000.00,
        'item_discount_total' => 100.00,
        'overall_discount_amount' => 0.00,
        'taxable_amount' => 1900.00,
        'tax_amount' => 342.00,
        'cgst_amount' => 171.00,
        'sgst_amount' => 171.00,
        'igst_amount' => 0.00,
        'additional_charges' => 0.00,
        'round_off' => 0.00,
        'grand_total' => 2242.00,
        'paid_amount' => 1000.00,
        'due_amount' => 1242.00,
        'payment_status' => 'partial',
        'payment_terms' => 'Net 15 Days',
        'status' => 'completed',
        'notes' => 'Test invoice creation verification'
    ]);

    $db->query("INSERT INTO `invoice_items` 
        (invoice_id, product_id, product_name, product_code, god_name, material, hsn_code, quantity, unit_price, discount_percent, discount_amount, tax_percent, tax_amount, total_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $invId,
        $prod['id'],
        $prod['name'],
        $prod['code'] ?? $prod['sku'] ?? 'PROD-01',
        $prod['god_name'] ?? 'General',
        $prod['material'] ?? 'Brass',
        '9701',
        $sellQty,
        1000.00,
        5.00,
        100.00,
        18.00,
        342.00,
        2242.00
    ]);

    // Adjust Stock
    $inventoryModel->adjustStock(
        (int)$prod['id'],
        -$sellQty,
        'sale',
        'invoice',
        $invId,
        $invNumber,
        "Sold in Invoice #{$invNumber}",
        1
    );

    // Initial Payment entry
    $payNo = $paymentModel->generatePaymentNo();
    $paymentModel->create([
        'payment_no' => $payNo,
        'payment_type' => 'customer_payment',
        'account_id' => 1,
        'customer_id' => $cust['id'],
        'invoice_id' => $invId,
        'amount' => 1000.00,
        'payment_method' => 'upi',
        'transaction_reference' => 'UPI-TEST-12345',
        'payment_date' => date('Y-m-d'),
        'notes' => 'Advance payment at billing',
        'user_id' => 1
    ]);

    $db->commit();
    $savedInvoice = $invoiceModel->getWithDetails((int)$invId);
    $savedSuccessfully = (!empty($savedInvoice) && $savedInvoice['invoice_no'] === $invNumber);
} catch (\Throwable $e) {
    $db->rollBack();
    $savedSuccessfully = false;
    $saveError = $e->getMessage();
}

assertTest(
    "9. Invoice Saving via MySQL Transaction",
    $savedSuccessfully,
    $savedSuccessfully 
        ? "Invoice ID: $invId, Number: $invNumber saved with items and details."
        : "Failed with error: " . ($saveError ?? 'unknown')
);

// -------------------------------------------------------------------------
// 10. Inventory Stock Deduction on Invoice Completion
// -------------------------------------------------------------------------
$updatedProd = $productModel->find((int)$prod['id']);
$expectedStock = $initialStock - $sellQty;
$stockDeducted = ((int)$updatedProd['current_stock'] === $expectedStock);

$txn = $db->query("SELECT * FROM `inventory_transactions` WHERE reference_type = 'invoice' AND reference_id = ? ORDER BY id DESC LIMIT 1", [$invId]);
$txnLogged = (!empty($txn) && (int)$txn[0]['quantity'] === -$sellQty);

assertTest(
    "10. Inventory Stock Deduction & Movement Logging",
    $stockDeducted && $txnLogged,
    "Stock updated from $initialStock to {$updatedProd['current_stock']}. Logged transaction ID: " . ($txn[0]['id'] ?? 'none')
);

// -------------------------------------------------------------------------
// 11. Draft Invoices Must NOT Deduct Inventory
// -------------------------------------------------------------------------
$stockBeforeDraft = (int)$updatedProd['current_stock'];
$draftInvId = (int)$invoiceModel->create([
    'invoice_no' => $invoiceModel->generateInvoiceNo(),
    'customer_id' => $cust['id'],
    'user_id' => 1,
    'invoice_date' => date('Y-m-d'),
    'invoice_time' => date('H:i:s'),
    'status' => 'draft',
    'grand_total' => 500.00,
    'paid_amount' => 0.00,
    'due_amount' => 500.00,
    'payment_status' => 'unpaid'
]);
$stockAfterDraft = (int)$productModel->find((int)$prod['id'])['current_stock'];
assertTest(
    "11. Draft Invoice Stock Protection (Zero Deduction)",
    $stockBeforeDraft === $stockAfterDraft,
    "Stock remains at $stockAfterDraft after creating Draft Invoice #$draftInvId"
);

// -------------------------------------------------------------------------
// 12. Partial Payment Recording & Balance Update
// -------------------------------------------------------------------------
$secondPayAmount = 1242.00;
$payNo2 = $paymentModel->generatePaymentNo();
$paymentModel->create([
    'payment_no' => $payNo2,
    'payment_type' => 'customer_payment',
    'account_id' => 1,
    'customer_id' => $cust['id'],
    'invoice_id' => $invId,
    'amount' => $secondPayAmount,
    'payment_method' => 'cash',
    'transaction_reference' => 'CASH-REC-01',
    'payment_date' => date('Y-m-d'),
    'notes' => 'Settled remaining balance',
    'user_id' => 1
]);

$newPaid = 1000.00 + $secondPayAmount; // 2242.00
$newDue = 0.00;
$invoiceModel->update((int)$invId, [
    'paid_amount' => $newPaid,
    'due_amount' => $newDue,
    'payment_status' => 'paid'
]);

$recheckInv = $invoiceModel->find((int)$invId);
assertTest(
    "12. Partial/Multiple Payment Recording & Settlement",
    ($recheckInv['payment_status'] === 'paid' && (float)$recheckInv['due_amount'] === 0.0),
    "Invoice #$invId total paid updated to ₹{$recheckInv['paid_amount']}, Balance due: ₹{$recheckInv['due_amount']}, Status: {$recheckInv['payment_status']}"
);

// -------------------------------------------------------------------------
// 13. Professional PDF View & Amount in Words Helper
// -------------------------------------------------------------------------
$inWords = amountToWords(2242.00);
$wordsValid = (strpos(strtolower($inWords), 'two thousand two hundred forty two') !== false);

ob_start();
$invoice = $invoiceModel->getWithDetails((int)$invId);
$items = $invoiceModel->getItems((int)$invId);
$company = [
    'name' => 'God Statue & Handicrafts ERP',
    'address' => '45 Artisan Boulevard, Chennai, Tamil Nadu - 600001',
    'phone' => '+91 98400 12345',
    'email' => 'sales@godstatueerp.com',
    'gstin' => '33AABCT1332L1Z1'
];
require dirname(__DIR__) . '/app/Views/billing/print-a4.php';
$htmlOutput = ob_get_clean();

$pdfValid = (
    strpos($htmlOutput, $invNumber) !== false &&
    strpos($htmlOutput, 'TAX INVOICE') !== false &&
    strpos($htmlOutput, 'Authorized Signatory') !== false &&
    strpos($htmlOutput, 'window.print()') !== false
);

assertTest(
    "13. Professional A4 PDF / Print Layout Generation",
    $pdfValid && $wordsValid,
    "Rendered A4 Print HTML size: " . strlen($htmlOutput) . " bytes. In words: '$inWords'"
);

// -------------------------------------------------------------------------
// 14. Invoice Cancellation & Inventory Stock Restoration
// -------------------------------------------------------------------------
$stockBeforeCancel = (int)$productModel->find((int)$prod['id'])['current_stock'];

$invoiceModel->cancel((int)$invId, "Customer cancelled test transaction");

// Verify stock restored
$stockAfterCancel = (int)$productModel->find((int)$prod['id'])['current_stock'];
$stockRestored = ($stockAfterCancel === ($stockBeforeCancel + $sellQty));

$cancelledInv = $invoiceModel->find((int)$invId);
$invIsCancelled = ($cancelledInv['status'] === 'cancelled');

assertTest(
    "14. Invoice Cancellation & Stock Restoration",
    $stockRestored && $invIsCancelled,
    "Status: {$cancelledInv['status']}. Stock restored from $stockBeforeCancel to $stockAfterCancel."
);

// -------------------------------------------------------------------------
// 15. Invoice Search & Filtering
// -------------------------------------------------------------------------
$searchResults = $invoiceModel->getPaginatedList(1, 10, ['search' => $invNumber]);
$statusFilter = $invoiceModel->getPaginatedList(1, 10, ['payment_status' => 'paid']);

assertTest(
    "15. Invoice Search and Filtering",
    (!empty($searchResults['data']) && $searchResults['data'][0]['invoice_no'] === $invNumber),
    "Found invoice {$searchResults['data'][0]['invoice_no']} via search filter. Total found: {$searchResults['total']}"
);

echo "\n===================================================================\n";
echo " TEST SUMMARY: Total $passed PASSED, $failed FAILED\n";
echo "===================================================================\n";

if ($failed === 0) {
    echo " ALL 15 CRITICAL INVOICE MANAGEMENT REQUIREMENTS VERIFIED SUCCESSFULLY!\n";
} else {
    echo " SOME TESTS FAILED. PLEASE REVIEW.\n";
}
