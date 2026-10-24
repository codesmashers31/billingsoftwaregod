<div class="space-y-6 max-w-7xl mx-auto">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Top Welcome Banner -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-slate-900">Welcome, <?= sanitize(auth('name', 'Admin')) ?></h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    <?= sanitize(auth('role_name', 'Executive')) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Live business performance, inventory investment, margins, and sales metrics</p>
        </div>
        
        <!-- Digital Clock Card (Obsidian & Gold Premium Design) -->
        <div class="flex items-center bg-slate-900 rounded-xl border border-amber-500/30 p-1.5 pr-5 shadow-[0_4px_12px_rgba(217,119,6,0.15)] relative group cursor-pointer hover:border-amber-400 transition-all hover:shadow-[0_4px_16px_rgba(217,119,6,0.25)] overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-transparent pointer-events-none"></div>
            
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-900 shadow-inner mr-3 relative z-10">
                <i class="fas fa-hourglass-half text-lg"></i>
            </div>
            
            <div class="flex flex-col relative z-10">
                <div class="flex items-baseline gap-1.5">
                    <span id="pos-clock-time" class="text-xl font-bold tracking-widest text-amber-50" style="font-family: 'Share Tech Mono', monospace;">00:00:00</span>
                    <span id="pos-clock-ampm" class="text-[10px] font-black text-amber-400">PM</span>
                </div>
                <span id="pos-clock-date" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-0.5">JAN 01, 2025</span>
            </div>
            
            <input type="datetime-local" id="time-picker-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onclick="try { this.showPicker(); } catch(e) {}" title="Click to change date/time" />
            <button id="reset-time-btn" class="hidden absolute top-1.5 right-1.5 bg-rose-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[9px] shadow-lg hover:bg-rose-600 z-30 transition-transform hover:scale-110" title="Reset to real time" onclick="event.stopPropagation();">
                <i class="fas fa-undo"></i>
            </button>
        </div>

        <div class="flex-1 flex justify-end">
            <?php if (hasPermission('billing.pos')): ?>
            <a href="<?= url('billing') ?>" class="gold-btn text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5">
                <i class="fas fa-cash-register"></i> Open POS Terminal (F2)
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dynamic Date Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                <i class="fas fa-calendar-alt text-amber-600"></i> Date Filter:
            </span>
            <!-- Quick Filter Buttons -->
            <button type="button" onclick="changePeriod('today')" 
                    id="btn-filter-today"
                    class="filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer <?= $period === 'today' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                <i class="fas fa-sun mr-1 text-[10px]"></i> Today
            </button>
            <button type="button" onclick="changePeriod('week')" 
                    id="btn-filter-week"
                    class="filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer <?= $period === 'week' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                <i class="fas fa-calendar-week mr-1 text-[10px]"></i> Weekly (7 Days)
            </button>
            <button type="button" onclick="changePeriod('month')" 
                    id="btn-filter-month"
                    class="filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer <?= $period === 'month' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                <i class="fas fa-calendar mr-1 text-[10px]"></i> Monthly
            </button>
        </div>

        <!-- Custom Date Range Picker -->
        <div class="flex items-center gap-2 flex-wrap justify-end">
            <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-300 rounded-xl px-2.5 py-1 text-xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">From:</span>
                <input type="date" id="dash-from-date" value="<?= $startDate ?>" 
                       class="bg-transparent text-xs text-slate-800 font-mono font-bold outline-none cursor-pointer">
            </div>
            <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-300 rounded-xl px-2.5 py-1 text-xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">To:</span>
                <input type="date" id="dash-to-date" value="<?= $endDate ?>" 
                       class="bg-transparent text-xs text-slate-800 font-mono font-bold outline-none cursor-pointer">
            </div>
            <button type="button" onclick="applyCustomDateRange()" 
                    class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                <i class="fas fa-filter text-[10px]"></i> Filter
            </button>
        </div>
    </div>

    <!-- Active Filter Status Badge -->
    <div class="flex items-center justify-between text-xs text-slate-500 px-1">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Active View: <strong id="dash-active-period-label" class="text-slate-900 font-bold"><?= $periodTitle ?></strong> (<span id="dash-active-period-sub" class="text-amber-800 font-mono font-semibold"><?= $periodSubtitle ?></span>)</span>
        </div>
        <div id="dash-loading-indicator" class="hidden text-amber-600 font-bold items-center gap-1.5 text-xs">
            <i class="fas fa-spinner fa-spin"></i> Refreshing live metrics...
        </div>
    </div>

    <!-- 4 Primary KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Period Sales (Default: Today's Sales) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500" id="card-period-title"><?= $periodTitle ?></span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xs">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono" id="card-period-sales"><?= $periodSalesFormatted ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span id="card-period-count"><?= $periodInvoicesCount ?> invoices</span>
                <span class="text-amber-700 font-bold font-mono" id="card-period-subtag">Live Billed</span>
            </div>
        </div>

        <!-- 2. This Month Sales -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">This Month Sales</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center text-xs">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono" id="card-month-sales"><?= $monthSalesFormatted ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span id="card-month-count"><?= $monthInvoicesCount ?> orders this month</span>
                <span class="text-blue-600 font-bold"><?= date('F Y') ?></span>
            </div>
        </div>

        <!-- 3. Overall Sales (Lifetime) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-indigo-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Overall Sales</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center text-xs">
                    <i class="fas fa-award"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono" id="card-overall-sales"><?= $overallSalesFormatted ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span id="card-overall-count"><?= $overallInvoicesCount ?> total sales bills</span>
                <span class="text-indigo-600 font-bold">Lifetime Turnover</span>
            </div>
        </div>

        <!-- 4. Stock Valuation (Retail Value) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stock Valuation (Retail)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xs">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono" id="card-stock-val"><?= $stockValuationFormatted ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span><?= $totalProducts ?> active items</span>
                <span class="text-emerald-600 font-bold font-mono"><?= $totalStockPieces ?> in stock</span>
            </div>
        </div>
    </div>

    <!-- Product Sourcing Investment & Profit Margin Cards (Requested by User) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card A: Total Sourcing Investment (Buy Cost / Original Price) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-hand-holding-usd text-emerald-600"></i> Product Sourcing Investment
                </span>
                <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded border border-emerald-200">
                    Original Buy Cost
                </span>
            </div>
            <h3 class="text-2xl font-black text-emerald-700 font-mono mt-1" id="card-investment"><?= $totalProductInvestmentFormatted ?></h3>
            <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                Total purchase cost invested to buy and source all stocked products from artisans and suppliers.
            </p>
        </div>

        <!-- Card B: Expected Inventory Margin (Profit Amount) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-chart-pie text-blue-600"></i> Expected Inventory Profit
                </span>
                <span class="text-[10px] bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded border border-blue-200 font-mono" id="card-margin-badge">
                    <?= $stockMarginPercent ?>% Margin
                </span>
            </div>
            <h3 class="text-2xl font-black text-blue-700 font-mono mt-1" id="card-margin"><?= $stockExpectedMarginFormatted ?></h3>
            <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                Total retail selling value minus original buy cost on current store inventory.
            </p>
        </div>

        <!-- Card C: Period Realized Sales Profit Margin -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-trophy text-amber-600"></i> Period Sales Gross Profit
                </span>
                <span class="text-[10px] bg-amber-50 text-amber-800 font-bold px-2 py-0.5 rounded border border-amber-200 font-mono" id="card-period-margin-pct">
                    <?= $periodMarginPercent ?>% Margin
                </span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono mt-1" id="card-period-margin"><?= formatCurrency($periodGrossMargin) ?></h3>
            <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                Gross profit realized on sales invoices during this selected period (Revenue minus product buy costs).
            </p>
        </div>
    </div>

    <!-- Analytics Charts & Low Stock Alerts Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900" id="chart-title">Sales Revenue Trajectory</h3>
                    <p class="text-xs text-slate-500" id="chart-sub">Daily turnover from sales invoices in selected period</p>
                </div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-xl" id="chart-period-total">
                    Selected Total: <?= $periodSalesFormatted ?>
                </span>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Low Stock Alerts (1 Col) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-600"></i> Low Stock Alerts
                    </h3>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200">
                        <?= count($lowStockProducts) ?> Alert<?= count($lowStockProducts) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <div class="space-y-2.5 overflow-y-auto max-h-56 pr-1">
                    <?php if (empty($lowStockProducts)): ?>
                        <div class="p-6 text-center text-xs text-slate-400">
                            <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2 block"></i>
                            All idol items have healthy inventory stock.
                        </div>
                    <?php else: foreach ($lowStockProducts as $lp): ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                            <div class="overflow-hidden pr-2">
                                <div class="font-bold text-slate-900 truncate"><?= sanitize($lp['name']) ?></div>
                                <div class="text-[10px] text-slate-500 font-mono"><?= $lp['sku'] ?> • <?= sanitize($lp['material']) ?></div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold font-mono text-[10px]">
                                    <?= $lp['current_stock'] ?> / <?= $lp['min_stock'] ?> left
                                </span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <?php if (hasPermission('purchases.create')): ?>
            <div class="pt-4 mt-2 border-t border-slate-100">
                <a href="<?= url('purchases/create') ?>" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-cart-plus text-amber-600"></i> Create Sourcing Reorder
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Sales Invoices & Recent Forensic Audit Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Invoices for the Selected Period -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900" id="invoices-section-title">Sales Invoices (Selected Period)</h3>
                <a href="<?= url('billing/invoices') ?>" class="text-xs font-bold text-amber-700 hover:underline">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Invoice No</th>
                            <th class="py-2.5 px-3">Customer</th>
                            <th class="py-2.5 px-3 font-mono">Amount</th>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="recent-invoices-tbody">
                        <?php if (empty($recentInvoices)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-slate-400">No invoices recorded in this period.</td></tr>
                        <?php else: foreach ($recentInvoices as $inv): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900"><?= $inv['invoice_no'] ?></td>
                                <td class="py-2.5 px-3 font-medium text-slate-800"><?= sanitize($inv['customer_name']) ?></td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900"><?= formatCurrency($inv['grand_total']) ?></td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-500"><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="<?= url('billing/invoice/' . $inv['id']) ?>" class="p-1 text-slate-400 hover:text-amber-600" title="View Bill"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Trail -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">System Forensic Audit Activity</h3>
                <a href="<?= url('audit') ?>" class="text-xs font-bold text-amber-700 hover:underline">Full Log &rarr;</a>
            </div>

            <div class="space-y-2.5 overflow-y-auto max-h-72 pr-1">
                <?php if (empty($recentLogs)): ?>
                    <div class="p-6 text-center text-xs text-slate-400">No activity recorded.</div>
                <?php else: foreach ($recentLogs as $log): ?>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div class="overflow-hidden pr-2">
                            <span class="text-[10px] uppercase font-bold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200"><?= $log['module'] ?>: <?= $log['action'] ?></span>
                            <div class="text-slate-800 font-medium text-[11px] mt-1 truncate"><?= sanitize($log['description']) ?></div>
                        </div>
                        <div class="text-right shrink-0 text-[10px] text-slate-400 font-mono">
                            <div><?= $log['user_name'] ?? 'System' ?></div>
                            <div><?= date('H:i', strtotime($log['created_at'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Dynamic Filter Controller JS -->
<script>
let salesChartInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    initSalesChart(
        <?= json_encode($salesTrend['labels'] ?? []) ?>,
        <?= json_encode($salesTrend['data'] ?? []) ?>
    );
});

function initSalesChart(labels, data) {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    if (salesChartInstance) {
        salesChartInstance.destroy();
    }

    salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (₹)',
                data: data,
                borderColor: '#d97706',
                backgroundColor: 'rgba(217, 119, 6, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#d97706',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₹ ' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b', font: { size: 11, family: 'Inter' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { 
                        color: '#64748b', 
                        font: { size: 11, family: 'Inter' },
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                }
            }
        }
    });
}

