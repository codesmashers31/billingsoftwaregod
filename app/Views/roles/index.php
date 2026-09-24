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

    <!-- Active Candidates Info -->
    <div class="mt-8">
        <h2 class="text-lg font-extrabold text-slate-900 mb-4">Active Login Candidates</h2>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-200">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Current Role</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php if(!empty($usersList)): ?>
                            <?php foreach ($usersList as $u): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <?= sanitize($u['name']) ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono"><?= sanitize($u['username']) ?></td>
                                <td class="py-3 px-4 text-slate-500"><?= sanitize($u['email']) ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                        <?= sanitize($u['role_name'] ?? 'Unknown Role') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <?php if ($u['username'] !== 'superadmin' && hasPermission('roles.manage')): ?>
                                    <button onclick="manageUserAccess(<?= $u['id'] ?>, '<?= escapeHtml($u['name']) ?>')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200">
                                        Manage Access
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-slate-400">No active candidates found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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

<!-- Modal: Manage User Access -->
<div id="modal-user-access" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-4xl shadow-2xl relative flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900" id="modal-user-title">Manage User Access</h3>
                <p class="text-xs text-slate-500">Enable or disable specific permissions for this individual user.</p>
            </div>
            <button onclick="closeModal('modal-user-access')" class="text-slate-400 hover:text-slate-700 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="form-user-access" method="POST" action="" class="flex flex-col flex-1 overflow-hidden" data-ajax="true" data-reload="true">
            <?= csrf_field() ?>
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($groupedPermissions as $module => $perms): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                        <h4 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                            <i class="fas fa-cube text-slate-300"></i> <?= sanitize(ucfirst($module)) ?>
                        </h4>
                        <div class="space-y-2">
                            <?php foreach ($perms as $p): ?>
                            <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-100 transition-colors">
                                <div class="pt-0.5">
                                    <input type="checkbox" name="permissions[]" value="<?= $p['id'] ?>" class="user-perm-cb w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-600">
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800"><?= sanitize($p['name']) ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 bg-white flex justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModal('modal-user-access')" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 text-sm transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm transition-colors shadow-md shadow-amber-600/20">Save Access Permissions</button>
            </div>
        </form>
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

async function manageUserAccess(userId, userName) {
    document.getElementById('modal-user-title').textContent = 'Manage Access: ' + userName;
    document.getElementById('form-user-access').action = window.APP_URL + '/users/' + userId + '/permissions';
    
    // Reset all checkboxes
    document.querySelectorAll('.user-perm-cb').forEach(cb => cb.checked = false);
    
    try {
        const res = await fetch(window.APP_URL + '/users/' + userId + '/permissions');
        const data = await res.json();
        
        if (data.success && data.permission_ids) {
            data.permission_ids.forEach(id => {
                const cb = document.querySelector(`.user-perm-cb[value="${id}"]`);
                if (cb) cb.checked = true;
            });
        }
        openModal('modal-user-access');
    } catch (e) {
        console.error(e);
        alert('Failed to load user permissions.');
    }
}
</script>
