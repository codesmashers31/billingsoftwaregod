<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Category;

class ReportController extends Controller {
    public function sales(Request $request): void {
        $this->requirePermission('reports.sales');
        $db = Database::getInstance();

        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-d'));
        $catFilter = (int)$request->input('category_id', 0);
        $userFilter = (int)$request->input('user_id', 0);

        $conditions = ["i.status != 'cancelled' AND i.invoice_date BETWEEN ? AND ?"];
        $params = [$dateFrom, $dateTo];

        if ($userFilter > 0) {
            $conditions[] = "i.user_id = ?";
            $params[] = $userFilter;
        }

        $whereClause = implode(' AND ', $conditions);

        // Overall summary
        $summarySql = "SELECT COUNT(i.id) as total_invoices, 
                              COALESCE(SUM(i.subtotal), 0) as total_subtotal,
                              COALESCE(SUM(i.tax_amount), 0) as total_tax,
                              COALESCE(SUM(i.item_discount_total + i.overall_discount_amount), 0) as total_discount,
                              COALESCE(SUM(i.grand_total), 0) as total_sales,
                              COALESCE(SUM(i.paid_amount), 0) as total_collected,
                              COALESCE(SUM(i.due_amount), 0) as total_due
                       FROM `invoices` i 
                       WHERE {$whereClause}";
        $summary = $db->query($summarySql, $params)[0] ?? [];

        // Daily breakdown
        $dailySql = "SELECT i.invoice_date, COUNT(i.id) as invoice_count, SUM(i.grand_total) as daily_total 
                     FROM `invoices` i 
                     WHERE {$whereClause} 
                     GROUP BY i.invoice_date 
                     ORDER BY i.invoice_date DESC";
        $dailyRows = $db->query($dailySql, $params);

        // Product-wise sales
        $prodSql = "SELECT ii.product_name, ii.god_name, ii.material, SUM(ii.quantity) as total_qty, SUM(ii.total_amount) as total_amount 
                    FROM `invoice_items` ii 
                    JOIN `invoices` i ON ii.invoice_id = i.id 
                    WHERE {$whereClause} 
                    GROUP BY ii.product_id 
                    ORDER BY total_amount DESC LIMIT 20";
        $productRows = $db->query($prodSql, $params);

        $users = $db->query("SELECT id, name FROM `users` WHERE status = 'active' ORDER BY name ASC");
        $categories = $db->query("SELECT id, name FROM `categories` WHERE status = 'active' ORDER BY name ASC");

        if ($request->input('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename=sales_report_' . $dateFrom . '_to_' . $dateTo . '.csv');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Invoice Count', 'Total Sales (INR)']);
            foreach ($dailyRows as $r) {
                fputcsv($out, [$r['invoice_date'], $r['invoice_count'], $r['daily_total']]);
            }
            fclose($out);
            exit;
        }

        $this->render('reports.sales', [
            'pageTitle' => 'Sales & Revenue Analytics',
            'summary' => $summary,
            'dailyRows' => $dailyRows,
            'productRows' => $productRows,
            'users' => $users,
            'categories' => $categories,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'userFilter' => $userFilter,
            'catFilter' => $catFilter
        ]);
    }

