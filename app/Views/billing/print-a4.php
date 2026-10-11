<?php
$items = $items ?? $invoice['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $invoice['invoice_no'] ?> - <?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }
            body {
                background: white !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-4">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?= url('billing/invoices') ?>" class="text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                <i class="fas fa-arrow-left"></i> Back to Invoices
            </a>
            <span class="text-xs text-slate-400 font-mono">Invoice: <strong class="text-slate-800"><?= $invoice['invoice_no'] ?></strong></span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-print"></i> Print / Download PDF
            </button>
            <button onclick="window.close()" class="text-slate-500 hover:text-slate-700 text-xs font-bold px-3 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>

    <!-- Printable A4 Document Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border border-slate-200 sm:rounded-2xl shadow-sm text-slate-900 text-xs leading-relaxed">
        
        <!-- Header -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="max-w-md">
                <div class="flex items-center gap-3 mb-2">
                    <img src="<?= asset('images/logo.png') ?>" alt="Logo" class="w-12 h-12 object-contain" onerror="this.style.display='none'">
                    <div>
                        <h1 class="text-xl font-black tracking-wide font-serif text-slate-950 uppercase"><?= sanitize(getSetting('company_name', 'Divya Murti & God Statue Heritage')) ?></h1>
                        <span class="text-[10px] text-amber-800 font-bold uppercase tracking-wider block">Sacred Idols & Temple Art Heritage</span>
                    </div>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed"><?= nl2br(sanitize(getSetting('company_address', "108 Sannidhi Square, Near Kapaleeshwarar Temple\nMylapore, Chennai - 600004, Tamil Nadu"))) ?></p>
                <div class="mt-1.5 text-[11px] font-mono text-slate-700 space-x-2">
                    <span>GSTIN: <strong><?= getSetting('company_gstin', '33AABCG1234H1Z0') ?></strong></span>
                    <span>•</span>
                    <span>Phone: <?= getSetting('company_phone', '+91 44 2464 1008') ?></span>
                    <span>•</span>
                    <span>State: Tamil Nadu (33)</span>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block bg-slate-900 text-white font-mono font-bold text-xs uppercase px-3 py-1 rounded mb-2 tracking-widest">
                    <?= ($invoice['invoice_type'] ?? 'tax_invoice') === 'proforma_invoice' ? 'PROFORMA INVOICE' : 'TAX INVOICE' ?>
                </span>
                <div class="font-mono text-xs space-y-0.5">
                    <div>Invoice No: <strong class="text-sm font-black text-slate-950"><?= $invoice['invoice_no'] ?></strong></div>
                    <div>Date: <strong><?= formatDate($invoice['invoice_date']) ?></strong></div>
                    <?php if (!empty($invoice['due_date'])): ?>
                        <div>Due Date: <strong><?= formatDate($invoice['due_date']) ?></strong></div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['reference_no'])): ?>
                        <div>Ref / PO: <strong><?= sanitize($invoice['reference_no']) ?></strong></div>
                    <?php endif; ?>
                    <div>Place of Supply: <strong><?= sanitize($invoice['customer_state'] ?? 'Tamil Nadu') ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Billed & Shipped To Box -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6">
            <!-- Billed To -->
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1 tracking-wider">Billed To (Buyer / Consignee):</span>
                <div class="font-black text-sm text-slate-950"><?= sanitize($invoice['customer_name']) ?></div>
                <div class="text-slate-600 text-[11px] mt-0.5 leading-relaxed"><?= nl2br(sanitize($invoice['customer_address'] ?: 'Direct Counter Purchase')) ?></div>
                <div class="font-mono text-[11px] text-slate-700 mt-1 space-y-0.5">
                    <div>Mobile: <strong><?= $invoice['customer_mobile'] ?></strong></div>
                    <?php if (!empty($invoice['customer_email'])): ?>
                        <div>Email: <?= sanitize($invoice['customer_email']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['customer_gst'])): ?>
                        <div>Buyer GSTIN: <strong><?= $invoice['customer_gst'] ?></strong></div>
                    <?php endif; ?>
                    <div>State: <?= sanitize($invoice['customer_state'] ?? 'Tamil Nadu') ?> (Code: <?= sanitize($invoice['customer_state_code'] ?? '33') ?>)</div>
                </div>
            </div>

            <!-- Shipped To & Payment Info -->
            <div class="border-l border-slate-200 pl-6">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1 tracking-wider">Shipping & Transaction:</span>
                <div class="text-slate-700 text-[11px] leading-relaxed mb-2">
                    <span class="font-bold text-slate-800">Ship To:</span>
                    <?= nl2br(sanitize($invoice['customer_shipping_address'] ?: ($invoice['customer_address'] ?: 'Counter Delivery'))) ?>
                </div>
                <div class="font-mono text-[11px] text-slate-700 space-y-0.5">
                    <div>Payment Method: <strong class="uppercase"><?= $invoice['payment_method'] ?></strong></div>
                    <div>Payment Terms: <strong><?= sanitize($invoice['payment_terms'] ?? 'Immediate') ?></strong></div>
                    <div>Status: <strong class="uppercase text-<?= $invoice['payment_status'] === 'paid' ? 'emerald-700' : ($invoice['payment_status'] === 'partial' ? 'amber-700' : 'rose-700') ?>"><?= $invoice['payment_status'] ?></strong></div>
                    <div>Cashier / Agent: <?= sanitize($invoice['cashier_name']) ?></div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left text-xs mb-6 border-collapse border border-slate-300">
            <thead class="bg-slate-100 uppercase text-[10px] font-bold border-b border-slate-300">
                <tr>
                    <th class="p-2.5 border border-slate-300 w-8 text-center">#</th>
                    <th class="p-2.5 border border-slate-300">Product Description & Code</th>
                    <th class="p-2.5 border border-slate-300 text-center w-16">HSN</th>
                    <th class="p-2.5 border border-slate-300 text-center w-12">Qty</th>
                    <th class="p-2.5 border border-slate-300 text-right w-20">Rate (₹)</th>
                    <th class="p-2.5 border border-slate-300 text-center w-14">Disc</th>
                    <th class="p-2.5 border border-slate-300 text-center w-14">GST %</th>
                    <th class="p-2.5 border border-slate-300 text-right w-24">Amount (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-mono">
                <?php $idx = 1; foreach ($items as $it): 
                    $gross = $it['quantity'] * $it['unit_price'];
                    $disc = $it['discount_amount'] ?? (($gross * ($it['discount_percent'] ?? 0)) / 100);
                ?>
                    <tr>
                        <td class="p-2 border border-slate-300 text-center font-sans"><?= $idx++ ?></td>
                        <td class="p-2 border border-slate-300 font-sans">
                            <span class="font-bold text-slate-900 block"><?= sanitize($it['product_name']) ?></span>
                            <span class="text-[10px] text-slate-500 font-mono"><?= $it['product_code'] ?><?= !empty($it['material']) ? ' • ' . $it['material'] : '' ?></span>
                        </td>
                        <td class="p-2 border border-slate-300 text-center"><?= $it['hsn_code'] ?: '9701' ?></td>
                        <td class="p-2 border border-slate-300 text-center font-bold font-sans"><?= $it['quantity'] ?></td>
                        <td class="p-2 border border-slate-300 text-right"><?= number_format((float)$it['unit_price'], 2) ?></td>
                        <td class="p-2 border border-slate-300 text-center text-[11px]"><?= (float)($it['discount_percent'] ?? 0) > 0 ? $it['discount_percent'] . '%' : '-' ?></td>
                        <td class="p-2 border border-slate-300 text-center"><?= (float)($it['tax_percent'] ?? 12) ?>%</td>
                        <td class="p-2 border border-slate-300 text-right font-bold text-slate-950"><?= number_format((float)$it['total_amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals & Tax Calculation Breakdown -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <!-- Left Notes & Words (7 Cols) -->
            <div class="col-span-7 flex flex-col justify-between">
                <div>
                    <!-- Amount in words -->
                    <div class="p-3 bg-amber-50/50 border border-amber-200 rounded-xl mb-4">
                        <span class="text-[10px] uppercase font-bold text-amber-900 block tracking-wide">Amount in Words:</span>
                        <div class="font-bold text-slate-900 text-xs italic mt-0.5">
                            <?= amountToWords((float)$invoice['grand_total']) ?>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="text-[10px] text-slate-500 space-y-1">
                        <span class="font-bold uppercase text-slate-700 block">Terms & Conditions:</span>
                        <p class="leading-relaxed"><?= nl2br(sanitize(getSetting('terms_conditions', "1. Goods once sold can only be returned in undamaged condition within 7 days.\n2. Consecrated with traditional Shilpa Shastra Agamas.\n3. Subject to Chennai jurisdiction."))) ?></p>
                    </div>
                </div>

                <!-- Bank Info -->
                <div class="mt-4 pt-3 border-t border-slate-200 text-[10px] text-slate-500 font-mono">
                    <div>Bank: State Bank of India | A/C: 389201940291 | IFSC: SBIN0001008</div>
                </div>
            </div>

            <!-- Right Financial Summary (5 Cols) -->
            <div class="col-span-5 font-mono text-xs space-y-1.5 border border-slate-200 rounded-xl p-4 bg-slate-50">
                <div class="flex justify-between"><span>Subtotal (Gross):</span><span>₹<?= number_format((float)$invoice['subtotal'], 2) ?></span></div>
                
                <?php if ((float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']) > 0): ?>
                <div class="flex justify-between text-emerald-700">
                    <span>Discount Total:</span>
                    <span>-₹<?= number_format((float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']), 2) ?></span>
                </div>
                <?php endif; ?>

                <?php 
                $taxableVal = max(0, (float)$invoice['subtotal'] - (float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']));
                ?>
                <div class="flex justify-between font-bold text-slate-800 border-t border-slate-200 pt-1">
                    <span>Taxable Amount:</span>
                    <span>₹<?= number_format($taxableVal, 2) ?></span>
                </div>

                <!-- GST Split -->
                <?php if ((float)$invoice['igst_amount'] > 0): ?>
                    <div class="flex justify-between text-[11px]"><span>IGST:</span><span>₹<?= number_format((float)$invoice['igst_amount'], 2) ?></span></div>
                <?php else: ?>
                    <div class="flex justify-between text-[11px]"><span>CGST:</span><span>₹<?= number_format((float)$invoice['cgst_amount'], 2) ?></span></div>
                    <div class="flex justify-between text-[11px]"><span>SGST:</span><span>₹<?= number_format((float)$invoice['sgst_amount'], 2) ?></span></div>
                <?php endif; ?>

                <?php if ((float)($invoice['additional_charges'] ?? 0) > 0): ?>
                <div class="flex justify-between">
                    <span>Additional Charges:</span>
                    <span>+₹<?= number_format((float)$invoice['additional_charges'], 2) ?></span>
                </div>
                <?php endif; ?>

                <?php if ((float)$invoice['round_off'] != 0): ?>
                <div class="flex justify-between text-slate-500">
                    <span>Round-off:</span>
                    <span><?= (float)$invoice['round_off'] >= 0 ? '+' : '' ?>₹<?= number_format((float)$invoice['round_off'], 2) ?></span>
                </div>
                <?php endif; ?>

                <!-- Grand Total -->
                <div class="flex justify-between text-sm font-black border-t-2 border-slate-900 pt-2 text-slate-950">
                    <span>Grand Total:</span>
                    <span class="text-base">₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
                </div>

                <div class="border-t border-slate-200 pt-1.5 space-y-1 text-[11px]">
                    <div class="flex justify-between text-emerald-800 font-bold">
                        <span>Paid Amount:</span>
                        <span>₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span>
                    </div>
                    <?php if ((float)$invoice['due_amount'] > 0): ?>
                    <div class="flex justify-between text-rose-600 font-bold">
                        <span>Balance Due:</span>
                        <span>₹<?= number_format((float)$invoice['due_amount'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="pt-8 border-t border-slate-300 flex justify-between items-end text-center text-xs">
            <div>
                <div class="w-48 border-t border-slate-400 pt-1 text-slate-600">Customer Signature</div>
            </div>
            <div>
                <div class="w-56 border-t border-slate-400 pt-1 text-slate-900 font-bold">
                    For <?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?>
                    <span class="text-[10px] text-slate-400 font-normal block mt-0.5">Authorized Signatory</span>
                </div>
            </div>
        </div>

    </div>

    <?php if (!empty($autoPrint)): ?>
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 400);
        });
    </script>
    <?php endif; ?>

</body>
</html>
