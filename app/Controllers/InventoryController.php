<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Inventory;
use App\Models\Product;

class InventoryController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('inventory.view');
        $productModel = new Product();
        $invModel = new Inventory();

        $page = (int)$request->input('page', 1);
        $filters = [
            'search' => (string)$request->input('search', ''),
            'low_stock' => (string)$request->input('low_stock', '')
        ];

        $stockList = $productModel->getPaginatedList($page, 15, $filters);
        $recentTransactions = $invModel->getRecentTransactions(15);
        $products = $productModel->where("status != 'inactive'", [], 'name ASC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($stockList);
        }

        $this->render('inventory.index', [
            'pageTitle' => 'Inventory & Stock Management',
            'stockList' => $stockList['data'],
            'pagination' => $stockList,
            'recentTransactions' => $recentTransactions,
            'products' => $products,
            'filters' => $filters
        ]);
    }

    public function adjust(Request $request): void {
        $this->requirePermission('inventory.adjust');
        $productId = (int)$request->input('product_id');
        $type = $request->input('transaction_type', 'adjustment'); // stock_in, adjustment, damage
        $quantity = (int)$request->input('quantity');
        $notes = trim($request->input('notes', ''));

        if ($productId <= 0 || $quantity === 0) {
            Response::error('Please select a valid product and specify a non-zero quantity.');
        }

        // Adjust sign: for damage, quantity is negative
        if ($type === 'damage' && $quantity > 0) {
            $quantity = -$quantity;
        }

        $invModel = new Inventory();
        try {
            $invModel->adjustStock(
                $productId,
                $quantity,
                $type,
                'manual_adjustment',
                null,
                'ADJ-' . time(),
                $notes,
                Auth::id() ?: 1
            );

            $this->logActivity('adjust', 'inventory', $productId, null, "Adjusted stock for product #{$productId} by {$quantity} units ({$type})");
            Response::success('Inventory stock updated successfully!');
        } catch (\Throwable $e) {
            Response::error($e->getMessage());
        }
    }

    public function history(Request $request, array $params): void {
        $this->requirePermission('inventory.view');
        $productId = (int)($params['id'] ?? 0);
        $invModel = new Inventory();
        $productModel = new Product();

        $product = $productModel->find($productId);
        if (!$product) {
            Response::error('Product not found.');
        }

        $history = $invModel->getProductHistory($productId, 100);
        Response::json([
            'success' => true,
            'product' => $product,
            'history' => $history
        ]);
    }
}
