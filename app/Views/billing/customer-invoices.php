<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 flex items-center justify-center font-bold">
                    <i class="fas fa-file-invoice text-base"></i>
                </span>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Customer Invoice Details</h1>
                    <p class="text-xs text-slate-500">Live sales register from POS Billing Terminal, customer order invoices, and settlement tracking.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="<?= url('billing') ?>" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-cash-register"></i> POS Terminal <span class="text-[9px] bg-white/20 px-1 py-0.2 rounded font-extrabold uppercase">F2</span>
            </a>
            <a href="<?= url('billing/invoices/create') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all">
                <i class="fas fa-plus text-amber-600"></i> New Tax Invoice
            </a>
        </div>
    </div>

    <!-- Live KPI Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Invoiced Sales -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Sales Billed</span>
                <span class="text-xl font-extrabold text-slate-900 font-mono tracking-tight">₹<?= number_format((float)($metrics['total_sales'] ?? 0), 2) ?></span>
                <span class="text-[11px] text-slate-500 block mt-0.5 font-medium">Across <?= number_format((int)($metrics['total_invoices'] ?? 0)) ?> customer bills</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center text-lg">
                <i class="fas fa-coins"></i>
            </div>
        </div>

        <!-- Total Collected / Paid -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Collected</span>
                <span class="text-xl font-extrabold text-emerald-700 font-mono tracking-tight">₹<?= number_format((float)($metrics['total_paid'] ?? 0), 2) ?></span>
                <span class="text-[11px] text-emerald-600 block mt-0.5 font-medium"><i class="fas fa-check-circle text-[10px] mr-1"></i><?= number_format((int)($metrics['paid_count'] ?? 0)) ?> fully settled</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        <!-- Outstanding Due Balance -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Due / Pending Balance</span>
                <span class="text-xl font-extrabold text-rose-700 font-mono tracking-tight">₹<?= number_format((float)($metrics['total_due'] ?? 0), 2) ?></span>
                <span class="text-[11px] text-rose-600 block mt-0.5 font-medium"><i class="fas fa-clock text-[10px] mr-1"></i><?= number_format((int)($metrics['due_count'] ?? 0)) ?> with balance</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center justify-center text-lg">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>

        <!-- Total Invoices Generated -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Invoice Records</span>
                <span class="text-xl font-extrabold text-slate-900 font-mono tracking-tight"><?= number_format((int)($metrics['total_invoices'] ?? 0)) ?></span>
                <span class="text-[11px] text-slate-500 block mt-0.5 font-medium">Customer sales register</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center text-lg">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('customer-invoices') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-2.5">
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Search Customer / Invoice</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" 
                           placeholder="Invoice #, Customer Name, Mobile..." 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl pl-8 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Sales Mode</label>
                <select name="sales_mode" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-2.5 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Modes</option>
                    <option value="offline" <?= ($filters['sales_mode'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline (Store)</option>
                    <option value="online" <?= ($filters['sales_mode'] ?? '') === 'online' ? 'selected' : '' ?>>Online</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Statuses</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid (Settled)</option>
                    <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="unpaid" <?= ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid (Credit)</option>
                    <option value="overdue" <?= ($filters['payment_status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Methods</option>
                    <option value="cash" <?= ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="upi" <?= ($filters['payment_method'] ?? '') === 'upi' ? 'selected' : '' ?>>UPI / QR</option>
                    <option value="card" <?= ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Card</option>
                    <option value="split" <?= ($filters['payment_method'] ?? '') === 'split' ? 'selected' : '' ?>>Split Payment</option>
                    <option value="credit" <?= ($filters['payment_method'] ?? '') === 'credit' ? 'selected' : '' ?>>Credit / Ledger</option>
                    <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Date Range</label>
                <div class="grid grid-cols-2 gap-1.5">
                    <input type="date" name="date_from" value="<?= sanitize($filters['date_from'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-[11px] text-slate-800 outline-none" title="From Date">
                    <input type="date" name="date_to" value="<?= sanitize($filters['date_to'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-[11px] text-slate-800 outline-none" title="To Date">
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold px-3 py-2 rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1.5 h-[35px]">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="<?= url('customer-invoices') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 px-3 py-2 rounded-xl text-xs h-[35px] flex items-center justify-center" title="Reset Filters">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Main Invoices Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Invoice #</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Date & Time</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Customer Details</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-center">Items</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-right">Grand Total</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-right">Paid / Due</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-center">Payment Mode</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-center">Sales Mode</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-center">Status</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400 text-xl">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">No customer invoices found</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria or create a new sale at the POS terminal.</p>
                                <a href="<?= url('billing') ?>" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-amber-700 hover:text-amber-800">
                                    <i class="fas fa-cash-register"></i> Open POS Terminal
                                </a>
                            </td>
                        </tr>
                    <?php else: foreach ($invoices as $inv): 
                        $due = (float)($inv['due_amount'] ?? 0);
                        $paid = (float)($inv['paid_amount'] ?? 0);
                        $total = (float)($inv['grand_total'] ?? 0);
                        $payStat = strtolower($inv['payment_status'] ?? 'paid');
                        $invStat = strtolower($inv['status'] ?? 'completed');
                        
                        // Status badge colors
                        if ($invStat === 'cancelled') {
                            $statBadge = 'bg-slate-100 text-slate-600 border-slate-200';
                            $statLabel = 'Cancelled';
                        } elseif ($payStat === 'paid' || $due <= 0.01) {
                            $statBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            $statLabel = 'Paid';
                        } elseif ($payStat === 'partial') {
                            $statBadge = 'bg-amber-50 text-amber-800 border-amber-200';
                            $statLabel = 'Partial';
                        } else {
                            $statBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                            $statLabel = 'Unpaid';
                        }
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors <?= $invStat === 'cancelled' ? 'opacity-60 bg-slate-50/40' : '' ?>">
                            <!-- Invoice # & Type -->
                            <td class="py-3 px-4 font-mono">
                                <div class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                    <a href="<?= url('billing/invoice/' . $inv['id']) ?>" class="hover:text-amber-600 transition-colors">
                                        <?= sanitize($inv['invoice_no']) ?>
                                    </a>
                                </div>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="text-[9px] uppercase px-1.5 py-0.2 rounded font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <?= sanitize(str_replace('_', ' ', $inv['invoice_type'] ?? 'Tax Invoice')) ?>
                                    </span>
                                    <?php if (!empty($inv['reference_no'])): ?>
                                        <span class="text-[9px] text-slate-400 font-mono" title="Reference No">#<?= sanitize($inv['reference_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Date & Time -->
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800"><?= formatDate($inv['invoice_date']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $inv['invoice_time'] ? date('h:i A', strtotime($inv['invoice_time'])) : '' ?></div>
                            </td>

                            <!-- Customer Details -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900"><?= sanitize($inv['customer_name'] ?? 'Walk-in Customer') ?></div>
                                <?php if (!empty($inv['customer_mobile'])): ?>
                                    <div class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                                        <i class="fas fa-phone-alt text-[9px] text-slate-400"></i>
                                        <a href="tel:<?= sanitize($inv['customer_mobile']) ?>" class="hover:underline"><?= sanitize($inv['customer_mobile']) ?></a>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Items Summary -->
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                    <?= (int)($inv['items_count'] ?? 1) ?> items
                                </span>
                                <?php if (!empty($inv['total_qty']) && (int)$inv['total_qty'] > 0): ?>
                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">(<?= (int)$inv['total_qty'] ?> pcs)</span>
                                <?php endif; ?>
                            </td>

                            <!-- Grand Total -->
                            <td class="py-3 px-4 text-right font-mono">
                                <div class="font-extrabold text-slate-900 text-sm">₹<?= number_format($total, 2) ?></div>
                                <?php if ((float)($inv['tax_amount'] ?? 0) > 0): ?>
                                    <div class="text-[10px] text-slate-400">GST: ₹<?= number_format((float)$inv['tax_amount'], 2) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Paid / Due -->
                            <td class="py-3 px-4 text-right font-mono">
                                <div class="font-bold text-emerald-700">₹<?= number_format($paid, 2) ?></div>
                                <?php if ($due > 0.01): ?>
                                    <div class="text-[11px] font-bold text-rose-600 mt-0.5">Due: ₹<?= number_format($due, 2) ?></div>
                                <?php else: ?>
                                    <div class="text-[10px] text-slate-400">Clear</div>
                                <?php endif; ?>
                            </td>

                            <!-- Payment Method -->
                            <td class="py-3 px-4 text-center">
                                <?php 
                                    $method = strtolower($inv['payment_method'] ?? 'cash');
                                    $mIcon = match($method) {
                                        'cash' => 'fa-money-bill-wave text-emerald-600',
                                        'upi' => 'fa-qrcode text-indigo-600',
                                        'card' => 'fa-credit-card text-blue-600',
                                        'split' => 'fa-layer-group text-amber-600',
                                        'credit' => 'fa-handshake text-rose-600',
                                        default => 'fa-wallet text-slate-500'
                                    };
                                ?>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 capitalize">
                                    <i class="fas <?= $mIcon ?> text-[10px]"></i> <?= sanitize($method) ?>
                                </span>
                            </td>

                            <!-- Sales Mode (Offline / Online) -->
                            <td class="py-3 px-4 text-center">
                                <?php $smode = strtolower($inv['sales_mode'] ?? 'offline'); ?>
                                <?php if ($smode === 'online'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-globe text-[10px]"></i> Online
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fas fa-store text-[10px]"></i> Offline
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider <?= $statBadge ?>">
                                    <?= sanitize($statLabel) ?>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Instant Modal View -->
                                    <button type="button" onclick="previewInvoice(<?= (int)$inv['id'] ?>)" 
                                            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" 
                                            title="Quick View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <!-- Thermal 80mm Print -->
                                    <a href="<?= url('billing/invoice/' . $inv['id'] . '/print?type=thermal') ?>" target="_blank"
                                       class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 flex items-center justify-center transition-all" 
                                       title="Print Thermal 80mm Receipt">
                                        <i class="fas fa-receipt text-xs"></i>
                                    </a>

                                    <!-- A4 Print -->
                                    <a href="<?= url('billing/invoice/' . $inv['id'] . '/print?type=a4') ?>" target="_blank"
                                       class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center transition-all" 
                                       title="Print A4 Tax Invoice">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>

                                    <!-- Add Payment (if due) -->
                                    <?php if ($due > 0.01 && $invStat !== 'cancelled'): ?>
                                    <button type="button" onclick="openPaymentModal(<?= (int)$inv['id'] ?>, '<?= sanitize($inv['invoice_no']) ?>', <?= $due ?>)"
                                            class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center transition-all" 
                                            title="Collect Pending Due Amount">
                                        <i class="fas fa-hand-holding-usd text-xs"></i>
                                    </button>
                                    <?php endif; ?>

                                    <!-- Full Page View Link -->
                                    <a href="<?= url('billing/invoice/' . $inv['id']) ?>" 
                                       class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-all" 
                                       title="Full Invoice Page">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
            <div>
                Showing page <strong class="text-slate-800"><?= (int)$pagination['page'] ?></strong> of <strong class="text-slate-800"><?= (int)$pagination['total_pages'] ?></strong> 
                (Total: <strong><?= number_format((int)$pagination['total']) ?></strong> customer invoices)
            </div>
            <div class="flex items-center gap-1">
                <?php if ($pagination['page'] > 1): ?>
                    <a href="<?= url('customer-invoices?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] - 1]))) ?>" 
                       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold transition-all">
                        <i class="fas fa-chevron-left text-[10px] mr-1"></i> Prev
                    </a>
                <?php endif; ?>

                <?php for ($p = max(1, $pagination['page'] - 2); $p <= min($pagination['total_pages'], $pagination['page'] + 2); $p++): ?>
                    <a href="<?= url('customer-invoices?' . http_build_query(array_merge($filters, ['page' => $p]))) ?>" 
                       class="px-3 py-1.5 rounded-lg font-semibold transition-all <?= $p === $pagination['page'] ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-100 text-slate-700' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a href="<?= url('customer-invoices?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] + 1]))) ?>" 
                       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold transition-all">
                        Next <i class="fas fa-chevron-right text-[10px] ml-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Invoice Preview Modal -->
<div id="invoice-preview-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-invoice text-amber-600"></i>
                <h3 class="text-sm font-extrabold text-slate-900 font-mono" id="modal-invoice-no">Invoice Preview</h3>
            </div>
            <div class="flex items-center gap-2">
                <a id="modal-thermal-link" href="#" target="_blank" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold transition-all flex items-center gap-1">
                    <i class="fas fa-receipt"></i> Thermal
                </a>
                <a id="modal-a4-link" href="#" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold transition-all flex items-center gap-1">
                    <i class="fas fa-print"></i> A4 Print
                </a>
                <button type="button" onclick="closePreviewModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto space-y-4" id="modal-body-content">
            <div class="py-12 text-center text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl text-amber-600 mb-2"></i>
                <p class="text-xs">Loading customer invoice details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Inline Due Payment Modal -->
<div id="collect-payment-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
        <form id="payment-form" onsubmit="submitPayment(event)">
            <?= csrf_field() ?>
            <input type="hidden" id="pay-invoice-id" name="invoice_id" value="">
            
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hand-holding-usd text-emerald-700"></i>
                    <h3 class="text-sm font-extrabold text-emerald-950">Collect Customer Due Payment</h3>
                </div>
                <button type="button" onclick="closePaymentModal()" class="w-6 h-6 rounded-lg hover:bg-emerald-200 text-emerald-800 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 font-mono text-xs flex justify-between">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Invoice</span>
                        <strong class="text-slate-900" id="pay-invoice-no">-</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Pending Due</span>
                        <strong class="text-rose-700 text-sm" id="pay-due-display">₹0.00</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Amount (₹)</label>
                    <input type="number" step="0.01" id="pay-amount" name="amount" required min="1"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-emerald-500 rounded-xl px-3 py-2 text-sm font-bold text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                    <select id="pay-method" name="payment_method" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-emerald-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                        <option value="cash">Cash Counter</option>
                        <option value="upi">UPI / GPay / PhonePe</option>
                        <option value="card">Card (Debit/Credit POS)</option>
                        <option value="bank_transfer">Bank Transfer / NEFT</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reference / Note (Optional)</label>
                    <input type="text" id="pay-reference" name="transaction_reference" placeholder="e.g. UPI Ref / Cheque No"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                </div>
            </div>

            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-all">Cancel</button>
                <button type="submit" id="pay-submit-btn" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-check"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewInvoice(id) {
    const modal = document.getElementById('invoice-preview-modal');
    const body = document.getElementById('modal-body-content');
    const invNoTitle = document.getElementById('modal-invoice-no');
    const thermalLink = document.getElementById('modal-thermal-link');
    const a4Link = document.getElementById('modal-a4-link');

    thermalLink.href = '<?= url("billing/invoice/") ?>' + id + '/print?type=thermal';
    a4Link.href = '<?= url("billing/invoice/") ?>' + id + '/print?type=a4';

    modal.classList.remove('hidden');
    body.innerHTML = `
        <div class="py-12 text-center text-slate-400">
            <i class="fas fa-spinner fa-spin text-2xl text-amber-600 mb-2"></i>
            <p class="text-xs">Loading invoice #${id} details...</p>
        </div>
    `;

    fetch('<?= url("billing/invoice/") ?>' + id + '?json=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success || !res.invoice) {
            body.innerHTML = '<div class="p-4 text-center text-rose-600 text-xs">Failed to load invoice details.</div>';
            return;
        }

        const inv = res.invoice;
        const items = res.items || [];
        invNoTitle.textContent = inv.invoice_no;

        let itemsHtml = items.map((it, idx) => `
            <tr class="border-b border-slate-100 text-xs font-mono">
                <td class="py-2.5 px-3 text-slate-400 font-sans">${idx + 1}</td>
                <td class="py-2.5 px-3 font-sans">
                    <div class="font-bold text-slate-900">${it.product_name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${it.product_code || ''} ${it.hsn_code ? '• HSN: ' + it.hsn_code : ''}</div>
                </td>
                <td class="py-2.5 px-3 text-center font-bold text-slate-800 font-sans">${it.quantity}</td>
                <td class="py-2.5 px-3 text-right">₹${Number(it.unit_price).toFixed(2)}</td>
                <td class="py-2.5 px-3 text-right font-black text-slate-900">₹${Number(it.total_amount).toFixed(2)}</td>
            </tr>
        `).join('');

        body.innerHTML = `
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Customer</span>
                    <strong class="text-slate-900 text-sm block">${inv.customer_name || 'Walk-in Retail Customer'}</strong>
                    ${inv.customer_mobile ? '<span class="text-slate-600 font-mono text-xs"><i class="fas fa-phone-alt text-[9px] mr-1 text-slate-400"></i>' + inv.customer_mobile + '</span>' : ''}
                    ${inv.customer_city ? '<span class="text-slate-500 block text-[11px]">' + inv.customer_city + ', ' + (inv.customer_state || '') + '</span>' : ''}
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Invoice Meta</span>
                    <div class="font-mono text-xs text-slate-700">Date: <strong>${inv.invoice_date}</strong> ${inv.invoice_time || ''}</div>
                    <div class="font-mono text-xs text-slate-700">Cashier: <strong>${inv.cashier_name || 'Counter Operator'}</strong></div>
                    <div class="mt-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${inv.payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">${inv.payment_status}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Purchased Items (${items.length})</h4>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">#</th>
                                <th class="py-2.5 px-3">Product</th>
                                <th class="py-2.5 px-3 text-center">Qty</th>
                                <th class="py-2.5 px-3 text-right">Rate</th>
                                <th class="py-2.5 px-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 grid grid-cols-2 gap-4 font-mono text-xs">
                <div class="space-y-1 text-slate-600">
                    <div>Subtotal: <strong class="text-slate-900">₹${Number(inv.subtotal).toFixed(2)}</strong></div>
                    <div>GST Tax: <strong class="text-slate-900">₹${Number(inv.tax_amount).toFixed(2)}</strong></div>
                    ${Number(inv.overall_discount_amount) > 0 ? '<div>Discount: <strong class="text-emerald-700">-₹' + Number(inv.overall_discount_amount).toFixed(2) + '</strong></div>' : ''}
                    <div>Payment Method: <strong class="capitalize text-slate-900">${inv.payment_method}</strong></div>
                    <div>Sales Mode: <strong class="capitalize ${inv.sales_mode === 'online' ? 'text-blue-600 font-bold' : 'text-slate-900'}">${inv.sales_mode || 'offline'}</strong></div>
                </div>
                <div class="text-right space-y-1">
                    <div class="text-sm font-extrabold text-slate-900">Grand Total: ₹${Number(inv.grand_total).toFixed(2)}</div>
                    <div class="text-xs font-bold text-emerald-700">Paid: ₹${Number(inv.paid_amount).toFixed(2)}</div>
                    ${Number(inv.due_amount) > 0.01 ? '<div class="text-xs font-bold text-rose-600">Due Balance: ₹' + Number(inv.due_amount).toFixed(2) + '</div>' : '<div class="text-xs text-emerald-600 font-bold">✓ Fully Settled</div>'}
                </div>
            </div>
            ${inv.notes ? '<div class="p-2.5 bg-amber-50/70 border border-amber-200/60 rounded-xl text-xs text-amber-900"><i class="fas fa-sticky-note mr-1 text-amber-600"></i><strong>Notes:</strong> ' + inv.notes + '</div>' : ''}
        `;
    })
    .catch(err => {
        body.innerHTML = '<div class="p-4 text-center text-rose-600 text-xs">Error loading invoice: ' + err.message + '</div>';
    });
}

function closePreviewModal() {
    document.getElementById('invoice-preview-modal').classList.add('hidden');
}

function openPaymentModal(id, invoiceNo, dueAmount) {
    document.getElementById('pay-invoice-id').value = id;
    document.getElementById('pay-invoice-no').textContent = invoiceNo;
    document.getElementById('pay-due-display').textContent = '₹' + Number(dueAmount).toFixed(2);
    document.getElementById('pay-amount').value = Number(dueAmount).toFixed(2);
    document.getElementById('pay-amount').max = Number(dueAmount).toFixed(2);
    document.getElementById('collect-payment-modal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('collect-payment-modal').classList.add('hidden');
}

function submitPayment(e) {
    e.preventDefault();
    const id = document.getElementById('pay-invoice-id').value;
    const amount = document.getElementById('pay-amount').value;
    const method = document.getElementById('pay-method').value;
    const ref = document.getElementById('pay-reference').value;
    const btn = document.getElementById('pay-submit-btn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recording...';

    const formData = new FormData();
    formData.append('amount', amount);
    formData.append('payment_method', method);
    formData.append('transaction_reference', ref);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    fetch('<?= url("billing/invoice/") ?>' + id + '/payment', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Payment recording failed.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Record Payment';
        }
    })
    .catch(err => {
        alert('Network error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Record Payment';
    });
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
        closePaymentModal();
    }
});
</script>
