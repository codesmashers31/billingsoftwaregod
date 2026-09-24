<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Staff Accounts & User Management</h1>
            <p class="text-xs text-slate-500">Manage user access, role assignments (Cashier, Store Admin, Accountant), and account statuses</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= url('roles') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold shadow-sm">
                <i class="fas fa-user-shield text-amber-600 mr-1"></i> Manage Roles
            </a>
            <a href="<?= url('departments') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold shadow-sm">
                <i class="fas fa-building text-amber-600 mr-1"></i> Departments
            </a>
            <?php if (hasPermission('users.create')): ?>
            <button onclick="openModal('modal-add-user')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-user-plus"></i> Add Staff Member
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Staff Member</th>
                        <th class="py-3.5 px-4">Role & Department</th>
                        <th class="py-3.5 px-4">Username & Contact</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Last Login</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900"><?= sanitize($u['name']) ?></div>
                                        <div class="text-[10px] text-slate-400"><?= sanitize($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <?= sanitize($u['role_name']) ?>
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= sanitize($u['department_name'] ?? 'General') ?></div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-700">
                                <div class="font-bold text-slate-900">@<?= $u['username'] ?></div>
                                <div class="text-[10px] text-slate-400"><?= $u['phone'] ?? 'Staff' ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $u['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                <?= $u['last_login_at'] ? formatDate($u['last_login_at']) : 'Never' ?>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <?php if (hasPermission('users.edit')): ?>
                                <button onclick="openEditUser(<?= htmlspecialchars(json_encode($u)) ?>)" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-800 text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add User -->
<div id="modal-add-user" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Add Staff Account</h3>
            <button onclick="closeModal('modal-add-user')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('users/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Ramesh Sthapathi" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Username <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="ramesh" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Staff Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required placeholder="ramesh@godstatueerp.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Role <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= sanitize($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Department</label>
                    <select name="department_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="">None</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= sanitize($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-user')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Create Account</button>
            </div>
        </form>
    </div>
</div>
