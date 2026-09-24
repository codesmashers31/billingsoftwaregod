<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Chart of Accounts & Cash Book</h1>
            <p class="text-xs text-slate-500">Showroom cash drawer, bank ledgers (HDFC, SBI), double-entry audit balance, and transfer controls</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= url('accounts/payments') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold shadow-sm">
                <i class="fas fa-money-check-alt mr-1 text-amber-600"></i> Receipts & Payouts
            </a>
            <?php if (hasPermission('accounts.manage')): ?>
            <button onclick="openModal('modal-add-account')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> New Account
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Accounts Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($accounts as $acc): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[10px] uppercase font-bold border border-slate-200">
                            <?= $acc['account_code'] ?? $acc['code'] ?? '' ?>
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xs">
                            <i class="fas <?= $acc['type'] === 'cash' ? 'fa-wallet' : ($acc['type'] === 'bank' ? 'fa-university' : 'fa-balance-scale') ?>"></i>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 line-clamp-1"><?= sanitize($acc['name']) ?></h3>
                    <div class="text-[11px] text-slate-400 uppercase font-bold tracking-wider mt-0.5"><?= $acc['type'] ?></div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <span class="text-[10px] text-slate-400 block font-sans">Current Balance</span>
                    <span class="text-xl font-black font-mono text-slate-900">
                        <?= formatCurrency($acc['current_balance']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Double-Entry Ledger Transactions -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-exchange-alt text-amber-600"></i> Double-Entry Account Activity Ledger
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Account</th>
                        <th class="py-3 px-3">Type</th>
                        <th class="py-3 px-3">Debit (₹)</th>
                        <th class="py-3 px-3">Credit (₹)</th>
                        <th class="py-3 px-3">Balance After (₹)</th>
                        <th class="py-3 px-3">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    <?php if (empty($recentTransactions)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-slate-400 font-sans">No transactions recorded.</td></tr>
                    <?php else: foreach ($recentTransactions as $tx): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3 text-slate-500 font-sans"><?= formatDate($tx['transaction_date']) ?></td>
                            <td class="py-2.5 px-3 font-sans font-bold text-slate-900"><?= sanitize($tx['account_name']) ?></td>
                            <td class="py-2.5 px-3 uppercase text-[10px] font-bold <?= $tx['type'] === 'credit' ? 'text-emerald-700' : 'text-rose-600' ?>">
                                <?= $tx['type'] ?>
                            </td>
                            <td class="py-2.5 px-3 text-rose-600"><?= $tx['debit'] > 0 ? formatCurrency($tx['debit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-emerald-700 font-bold"><?= $tx['credit'] > 0 ? formatCurrency($tx['credit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-slate-900 font-bold"><?= formatCurrency($tx['balance_after']) ?></td>
                            <td class="py-2.5 px-3 text-slate-500 font-sans truncate max-w-xs"><?= sanitize($tx['description'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Account -->
<div id="modal-add-account" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Add New Ledger Account</h3>
            <button onclick="closeModal('modal-add-account')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('accounts/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Account Name</label>
                <input type="text" name="name" required placeholder="e.g. ICICI Current Account" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Account Code</label>
                <input type="text" name="code" required placeholder="ACC-ICICI" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Account Type</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="bank">Bank Account</option>
                    <option value="cash">Cash Register</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Opening Balance (₹)</label>
                <input type="number" step="0.01" name="opening_balance" value="0.00" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-account')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Account</button>
            </div>
        </form>
    </div>
</div>