/**
 * Switch Filter Period (today, week, month)
 */
async function changePeriod(period) {
    // Update button states
    ['today', 'week', 'month'].forEach(p => {
        const btn = document.getElementById(`btn-filter-${p}`);
        if (btn) {
            if (p === period) {
                btn.className = 'filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer bg-amber-600 text-white shadow-sm';
            } else {
                btn.className = 'filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700';
            }
        }
    });

    await fetchDashboardMetrics({ period: period });
}

/**
 * Apply Custom Date Range
 */
async function applyCustomDateRange() {
    const from = document.getElementById('dash-from-date')?.value;
    const to = document.getElementById('dash-to-date')?.value;

    if (!from || !to) {
        alert('Please select both From and To dates.');
        return;
    }

    // Reset quick pill buttons styling
    ['today', 'week', 'month'].forEach(p => {
        const btn = document.getElementById(`btn-filter-${p}`);
        if (btn) btn.className = 'filter-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700';
    });

    await fetchDashboardMetrics({
        period: 'custom',
        from_date: from,
        to_date: to
    });
}

/**
 * Fetch and update dashboard metrics dynamically via AJAX
 */
async function fetchDashboardMetrics(params) {
    const loadingEl = document.getElementById('dash-loading-indicator');
    if (loadingEl) loadingEl.classList.remove('hidden'), loadingEl.classList.add('flex');

    const searchParams = new URLSearchParams(params);
    searchParams.set('format', 'json');

    try {
        const res = await fetch(`<?= url('dashboard') ?>?${searchParams.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await res.json();

        if (result.success && result.data) {
            const d = result.data;

            // 1. Update Labels
            const periodLabelEl = document.getElementById('dash-active-period-label');
            const periodSubEl = document.getElementById('dash-active-period-sub');
            if (periodLabelEl) periodLabelEl.textContent = d.periodTitle;
            if (periodSubEl) periodSubEl.textContent = d.periodSubtitle;

            // 2. Update Primary Cards
            const pTitle = document.getElementById('card-period-title');
            const pSales = document.getElementById('card-period-sales');
            const pCount = document.getElementById('card-period-count');
            if (pTitle) pTitle.textContent = d.periodTitle;
            if (pSales) pSales.textContent = d.periodSalesFormatted;
            if (pCount) pCount.textContent = `${d.periodInvoicesCount} invoices`;

            const mSales = document.getElementById('card-month-sales');
            const mCount = document.getElementById('card-month-count');
            if (mSales) mSales.textContent = d.monthSalesFormatted;
            if (mCount) mCount.textContent = `${d.monthInvoicesCount} orders this month`;

            const oSales = document.getElementById('card-overall-sales');
            const oCount = document.getElementById('card-overall-count');
            if (oSales) oSales.textContent = d.overallSalesFormatted;
            if (oCount) oCount.textContent = `${d.overallInvoicesCount} total sales bills`;

            const sVal = document.getElementById('card-stock-val');
            if (sVal) sVal.textContent = d.stockValuationFormatted;

            // 3. Update Investment & Margin Cards
            const cInv = document.getElementById('card-investment');
            if (cInv) cInv.textContent = d.totalProductInvestmentFormatted;

            const cMarg = document.getElementById('card-margin');
            const cMargBadge = document.getElementById('card-margin-badge');
            if (cMarg) cMarg.textContent = d.stockExpectedMarginFormatted;
            if (cMargBadge) cMargBadge.textContent = `${d.stockMarginPercent}% Margin`;

            const cPeriodMarg = document.getElementById('card-period-margin');
            const cPeriodMargPct = document.getElementById('card-period-margin-pct');
            if (cPeriodMarg) cPeriodMarg.textContent = '₹' + parseFloat(d.periodGrossMargin).toLocaleString('en-IN', {minimumFractionDigits: 2});
            if (cPeriodMargPct) cPeriodMargPct.textContent = `${d.periodMarginPercent}% Margin`;

            // 4. Update Chart
            const chartTotalEl = document.getElementById('chart-period-total');
            if (chartTotalEl) chartTotalEl.textContent = `Selected Total: ${d.periodSalesFormatted}`;
            if (d.salesTrend) {
                initSalesChart(d.salesTrend.labels, d.salesTrend.data);
            }

            // 5. Update Recent Invoices Table
            const tbody = document.getElementById('recent-invoices-tbody');
            if (tbody && d.recentInvoices) {
                if (d.recentInvoices.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-slate-400">No invoices recorded in this period.</td></tr>`;
                } else {
                    let rowsHtml = '';
                    d.recentInvoices.forEach(inv => {
                        const dateFormatted = new Date(inv.invoice_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                        rowsHtml += `
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900">${inv.invoice_no}</td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">${escapeHtml(inv.customer_name)}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900">₹${parseFloat(inv.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-500">${dateFormatted}</td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="<?= url('billing/invoice/') ?>/${inv.id}" class="p-1 text-slate-400 hover:text-amber-600" title="View Bill"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = rowsHtml;
                }
            }

            // 6. Update URL in browser history without reload
            const cleanParams = new URLSearchParams(params);
            window.history.pushState({}, '', `<?= url('dashboard') ?>?${cleanParams.toString()}`);
        }
    } catch (e) {
        console.error('Failed to load dashboard metrics:', e);
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden'), loadingEl.classList.remove('flex');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>

<!-- Live Digital Clock Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    let timeOffset = parseInt(localStorage.getItem('dashboard_time_offset') || 0);

    const timePicker = document.getElementById('time-picker-input');
    const resetBtn = document.getElementById('reset-time-btn');

    if (timeOffset !== 0) {
        resetBtn.classList.remove('hidden');
    }

    resetBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        timeOffset = 0;
        localStorage.removeItem('dashboard_time_offset');
        resetBtn.classList.add('hidden');
        updateClock();
    });

    timePicker.addEventListener('change', (e) => {
        if (!e.target.value) return;
        const selectedTime = new Date(e.target.value).getTime();
        const realTime = Date.now();
        timeOffset = selectedTime - realTime;
        localStorage.setItem('dashboard_time_offset', timeOffset);
        resetBtn.classList.remove('hidden');
        updateClock();
    });

    function updateClock() {
        const now = new Date(Date.now() + timeOffset);
        
        // Sync picker value with current simulated time
        const tzOffset = now.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(now.getTime() - tzOffset)).toISOString().slice(0, 16);
        if (document.activeElement !== timePicker) {
            timePicker.value = localISOTime;
        }

        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        
        hours = hours % 12;
        hours = hours ? hours : 12;
        const formattedHours = String(hours).padStart(2, '0');
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const formattedDate = `${months[now.getMonth()]} ${String(now.getDate()).padStart(2, '0')}, ${now.getFullYear()}`;
        
        const timeEl = document.getElementById('pos-clock-time');
        const ampmEl = document.getElementById('pos-clock-ampm');
        const dateEl = document.getElementById('pos-clock-date');
        
        if (timeEl) timeEl.textContent = `${formattedHours}:${minutes}:${seconds}`;
        if (ampmEl) ampmEl.textContent = ampm;
        if (dateEl) dateEl.textContent = formattedDate;
    }
    
    updateClock();
    setInterval(updateClock, 1000);
});
</script>
