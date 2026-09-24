<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Product Categories</h1>
            <p class="text-xs text-slate-500">Classify god statues, spiritual artifacts, pooja accessories, and temple decor</p>
        </div>
        <?php if (hasPermission('categories.manage')): ?>
        <button onclick="openModal('modal-add-category')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-plus"></i> Add New Category
        </button>
        <?php endif; ?>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($categories as $c): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-mono font-bold text-xs border border-amber-200">
                            <?= $c['code'] ?>
                        </span>
                        <div class="text-[11px] text-slate-500 font-semibold space-x-2">
                            <span><i class="fas fa-sitemap text-amber-600"></i> <?= $c['subcategory_count'] ?? 0 ?> subcats</span>
                            <span>•</span>
                            <span><i class="fas fa-box text-amber-600"></i> <?= $c['product_count'] ?? 0 ?> products</span>
                        </div>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5"><?= sanitize($c['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= sanitize($c['description'] ?? 'No description provided.') ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-slate-900">
                        <?= $c['total_stock'] ?? 0 ?> in stock
                    </span>
                    <div class="flex items-center gap-1">
                        <?php if (hasPermission('categories.manage')): ?>
                        <button onclick="openEditCategory(<?= htmlspecialchars(json_encode($c)) ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-amber-800 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(<?= $c['id'] ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add Category -->
<div id="modal-add-category" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Add Product Category</h3>
            <button onclick="closeModal('modal-add-category')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('categories/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Name</label>
                <input type="text" name="name" required placeholder="e.g. Hindu God Statues" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Code</label>
                <input type="text" name="code" required placeholder="CAT-HINDU" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Category highlights..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-category')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Category -->
<div id="modal-edit-category" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Edit Category</h3>
            <button onclick="closeModal('modal-edit-category')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form id="form-edit-category" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Name</label>
                <input type="text" id="edit-cat-name" name="name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea id="edit-cat-desc" name="description" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select id="edit-cat-status" name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-category')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCategory(cat) {
    document.getElementById('form-edit-category').action = window.APP_URL + '/categories/edit/' + cat.id;
    document.getElementById('edit-cat-name').value = cat.name;
    document.getElementById('edit-cat-desc').value = cat.description || '';
    document.getElementById('edit-cat-status').value = cat.status;
    openModal('modal-edit-category');
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category? Subcategories under it will also be deleted.')) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(window.APP_URL + '/categories/delete/' + id, {
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
