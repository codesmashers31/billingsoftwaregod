<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Sourcing Purchases & Artisan Orders</h1>
            <p class="text-xs text-slate-500">Record intake orders from sthapathis, auto-restock physical showroom inventory, and manage payables</p>
        </div>
        <?php if (hasPermission('purchases.create')): ?>
        <a href="<?= url('purchases/create') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-cart-plus"></i> New Sourcing Purchase
        </a>
        <?php endif; ?>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Purchase No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Sthapathi / Foundry</th>
                        <th class="py-3.5 px-4">Grand Total</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($purchases)): ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">No purchase records registered.</td></tr>
                    <?php else: foreach ($purchases as $p): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= $p['purchase_no'] ?></td>
                            <td class="py-3.5 px-4 text-slate-700"><?= formatDate($p['purchase_date']) ?></td>
                            <td class="py-3.5 px-4 font-bold text-slate-900"><?= sanitize($p['supplier_name']) ?></td>
                            <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm"><?= formatCurrency($p['grand_total']) ?></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $p['status'] === 'received' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <button onclick="viewPurchaseDetails(<?= $p['id'] ?>)" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
