<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Welcome Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-slate-900">Welcome, <?= sanitize(auth('name', 'Admin')) ?></h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    <?= sanitize(auth('role_name', 'Executive')) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Live business performance, inventory health, and billing summary for <?= date('l, d F Y') ?></p>
        </div>
        
        <?php if (hasPermission('billing.pos')): ?>
        <a href="<?= url('billing') ?>" class="gold-btn text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md">
            <i class="fas fa-cash-register"></i> Open POS Terminal (F2)
        </a>
        <?php endif; ?>
    </div>

    <!-- 4 Primary KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today's Sales -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Sales</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xs">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono"><?= formatCurrency($todaySales) ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span><?= $todayInvoicesCount ?> invoices today</span>
                <span class="text-amber-700 font-bold font-mono">Live Billed</span>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">This Month Sales</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center text-xs">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono"><?= formatCurrency($monthSales) ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span><?= $monthInvoicesCount ?> orders this month</span>
                <span class="text-blue-600 font-bold"><?= date('F') ?></span>
            </div>
        </div>

        <!-- Inventory Valuation -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stock Valuation</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xs">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 font-mono"><?= formatCurrency($stockValuation) ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span><?= $totalProducts ?> idol items</span>
                <span class="text-emerald-600 font-bold font-mono"><?= $totalStockPieces ?> in stock</span>
            </div>
        </div>

        <!-- Customer Receivables -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-purple-400 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Customer Due</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center text-xs">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-rose-600 font-mono"><?= formatCurrency($customerOutstanding) ?></h3>
            <div class="flex items-center justify-between mt-2 text-[11px] text-slate-500 font-medium">
                <span><?= $totalCustomers ?> customers</span>
                <span class="text-purple-600 font-bold">Ledger Dues</span>
            </div>
        </div>
    </div>

    <!-- Secondary KPI Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Cash in Hand -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Showroom Cash Drawer</span>
                <span class="text-lg font-black font-mono text-slate-900"><?= formatCurrency($cashInHand) ?></span>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-200">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <!-- Bank Balance -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Bank Accounts (HDFC + SBI)</span>
                <span class="text-lg font-black font-mono text-slate-900"><?= formatCurrency($bankBalance) ?></span>
            </div>
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-200">
                <i class="fas fa-university"></i>
            </div>
        </div>

        <!-- Supplier Payables -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Artisan Sourcing Payables</span>
                <span class="text-lg font-black font-mono text-slate-900"><?= formatCurrency($supplierPayables) ?></span>
            </div>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-200">
                <i class="fas fa-hammer"></i>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Low Stock Alerts Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">7-Day Sales Revenue Trajectory</h3>
                    <p class="text-xs text-slate-500">Daily gross turnover from sales invoices</p>
                </div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-xl">
                    Gross: <?= formatCurrency($totalRevenue) ?>
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
        <!-- Recent Invoices -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">Recent Sales Invoices</h3>
                <a href="<?= url('billing/invoices') ?>" class="text-xs font-bold text-amber-700 hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Invoice No</th>
                            <th class="py-2.5 px-3">Customer</th>
                            <th class="py-2.5 px-3 font-mono">Amount</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recentInvoices)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-slate-400">No invoices recorded yet.</td></tr>
                        <?php else: foreach ($recentInvoices as $inv): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900"><?= $inv['invoice_no'] ?></td>
                                <td class="py-2.5 px-3 font-medium text-slate-800"><?= sanitize($inv['customer_name']) ?></td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900"><?= formatCurrency($inv['grand_total']) ?></td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $inv['payment_status'] === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                                        <?= ucfirst($inv['payment_status']) ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="<?= url('billing/invoice/' . $inv['id']) ?>" class="p-1 text-slate-400 hover:text-amber-600"><i class="fas fa-eye"></i></a>
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
                <a href="<?= url('audit') ?>" class="text-xs font-bold text-amber-700 hover:underline">Full Log</a>
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

<!-- Chart Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const labels = <?= json_encode($salesTrend['labels'] ?? []) ?>;
    const data = <?= json_encode($salesTrend['data'] ?? []) ?>;

    new Chart(ctx, {
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
});
</script>
