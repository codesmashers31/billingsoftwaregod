<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Organizational Departments</h1>
            <p class="text-xs text-slate-500">Divisions managing showroom sales, temple casting foundry, accounting, and inventory</p>
        </div>
        <?php if (hasPermission('users.manage')): ?>
        <button onclick="openModal('modal-add-dept')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-plus"></i> Add Department
        </button>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($departments as $d): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-mono font-bold text-xs border border-amber-200">
                            <?= $d['code'] ?>
                        </span>
                        <span class="text-xs text-slate-500 font-semibold"><i class="fas fa-users text-amber-600"></i> <?= $d['user_count'] ?? 0 ?> staff</span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5"><?= sanitize($d['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= sanitize($d['description'] ?? 'Organizational unit.') ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Active Division</span>
                    <?php if (hasPermission('users.manage')): ?>
                    <button onclick="deleteDepartment(<?= $d['id'] ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add Dept -->
<div id="modal-add-dept" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Add Department</h3>
            <button onclick="closeModal('modal-add-dept')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('departments/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Department Name</label>
                <input type="text" name="name" required placeholder="e.g. Sculpting & Finishing Wing" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Department Code</label>
                <input type="text" name="code" required placeholder="DEP-SCULPT" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-dept')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Department</button>
            </div>
        </form>
    </div>
</div>

<script>
async function deleteDepartment(id) {
    if (!confirm('Are you sure you want to delete this department?')) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(window.APP_URL + '/departments/delete/' + id, {
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
