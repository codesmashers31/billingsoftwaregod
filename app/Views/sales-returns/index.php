<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Sales Returns & Exchange Register</h1>
            <p class="text-xs text-slate-500">Record returned idol items, issue refund vouchers, and auto-restock physical inventory</p>
        </div>
        <?php if (hasPermission('billing.returns')): ?>
        <a href="<?= url('sales-returns/create') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-undo-alt"></i> Process New Return
        </a>
        <?php endif; ?>
    </div>

    <!-- Returns Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Return No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Original Invoice</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Refund Amount</th>
                        <th class="py-3.5 px-4">Mode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($returns)): ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">No sales return records registered.</td></tr>
                    <?php else: foreach ($returns as $r): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= $r['return_no'] ?></td>
                            <td class="py-3.5 px-4 text-slate-700"><?= formatDate($r['return_date']) ?></td>
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-800"><?= $r['invoice_no'] ?></td>
                            <td class="py-3.5 px-4 font-bold text-slate-900"><?= sanitize($r['customer_name']) ?></td>
                            <td class="py-3.5 px-4 font-mono font-black text-rose-600 text-sm"><?= formatCurrency($r['refund_amount']) ?></td>
                            <td class="py-3.5 px-4 uppercase text-[10px] font-bold text-slate-500"><?= $r['refund_mode'] ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
