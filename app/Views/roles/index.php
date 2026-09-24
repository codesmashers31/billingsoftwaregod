<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">User Roles & Access Permissions</h1>
            <p class="text-xs text-slate-500">Fine-grained Role-Based Access Control (RBAC) governing 44 actions across modules</p>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($roles as $r): ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-amber-400 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-mono font-bold text-xs border border-amber-200">
                            <?= $r['slug'] ?>
                        </span>
                        <span class="text-xs text-slate-500 font-semibold"><i class="fas fa-users text-amber-600"></i> <?= $r['user_count'] ?? 0 ?> users</span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5"><?= sanitize($r['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= sanitize($r['description'] ?? 'System defined access role.') ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                        <?= $r['permission_count'] ?? 0 ?> Permissions
                    </span>
                    <button onclick="viewRolePermissions(<?= $r['id'] ?>, '<?= escapeHtml($r['name']) ?>')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        View Permissions
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Role Permissions -->
<div id="modal-role-perms" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-2xl shadow-2xl relative max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900" id="modal-role-title">Role Permissions</h3>
            <button onclick="closeModal('modal-role-perms')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-role-perms-body" class="grid grid-cols-2 gap-2 text-xs"></div>
    </div>
</div>

<script>
async function viewRolePermissions(roleId, roleName) {
    document.getElementById('modal-role-title').textContent = 'Permissions: ' + roleName;
    try {
        const res = await fetch(window.APP_URL + '/roles/permissions/' + roleId);
        const data = await res.json();
        let html = '';
        (data.permissions || []).forEach(p => {
            html += `
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-900">${p.name}</span>
                        <div class="text-[10px] text-slate-400 font-mono">${p.slug}</div>
                    </div>
                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                </div>
            `;
        });
        document.getElementById('modal-role-perms-body').innerHTML = html || '<div class="col-span-2 p-6 text-center text-slate-400">All Super Admin Permissions Enabled.</div>';
        openModal('modal-role-perms');
    } catch(e) { console.error(e); }
}
</script>
