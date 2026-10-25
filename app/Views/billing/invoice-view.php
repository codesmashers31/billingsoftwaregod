<?php if (!empty($supplier)): ?>
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-slate-900">Supplier Invoice <?= sanitize($supplier['supplier_id']) ?></h1>
                <?php 
                    $stat = $supplier['payment_status'] ?? 'Unpaid';
                    $badgeCls = $stat === 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($stat === 'Partially Paid' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                ?>
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] border <?= $badgeCls ?>">
                    <?= sanitize($stat) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Supplier: <?= sanitize($supplier['supplier_name']) ?> • Last Updated: <?= formatDate($supplier['payment_date'] ?? date('Y-m-d')) ?></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url('billing/invoices') ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Back to Invoices
            </a>
        </div>
    </div>

    <!-- Supplier & Purchase Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800 block mb-1">Company / Studio</span>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize(getSetting('company_name', 'Divya Murti Heritage ERP')) ?></h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= nl2br(sanitize(getSetting('company_address', '108 Sannidhi Square, Mylapore, Chennai - 600004'))) ?></p>
            </div>

            <div class="sm:text-right">
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800 block mb-1">Supplier Details</span>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize($supplier['supplier_name']) ?></h3>
                <p class="text-xs text-slate-600 mt-1 font-mono">Supplier ID: <strong><?= sanitize($supplier['supplier_id']) ?></strong></p>
                <?php if (!empty($supplier['mobile'])): ?>
                    <p class="text-xs text-slate-600 font-mono">Mobile: <?= sanitize($supplier['mobile']) ?></p>
                <?php endif; ?>
                <?php if (!empty($supplier['company_name'])): ?>
                    <p class="text-xs text-slate-500"><?= sanitize($supplier['company_name']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Associated Products Table -->
        <div>
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Associated Products & Purchase Details</h4>
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Product Name</th>
                            <th class="py-3 px-3 text-center">Pieces Count (Qty)</th>
                            <th class="py-3 px-3 text-right">One Piece Rate (₹)</th>
                            <th class="py-3 px-3 text-right">Total Purchase Price (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        <?php if (empty($products)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-slate-400 font-sans">No products linked to this supplier.</td></tr>
                        <?php else: $idx = 1; foreach ($products as $item): ?>
                            <tr>
                                <td class="py-3 px-3 text-slate-400 font-sans"><?= $idx++ ?></td>
                                <td class="py-3 px-3 font-sans">
                                    <div class="font-bold text-slate-900"><?= sanitize($item['product_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= sanitize($item['code'] ?? $item['sku'] ?? '') ?></div>
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-slate-900"><?= (int)$item['pieces_count'] ?></td>
                                <td class="py-3 px-3 text-right text-slate-800">₹<?= number_format((float)$item['piece_rate'], 2) ?></td>
                                <td class="py-3 px-3 text-right font-black text-slate-900">₹<?= number_format((float)$item['total_purchase_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary & Balance -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 font-mono text-xs">
            <div class="bg-blue-50/60 p-4 rounded-xl border border-blue-200">
                <span class="text-[10px] uppercase font-bold tracking-wider text-blue-700 block">Total Purchase Amount</span>
                <span class="text-lg font-black text-blue-950 mt-1 block">₹<?= number_format((float)$supplier['total_amount'], 2) ?></span>
            </div>
            <div class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-200">
                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-700 block">Paid Amount (<?= sanitize($supplier['payment_method'] ?? 'Cash') ?>)</span>
                <span class="text-lg font-black text-emerald-950 mt-1 block">₹<?= number_format((float)$supplier['paid_amount'], 2) ?></span>
            </div>
            <div class="bg-rose-50/60 p-4 rounded-xl border border-rose-200">
                <span class="text-[10px] uppercase font-bold tracking-wider text-rose-700 block">Remaining Balance</span>
                <span class="text-lg font-black text-rose-950 mt-1 block">₹<?= number_format((float)$supplier['balance_amount'], 2) ?></span>
            </div>
        </div>
    </div>
</div>
<?php elseif (!empty($invoice)): ?>
<!-- Complete Customer Sales & POS Invoice Details View -->
<div class="space-y-6 max-w-5xl mx-auto pb-12">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl font-extrabold text-slate-900 font-mono tracking-tight">Invoice <?= sanitize($invoice['invoice_no']) ?></h1>
                <?php 
                    $stat = $invoice['payment_status'] ?? 'unpaid';
                    $badgeCls = $stat === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($stat === 'partial' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                    $statLabel = $stat === 'partial' ? 'Partially Paid' : ucfirst($stat);
                ?>
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] border uppercase tracking-wide <?= $badgeCls ?>">
                    <?= sanitize($statLabel) ?>
                </span>
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wide">
                    <?= sanitize(ucfirst($invoice['status'] ?? 'completed')) ?>
                </span>
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-amber-50 text-amber-800 border border-amber-200 uppercase tracking-wide">
                    <?= sanitize(ucwords(str_replace('_', ' ', $invoice['invoice_type'] ?? 'tax_invoice'))) ?>
                </span>
                <?php $smode = strtolower($invoice['sales_mode'] ?? 'offline'); ?>
                <?php if ($smode === 'online'): ?>
                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-globe text-[10px]"></i> Online Sale
                    </span>
                <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-store text-[10px]"></i> Offline Sale
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                <span><i class="far fa-calendar-alt text-slate-400 mr-1"></i> Date: <strong><?= formatDate($invoice['invoice_date']) ?></strong> <?= $invoice['invoice_time'] ?? '' ?></span>
                <span>•</span>
                <span><i class="far fa-user text-slate-400 mr-1"></i> Cashier: <strong><?= sanitize($invoice['cashier_name'] ?? 'Counter Operator') ?></strong></span>
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= url('billing/invoice/' . $invoice['id'] . '/print?type=thermal') ?>" target="_blank" 
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-200 shadow-2xs">
                <i class="fas fa-receipt text-amber-600"></i> Thermal 80mm
            </a>
            <a href="<?= url('billing/invoice/' . $invoice['id'] . '/print?type=a4') ?>" target="_blank" 
               class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-file-invoice"></i> Tax Invoice A4
            </a>
            <a href="<?= url('billing/pos') ?>" 
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-200">
                <i class="fas fa-cash-register text-slate-500"></i> POS Terminal [F2]
            </a>
            <a href="<?= url('billing/invoices') ?>" 
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-200">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Main Invoice Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <!-- Header Info Grid: Company Profile vs Customer Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
            <!-- Left: Company / Store Profile -->
            <div class="space-y-2">
                <span class="text-[10px] uppercase font-extrabold tracking-wider text-amber-800 block">Billed From (Store / Studio)</span>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize(getSetting('company_name', 'Sastha Sasti')) ?></h3>
                <p class="text-xs text-slate-600 leading-relaxed"><?= nl2br(sanitize(getSetting('company_address', 'No.41/13, Narayanappan Naiken Main St, NN Garden, Washermanpet, Chennai, Tamil Nadu 600021'))) ?></p>
                <div class="pt-1 text-xs text-slate-600 font-mono space-y-0.5">
                    <div><span class="text-slate-400">GSTIN:</span> <strong><?= sanitize(getSetting('company_gstin', '33AABCG1234H1Z0')) ?></strong></div>
                    <div><span class="text-slate-400">Phone:</span> <?= sanitize(getSetting('company_phone', '9840796300')) ?></div>
                    <div><span class="text-slate-400">State:</span> <?= sanitize(getSetting('company_state', 'Tamil Nadu')) ?> (State Code: 33)</div>
                </div>
            </div>

            <!-- Right: Customer Buying Profile -->
            <div class="md:text-right space-y-2">
                <div class="flex items-center md:justify-end gap-2">
                    <span class="text-[10px] uppercase font-extrabold tracking-wider text-amber-800">Customer Details</span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">
                        <?= sanitize(ucfirst($invoice['customer_type'] ?? 'Retail')) ?>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900"><?= sanitize($invoice['customer_name'] ?? 'Walk-in Retail Customer') ?></h3>
                <div class="text-xs text-slate-600 font-mono space-y-0.5">
                    <div><span class="text-slate-400">Mobile:</span> <strong class="text-slate-900"><?= !empty($invoice['customer_mobile']) ? sanitize($invoice['customer_mobile']) : 'N/A' ?></strong></div>
                    <?php if (!empty($invoice['customer_email'])): ?>
                        <div><span class="text-slate-400">Email:</span> <?= sanitize($invoice['customer_email']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['customer_gst'])): ?>
                        <div><span class="text-slate-400">GSTIN:</span> <strong class="text-amber-800"><?= sanitize($invoice['customer_gst']) ?></strong></div>
                    <?php endif; ?>
                    <div><span class="text-slate-400">Place of Supply:</span> <?= sanitize($invoice['customer_state'] ?? 'Tamil Nadu') ?> (<?= sanitize($invoice['customer_state_code'] ?? '33') ?>)</div>
                    <?php if (!empty($invoice['customer_address'])): ?>
                        <div class="text-slate-500 font-sans text-[11px] mt-1"><?= nl2br(sanitize($invoice['customer_address'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Purchased Line Items Table -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-boxes text-amber-600"></i> Purchased Products & GST Line Items
                </h4>
                <span class="text-xs font-mono text-slate-500 font-semibold"><?= count($items ?? []) ?> item(s)</span>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">#</th>
                            <th class="py-3 px-3 min-w-[200px]">Product / Statue Description</th>
                            <th class="py-3 px-3 text-center w-20">HSN</th>
                            <th class="py-3 px-3 text-center w-16">Qty</th>
                            <th class="py-3 px-3 text-right w-24">Rate (₹)</th>
                            <th class="py-3 px-3 text-center w-24">Discount</th>
                            <th class="py-3 px-3 text-center w-28">GST Tax Rate</th>
                            <th class="py-3 px-3 text-right w-28">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        <?php if (empty($items)): ?>
                            <tr><td colspan="8" class="py-8 text-center text-slate-400 font-sans">No products linked to this invoice.</td></tr>
                        <?php else: $idx = 1; foreach ($items as $it): 
                            $unitPrice = (float)($it['unit_price'] ?? 0);
                            $qty = (int)($it['quantity'] ?? 1);
                            $discPct = (float)($it['discount_percent'] ?? 0);
                            $discAmt = (float)($it['discount_amount'] ?? 0);
                            $taxPct = (float)($it['tax_percent'] ?? 12);
                            $taxAmt = (float)($it['tax_amount'] ?? 0);
                            $totalAmt = (float)($it['total_amount'] ?? 0);
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3 text-center text-slate-400 font-sans"><?= $idx++ ?></td>
                                <td class="py-3 px-3 font-sans">
                                    <div class="font-bold text-slate-900"><?= sanitize($it['product_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono flex items-center gap-1.5 mt-0.5">
                                        <span><?= sanitize($it['product_code'] ?? '') ?></span>
                                        <?php if (!empty($it['material'])): ?>
                                            <span>• <?= sanitize($it['material']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($it['god_name'])): ?>
                                            <span>• <?= sanitize($it['god_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-slate-600"><?= sanitize($it['hsn_code'] ?: '9701') ?></td>
                                <td class="py-3 px-3 text-center font-bold text-slate-900"><?= $qty ?></td>
                                <td class="py-3 px-3 text-right text-slate-800">₹<?= number_format($unitPrice, 2) ?></td>
                                <td class="py-3 px-3 text-center">
                                    <?php if ($discAmt > 0): ?>
                                        <span class="text-emerald-700 font-bold">-₹<?= number_format($discAmt, 2) ?></span>
                                        <span class="text-[10px] text-slate-400 block">(<?= $discPct ?>%)</span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[11px]">
                                        <?= $taxPct ?>%
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">+₹<?= number_format($taxAmt, 2) ?></span>
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-900">₹<?= number_format($totalAmt, 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Summary & Payment Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-4 border-t border-slate-100">
            <!-- Left: Payment Settlement & Remarks (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Payment Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 font-mono text-xs">
                    <div class="bg-blue-50/60 p-3.5 rounded-xl border border-blue-200">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-700 block">Net Grand Total</span>
                        <span class="text-base font-black text-blue-950 mt-1 block">₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
                    </div>
                    <div class="bg-emerald-50/60 p-3.5 rounded-xl border border-emerald-200">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-700 block">Amount Paid (<?= strtoupper($invoice['payment_method'] ?? 'cash') ?> • <?= strtoupper($invoice['sales_mode'] ?? 'OFFLINE') ?>)</span>
                        <span class="text-base font-black text-emerald-950 mt-1 block">₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span>
                    </div>
                    <div class="bg-rose-50/60 p-3.5 rounded-xl border border-rose-200">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-rose-700 block">Balance Due</span>
                        <span class="text-base font-black text-rose-950 mt-1 block">₹<?= number_format((float)$invoice['due_amount'], 2) ?></span>
                    </div>
                </div>

                <!-- Collect Remaining Payment Trigger (if due balance exists) -->
                <?php if ((float)$invoice['due_amount'] > 0 && $invoice['status'] !== 'cancelled'): ?>
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-bold text-amber-900 block flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-amber-600"></i> Outstanding Due Balance: ₹<?= number_format((float)$invoice['due_amount'], 2) ?>
                            </span>
                            <span class="text-[11px] text-amber-700 mt-0.5 block">Record customer payment to clear invoice balance and update ledger.</span>
                        </div>
                        <button type="button" onclick="openPaymentCollectModal()" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm whitespace-nowrap transition-colors flex items-center gap-1.5">
                            <i class="fas fa-hand-holding-usd"></i> Record Payment
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Amount in Words -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="text-[10px] uppercase font-bold tracking-wide text-slate-500 block">Amount in Words:</span>
                    <span class="text-xs font-bold text-slate-800 italic mt-0.5 block">
                        <?= amountToWords((float)$invoice['grand_total']) ?>
                    </span>
                </div>

                <!-- Notes & Terms -->
                <?php if (!empty($invoice['notes'])): ?>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <span class="text-[10px] uppercase font-bold tracking-wide text-slate-500 block mb-1">Invoice Notes / Consecration Remarks:</span>
                        <p class="text-slate-700 leading-relaxed font-sans"><?= nl2br(sanitize($invoice['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Tax & Financial Breakdown (5 Cols) -->
            <div class="lg:col-span-5 font-mono text-xs space-y-2 border border-slate-200 rounded-xl p-5 bg-slate-50">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal (Gross):</span>
                    <span class="font-bold text-slate-800">₹<?= number_format((float)$invoice['subtotal'], 2) ?></span>
                </div>

                <?php 
                    $discSum = (float)($invoice['item_discount_total'] ?? 0) + (float)($invoice['overall_discount_amount'] ?? 0);
                    if ($discSum > 0): 
                ?>
                    <div class="flex justify-between text-emerald-700">
                        <span>Total Discounts:</span>
                        <span>-₹<?= number_format($discSum, 2) ?></span>
                    </div>
                <?php endif; ?>

                <?php 
                    $taxableBase = max(0, (float)$invoice['subtotal'] - $discSum);
                ?>
                <div class="flex justify-between font-bold text-slate-800 border-t border-slate-200 pt-1.5">
                    <span>Net Taxable Value:</span>
                    <span>₹<?= number_format($taxableBase, 2) ?></span>
                </div>

                <!-- Dynamic GST Taxes Breakdown -->
                <?php if ((float)$invoice['igst_amount'] > 0): ?>
                    <div class="flex justify-between text-blue-700">
                        <span>IGST Tax:</span>
                        <span>₹<?= number_format((float)$invoice['igst_amount'], 2) ?></span>
                    </div>
                <?php else: ?>
                    <div class="flex justify-between text-blue-700">
                        <span>CGST Tax:</span>
                        <span>₹<?= number_format((float)$invoice['cgst_amount'], 2) ?></span>
                    </div>
                    <div class="flex justify-between text-blue-700">
                        <span>SGST Tax:</span>
                        <span>₹<?= number_format((float)$invoice['sgst_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between text-slate-700 font-semibold border-t border-dashed border-slate-200 pt-1">
                    <span>Total GST Tax Amount:</span>
                    <span class="text-blue-800 font-bold">₹<?= number_format((float)$invoice['tax_amount'], 2) ?></span>
                </div>

                <?php if ((float)($invoice['additional_charges'] ?? 0) > 0): ?>
                    <div class="flex justify-between text-slate-600">
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
                <div class="flex justify-between text-base font-black border-t-2 border-slate-900 pt-2.5 text-slate-950">
                    <span class="text-sm uppercase tracking-wide">Grand Net Total:</span>
                    <span class="text-amber-800 font-black">₹<?= number_format((float)$invoice['grand_total'], 2) ?></span>
                </div>

                <div class="border-t border-slate-200 pt-2 space-y-1 text-[11px]">
                    <div class="flex justify-between text-emerald-800 font-bold">
                        <span>Paid Amount:</span>
                        <span>₹<?= number_format((float)$invoice['paid_amount'], 2) ?></span>
                    </div>
                    <?php if ((float)$invoice['due_amount'] > 0): ?>
                        <div class="flex justify-between text-rose-600 font-bold">
                            <span>Remaining Due:</span>
                            <span>₹<?= number_format((float)$invoice['due_amount'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Payment Transactions History (if any) -->
        <?php if (!empty($payments)): ?>
            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-history text-amber-600"></i> Recorded Payment Transactions
                </h4>
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-xs font-mono">
                        <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 border-b border-slate-200 font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Receipt / Payment #</th>
                                <th class="py-2.5 px-3">Method</th>
                                <th class="py-2.5 px-3">Reference / Notes</th>
                                <th class="py-2.5 px-3 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($payments as $p): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 px-3 text-slate-600"><?= formatDate($p['payment_date']) ?></td>
                                    <td class="py-2.5 px-3 font-bold text-slate-800"><?= sanitize($p['payment_no']) ?></td>
                                    <td class="py-2.5 px-3 uppercase text-slate-700"><?= sanitize($p['payment_method']) ?></td>
                                    <td class="py-2.5 px-3 font-sans text-slate-600 text-[11px]"><?= sanitize($p['notes'] ?: ($p['transaction_reference'] ?: '-')) ?></td>
                                    <td class="py-2.5 px-3 text-right font-bold text-emerald-700">₹<?= number_format((float)$p['amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal: Record Customer Payment -->
<?php if ((float)$invoice['due_amount'] > 0): ?>
<div id="modal-collect-payment" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6 relative">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-amber-600"></i> Record Payment
            </h3>
            <button type="button" onclick="closePaymentCollectModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="form-collect-payment" onsubmit="submitCollectPayment(event)" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Invoice Number</label>
                <input type="text" readonly value="<?= sanitize($invoice['invoice_no']) ?>" class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2 font-mono font-bold text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Payment Amount (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0.01" max="<?= (float)$invoice['due_amount'] ?>" name="amount" id="pay_input_amount" required 
                       value="<?= (float)$invoice['due_amount'] ?>" 
                       class="w-full bg-emerald-50/40 border border-emerald-400 text-emerald-900 font-bold rounded-xl px-3 py-2 text-sm font-mono outline-none focus:border-emerald-600">
                <p class="text-[10px] text-slate-400 mt-1 font-mono">Max due balance: ₹<?= number_format((float)$invoice['due_amount'], 2) ?></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 outline-none focus:border-amber-500 font-semibold">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / QR</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Payment Date</label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 outline-none focus:border-amber-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deposit Account</label>
                <select name="account_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 outline-none focus:border-amber-500">
                    <?php foreach (($accounts ?? []) as $acc): ?>
                        <option value="<?= $acc['id'] ?>"><?= sanitize($acc['name']) ?> (<?= ucfirst($acc['type']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Notes / Transaction Reference</label>
                <input type="text" name="notes" placeholder="e.g. Cleared remaining balance via Cash" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 outline-none focus:border-amber-500">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closePaymentCollectModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="submit" id="btn-submit-pay" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="fas fa-check"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentCollectModal() {
    const modal = document.getElementById('modal-collect-payment');
    if (modal) modal.classList.remove('hidden');
}

function closePaymentCollectModal() {
    const modal = document.getElementById('modal-collect-payment');
    if (modal) modal.classList.add('hidden');
}

async function submitCollectPayment(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit-pay');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...'; }

    const form = document.getElementById('form-collect-payment');
    const formData = new FormData(form);

    try {
        const res = await fetch('<?= url("billing/invoice/" . $invoice["id"] . "/payment") ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message || 'Payment recorded successfully!');
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast('error', data.message || 'Payment failed.');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Save Payment'; }
        }
    } catch (err) {
        console.error(err);
        showToast('error', 'Network error while recording payment.');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Save Payment'; }
    }
}
</script>
<?php endif; ?>

<?php else: ?>
<!-- Fallback if no invoice found -->
<div class="space-y-6 max-w-4xl mx-auto py-12 text-center">
    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
        <i class="fas fa-file-invoice"></i>
    </div>
    <h1 class="text-xl font-bold text-slate-800">Invoice Not Found</h1>
    <p class="text-xs text-slate-500">The invoice record you are looking for does not exist or has been removed.</p>
    <a href="<?= url('billing/invoices') ?>" class="inline-block mt-3 px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold shadow-sm">
        Back to Invoices
    </a>
</div>
<?php endif; ?>
