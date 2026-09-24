<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\AuditLog;

class DashboardController extends Controller {
    public function index(Request $request): void {
        Auth::requireAuth();
        $db = Database::getInstance();

        // 1. Core KPIs
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Today's Sales
        $resTodaySales = $db->query("SELECT COALESCE(SUM(grand_total), 0) as total, COUNT(*) as count FROM `invoices` WHERE invoice_date = ? AND status != 'cancelled'", [$today]);
        $todaySales = (float)($resTodaySales[0]['total'] ?? 0);
        $todayInvoicesCount = (int)($resTodaySales[0]['count'] ?? 0);

        // Monthly Sales
        $resMonthSales = $db->query("SELECT COALESCE(SUM(grand_total), 0) as total, COUNT(*) as count FROM `invoices` WHERE DATE_FORMAT(invoice_date, '%Y-%m') = ? AND status != 'cancelled'", [$thisMonth]);
        $monthSales = (float)($resMonthSales[0]['total'] ?? 0);
        $monthInvoicesCount = (int)($resMonthSales[0]['count'] ?? 0);

        // Total Lifetime Revenue
        $resRevenue = $db->query("SELECT COALESCE(SUM(grand_total), 0) as total FROM `invoices` WHERE status != 'cancelled'");
        $totalRevenue = (float)($resRevenue[0]['total'] ?? 0);

        // Customer Count & Outstanding
        $resCust = $db->query("SELECT COUNT(*) as total_cust, COALESCE(SUM(outstanding_balance), 0) as total_outstanding FROM `customers` WHERE status = 'active'");
        $totalCustomers = (int)($resCust[0]['total_cust'] ?? 0);
        $customerOutstanding = (float)($resCust[0]['total_outstanding'] ?? 0);

        // Product Count, Stock Valuation & Total Pieces
        $resProd = $db->query("SELECT COUNT(*) as total_prod, COALESCE(SUM(current_stock * purchase_price), 0) as stock_valuation, COALESCE(SUM(current_stock), 0) as total_pieces FROM `products` WHERE status != 'inactive'");
        $totalProducts = (int)($resProd[0]['total_prod'] ?? 0);
        $stockValuation = (float)($resProd[0]['stock_valuation'] ?? 0);
        $totalStockPieces = (int)($resProd[0]['total_pieces'] ?? 0);

        // Cash & Bank Balances
        $resCash = $db->query("SELECT COALESCE(current_balance, 0) as cash FROM `accounts` WHERE type = 'cash' LIMIT 1");
        $cashInHand = (float)($resCash[0]['cash'] ?? 0);

        $resBank = $db->query("SELECT COALESCE(SUM(current_balance), 0) as bank FROM `accounts` WHERE type = 'bank'");
        $bankBalance = (float)($resBank[0]['bank'] ?? 0);

        // Supplier Payables
        $resSupp = $db->query("SELECT COALESCE(SUM(outstanding_balance), 0) as payables FROM `suppliers`");
        $supplierPayables = (float)($resSupp[0]['payables'] ?? 0);

        // Low stock count & list
        $lowStockProducts = (new Product())->getLowStockProducts(6);
        $lowStockCount = count($lowStockProducts);

        // Today's Expenses
        $resTodayExp = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM `expenses` WHERE expense_date = ? AND status = 'approved'", [$today]);
        $todayExpenses = (float)($resTodayExp[0]['total'] ?? 0);

        // 2. Chart Data: Last 7 Days Sales Trend
        $salesTrend = ['labels' => [], 'data' => []];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dayLabel = date('D, d M', strtotime($d));
            $resD = $db->query("SELECT COALESCE(SUM(grand_total), 0) as total FROM `invoices` WHERE invoice_date = ? AND status != 'cancelled'", [$d]);
            $salesTrend['labels'][] = $dayLabel;
            $salesTrend['data'][] = (float)($resD[0]['total'] ?? 0);
        }

        // 3. Chart Data: Top Selling Statues by Material
        $matRows = $db->query("SELECT p.material, COALESCE(SUM(ii.quantity), 0) as qty 
                               FROM `invoice_items` ii 
                               JOIN `products` p ON ii.product_id = p.id 
                               GROUP BY p.material 
                               ORDER BY qty DESC LIMIT 5");
        $materialChart = [
            'labels' => !empty($matRows) ? array_column($matRows, 'material') : ['Brass', 'Marble', 'Panchaloha', 'Black Stone'],
            'data' => !empty($matRows) ? array_map('intval', array_column($matRows, 'qty')) : [12, 8, 4, 3]
        ];

        // 4. Payment Method Distribution
        $payRows = $db->query("SELECT payment_method, COUNT(*) as count, SUM(grand_total) as total 
                              FROM `invoices` 
                              WHERE status != 'cancelled' 
                              GROUP BY payment_method");
        $paymentChart = [
            'labels' => array_map('strtoupper', array_column($payRows, 'payment_method')),
            'data' => array_map('floatval', array_column($payRows, 'total'))
        ];

        // 5. Recent Invoices
        $recentInvoices = $db->query("SELECT i.*, c.name as customer_name, u.name as cashier_name 
                                      FROM `invoices` i 
                                      JOIN `customers` c ON i.customer_id = c.id 
                                      JOIN `users` u ON i.user_id = u.id 
                                      ORDER BY i.id DESC LIMIT 6");

        // 6. Recent Audit Logs
        $recentLogs = (new AuditLog())->getRecentLogs(6);

        $this->render('dashboard.index', [
            'pageTitle' => 'Executive Dashboard - ' . getSetting('company_name', 'Divya Murti ERP'),
            'todaySales' => $todaySales,
            'todayInvoicesCount' => $todayInvoicesCount,
            'monthSales' => $monthSales,
            'monthInvoicesCount' => $monthInvoicesCount,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'customerOutstanding' => $customerOutstanding,
            'totalProducts' => $totalProducts,
            'stockValuation' => $stockValuation,
            'totalStockPieces' => $totalStockPieces,
            'cashInHand' => $cashInHand,
            'bankBalance' => $bankBalance,
            'supplierPayables' => $supplierPayables,
            'lowStockCount' => $lowStockCount,
            'lowStockProducts' => $lowStockProducts,
            'todayExpenses' => $todayExpenses,
            'salesTrend' => $salesTrend,
            'materialChart' => $materialChart,
            'paymentChart' => $paymentChart,
            'recentInvoices' => $recentInvoices,
            'recentLogs' => $recentLogs
        ]);
    }
}
