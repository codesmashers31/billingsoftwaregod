<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Sales Analytics & Tax Report</h1>
            <p class="text-xs text-slate-500">Gross revenue, GST tax collections (CGST, SGST, IGST), and sales performance breakdown</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('reports/sales') ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">From Date</label>
                <input type="date" name="date_from" value="<?= sanitize($dateFrom ?? date('Y-m-01')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">To Date</label>
                <input type="date" name="date_to" value="<?= sanitize($dateTo ?? date('Y-m-d')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 gold-btn font-bold py-2 rounded-xl text-xs shadow-sm">Generate Report</button>
                <a href="<?= url('reports/sales?date_from=' . ($dateFrom ?? '') . '&date_to=' . ($dateTo ?? '') . '&export=csv') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl text-xs border border-slate-200">
                    <i class="fas fa-file-excel text-emerald-600"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Gross Sales Revenue</span>
            <h3 class="text-2xl font-black text-slate-900 font-mono"><?= formatCurrency($summary['total_sales'] ?? 0) ?></h3>
            <span class="text-[10px] text-slate-400 font-mono mt-1 block"><?= $summary['total_invoices'] ?? 0 ?> Invoices</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total GST Tax Collected</span>
            <h3 class="text-2xl font-black text-blue-600 font-mono"><?= formatCurrency($summary['total_tax'] ?? 0) ?></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">CGST + SGST + IGST</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Total Discounts Given</span>
            <h3 class="text-2xl font-black text-rose-600 font-mono"><?= formatCurrency($summary['total_discount'] ?? 0) ?></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Item & Bill Level</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Average Bill Value</span>
            <h3 class="text-2xl font-black text-amber-800 font-mono">
                <?= formatCurrency(($summary['total_invoices'] ?? 0) > 0 ? (($summary['total_sales'] ?? 0) / $summary['total_invoices']) : 0) ?>
            </h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Per Transaction</span>
        </div>
    </div>
</div>
