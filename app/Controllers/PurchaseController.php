<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Account;

class PurchaseController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('purchases.view');
        $purchModel = new Purchase();
        $suppModel = new Supplier();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');
        $status = (string)$request->input('status', '');

        $conditions = ["1=1"];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(p.purchase_no LIKE ? OR s.name LIKE ? OR s.company_name LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }
        if (!empty($status)) {
            $conditions[] = "p.status = ?";
            $params[] = $status;
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * 15);

        $db = Database::getInstance();
        $countSql = "SELECT COUNT(*) as total FROM `purchases` p JOIN `suppliers` s ON p.supplier_id = s.id WHERE {$whereClause}";
        $totalRes = $db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT p.*, s.name as supplier_name, s.company_name, u.name as creator_name 
                    FROM `purchases` p 
                    JOIN `suppliers` s ON p.supplier_id = s.id 
                    JOIN `users` u ON p.created_by = u.id 
                    WHERE {$whereClause} 
                    ORDER BY p.id DESC 
                    LIMIT 15 OFFSET {$offset}";
        $rows = $db->query($dataSql, $params);

        $pagination = [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => 15,
            'total_pages' => (int)ceil($total / 15)
        ];

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($pagination);
        }

        $suppliers = $suppModel->where("status = 'active'", [], 'name ASC');

        $this->render('purchases.index', [
            'pageTitle' => 'Purchases & Sourcing Orders',
            'purchases' => $rows,
            'pagination' => $pagination,
            'suppliers' => $suppliers,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create(Request $request): void {
        $this->requirePermission('purchases.create');
        $suppModel = new Supplier();
        $prodModel = new Product();

        $suppliers = $suppModel->where("status = 'active'", [], 'name ASC');
        $products = $prodModel->where("status != 'inactive'", [], 'name ASC');

        $this->render('purchases.create', [
            'pageTitle' => 'New Statue Procurement / Purchase Order',
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('purchases.create');
        $supplierId = (int)$request->input('supplier_id');
        $purchaseDate = $request->input('purchase_date', date('Y-m-d'));
        $items = $request->input('items', []);

        if ($supplierId <= 0 || empty($items) || !is_array($items)) {
            Response::error('Please select a supplier and add at least one product.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $purchModel = new Purchase();
            $purchaseNo = $purchModel->generatePurchaseNo();

            $subtotal = 0;
            $taxTotal = 0;
            $grandTotal = 0;

            $validatedItems = [];
            foreach ($items as $item) {
                $pId = (int)($item['product_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                $rate = (float)($item['purchase_price'] ?? 0);
                $taxPercent = (float)($item['tax_percent'] ?? 0);

                if ($pId > 0 && $qty > 0 && $rate >= 0) {
                    $itemSub = $qty * $rate;
                    $itemTax = ($itemSub * $taxPercent) / 100;
                    $itemTotal = $itemSub + $itemTax;

                    $subtotal += $itemSub;
                    $taxTotal += $itemTax;
                    $grandTotal += $itemTotal;

                    $validatedItems[] = [
                        'product_id' => $pId,
                        'quantity' => $qty,
                        'purchase_price' => $rate,
                        'tax_percent' => $taxPercent,
                        'total_amount' => $itemTotal
                    ];
                }
            }

            if (empty($validatedItems)) {
                throw new \Exception('No valid purchase items found.');
            }

            $purchaseId = $purchModel->create([
                'purchase_no' => $purchaseNo,
                'supplier_id' => $supplierId,
                'purchase_date' => $purchaseDate,
                'subtotal' => $subtotal,
                'discount_amount' => 0.00,
                'tax_amount' => $taxTotal,
                'total_amount' => $grandTotal,
                'paid_amount' => 0.00,
                'due_amount' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $request->input('notes'),
                'created_by' => Auth::id() ?: 1
            ]);

            foreach ($validatedItems as $vItem) {
                $db->query("INSERT INTO `purchase_items` (`purchase_id`, `product_id`, `quantity`, `purchase_price`, `tax_percent`, `total_amount`) 
                            VALUES (?, ?, ?, ?, ?, ?)", [
                    $purchaseId, $vItem['product_id'], $vItem['quantity'], $vItem['purchase_price'], $vItem['tax_percent'], $vItem['total_amount']
                ]);
            }

            $db->commit();
            $this->logActivity('create', 'purchases', (int)$purchaseId, $purchaseNo, "Created purchase order {$purchaseNo} for " . formatCurrency($grandTotal));

            Response::success('Purchase order created successfully!', [
                'purchase_id' => $purchaseId,
                'redirect' => url('purchases')
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function approve(Request $request, array $params): void {
        $this->requirePermission('purchases.approve');
        $id = (int)($params['id'] ?? 0);
        $purchModel = new Purchase();
        $purchase = $purchModel->find($id);

        if (!$purchase) {
            Response::error('Purchase order not found.');
        }

        if ($purchase['status'] === 'approved') {
            Response::error('Purchase order has already been approved and stocked.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $items = $purchModel->getItems($id);
            $invModel = new Inventory();

            // 1. Increment inventory for each item
            foreach ($items as $item) {
                $invModel->adjustStock(
                    (int)$item['product_id'],
                    (int)$item['quantity'],
                    'purchase',
                    'purchase',
                    $id,
                    $purchase['purchase_no'],
                    "Restocked from PO {$purchase['purchase_no']}",
                    Auth::id() ?: 1
                );
            }

            // 2. Update supplier outstanding balance
            $suppModel = new Supplier();
            $supplier = $suppModel->find((int)$purchase['supplier_id']);
            if ($supplier) {
                $newOutstanding = (float)$supplier['outstanding_balance'] + (float)$purchase['total_amount'];
                $suppModel->update((int)$purchase['supplier_id'], ['outstanding_balance' => $newOutstanding]);
            }

            // 3. Mark purchase approved
            $purchModel->update($id, [
                'status' => 'approved',
                'approved_by' => Auth::id() ?: 1,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            $db->commit();
            $this->logActivity('approve', 'purchases', $id, $purchase['purchase_no'], "Approved PO {$purchase['purchase_no']} and stocked items");
            Response::success('Purchase order approved! Inventory and supplier balance updated.');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function view(Request $request, array $params): void {
        $this->requirePermission('purchases.view');
        $id = (int)($params['id'] ?? 0);
        $purchModel = new Purchase();
        $purchase = $purchModel->getWithDetails($id);

        if (!$purchase) {
            Response::error('Purchase order not found.');
        }

        $items = $purchModel->getItems($id);
        Response::json([
            'success' => true,
            'purchase' => $purchase,
            'items' => $items
        ]);
    }
}
