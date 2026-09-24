<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Sales Invoices & Billing Register</h1>
            <p class="text-xs text-slate-500">Search past customer bills, reprint tax & thermal receipts, process returns, or cancel invoices</p>
        </div>
        <?php if (hasPermission('billing.pos')): ?>
        <a href="<?= url('billing') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-cash-register"></i> Open POS Terminal
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('billing/invoices') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" placeholder="Search by invoice number, customer name, mobile..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none">
            </div>
            <div>
                <input type="date" name="date_from" value="<?= sanitize($filters['date_from'] ?? '') ?>" 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <select name="status" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Statuses</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="<?= url('billing/invoices') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 px-3 py-2 rounded-xl text-xs">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Invoice No</th>
                        <th class="py-3.5 px-4">Date & Time</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Grand Total</th>
                        <th class="py-3.5 px-4">Paid / Due</th>
                        <th class="py-3.5 px-4">Payment Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="7" class="py-12 text-center text-slate-400">No invoices recorded matching filters.</td></tr>
                    <?php else: foreach ($invoices as $inv): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-black text-slate-900 text-sm"><?= $inv['invoice_no'] ?></span>
                                <span class="text-[10px] text-slate-400 block uppercase">Cashier: <?= sanitize($inv['cashier_name']) ?></span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <div class="font-semibold text-slate-800"><?= formatDate($inv['invoice_date']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $inv['invoice_time'] ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900"><?= sanitize($inv['customer_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= sanitize($inv['customer_mobile']) ?></div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm">
                                <?= formatCurrency($inv['grand_total']) ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <div class="text-emerald-700 font-bold">Paid: <?= formatCurrency($inv['paid_amount']) ?></div>
                                <?php if ($inv['due_amount'] > 0): ?>
                                    <div class="text-rose-600 font-bold">Due: <?= formatCurrency($inv['due_amount']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ($inv['status'] === 'cancelled'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">Cancelled</span>
                                <?php elseif ($inv['payment_status'] === 'paid'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Paid</span>
                                <?php elseif ($inv['payment_status'] === 'partial'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200">Partial</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <a href="<?= url('billing/invoice/' . $inv['id']) ?>" title="View Invoice" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs inline-block">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= url('billing/invoice/' . $inv['id'] . '/print?type=a4') ?>" target="_blank" title="Print A4" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs inline-block">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="<?= url('billing/invoice/' . $inv['id'] . '/print?type=thermal') ?>" target="_blank" title="Print Thermal" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs inline-block">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                <?php if ($inv['status'] !== 'cancelled' && hasPermission('billing.cancel')): ?>
                                <button onclick="cancelInvoice(<?= $inv['id'] ?>)" title="Cancel Invoice" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function cancelInvoice(id) {
    if (!confirm('Are you sure you want to cancel this invoice? All sold god statues and items will be automatically restocked back to inventory.')) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(window.APP_URL + '/billing/invoice/' + id + '/cancel', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast('error', data.message);
        }
    } catch(e) { console.error(e); }
}
</script>