    public function inventory(Request $request): void {
        $this->requirePermission('reports.inventory');
        $db = Database::getInstance();

        $catFilter = (int)$request->input('category_id', 0);
        $stockFilter = $request->input('stock_filter', 'all'); // all, low, out

        $conditions = ["p.status != 'inactive'"];
        $params = [];

        if ($catFilter > 0) {
            $conditions[] = "p.category_id = ?";
            $params[] = $catFilter;
        }

        if ($stockFilter === 'low') {
            $conditions[] = "p.current_stock <= p.min_stock AND p.current_stock > 0";
        } elseif ($stockFilter === 'out') {
            $conditions[] = "p.current_stock = 0";
        }

        $whereClause = implode(' AND ', $conditions);

        // Valuation summary
        $valSql = "SELECT COUNT(id) as total_items, 
                          COALESCE(SUM(current_stock), 0) as total_pieces,
                          COALESCE(SUM(current_stock * purchase_price), 0) as total_cost_value,
                          COALESCE(SUM(current_stock * selling_price), 0) as total_retail_value 
                   FROM `products` p WHERE {$whereClause}";
        $valSummary = $db->query($valSql, $params)[0] ?? [];

        // Detail items
        $itemsSql = "SELECT p.*, c.name as category_name 
                     FROM `products` p 
                     JOIN `categories` c ON p.category_id = c.id 
                     WHERE {$whereClause} 
                     ORDER BY p.current_stock ASC";
        $items = $db->query($itemsSql, $params);
        $categories = $db->query("SELECT id, name FROM `categories` WHERE status = 'active' ORDER BY name ASC");

        if ($request->input('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename=inventory_valuation_' . date('Ymd') . '.csv');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Code', 'Product Name', 'Deity', 'Material', 'Stock', 'Unit Cost', 'Unit Retail', 'Total Cost Value', 'Total Retail Value']);
            foreach ($items as $p) {
                fputcsv($out, [
                    $p['code'], $p['name'], $p['god_name'], $p['material'], $p['current_stock'],
                    $p['purchase_price'], $p['selling_price'],
                    $p['current_stock'] * $p['purchase_price'],
                    $p['current_stock'] * $p['selling_price']
                ]);
            }
            fclose($out);
            exit;
        }

        $this->render('reports.inventory', [
            'pageTitle' => 'Stock Valuation & Inventory Reports',
            'summary' => $valSummary,
            'items' => $items,
            'categories' => $categories,
            'catFilter' => $catFilter,
            'stockFilter' => $stockFilter
        ]);
    }

    public function financial(Request $request): void {
        $this->requirePermission('reports.financial');
        $db = Database::getInstance();

        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-d'));

        // 1. Sales Income
        $resSales = $db->query("SELECT COALESCE(SUM(grand_total), 0) as total_sales, 
                                       COALESCE(SUM(tax_amount), 0) as total_tax 
                                FROM `invoices` 
                                WHERE status != 'cancelled' AND invoice_date BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        $totalSales = (float)($resSales[0]['total_sales'] ?? 0);
        $totalGst = (float)($resSales[0]['total_tax'] ?? 0);

        // 2. Cost of Goods Sold (approx from invoice items * purchase price)
        $resCogs = $db->query("SELECT COALESCE(SUM(ii.quantity * p.purchase_price), 0) as total_cogs 
                               FROM `invoice_items` ii 
                               JOIN `invoices` i ON ii.invoice_id = i.id 
                               JOIN `products` p ON ii.product_id = p.id 
                               WHERE i.status != 'cancelled' AND i.invoice_date BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        $totalCogs = (float)($resCogs[0]['total_cogs'] ?? 0);

        // 3. Operating Expenses
        $resExp = $db->query("SELECT COALESCE(SUM(amount), 0) as total_exp 
                              FROM `expenses` 
                              WHERE status = 'approved' AND expense_date BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        $totalExpenses = (float)($resExp[0]['total_exp'] ?? 0);

        // 4. Sales Returns / Refunds
        $resRet = $db->query("SELECT COALESCE(SUM(total_refund_amount), 0) as total_refunds 
                              FROM `sales_returns` 
                              WHERE status = 'approved' AND return_date BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        $totalRefunds = (float)($resRet[0]['total_refunds'] ?? 0);

        // Gross & Net Profit
        $netSales = $totalSales - $totalRefunds;
        $grossProfit = $netSales - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        // Expense category breakdown
        $expCategories = $db->query("SELECT ec.name, COALESCE(SUM(e.amount), 0) as total 
                                     FROM `expenses` e 
                                     JOIN `expense_categories` ec ON e.category_id = ec.id 
                                     WHERE e.status = 'approved' AND e.expense_date BETWEEN ? AND ? 
                                     GROUP BY ec.id 
                                     ORDER BY total DESC", [$dateFrom, $dateTo]);

        $this->render('reports.financial', [
            'pageTitle' => 'Financial P&L & Profitability Statement',
            'totalSales' => $totalSales,
            'totalRefunds' => $totalRefunds,
            'netSales' => $netSales,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'totalGst' => $totalGst,
            'expCategories' => $expCategories,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}
