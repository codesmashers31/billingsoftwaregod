<div class="max-w-3xl mx-auto bg-white p-8 border border-slate-300 rounded-lg shadow-sm text-slate-900 font-sans text-xs">
    <!-- Invoice Header -->
    <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <img src="<?= asset('images/logo.png') ?>" alt="Logo" class="w-12 h-12 object-contain mix-blend-multiply">
                <span class="text-2xl font-bold font-serif uppercase tracking-wider"><?= sanitize(getSetting('company_name', 'Divya Murti & God Statue Heritage')) ?></span>
            </div>
            <p class="text-slate-600 max-w-sm"><?= nl2br(sanitize(getSetting('company_address', '108 Sannidhi Square, Near Kapaleeshwarar Temple, Mylapore, Chennai - 600004'))) ?></p>
            <p class="mt-1 font-mono text-[11px]">GSTIN: <strong><?= getSetting('company_gstin', '33AABCG1234H1Z0') ?></strong> | Phone: <?= getSetting('company_phone', '+91 44 2464 1008') ?></p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold uppercase tracking-widest text-slate-800">TAX INVOICE</h2>
            <div class="font-mono mt-1 space-y-0.5">
                <div>Invoice No: <strong><?= $invoice['invoice_no'] ?></strong></div>
                <div>Date: <?= formatDate($invoice['invoice_date']) ?></div>
                <div>Time: <?= $invoice['invoice_time'] ?></div>
                <div>Place of Supply: <?= sanitize($invoice['customer_state'] ?? 'Tamil Nadu') ?></div>
            </div>
        </div>
    </div>

    <!-- Billed To Box -->
    <div class="p-3 bg-slate-50 border border-slate-200 rounded-md mb-6 grid grid-cols-2 gap-4">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-0.5">Buyer / Consignee:</span>
            <div class="font-bold text-sm text-slate-900"><?= sanitize($invoice['customer_name']) ?></div>
            <div class="text-slate-600"><?= sanitize($invoice['customer_address'] ?? 'Showroom Counter') ?></div>
            <div class="font-mono mt-1">Mobile: <strong><?= $invoice['customer_mobile'] ?></strong></div>
        </div>
        <div class="text-right font-mono">
            <?php if ($invoice['customer_gst']): ?>
                <div>Buyer GSTIN: <strong><?= $invoice['customer_gst'] ?></strong></div>
            <?php endif; ?>
            <div>Payment Mode: <strong class="uppercase"><?= $invoice['payment_method'] ?></strong></div>
            <div>Cashier: <?= sanitize($invoice['cashier_name']) ?></div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left text-xs mb-6 border-collapse border border-slate-300">
        <thead class="bg-slate-100 uppercase text-[10px] font-bold border-b border-slate-300">
            <tr>
                <th class="p-2 border border-slate-300 w-8">#</th>
                <th class="p-2 border border-slate-300">Item Description (God Statue & Finish)</th>
                <th class="p-2 border border-slate-300 text-center w-16">HSN</th>
                <th class="p-2 border border-slate-300 text-right w-20">Rate (₹)</th>
                <th class="p-2 border border-slate-300 text-center w-12">Qty</th>
                <th class="p-2 border border-slate-300 text-right w-16">Disc %</th>
                <th class="p-2 border border-slate-300 text-right w-16">GST %</th>
                <th class="p-2 border border-slate-300 text-right w-24">Amount (₹)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 font-mono">
            <?php $idx = 1; foreach ($items as $it): ?>
                <tr>
                    <td class="p-2 border border-slate-300 text-center font-sans"><?= $idx++ ?></td>
                    <td class="p-2 border border-slate-300 font-sans">
                        <span class="font-bold"><?= sanitize($it['product_name']) ?></span>
                        <span class="text-[10px] text-slate-500 block"><?= $it['material'] ?? '' ?> (<?= $it['product_code'] ?>)</span>
                    </td>
                    <td class="p-2 border border-slate-300 text-center"><?= $it['hsn_code'] ?></td>
                    <td class="p-2 border border-slate-300 text-right"><?= number_format((float)$it['unit_price'], 2) ?></td>
                    <td class="p-2 border border-slate-300 text-center font-bold font-sans"><?= $it['quantity'] ?></td>
                    <td class="p-2 border border-slate-300 text-right"><?= $it['discount_percent'] > 0 ? $it['discount_percent'] . '%' : '-' ?></td>
                    <td class="p-2 border border-slate-300 text-right"><?= $it['tax_percent'] ?>%</td>
                    <td class="p-2 border border-slate-300 text-right font-bold"><?= number_format((float)$it['total_amount'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals Breakdown & Bank Info -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="text-slate-600 text-[11px] space-y-1">
            <span class="font-bold text-slate-900 block uppercase">Terms & Conditions:</span>
            <p class="leading-relaxed"><?= nl2br(sanitize(getSetting('terms_conditions', "1. Goods once sold can only be returned in unbroken condition within 7 days.\n2. Consecrated with Shilpa Shastra traditions."))) ?></p>
        </div>

        <div class="font-mono text-xs space-y-1.5 border-t border-slate-300 pt-2">
            <div class="flex justify-between"><span>Subtotal:</span><span>₹<?= number_format((float)$invoice['subtotal'], 2) ?></span></div>
            <div class="flex justify-between"><span>Discount Total:</span><span>-₹<?= number_format((float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']), 2) ?></span></div>
            <div class="flex justify-between"><span>CGST:</span><span>₹<?= number_format((float)$invoice['cgst_amount'], 2) ?></span></div>
            <div class="flex justify-between"><span>SGST:</span><span>₹<?= number_format((float)$invoice['sgst_amount'], 2) ?></span></div>
            <div class="flex justify-between"><span>Round-off:</span><span><?= $invoice['round_off'] >= 0 ? '+' : '' ?>₹<?= number_format((float)$invoice['round_off'], 2) ?></span></div>
            <div class="flex justify-between text-base font-bold border-t-2 border-slate-900 pt-1 text-slate-950">
                <span>Grand Total:</span>
                <span>₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
            </div>
            <div class="flex justify-between text-[11px]"><span>Paid Amount:</span><span>₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span></div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="pt-8 border-t border-slate-300 flex justify-between items-end text-center text-xs">
        <div>
            <span class="block border-t border-slate-400 w-40 pt-1 text-slate-600">Customer Signature</span>
        </div>
        <div>
            <span class="block border-t border-slate-400 w-48 pt-1 text-slate-900 font-bold">For <?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></span>
            <span class="text-[10px] text-slate-500">Authorized Signatory</span>
        </div>
    </div>
</div>

