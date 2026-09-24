<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Inventory;

class ProductController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('products.view');
        $productModel = new Product();
        $catModel = new Category();

        $page = (int)$request->input('page', 1);
        $filters = [
            'search' => (string)$request->input('search', ''),
            'category_id' => $request->input('category_id'),
            'material' => (string)$request->input('material', ''),
            'status' => (string)$request->input('status', ''),
            'low_stock' => (string)$request->input('low_stock', ''),
        ];

        $result = $productModel->getPaginatedList($page, 15, $filters);
        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $materials = $productModel->getDistinctMaterials();

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('products.index', [
            'pageTitle' => 'God Statues & Religious Catalog',
            'products' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'materials' => $materials,
            'filters' => $filters
        ]);
    }

    public function create(Request $request): void {
        $this->requirePermission('products.create');
        $catModel = new Category();
        $categories = $catModel->where("status = 'active'", [], 'name ASC');

        $this->render('products.create', [
            'pageTitle' => 'Add New God Statue / Religious Item',
            'categories' => $categories
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('products.create');

        $errors = $this->validate($request->all(), [
            'name' => 'required|min:3',
            'sku' => 'required',
            'category_id' => 'required',
            'selling_price' => 'required|numeric'
        ]);

        $productModel = new Product();
        $sku = trim($request->input('sku'));
        $barcode = trim($request->input('barcode')) ?: $sku;
        $code = trim($request->input('code')) ?: 'PRD-' . strtoupper(substr(uniqid(), -6));

        if ($productModel->findBy('sku', $sku)) {
            $errors['sku'][] = 'SKU already exists.';
        }
        if ($productModel->findBy('barcode', $barcode)) {
            $errors['barcode'][] = 'Barcode already in use.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) Response::error('Validation failed', $errors);
            Session::flash('error', 'Please fix form validation errors.');
            Response::redirect('/products/create');
        }

        // Handle Image upload if provided
        $imagePath = 'assets/images/statues/placeholder.png';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/assets/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'assets/uploads/products/' . $fileName;
            }
        }

        $stock = (int)$request->input('current_stock', 0);
        $productId = $productModel->create([
            'name' => $request->input('name'),
            'code' => $code,
            'sku' => $sku,
            'barcode' => $barcode,
            'category_id' => (int)$request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'brand' => $request->input('brand', 'Divine Heritage'),
            'description' => $request->input('description'),
            'god_name' => $request->input('god_name'),
            'material' => $request->input('material', 'Brass'),
            'color' => $request->input('color'),
            'height' => (float)$request->input('height', 0),
            'width' => (float)$request->input('width', 0),
            'length' => (float)$request->input('length', 0),
            'weight' => (float)$request->input('weight', 0),
            'size' => $request->input('size', 'Medium'),
            'finish_type' => $request->input('finish_type', 'Antique'),
            'purchase_price' => (float)$request->input('purchase_price', 0),
            'selling_price' => (float)$request->input('selling_price', 0),
            'wholesale_price' => (float)$request->input('wholesale_price', 0),
            'discount_percent' => (float)$request->input('discount_percent', 0),
            'gst_percent' => (float)$request->input('gst_percent', 12),
            'hsn_code' => $request->input('hsn_code', '9701'),
            'current_stock' => $stock,
            'min_stock' => (int)$request->input('min_stock', 2),
            'max_stock' => (int)$request->input('max_stock', 50),
            'stock_location' => $request->input('stock_location', 'Main Showroom'),
            'image' => $imagePath,
            'status' => $stock > 0 ? 'active' : 'out_of_stock',
            'created_by' => Auth::id() ?: 1
        ]);

        // Record initial inventory transaction if stock > 0
        if ($stock > 0) {
            $invModel = new Inventory();
            $invModel->create([
                'product_id' => (int)$productId,
                'transaction_type' => 'stock_in',
                'reference_type' => 'opening_stock',
                'reference_no' => 'INIT-' . $code,
                'previous_stock' => 0,
                'quantity' => $stock,
                'new_stock' => $stock,
                'notes' => 'Initial product opening stock count',
                'user_id' => Auth::id() ?: 1
            ]);
        }

        $this->logActivity('create', 'products', (int)$productId, $sku, "Added new product {$request->input('name')}");

        if ($request->isAjax()) {
            Response::success('Product created successfully!', ['id' => $productId, 'redirect' => url('products')]);
        }

        Session::flash('success', 'Product registered successfully.');
        Response::redirect('/products');
    }

    public function edit(Request $request, array $params): void {
        $this->requirePermission('products.edit');
        $id = (int)($params['id'] ?? 0);
        $productModel = new Product();
        $catModel = new Category();
        $subModel = new SubCategory();

        $product = $productModel->getWithDetails($id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            Response::redirect('/products');
        }

        $categories = $catModel->where("status = 'active'", [], 'name ASC');
        $subcategories = $product['category_id'] ? $subModel->getByCategory((int)$product['category_id']) : [];

        $this->render('products.edit', [
            'pageTitle' => 'Edit Statue: ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
            'subcategories' => $subcategories
        ]);
    }

    public function update(Request $request, array $params): void {
        $this->requirePermission('products.edit');
        $id = (int)($params['id'] ?? 0);
        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            if ($request->isAjax()) Response::error('Product not found.');
            Session::flash('error', 'Product not found.');
            Response::redirect('/products');
        }

        $updateData = [
            'name' => $request->input('name'),
            'category_id' => (int)$request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'brand' => $request->input('brand', 'Divine Heritage'),
            'description' => $request->input('description'),
            'god_name' => $request->input('god_name'),
            'material' => $request->input('material'),
            'color' => $request->input('color'),
            'height' => (float)$request->input('height', 0),
            'width' => (float)$request->input('width', 0),
            'length' => (float)$request->input('length', 0),
            'weight' => (float)$request->input('weight', 0),
            'size' => $request->input('size'),
            'finish_type' => $request->input('finish_type'),
            'purchase_price' => (float)$request->input('purchase_price', 0),
            'selling_price' => (float)$request->input('selling_price', 0),
            'wholesale_price' => (float)$request->input('wholesale_price', 0),
            'discount_percent' => (float)$request->input('discount_percent', 0),
            'gst_percent' => (float)$request->input('gst_percent', 12),
            'hsn_code' => $request->input('hsn_code', '9701'),
            'min_stock' => (int)$request->input('min_stock', 2),
            'max_stock' => (int)$request->input('max_stock', 50),
            'stock_location' => $request->input('stock_location'),
            'status' => $request->input('status', 'active'),
            'updated_by' => Auth::id() ?: 1
        ];

        // Handle Image upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/assets/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $updateData['image'] = 'assets/uploads/products/' . $fileName;
            }
        }

        $productModel->update($id, $updateData);
        $this->logActivity('update', 'products', $id, $product['sku'], "Updated product specs {$product['name']}");

        if ($request->isAjax()) {
            Response::success('Product updated successfully!', ['redirect' => url('products')]);
        }

        Session::flash('success', 'Product updated successfully.');
        Response::redirect('/products');
    }

    public function view(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        $productModel = new Product();
        $invModel = new Inventory();

        $product = $productModel->getWithDetails($id);
        if (!$product) {
            Response::error('Product not found.');
        }

        $history = $invModel->getProductHistory($id, 10);
        Response::json([
            'success' => true,
            'product' => $product,
            'history' => $history
        ]);
    }

    public function delete(Request $request, array $params): void {
        $this->requirePermission('products.delete');
        $id = (int)($params['id'] ?? 0);
        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            Response::error('Product not found.');
        }

        $productModel->delete($id);
        $this->logActivity('delete', 'products', $id, $product['sku'], "Deleted product {$product['name']}");
        Response::success('Product deleted successfully.');
    }

    public function export(Request $request): void {
        $this->requirePermission('products.export');
        $productModel = new Product();
        $products = $productModel->all('id ASC');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=god_statues_catalog_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Code', 'SKU', 'Barcode', 'Product Name', 'God/Deity', 'Material', 'Height (in)', 'Weight (kg)', 'Finish', 'Selling Price', 'Wholesale Price', 'Stock', 'Status']);

        foreach ($products as $p) {
            fputcsv($output, [
                $p['id'], $p['code'], $p['sku'], $p['barcode'], $p['name'],
                $p['god_name'], $p['material'], $p['height'], $p['weight'],
                $p['finish_type'], $p['selling_price'], $p['wholesale_price'],
                $p['current_stock'], $p['status']
            ]);
        }
        fclose($output);
        exit;
    }
}
