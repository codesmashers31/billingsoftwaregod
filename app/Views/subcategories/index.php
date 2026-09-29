<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Subcategories (Deities & Sacred Forms)</h1>
            <p class="text-xs text-slate-500">Classify specific god forms: Ganesha, Murugan, Shiva, Krishna, Lakshmi, Buddha, Deepams, etc.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <?php if (hasPermission('categories.manage')): ?>
            <button type="button" onclick="openModal('import-subcat-modal')" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                <i class="fas fa-file-import text-indigo-600"></i> Import CSV
            </button>
            <button onclick="openModal('modal-add-subcategory')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> Add Subcategory
            </button>
            <?php endif; ?>
        </div>
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

<!-- Import Subcategories CSV Modal -->
<div id="import-subcat-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-base font-extrabold text-slate-900"><i class="fas fa-file-csv text-indigo-600 mr-2"></i> Bulk Import Subcategories</h3>
            <button type="button" onclick="closeModal('import-subcat-modal')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= url('subcategories/import') ?>" method="POST" enctype="multipart/form-data" class="p-5">
            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
            <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs text-amber-800 font-medium mb-2">Ensure your CSV matches our strict data format.</p>
                <a href="<?= url('subcategories/template') ?>" class="text-xs font-bold text-amber-700 hover:text-amber-900 underline flex items-center gap-1">
                    <i class="fas fa-download"></i> Download Sample CSV Template
                </a>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-2">Upload CSV File <span class="text-rose-500">*</span></label>
                <input type="file" name="csv_file" accept=".csv" required 
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-2 bg-slate-50">
            </div>
            <p class="text-[10px] text-slate-500 mb-6"><i class="fas fa-info-circle"></i> Strict validation enabled: Duplicate Names or Codes will be rejected. Category ID is required.</p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('import-subcat-modal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Start Import
                </button>
            </div>
        </form>
    </div>
</div>
