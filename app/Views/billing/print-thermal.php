<div class="thermal-receipt max-w-[80mm] mx-auto bg-white p-4 text-black font-mono text-[11px] leading-tight">
    <div class="text-center pb-2 border-b border-dashed border-black flex flex-col items-center">
        <img src="<?= asset('images/logo.png') ?>" alt="Logo" class="w-10 h-10 object-contain mix-blend-multiply mb-1 grayscale">
        <h2 class="text-base font-bold uppercase tracking-wider"><?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></h2>
        <p class="text-[10px]"><?= sanitize(getSetting('company_address', '108 Sannidhi Square, Mylapore, Chennai - 600004')) ?></p>
        <p class="text-[10px]">Ph: <?= getSetting('company_phone', '+91 44 2464 1008') ?></p>
        <p class="text-[10px]">GSTIN: <?= getSetting('company_gstin', '33AABCG1234H1Z0') ?></p>
    </div>

    <div class="py-2 border-b border-dashed border-black text-[10px] space-y-0.5">
        <div class="flex justify-between"><span>Bill No:</span><strong><?= $invoice['invoice_no'] ?></strong></div>
        <div class="flex justify-between"><span>Date/Time:</span><span><?= $invoice['invoice_date'] ?> <?= $invoice['invoice_time'] ?></span></div>
        <div class="flex justify-between"><span>Customer:</span><span><?= sanitize($invoice['customer_name']) ?></span></div>
        <div class="flex justify-between"><span>Cashier:</span><span><?= sanitize($invoice['cashier_name']) ?></span></div>
    </div>

    <!-- Items -->
    <table class="w-full text-left my-2 text-[10px]">
        <thead>
            <tr class="border-b border-black">
                <th class="py-1">Item</th>
                <th class="py-1 text-center">Qty</th>
                <th class="py-1 text-right">Rate</th>
                <th class="py-1 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-dashed divide-black">
            <?php foreach ($items as $it): ?>
                <tr>
                    <td class="py-1">
                        <div><?= sanitize($it['product_name']) ?></div>
                    </td>
                    <td class="py-1 text-center"><?= $it['quantity'] ?></td>
                    <td class="py-1 text-right"><?= number_format((float)$it['unit_price'], 0) ?></td>
                    <td class="py-1 text-right font-bold"><?= number_format((float)$it['total_amount'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="py-2 border-t-2 border-black space-y-1 text-[10px]">
        <div class="flex justify-between"><span>Subtotal:</span><span>₹<?= number_format((float)$invoice['subtotal'], 2) ?></span></div>
        <?php if ($invoice['item_discount_total'] + $invoice['overall_discount_amount'] > 0): ?>
            <div class="flex justify-between"><span>Discount:</span><span>-₹<?= number_format((float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']), 2) ?></span></div>
        <?php endif; ?>
        <div class="flex justify-between"><span>GST Tax (12%):</span><span>₹<?= number_format((float)$invoice['tax_amount'], 2) ?></span></div>
        <div class="flex justify-between"><span>Round-off:</span><span><?= $invoice['round_off'] >= 0 ? '+' : '' ?>₹<?= number_format((float)$invoice['round_off'], 2) ?></span></div>
        <div class="flex justify-between text-xs font-bold pt-1 border-t border-dashed border-black">
            <span>NET TOTAL:</span>
            <span>₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
        </div>
        <div class="flex justify-between pt-0.5"><span>Paid (<?= strtoupper($invoice['payment_method']) ?>):</span><span>₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span></div>
    </div>

    <div class="text-center pt-3 border-t border-dashed border-black text-[9px]">
        <p class="font-bold">THANK YOU • VISIT AGAIN</p>
        <p class="mt-1 text-slate-600">Sacred Idols consecrated per Shilpa Shastra</p>
    </div>
</div>

