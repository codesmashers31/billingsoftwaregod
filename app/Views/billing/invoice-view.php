<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-slate-900">Invoice <?= $invoice['invoice_no'] ?></h1>
                <?php if ($invoice['status'] === 'cancelled'): ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">Cancelled</span>
                <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Completed</span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Date: <?= formatDate($invoice['invoice_date']) ?> at <?= $invoice['invoice_time'] ?> • Billed by <?= sanitize($invoice['cashier_name']) ?></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url('billing/invoice/' . $invoice['id'] . '/print?type=thermal') ?>" target="_blank" class="px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-sm border border-slate-300">
                <i class="fas fa-receipt text-amber-600"></i> Thermal 80mm
            </a>
            <a href="<?= url('billing/invoice/' . $invoice['id'] . '/print?type=a4') ?>" target="_blank" class="gold-btn px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-print"></i> Tax Invoice A4
            </a>
            <a href="<?= url('billing/invoices') ?>" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                Back
            </a>
        </div>
    </div>

    <!-- Invoice Details Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Company & Customer Header Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800 block mb-1">Seller / Consecration Studio</span>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize(getSetting('company_name', 'Divya Murti & God Statue Heritage')) ?></h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= nl2br(sanitize(getSetting('company_address', '108 Sannidhi Square, Mylapore, Chennai - 600004'))) ?></p>
                <div class="text-xs text-slate-600 mt-1.5 font-mono">
                    <span>GSTIN: <strong class="text-slate-900"><?= getSetting('company_gstin', '33AABCG1234H1Z0') ?></strong></span><br>
                    <span>Phone: <?= getSetting('company_phone', '+91 44 2464 1008') ?></span>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800 block mb-1">Customer / Billed To</span>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize($invoice['customer_name']) ?></h3>
                <p class="text-xs text-slate-600 mt-1"><?= sanitize($invoice['customer_address'] ?? 'Showroom Counter') ?></p>
                <div class="text-xs text-slate-600 mt-1.5 font-mono">
                    <span>Mobile: <strong class="text-slate-900"><?= $invoice['customer_mobile'] ?></strong></span><br>
                    <?php if ($invoice['customer_gst']): ?>
                        <span>GSTIN: <strong class="text-slate-900"><?= $invoice['customer_gst'] ?></strong></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 border-b border-slate-200 font-bold">
                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">God Statue / Item</th>
                        <th class="py-3 px-3">HSN</th>
                        <th class="py-3 px-3 text-right">Rate (₹)</th>
                        <th class="py-3 px-3 text-center">Qty</th>
                        <th class="py-3 px-3 text-right">Disc</th>
                        <th class="py-3 px-3 text-right">GST %</th>
                        <th class="py-3 px-3 text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    <?php $idx = 1; foreach ($items as $item): ?>
                        <tr>
                            <td class="py-3 px-3 text-slate-400 font-sans"><?= $idx++ ?></td>
                            <td class="py-3 px-3 font-sans">
                                <div class="font-bold text-slate-900"><?= sanitize($item['product_name']) ?></div>
                                <div class="text-[10px] text-slate-500 font-mono"><?= $item['product_code'] ?> • <?= $item['material'] ?? '' ?></div>
                            </td>
                            <td class="py-3 px-3 text-slate-500"><?= $item['hsn_code'] ?></td>
                            <td class="py-3 px-3 text-right text-slate-800">₹<?= number_format((float)$item['unit_price'], 2) ?></td>
                            <td class="py-3 px-3 text-center font-bold text-slate-900"><?= $item['quantity'] ?></td>
                            <td class="py-3 px-3 text-right text-rose-600"><?= $item['discount_percent'] > 0 ? $item['discount_percent'] . '%' : '-' ?></td>
                            <td class="py-3 px-3 text-right text-slate-600"><?= $item['tax_percent'] ?>%</td>
                            <td class="py-3 px-3 text-right font-bold text-slate-900">₹<?= number_format((float)$item['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary & Tax Split Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 font-mono text-xs">
            <div class="space-y-2 text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">GST Tax Split</span>
                <div class="flex justify-between"><span>CGST:</span><span class="text-slate-900">₹<?= number_format((float)$invoice['cgst_amount'], 2) ?></span></div>
                <div class="flex justify-between"><span>SGST:</span><span class="text-slate-900">₹<?= number_format((float)$invoice['sgst_amount'], 2) ?></span></div>
                <div class="flex justify-between"><span>IGST:</span><span class="text-slate-900">₹<?= number_format((float)$invoice['igst_amount'], 2) ?></span></div>
                <div class="flex justify-between pt-1 border-t border-slate-200 font-bold text-slate-900"><span>Total Tax:</span><span>₹<?= number_format((float)$invoice['tax_amount'], 2) ?></span></div>
            </div>

            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="flex justify-between text-slate-600"><span>Subtotal:</span><span>₹<?= number_format((float)$invoice['subtotal'], 2) ?></span></div>
                <div class="flex justify-between text-rose-600 font-semibold"><span>Total Discount:</span><span>-₹<?= number_format((float)($invoice['item_discount_total'] + $invoice['overall_discount_amount']), 2) ?></span></div>
                <div class="flex justify-between text-slate-500"><span>Round-off:</span><span><?= $invoice['round_off'] >= 0 ? '+' : '' ?>₹<?= number_format((float)$invoice['round_off'], 2) ?></span></div>
                <div class="flex justify-between pt-2 border-t border-slate-200 text-base font-black text-slate-900">
                    <span>Grand Total:</span>
                    <span>₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold text-xs"><span>Paid (<?= strtoupper($invoice['payment_method']) ?>):</span><span>₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span></div>
                <?php if ($invoice['due_amount'] > 0): ?>
                    <div class="flex justify-between text-rose-600 font-bold text-xs"><span>Balance Due:</span><span>₹<?= number_format((float)$invoice['due_amount'], 2) ?></span></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Blessing -->
        <div class="pt-4 border-t border-slate-100 text-center text-slate-500 text-xs">
            <p class="italic">"<?= sanitize(getSetting('invoice_footer_note', 'All idols are handcrafted and casted in accordance with Shilpa Shastra.')) ?>"</p>
        </div>
    </div>
</div>
