<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Supplier;

class SupplierController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('suppliers.view');
        $suppModel = new Supplier();

        $page = (int)$request->input('page', 1);
        $search = (string)$request->input('search', '');

        $conditions = ["1=1"];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(name LIKE ? OR company_name LIKE ? OR mobile LIKE ? OR supplier_code LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $result = $suppModel->paginate($page, 15, implode(' AND ', $conditions), $params, 'id DESC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('suppliers.index', [
            'pageTitle' => 'Artisans & Suppliers Directory',
            'suppliers' => $result['data'],
            'pagination' => $result,
            'search' => $search
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('suppliers.manage');
        $name = trim($request->input('name', ''));
        $mobile = trim($request->input('mobile', ''));

        if (empty($name) || empty($mobile)) {
            Response::error('Supplier name and mobile number are required.');
        }

        $suppModel = new Supplier();
        $code = 'SUP-' . str_pad((string)($suppModel->count() + 1), 4, '0', STR_PAD_LEFT);

        $id = $suppModel->create([
            'supplier_code' => $code,
            'name' => $name,
            'company_name' => $request->input('company_name'),
            'mobile' => $mobile,
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'gst_number' => $request->input('gst_number'),
            'bank_name' => $request->input('bank_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'bank_ifsc' => $request->input('bank_ifsc'),
            'status' => 'active'
        ]);

        $this->logActivity('create', 'suppliers', (int)$id, $code, "Added supplier/artisan {$name}");
        Response::success('Supplier registered successfully!', ['id' => $id]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('suppliers.manage');
        $id = (int)($params['id'] ?? 0);
        $suppModel = new Supplier();
        $supp = $suppModel->find($id);

        if (!$supp) {
            Response::error('Supplier not found.');
        }

        $suppModel->update($id, [
            'name' => $request->input('name'),
            'company_name' => $request->input('company_name'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'gst_number' => $request->input('gst_number'),
            'bank_name' => $request->input('bank_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'bank_ifsc' => $request->input('bank_ifsc'),
            'status' => $request->input('status', 'active')
        ]);

        $this->logActivity('update', 'suppliers', $id, $supp['supplier_code'], "Updated supplier {$supp['name']}");
        Response::success('Supplier updated successfully.');
    }

    public function saveWithProducts(Request $request): void {
        $this->requirePermission('suppliers.manage');

        $name = trim((string)($request->input('supplier_name') ?: $request->input('name', '')));
        $mobile = trim((string)($request->input('mobile_number') ?: $request->input('mobile', '')));

        if (empty($name) || empty($mobile)) {
            Response::error('Supplier name and mobile number are required.');
        }

        $db = \App\Core\Database::getInstance();
        $suppModel = new Supplier();
        $prodModel = new \App\Models\Product();
        $catModel = new \App\Models\Category();
        $subModel = new \App\Models\SubCategory();

        $supplierId = (int)$request->input('supplier_id', 0);
        $isEdit = false;

        $db->beginTransaction();

        try {
            if ($supplierId > 0 && ($supp = $suppModel->find($supplierId))) {
                $isEdit = true;
                $suppModel->update($supplierId, [
                    'name' => $name,
                    'mobile' => $mobile,
                    'email' => $request->input('email', $supp['email']),
                    'address' => $request->input('address', $supp['address']),
                    'city' => $request->input('city', $supp['city']),
                    'state' => $request->input('state', $supp['state']),
                    'gst_number' => $request->input('gst_number', $supp['gst_number']),
                    'status' => $request->input('status', $supp['status'] ?? 'active')
                ]);
                $code = $supp['supplier_code'];
            } else {
                $code = 'SUP-' . str_pad((string)($suppModel->count() + 1), 4, '0', STR_PAD_LEFT);
                $supplierId = (int)$suppModel->create([
                    'supplier_code' => $code,
                    'name' => $name,
                    'company_name' => $request->input('company_name'),
                    'mobile' => $mobile,
                    'email' => $request->input('email'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'gst_number' => $request->input('gst_number'),
                    'status' => $request->input('status', 'active')
                ]);
            }

            // Process Products Supplied
            $prodNames = $request->input('sp_product_name', []);
            $prodCats = $request->input('sp_product_category', []);
            $prodSubcats = $request->input('sp_product_subcategory', []);
            $prodUnits = $request->input('sp_unit', []);
            $prodRates = $request->input('sp_piece_rate', []);
            $prodPurchases = $request->input('sp_purchase_price', []);
            $prodSellings = $request->input('sp_selling_price', []);
            $prodGsts = $request->input('sp_gst', []);
            $prodBarcodes = $request->input('sp_barcode', []);
            $prodIds = $request->input('sp_product_id', []);

            $savedProducts = [];

            if (is_array($prodNames)) {
                foreach ($prodNames as $i => $rawName) {
                    $pName = trim((string)$rawName);
                    if ($pName === '') continue;

                    $catRaw = trim((string)($prodCats[$i] ?? ''));
                    if ($catRaw === '') {
                        $catRaw = 'General';
                    }
                    $cat = $catModel->findOrCreate($catRaw);
                    $catId = (int)$cat['id'];

                    $subRaw = trim((string)($prodSubcats[$i] ?? ''));
                    $subId = null;
                    if ($subRaw !== '') {
                        $sub = $subModel->findOrCreate($subRaw, $catId);
                        $subId = $sub ? (int)$sub['id'] : null;
                    }

                    $units = max(0, (int)($prodUnits[$i] ?? 0));
                    $pieceRate = max(0, (float)($prodRates[$i] ?? 0));
                    $purchasePrice = (float)($prodPurchases[$i] ?? 0);
                    if ($purchasePrice <= 0) {
                        $purchasePrice = $units > 0 && $pieceRate > 0 ? ($units * $pieceRate) : $pieceRate;
                    }

                    $sellingPrice = (float)($prodSellings[$i] ?? 0);
                    if ($sellingPrice <= 0) {
                        $sellingPrice = $pieceRate > 0 ? ($pieceRate * 1.6) : ($purchasePrice * 1.6);
                    }

                    $gst = max(0, (float)($prodGsts[$i] ?? 0));
                    $barcode = $prodModel->generateUniqueBarcode($prodBarcodes[$i] ?? null);
                    $pId = !empty($prodIds[$i]) ? (int)$prodIds[$i] : 0;

                    if ($pId > 0 && ($existingProd = $prodModel->find($pId))) {
                        $prodModel->update($pId, [
                            'supplier_id' => $supplierId,
                            'name' => $pName,
                            'category_id' => $catId,
                            'subcategory_id' => $subId,
                            'purchase_price' => $purchasePrice,
                            'piece_rate' => $pieceRate,
                            'selling_price' => $sellingPrice,
                            'gst_percent' => $gst,
                            'current_stock' => $units,
                            'status' => 'active',
                            'updated_by' => \App\Core\Auth::id() ?: 1
                        ]);
                        $savedProdId = $pId;
                    } else {
                        $sku = $prodModel->generateUniqueSku();
                        $savedProdId = (int)$prodModel->create([
                            'supplier_id' => $supplierId,
                            'name' => $pName,
                            'code' => $sku,
                            'sku' => $sku,
                            'barcode' => $barcode,
                            'category_id' => $catId,
                            'subcategory_id' => $subId,
                            'brand' => 'Divine Heritage',
                            'purchase_price' => $purchasePrice,
                            'piece_rate' => $pieceRate,
                            'selling_price' => $sellingPrice,
                            'gst_percent' => $gst,
                            'current_stock' => $units,
                            'status' => 'active',
                            'created_by' => \App\Core\Auth::id() ?: 1
                        ]);
                    }

                    $savedProducts[] = $prodModel->getWithDetails($savedProdId);
                }
            }

            $db->commit();
            $this->logActivity($isEdit ? 'update' : 'create', 'suppliers', $supplierId, $code, ($isEdit ? 'Updated' : 'Added') . " supplier/artisan {$name} with " . count($savedProducts) . " products");

            $savedSupplier = $suppModel->find($supplierId);
            $savedSupplier['product_count'] = count($suppModel->getProducts($supplierId));

            Response::success($isEdit ? 'Supplier and associated products updated.' : 'Supplier and products saved.', [
                'supplier' => $savedSupplier,
                'products' => $savedProducts
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error('Failed to save supplier and products: ' . $e->getMessage());
        }
    }

    public function getProducts(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        $suppModel = new Supplier();
        $supp = $suppModel->find($id);

        if (!$supp) {
            Response::error('Supplier not found.');
        }

        $products = $suppModel->getProducts($id);
        Response::json([
            'success' => true,
            'supplier' => $supp,
            'products' => $products
        ]);
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('suppliers.manage');
        $id = (int)($params['id'] ?? 0);
        $suppModel = new Supplier();
        $supp = $suppModel->find($id);

        if (!$supp) {
            Response::error('Supplier not found.');
        }

        $db = \App\Core\Database::getInstance();
        $db->query("DELETE FROM `products` WHERE supplier_id = ?", [$id]);
        $suppModel->delete($id);
        $this->logActivity('delete', 'suppliers', $id, $supp['supplier_code'], "Deleted supplier {$supp['name']}");
        Response::success('Supplier deleted successfully.');
    }
}
