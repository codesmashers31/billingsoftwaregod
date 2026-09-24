<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Expense Vouchers & Petty Cash</h1>
            <p class="text-xs text-slate-500">Record showroom rent, artisan foundry freight, pooja supplies, packaging, and electricity bills</p>
        </div>
        <?php if (hasPermission('expenses.create')): ?>
        <button onclick="openModal('modal-add-expense')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-receipt"></i> New Expense Voucher
        </button>
        <?php endif; ?>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Voucher No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Title / Purpose</th>
                        <th class="py-3.5 px-4">Account</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    <?php if (empty($expenses)): ?>
                        <tr><td colspan="7" class="py-12 text-center text-slate-400 font-sans">No expenses recorded.</td></tr>
                    <?php else: foreach ($expenses as $ex): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900"><?= $ex['voucher_no'] ?></td>
                            <td class="py-3.5 px-4 text-slate-600 font-sans"><?= formatDate($ex['expense_date']) ?></td>
                            <td class="py-3.5 px-4 font-sans font-semibold text-slate-800"><?= sanitize($ex['category_name']) ?></td>
                            <td class="py-3.5 px-4 font-sans text-slate-900"><?= sanitize($ex['title']) ?></td>
                            <td class="py-3.5 px-4 font-sans text-slate-600"><?= sanitize($ex['account_name']) ?></td>
                            <td class="py-3.5 px-4 font-black text-rose-600 text-sm"><?= formatCurrency($ex['amount']) ?></td>
                            <td class="py-3.5 px-4 font-sans">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $ex['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' ?>">
                                    <?= ucfirst($ex['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Expense -->
<div id="modal-add-expense" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Record Expense Voucher</h3>
            <button onclick="closeModal('modal-add-expense')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('expenses/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Expense Category <span class="text-rose-500">*</span></label>
                <select name="expense_category_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <?php foreach ($categories as $ec): ?>
                        <option value="<?= $ec['id'] ?>"><?= sanitize($ec['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Source Account <span class="text-rose-500">*</span></label>
                <select name="account_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <?php foreach ($accounts as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= sanitize($a['name']) ?> (<?= formatCurrency($a['current_balance']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Expense Title / Purpose <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="e.g. Bronze Foundry Freight & Toll" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Amount (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono font-bold focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Expense Date</label>
                    <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI / QR</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-expense')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Voucher</button>
            </div>
        </form>
    </div>
</div>
