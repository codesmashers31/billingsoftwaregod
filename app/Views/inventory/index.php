<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Inventory & Stock Control Register</h1>
            <p class="text-xs text-slate-500">Strict physical stock auditing, damage records, showroom intake, and movement trails</p>
        </div>
        <?php if (hasPermission('inventory.adjust')): ?>
        <button onclick="openModal('modal-adjust-stock')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-sliders-h"></i> Stock Adjustment / Damage Entry
        </button>
        <?php endif; ?>
    </div>

    <!-- Stock Levels & History Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Stock Levels Table -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Current Physical Stock Levels</h3>
                <form method="GET" action="<?= url('inventory') ?>" class="flex items-center gap-2">
                    <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" placeholder="Search statue..." 
                           class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 outline-none focus:border-amber-500">
                    <button type="submit" class="p-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Statue Item</th>
                            <th class="py-3 px-3">SKU</th>
                            <th class="py-3 px-3">Stock Level</th>
                            <th class="py-3 px-3">Location</th>
                            <th class="py-3 px-3 text-right">Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($stockList as $p): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-900"><?= sanitize($p['name']) ?></div>
                                    <div class="text-[10px] text-amber-700 font-semibold"><?= sanitize($p['material'] ?? '') ?></div>
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-500"><?= $p['sku'] ?></td>
                                <td class="py-3 px-3 font-mono">
                                    <?php if ($p['current_stock'] <= $p['min_stock']): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px] border border-rose-200">
                                            <?= $p['current_stock'] ?> units (Low)
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200">
                                            <?= $p['current_stock'] ?> units
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-slate-500 text-[11px]"><?= sanitize($p['stock_location'] ?? 'Showroom') ?></td>
                                <td class="py-3 px-3 text-right">
                                    <button onclick="viewStockHistory(<?= $p['id'] ?>, '<?= escapeHtml($p['name']) ?>')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px]">
                                        Audit Trail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Inventory Transactions -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <i class="fas fa-history text-amber-600"></i> Recent Stock Movements
                </h3>
                <p class="text-xs text-slate-400 mb-4">Every physical alteration recorded with user stamp</p>

                <div class="space-y-2.5 max-h-[65vh] overflow-y-auto pr-1">
                    <?php if (empty($recentTransactions)): ?>
                        <div class="p-6 text-center text-xs text-slate-400">No recent stock movements.</div>
                    <?php else: foreach ($recentTransactions as $tx): ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold uppercase text-amber-800 text-[10px] px-2 py-0.5 rounded bg-amber-50 border border-amber-200">
                                    <?= str_replace('_', ' ', $tx['transaction_type']) ?>
                                </span>
                                <span class="font-mono font-bold <?= $tx['quantity'] > 0 ? 'text-emerald-700' : 'text-rose-700' ?>">
                                    <?= $tx['quantity'] > 0 ? '+' : '' ?><?= $tx['quantity'] ?> units
                                </span>
                            </div>
                            <h5 class="text-xs font-bold text-slate-900 mt-1.5 line-clamp-1"><?= sanitize($tx['product_name']) ?></h5>
                            <div class="text-[10px] text-slate-500 mt-1 flex items-center justify-between font-medium">
                                <span>Ref: <?= $tx['reference_no'] ?: 'Manual' ?></span>
                                <span>By: <?= sanitize($tx['user_name'] ?? 'System') ?></span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Stock Adjustment -->
<div id="modal-adjust-stock" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Adjust Physical Stock</h3>
            <button onclick="closeModal('modal-adjust-stock')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('inventory/adjust') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Select God Statue / Item <span class="text-rose-500">*</span></label>
                <select name="product_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="">Choose item...</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= sanitize($p['name']) ?> (Current: <?= $p['current_stock'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Transaction Type</label>
                <select name="transaction_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="adjustment">Physical Audit Adjustment (± Count)</option>
                    <option value="damage">Damaged Idol / Casting Flaw Write-off (-)</option>
                    <option value="stock_in">Manual Stock In (+)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Quantity (+ for add, - for remove)</label>
                <input type="number" name="quantity" required placeholder="e.g. 5 or -2" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono font-bold focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Audit Reason / Notes</label>
                <textarea name="notes" rows="2" placeholder="e.g. Broken prabhavali arch during transit..." 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-adjust-stock')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View History -->
<div id="modal-stock-history" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-2xl shadow-2xl relative max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900" id="modal-hist-title">Stock History</h3>
            <button onclick="closeModal('modal-stock-history')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-hist-body" class="space-y-2 text-xs"></div>
    </div>
</div>

<script>
async function viewStockHistory(id, name) {
    document.getElementById('modal-hist-title').textContent = 'Audit History: ' + name;
    try {
        const res = await fetch(window.APP_URL + '/inventory/history/' + id);
        const data = await res.json();
        let html = '';
        (data.history || []).forEach(h => {
            html += `
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <div>
                        <div class="font-bold uppercase text-amber-800 text-xs">${h.transaction_type}</div>
                        <div class="text-slate-500 text-[10px] font-mono">Ref: ${h.reference_no || 'N/A'} • ${h.created_at}</div>
                        ${h.notes ? `<div class="text-[11px] text-slate-700 mt-1 italic">"${h.notes}"</div>` : ''}
                    </div>
                    <div class="text-right font-mono">
                        <span class="text-sm font-black ${h.quantity > 0 ? 'text-emerald-700' : 'text-rose-700'}">
                            ${h.quantity > 0 ? '+' : ''}${h.quantity}
                        </span>
                        <div class="text-[10px] text-slate-500 font-semibold">Balance: ${h.new_stock}</div>
                    </div>
                </div>
            `;
        });
        document.getElementById('modal-hist-body').innerHTML = html || '<div class="p-6 text-center text-slate-400">No stock history recorded.</div>';
        openModal('modal-stock-history');
    } catch(e) { console.error(e); }
}
</script>
