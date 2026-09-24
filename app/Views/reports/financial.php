<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Profit & Loss Financial Statement</h1>
            <p class="text-xs text-slate-500">Real-time financial statement computing Gross Margin, COGS, Operating Expenses, and Net Profit</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('reports/financial') ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">From Date</label>
                <input type="date" name="date_from" value="<?= sanitize($dateFrom ?? date('Y-m-01')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">To Date</label>
                <input type="date" name="date_to" value="<?= sanitize($dateTo ?? date('Y-m-d')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full gold-btn font-bold py-2 rounded-xl text-xs shadow-sm">Generate P&L Statement</button>
            </div>
        </form>
    </div>

    <!-- Income Statement Sheet -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6 font-mono">
        <div class="text-center font-sans border-b border-slate-100 pb-4">
            <h2 class="text-lg font-black text-slate-900"><?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></h2>
            <p class="text-xs text-slate-500">Statement of Profit & Loss for <?= formatDate($dateFrom ?? date('Y-m-01')) ?> to <?= formatDate($dateTo ?? date('Y-m-d')) ?></p>
        </div>

        <div class="space-y-3 text-xs">
            <!-- 1. Revenue -->
            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                <span class="font-sans font-bold text-slate-900">1. Gross Revenue from Idol Sales</span>
                <span class="text-base font-black text-slate-900"><?= formatCurrency($totalSales ?? 0) ?></span>
            </div>

            <?php if (($totalRefunds ?? 0) > 0): ?>
            <div class="flex justify-between items-center py-1 text-slate-600 pl-4">
                <span class="font-sans">Less: Sales Returns & Exchanges</span>
                <span class="text-rose-600 font-bold">-<?= formatCurrency($totalRefunds ?? 0) ?></span>
            </div>
            <div class="flex justify-between items-center py-1 font-bold text-slate-800 pl-4 border-b border-slate-100">
                <span class="font-sans">Net Sales Revenue</span>
                <span><?= formatCurrency($netSales ?? 0) ?></span>
            </div>
            <?php endif; ?>

            <!-- 2. Cost of Goods Sold -->
            <div class="flex justify-between items-center py-2 text-slate-600 pl-4">
                <span class="font-sans">Less: Cost of Goods Sold (Artisan Foundry Base Cost)</span>
                <span class="text-rose-600 font-bold">-<?= formatCurrency($totalCogs ?? 0) ?></span>
            </div>

            <!-- Gross Profit -->
            <div class="flex justify-between items-center py-3 bg-slate-50 px-4 rounded-xl font-bold text-slate-900 border border-slate-200">
                <span class="font-sans uppercase">Gross Profit</span>
                <span class="text-base font-black text-emerald-700"><?= formatCurrency($grossProfit ?? 0) ?></span>
            </div>

            <!-- 3. Operating Expenses -->
            <div class="pt-3">
                <span class="font-sans font-bold text-slate-900 block mb-2">2. Operating Expenses</span>
                <?php foreach (($expCategories ?? []) as $ec): ?>
                    <div class="flex justify-between items-center py-1 text-slate-500 pl-4 text-[11px]">
                        <span class="font-sans"><?= sanitize($ec['name']) ?></span>
                        <span><?= formatCurrency($ec['total']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="flex justify-between items-center py-2 text-rose-600 font-bold border-t border-slate-100 pl-4 mt-2">
                    <span class="font-sans">Total Operating Expenses</span>
                    <span>-<?= formatCurrency($totalExpenses ?? 0) ?></span>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="flex justify-between items-center py-4 bg-amber-50 px-5 rounded-2xl border border-amber-200 font-bold mt-4">
                <div>
                    <span class="font-sans text-sm font-black uppercase text-amber-900 block">Estimated Net Profit</span>
                    <span class="text-[10px] text-amber-700 font-sans">Turnover Net Yield</span>
                </div>
                <span class="text-2xl font-black text-emerald-700"><?= formatCurrency($netProfit ?? 0) ?></span>
            </div>
        </div>
    </div>
</div>
