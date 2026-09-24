<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Forensic System Audit Trail</h1>
            <p class="text-xs text-slate-500">Immutable ledger recording every login, stock alteration, billing transaction, and user action</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Timestamp</th>
                        <th class="py-3.5 px-4">User</th>
                        <th class="py-3.5 px-4">Module</th>
                        <th class="py-3.5 px-4">Action</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4 font-mono">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">No audit records found.</td></tr>
                    <?php else: foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]"><?= $log['created_at'] ?></td>
                            <td class="py-3.5 px-4 font-bold text-slate-900"><?= sanitize($log['user_name'] ?? 'System') ?></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] uppercase font-bold border border-slate-200">
                                    <?= $log['module'] ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-amber-800 text-[11px]"><?= $log['action'] ?></td>
                            <td class="py-3.5 px-4 text-slate-800 font-medium max-w-sm truncate"><?= sanitize($log['description']) ?></td>
                            <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]"><?= $log['ip_address'] ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
