<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Inventory Valuation & Stock Analytics</h1>
            <p class="text-xs text-slate-500">Live physical stock appraisal at cost vs retail price, margin spread, and low stock warnings</p>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Stock Cost Value (COGS)</span>
            <h3 class="text-2xl font-black text-slate-900 font-mono"><?= formatCurrency($summary['total_cost_value'] ?? 0) ?></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Artisan procurement valuation</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Stock Retail Value</span>
            <h3 class="text-2xl font-black text-emerald-700 font-mono"><?= formatCurrency($summary['total_retail_value'] ?? 0) ?></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Showroom retail sales potential</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Physical Units</span>
            <h3 class="text-2xl font-black text-amber-800 font-mono"><?= number_format($summary['total_pieces'] ?? 0) ?> Idols</h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Across all showroom racks</span>
        </div>
    </div>

    <!-- Detailed Inventory Valuation Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">God Statue / Item</th>
                        <th class="py-3 px-4">Material</th>
                        <th class="py-3 px-4 text-center">Stock</th>
                        <th class="py-3 px-4 text-right">Cost Price (₹)</th>
                        <th class="py-3 px-4 text-right">Retail Price (₹)</th>
                        <th class="py-3 px-4 text-right">Total Cost Val (₹)</th>
                        <th class="py-3 px-4 text-right">Total Retail Val (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-sans font-bold text-slate-900"><?= sanitize($item['name']) ?></td>
                            <td class="py-3 px-4 font-sans text-amber-800 font-semibold"><?= sanitize($item['material']) ?></td>
                            <td class="py-3 px-4 text-center font-bold <?= $item['current_stock'] <= $item['min_stock'] ? 'text-rose-600' : 'text-slate-900' ?>">
                                <?= $item['current_stock'] ?>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-700"><?= formatCurrency($item['purchase_price']) ?></td>
                            <td class="py-3 px-4 text-right text-slate-900 font-bold"><?= formatCurrency($item['selling_price']) ?></td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900"><?= formatCurrency($item['current_stock'] * $item['purchase_price']) ?></td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700"><?= formatCurrency($item['current_stock'] * $item['selling_price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
