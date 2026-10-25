<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\AuditLog;

class DashboardController extends Controller {
    public function index(Request $request): void {
        Auth::requireAuth();

        $user = Auth::user();
        if ($user && ($user['username'] === 'priya' || strpos($user['role_slug'] ?? '', 'cashier') !== false)) {
            Response::redirect('/pos');
        }

        $db = Database::getInstance();

        // 1. Determine Selected Filter Period (Default: today)
        $period = (string)$request->input('period', 'today');
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        if ($period === 'week') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = $today;
            $periodTitle = "This Week's Sales";
            $periodSubtitle = date('d M', strtotime($startDate)) . " - " . date('d M Y', strtotime($endDate));
        } elseif ($period === 'month') {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $periodTitle = "This Month's Sales";
            $periodSubtitle = date('F Y');
        } elseif ($period === 'custom') {
            $startDate = (string)$request->input('from_date', $today);
            $endDate = (string)$request->input('to_date', $startDate);
            if ($startDate > $endDate) {
                $tmp = $startDate; $startDate = $endDate; $endDate = $tmp;
            }
            $periodTitle = ($startDate === $endDate) ? "Sales for " . date('d M Y', strtotime($startDate)) : "Custom Period Sales";
            $periodSubtitle = date('d M Y', strtotime($startDate)) . ($startDate !== $endDate ? ' - ' . date('d M Y', strtotime($endDate)) : '');
        } else {
            // Default: Today
            $period = 'today';
            $startDate = $today;
            $endDate = $today;
            $periodTitle = "Today's Sales";
            $periodSubtitle = date('l, d M Y');
        }

        // 2. Selected Period Sales
        $resPeriodSales = $db->query(
            "SELECT COALESCE(SUM(grand_total), 0) as total, COUNT(*) as count 
             FROM `invoices` 
             WHERE invoice_date BETWEEN ? AND ? AND status != 'cancelled'",
            [$startDate, $endDate]
        );
        $periodSales = (float)($resPeriodSales[0]['total'] ?? 0);
        $periodInvoicesCount = (int)($resPeriodSales[0]['count'] ?? 0);

        // 3. This Month Sales
        $resMonthSales = $db->query(
            "SELECT COALESCE(SUM(grand_total), 0) as total, COUNT(*) as count 
             FROM `invoices` 
             WHERE DATE_FORMAT(invoice_date, '%Y-%m') = ? AND status != 'cancelled'",
            [$thisMonth]
        );
        $monthSales = (float)($resMonthSales[0]['total'] ?? 0);
        $monthInvoicesCount = (int)($resMonthSales[0]['count'] ?? 0);

        // 4. Overall Lifetime Sales ("Over all salaeres")
        $resOverallSales = $db->query(
            "SELECT COALESCE(SUM(grand_total), 0) as total, COUNT(*) as count 
             FROM `invoices` 
             WHERE status != 'cancelled'"
        );
        $overallSales = (float)($resOverallSales[0]['total'] ?? 0);
        $overallInvoicesCount = (int)($resOverallSales[0]['count'] ?? 0);

        // 5. Stock Valuation & Sourcing Investment ("how invert to buy the product from product originl price")
        $resStock = $db->query(
            "SELECT 
                COUNT(*) as total_prod, 
                COALESCE(SUM(current_stock), 0) as total_pieces,
                COALESCE(SUM(current_stock * selling_price), 0) as retail_valuation,
                COALESCE(SUM(current_stock * purchase_price), 0) as total_investment
             FROM `products` 
             WHERE status != 'inactive'"
        );
        $totalProducts = (int)($resStock[0]['total_prod'] ?? 0);
        $totalStockPieces = (int)($resStock[0]['total_pieces'] ?? 0);
        $stockValuation = (float)($resStock[0]['retail_valuation'] ?? 0);
        $totalProductInvestment = (float)($resStock[0]['total_investment'] ?? 0);

