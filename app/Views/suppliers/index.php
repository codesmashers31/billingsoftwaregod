<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Master Sthapathis & Bronze Foundries</h1>
            <p class="text-xs text-slate-500">Track traditional sculptors, raw brass metal foundries, purchase history, and outstanding payables</p>
        </div>
        <?php if (hasPermission('suppliers.manage') || hasPermission('suppliers.create')): ?>
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
                        <?php if (hasPermission('suppliers.manage') || hasPermission('suppliers.edit')): ?>
                        <button onclick="openEditSupplier(<?= htmlspecialchars(json_encode($s)) ?>)" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-amber-800 text-xs transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php endif; ?>
                        <?php if (hasPermission('suppliers.manage')): ?>
                        <button onclick="deleteSupplier('<?= $s['id'] ?>')" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs transition-colors">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($suppliers)): ?>
            <div class="col-span-full py-16 text-center bg-white border border-dashed border-slate-200 rounded-2xl">
                <div class="w-16 h-16 mx-auto mb-4 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-sm">
                    <i class="fas fa-hammer text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No suppliers found</h3>
                <p class="text-xs text-slate-400 mt-1">Get started by registering your first artisan or foundry above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Add Supplier -->
<div id="modal-add-supplier" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative mx-auto">
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
                    <input type="text" name="mobile" required placeholder="9443100001" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
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
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Address</label>
                <input type="text" name="address" placeholder="Workshop Street, Swamimalai" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-supplier')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Sthapathi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Supplier -->
<div id="modal-edit-supplier" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative mx-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Edit Sthapathi / Foundry</h3>
            <button onclick="closeModal('modal-edit-supplier')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form id="form-edit-supplier" action="" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foundry / Workshop Name <span class="text-rose-500">*</span></label>
                <input type="text" id="edit-supplier-name" name="name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Master Sthapathi / Contact</label>
                    <input type="text" id="edit-supplier-contact" name="contact_person" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-supplier-mobile" name="mobile" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" id="edit-supplier-email" name="email" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN Number</label>
                    <input type="text" id="edit-supplier-gst" name="gst_number" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                    <input type="text" id="edit-supplier-city" name="city" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                    <input type="text" id="edit-supplier-state" name="state" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Address</label>
                <input type="text" id="edit-supplier-address" name="address" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-supplier')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Update Sthapathi</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditSupplier(supplier) {
    const form = document.getElementById('form-edit-supplier');
    form.action = '<?= url("suppliers/edit/") ?>' + supplier.id;
    document.getElementById('edit-supplier-name').value = supplier.name || '';
    document.getElementById('edit-supplier-contact').value = supplier.contact_person || '';
    document.getElementById('edit-supplier-mobile').value = supplier.mobile || supplier.phone || '';
    document.getElementById('edit-supplier-email').value = supplier.email || '';
    document.getElementById('edit-supplier-gst').value = supplier.gst_number || '';
    document.getElementById('edit-supplier-city').value = supplier.city || '';
    document.getElementById('edit-supplier-state').value = supplier.state || '';
    document.getElementById('edit-supplier-address').value = supplier.address || '';
    openModal('modal-edit-supplier');
}

async function deleteSupplier(id) {
    let confirmed = false;
    if (typeof showConfirmDialog === 'function') {
        confirmed = await showConfirmDialog({
            title: 'Delete Supplier',
            message: 'Are you sure you want to delete this supplier? This action cannot be undone.',
            confirmText: 'Yes, Delete',
            type: 'danger'
        });
    } else {
        confirmed = confirm('Are you sure you want to delete this supplier?');
    }
    if (!confirmed) return;

    try {
        const formData = new FormData();
        formData.append('_csrf_token', '<?= csrf_token() ?>');

        const res = await fetch('<?= url("suppliers/delete/") ?>' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            if (typeof showToast === 'function') {
                showToast('success', data.message || 'Supplier deleted successfully.');
            }
            setTimeout(() => window.location.reload(), 500);
        } else {
            if (typeof showToast === 'function') {
                showToast('error', data.message || 'Could not delete supplier.');
            } else {
                alert(data.message || 'Could not delete supplier.');
            }
        }
    } catch (e) {
        console.error(e);
        if (typeof showToast === 'function') {
            showToast('error', 'Network error occurred.');
        }
    }
}
</script>
