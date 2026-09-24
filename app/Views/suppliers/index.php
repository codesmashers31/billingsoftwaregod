<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Master Sthapathis & Bronze Foundries</h1>
            <p class="text-xs text-slate-500">Track traditional sculptors, raw brass metal foundries, purchase history, and outstanding payables</p>
        </div>
        <?php if (hasPermission('suppliers.create')): ?>
        <button onclick="openModal('modal-add-supplier')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-hammer"></i> Register Sthapathi / Foundry
        </button>
        <?php endif; ?>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($suppliers as $s): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-mono font-bold text-xs border border-amber-200">
                            <?= $s['supplier_code'] ?? $s['code'] ?? '' ?>
                        </span>
                        <span class="text-[10px] text-slate-500 font-semibold"><?= sanitize($s['city'] ?? '') ?>, <?= sanitize($s['state'] ?? 'Tamil Nadu') ?></span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1"><?= sanitize($s['name']) ?></h3>
                    <div class="text-xs text-slate-600 font-medium"><?= sanitize($s['contact_person'] ?? '') ?></div>
                    <div class="text-xs font-mono text-slate-500 mt-1"><?= $s['mobile'] ?? $s['phone'] ?? '' ?> • <?= $s['email'] ?: 'No email' ?></div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-sans">Outstanding Payable</span>
                        <span class="text-sm font-black <?= $s['outstanding_balance'] > 0 ? 'text-amber-800' : 'text-emerald-700' ?>">
                            <?= formatCurrency($s['outstanding_balance']) ?>
                        </span>
                    </div>

                    <div class="flex items-center gap-1 font-sans">
                        <?php if (hasPermission('suppliers.edit')): ?>
                        <button onclick="openEditSupplier(<?= htmlspecialchars(json_encode($s)) ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-amber-800 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add Supplier -->
<div id="modal-add-supplier" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Register Sthapathi / Foundry</h3>
            <button onclick="closeModal('modal-add-supplier')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('suppliers/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foundry / Workshop Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Swamimalai Bronze Heritage Works" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Master Sthapathi / Contact</label>
                    <input type="text" name="contact_person" placeholder="Master Devasenapathy" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" required placeholder="9443100001" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="foundry@domain.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN Number</label>
                    <input type="text" name="gst_number" placeholder="33AABCF9876K1Z2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                    <input type="text" name="city" value="Swamimalai" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                    <input type="text" name="state" value="Tamil Nadu" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-supplier')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Sthapathi</button>
            </div>
        </form>
    </div>
</div>