        // 6. Margin Calculation ("how i get for the marign with that that also i want")
        // Expected gross profit on current inventory
        $stockExpectedMargin = max(0, $stockValuation - $totalProductInvestment);
        $stockMarginPercent = $stockValuation > 0 ? round(($stockExpectedMargin / $stockValuation) * 100, 1) : 0;

        // Realized Sales Margin in the selected period (Revenue minus Purchase Cost of items sold)
        $resSalesMargin = $db->query(
            "SELECT 
                COALESCE(SUM(ii.total_amount), 0) as period_rev,
                COALESCE(SUM(ii.quantity * p.purchase_price), 0) as period_cogs
             FROM `invoice_items` ii
             JOIN `invoices` i ON ii.invoice_id = i.id
             LEFT JOIN `products` p ON ii.product_id = p.id
             WHERE i.status != 'cancelled' AND i.invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $periodRevenue = (float)($resSalesMargin[0]['period_rev'] ?? 0);
        $periodCogs = (float)($resSalesMargin[0]['period_cogs'] ?? 0);
        $periodGrossMargin = $periodRevenue - $periodCogs;
        $periodMarginPercent = $periodRevenue > 0 ? round(($periodGrossMargin / $periodRevenue) * 100, 1) : 0;

        // 6b. Online vs Offline Sales Breakdown (Period & Overall)
        $resPeriodOnline = $db->query(
            "SELECT 
                COALESCE(SUM(grand_total), 0) as online_sales,
                COUNT(*) as online_count
             FROM `invoices` 
             WHERE sales_mode = 'online' AND status != 'cancelled' AND invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $periodOnlineSales = (float)($resPeriodOnline[0]['online_sales'] ?? 0);
        $periodOnlineCount = (int)($resPeriodOnline[0]['online_count'] ?? 0);
        $periodOnlinePercent = $periodSales > 0 ? round(($periodOnlineSales / $periodSales) * 100, 1) : 0;

        $resOverallOnline = $db->query(
            "SELECT 
                COALESCE(SUM(grand_total), 0) as online_sales,
                COUNT(*) as online_count
             FROM `invoices` 
             WHERE sales_mode = 'online' AND status != 'cancelled'"
        );
        $overallOnlineSales = (float)($resOverallOnline[0]['online_sales'] ?? 0);
        $overallOnlineCount = (int)($resOverallOnline[0]['online_count'] ?? 0);

        $resPeriodOffline = $db->query(
            "SELECT 
                COALESCE(SUM(grand_total), 0) as offline_sales,
                COUNT(*) as offline_count
             FROM `invoices` 
             WHERE (sales_mode = 'offline' OR sales_mode IS NULL OR sales_mode = '') AND status != 'cancelled' AND invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $periodOfflineSales = (float)($resPeriodOffline[0]['offline_sales'] ?? 0);
        $periodOfflineCount = (int)($resPeriodOffline[0]['offline_count'] ?? 0);

        $resOverallOffline = $db->query(
            "SELECT 
                COALESCE(SUM(grand_total), 0) as offline_sales,
                COUNT(*) as offline_count
             FROM `invoices` 
             WHERE (sales_mode = 'offline' OR sales_mode IS NULL OR sales_mode = '') AND status != 'cancelled'"
        );
        $overallOfflineSales = (float)($resOverallOffline[0]['offline_sales'] ?? 0);
        $overallOfflineCount = (int)($resOverallOffline[0]['offline_count'] ?? 0);

        // 6c. Expected Inventory Profit Target & Actual Realized Profit from Sales
        $targetProfitAmount = (float)getSetting('inventory_profit_target_amount', 100000.00);
        $targetStartDate = (string)getSetting('inventory_profit_target_start', date('Y-m-01'));
        $targetEndDate = (string)getSetting('inventory_profit_target_end', date('Y-m-t'));

        $resTargetProfit = $db->query(
            "SELECT 
                COALESCE(SUM(ii.total_amount), 0) as target_period_sales,
                COALESCE(SUM(ii.quantity * COALESCE(p.piece_rate, p.purchase_price, 0)), 0) as target_period_cogs,
                COUNT(DISTINCT i.id) as target_bills_count
             FROM `invoice_items` ii
             JOIN `invoices` i ON ii.invoice_id = i.id
             LEFT JOIN `products` p ON ii.product_id = p.id
             WHERE i.status != 'cancelled' AND i.invoice_date BETWEEN ? AND ?",
            [$targetStartDate, $targetEndDate]
        );
        $targetPeriodSales = (float)($resTargetProfit[0]['target_period_sales'] ?? 0);
        $targetPeriodCogs = (float)($resTargetProfit[0]['target_period_cogs'] ?? 0);
        $targetAchievedProfit = max(0, $targetPeriodSales - $targetPeriodCogs);
        $targetBillsCount = (int)($resTargetProfit[0]['target_bills_count'] ?? 0);

        $targetPercent = $targetProfitAmount > 0 ? round(($targetAchievedProfit / $targetProfitAmount) * 100, 1) : 0;
        $targetRemaining = max(0, $targetProfitAmount - $targetAchievedProfit);
        $targetSurplus = max(0, $targetAchievedProfit - $targetProfitAmount);
        $isTargetAchieved = ($targetAchievedProfit >= $targetProfitAmount && $targetProfitAmount > 0);

        // 7. Dynamic Sales Trend Chart Data (Adapts to Selected Period)
        $salesTrend = ['labels' => [], 'data' => []];
        if ($period === 'today') {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $salesTrend['labels'][] = date('D, d M', strtotime($d));
                $r = $db->query("SELECT COALESCE(SUM(grand_total), 0) as t FROM `invoices` WHERE invoice_date = ? AND status != 'cancelled'", [$d]);
                $salesTrend['data'][] = (float)($r[0]['t'] ?? 0);
            }
        } elseif ($period === 'week') {
            for ($d = $startDate; $d <= $endDate; $d = date('Y-m-d', strtotime($d . ' +1 day'))) {
                $salesTrend['labels'][] = date('D, d M', strtotime($d));
                $r = $db->query("SELECT COALESCE(SUM(grand_total), 0) as t FROM `invoices` WHERE invoice_date = ? AND status != 'cancelled'", [$d]);
                $salesTrend['data'][] = (float)($r[0]['t'] ?? 0);
            }
        } elseif ($period === 'month') {
            for ($d = $startDate; $d <= $endDate; $d = date('Y-m-d', strtotime($d . ' +2 days'))) {
                $nextD = date('Y-m-d', min(strtotime($endDate), strtotime($d . ' +1 day')));
                $salesTrend['labels'][] = date('d M', strtotime($d));
                $r = $db->query("SELECT COALESCE(SUM(grand_total), 0) as t FROM `invoices` WHERE invoice_date BETWEEN ? AND ? AND status != 'cancelled'", [$d, $nextD]);
                $salesTrend['data'][] = (float)($r[0]['t'] ?? 0);
            }
        } else {
            $dayCount = max(1, (int)round((strtotime($endDate) - strtotime($startDate)) / 86400));
            $step = max(1, (int)ceil($dayCount / 10));
            for ($d = $startDate; $d <= $endDate; $d = date('Y-m-d', strtotime($d . " +{$step} days"))) {
                $salesTrend['labels'][] = date('d M', strtotime($d));
                $rangeEnd = date('Y-m-d', min(strtotime($endDate), strtotime($d . " +" . ($step - 1) . " days")));
                $r = $db->query("SELECT COALESCE(SUM(grand_total), 0) as t FROM `invoices` WHERE invoice_date BETWEEN ? AND ? AND status != 'cancelled'", [$d, $rangeEnd]);
                $salesTrend['data'][] = (float)($r[0]['t'] ?? 0);
            }
        }

        // 8. Low stock count & list
        $lowStockProducts = (new Product())->getLowStockProducts(6);

        // 9. Recent Invoices in Selected Period (fallback to latest)
        $recentInvoices = $db->query(
            "SELECT i.*, c.name as customer_name, u.name as cashier_name 
             FROM `invoices` i 
             JOIN `customers` c ON i.customer_id = c.id 
             JOIN `users` u ON i.user_id = u.id 
             WHERE i.invoice_date BETWEEN ? AND ?
             ORDER BY i.id DESC LIMIT 8",
            [$startDate, $endDate]
        );
        if (empty($recentInvoices)) {
            $recentInvoices = $db->query(
                "SELECT i.*, c.name as customer_name, u.name as cashier_name 
                 FROM `invoices` i 
                 JOIN `customers` c ON i.customer_id = c.id 
                 JOIN `users` u ON i.user_id = u.id 
                 ORDER BY i.id DESC LIMIT 8"
            );
        }

        // 10. Audit Logs
        $recentLogs = (new AuditLog())->getRecentLogs(6);

        $payload = [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodTitle' => $periodTitle,
            'periodSubtitle' => $periodSubtitle,
            'periodSales' => $periodSales,
            'periodSalesFormatted' => formatCurrency($periodSales),
            'periodInvoicesCount' => $periodInvoicesCount,
            'monthSales' => $monthSales,
            'monthSalesFormatted' => formatCurrency($monthSales),
            'monthInvoicesCount' => $monthInvoicesCount,
            'overallSales' => $overallSales,
            'overallSalesFormatted' => formatCurrency($overallSales),
            'overallInvoicesCount' => $overallInvoicesCount,
            'stockValuation' => $stockValuation,
            'stockValuationFormatted' => formatCurrency($stockValuation),
            'totalProductInvestment' => $totalProductInvestment,
            'totalProductInvestmentFormatted' => formatCurrency($totalProductInvestment),
            'stockExpectedMargin' => $stockExpectedMargin,
            'stockExpectedMarginFormatted' => formatCurrency($stockExpectedMargin),
            'stockMarginPercent' => $stockMarginPercent,
            'periodRevenue' => $periodRevenue,
            'periodCogs' => $periodCogs,
            'periodGrossMargin' => $periodGrossMargin,
            'periodMarginPercent' => $periodMarginPercent,
            'periodOnlineSales' => $periodOnlineSales,
            'periodOnlineSalesFormatted' => formatCurrency($periodOnlineSales),
            'periodOnlineCount' => $periodOnlineCount,
            'periodOnlinePercent' => $periodOnlinePercent,
            'overallOnlineSales' => $overallOnlineSales,
            'overallOnlineSalesFormatted' => formatCurrency($overallOnlineSales),
            'overallOnlineCount' => $overallOnlineCount,
            'periodOfflineSales' => $periodOfflineSales,
            'periodOfflineSalesFormatted' => formatCurrency($periodOfflineSales),
            'periodOfflineCount' => $periodOfflineCount,
            'overallOfflineSales' => $overallOfflineSales,
            'overallOfflineSalesFormatted' => formatCurrency($overallOfflineSales),
            'overallOfflineCount' => $overallOfflineCount,
            'targetProfitAmount' => $targetProfitAmount,
            'targetProfitAmountFormatted' => formatCurrency($targetProfitAmount),
            'targetStartDate' => $targetStartDate,
            'targetEndDate' => $targetEndDate,
            'targetPeriodSales' => $targetPeriodSales,
            'targetPeriodSalesFormatted' => formatCurrency($targetPeriodSales),
            'targetAchievedProfit' => $targetAchievedProfit,
            'targetAchievedProfitFormatted' => formatCurrency($targetAchievedProfit),
            'targetBillsCount' => $targetBillsCount,
            'targetPercent' => $targetPercent,
            'targetRemaining' => $targetRemaining,
            'targetRemainingFormatted' => formatCurrency($targetRemaining),
            'targetSurplus' => $targetSurplus,
            'targetSurplusFormatted' => formatCurrency($targetSurplus),
            'isTargetAchieved' => $isTargetAchieved,
            'totalProducts' => $totalProducts,
            'totalStockPieces' => $totalStockPieces,
            'lowStockProducts' => $lowStockProducts,
            'salesTrend' => $salesTrend,
            'recentInvoices' => $recentInvoices,
            'recentLogs' => $recentLogs
        ];

        if ($request->isAjax() || $request->input('format') === 'json') {
            Response::json(['success' => true, 'data' => $payload]);
            return;
        }

        $this->render('dashboard.index', array_merge($payload, [
            'pageTitle' => 'Executive Dashboard - ' . getSetting('company_name', 'Divya Murti ERP')
        ]));
    }

    public function saveProfitTarget(Request $request): void {
        Auth::requireAuth();
        $amount = max(0, (float)$request->input('target_profit_amount', 0));
        $startDate = trim((string)$request->input('target_start_date', ''));
        $endDate = trim((string)$request->input('target_end_date', ''));

        if (empty($startDate)) $startDate = date('Y-m-01');
        if (empty($endDate)) $endDate = date('Y-m-t');
        if ($startDate > $endDate) {
            $tmp = $startDate; $startDate = $endDate; $endDate = $tmp;
        }

        $settingModel = new \App\Models\Setting();
        $settingModel->set('inventory_profit_target_amount', $amount, 'dashboard', 'Target Inventory Profit Amount');
        $settingModel->set('inventory_profit_target_start', $startDate, 'dashboard', 'Target Inventory Profit Start Date');
        $settingModel->set('inventory_profit_target_end', $endDate, 'dashboard', 'Target Inventory Profit End Date');

        $db = Database::getInstance();
        $resTargetProfit = $db->query(
            "SELECT 
                COALESCE(SUM(ii.total_amount), 0) as target_period_sales,
                COALESCE(SUM(ii.quantity * COALESCE(p.piece_rate, p.purchase_price, 0)), 0) as target_period_cogs,
                COUNT(DISTINCT i.id) as target_bills_count
             FROM `invoice_items` ii
             JOIN `invoices` i ON ii.invoice_id = i.id
             LEFT JOIN `products` p ON ii.product_id = p.id
             WHERE i.status != 'cancelled' AND i.invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $targetPeriodSales = (float)($resTargetProfit[0]['target_period_sales'] ?? 0);
        $targetPeriodCogs = (float)($resTargetProfit[0]['target_period_cogs'] ?? 0);
        $targetAchievedProfit = max(0, $targetPeriodSales - $targetPeriodCogs);
        $targetBillsCount = (int)($resTargetProfit[0]['target_bills_count'] ?? 0);

        $targetPercent = $amount > 0 ? round(($targetAchievedProfit / $amount) * 100, 1) : 0;
        $targetRemaining = max(0, $amount - $targetAchievedProfit);
        $targetSurplus = max(0, $targetAchievedProfit - $amount);
        $isTargetAchieved = ($targetAchievedProfit >= $amount && $amount > 0);

        Response::json([
            'success' => true,
            'message' => 'Expected Inventory Profit Target updated successfully!',
            'data' => [
                'targetProfitAmount' => $amount,
                'targetProfitAmountFormatted' => formatCurrency($amount),
                'targetStartDate' => $startDate,
                'targetEndDate' => $endDate,
                'targetPeriodSales' => $targetPeriodSales,
                'targetPeriodSalesFormatted' => formatCurrency($targetPeriodSales),
                'targetAchievedProfit' => $targetAchievedProfit,
                'targetAchievedProfitFormatted' => formatCurrency($targetAchievedProfit),
                'targetBillsCount' => $targetBillsCount,
                'targetPercent' => $targetPercent,
                'targetRemaining' => $targetRemaining,
                'targetRemainingFormatted' => formatCurrency($targetRemaining),
                'targetSurplus' => $targetSurplus,
                'targetSurplusFormatted' => formatCurrency($targetSurplus),
                'isTargetAchieved' => $isTargetAchieved,
            ]
        ]);
    }
}
