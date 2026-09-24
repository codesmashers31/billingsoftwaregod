<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Subcategories (Deities & Sacred Forms)</h1>
            <p class="text-xs text-slate-500">Classify specific god forms: Ganesha, Murugan, Shiva, Krishna, Lakshmi, Buddha, Deepams, etc.</p>
        </div>
        <?php if (hasPermission('categories.manage')): ?>
        <button onclick="openModal('modal-add-subcategory')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-plus"></i> Add Subcategory
        </button>
        <?php endif; ?>
    </div>

    <!-- Subcategories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($subcategories as $s): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-mono font-bold text-xs border border-amber-200">
                            <?= $s['code'] ?>
                        </span>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-amber-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                            <?= sanitize($s['category_name']) ?>
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5"><?= sanitize($s['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= sanitize($s['description'] ?? 'Deity form and catalog classification.') ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold flex items-center gap-1">
                        <i class="fas fa-box text-amber-600"></i> <?= $s['product_count'] ?? 0 ?> items
                    </span>
                    <div class="flex items-center gap-1">
                        <?php if (hasPermission('categories.manage')): ?>
                        <button onclick="openEditSubcategory(<?= htmlspecialchars(json_encode($s)) ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-amber-800 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteSubcategory(<?= $s['id'] ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add Subcategory -->
<div id="modal-add-subcategory" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Add Subcategory</h3>
            <button onclick="closeModal('modal-add-subcategory')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('subcategories/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Parent Category</label>
                <select name="category_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory Name</label>
                <input type="text" name="name" required placeholder="e.g. Lord Ganesha (Vinayagar)" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory Code</label>
                <input type="text" name="code" required placeholder="SUB-GANESHA" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-subcategory')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Subcategory</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Subcategory -->
<div id="modal-edit-subcategory" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Edit Subcategory</h3>
            <button onclick="closeModal('modal-edit-subcategory')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form id="form-edit-subcategory" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Parent Category</label>
                <select id="edit-sub-category" name="category_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory Name</label>
                <input type="text" id="edit-sub-name" name="name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea id="edit-sub-desc" name="description" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select id="edit-sub-status" name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-subcategory')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditSubcategory(sub) {
    document.getElementById('form-edit-subcategory').action = window.APP_URL + '/subcategories/edit/' + sub.id;
    document.getElementById('edit-sub-category').value = sub.category_id;
    document.getElementById('edit-sub-name').value = sub.name;
    document.getElementById('edit-sub-desc').value = sub.description || '';
    document.getElementById('edit-sub-status').value = sub.status;
    openModal('modal-edit-subcategory');
}

async function deleteSubcategory(id) {
    if (!confirm('Are you sure you want to delete this subcategory?')) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(window.APP_URL + '/subcategories/delete/' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 500);
        } else {
            showToast('error', data.message);
        }
    } catch(e) { console.error(e); }
}
</script>
