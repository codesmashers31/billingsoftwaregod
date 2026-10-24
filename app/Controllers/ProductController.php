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
use App\Models\Supplier;
use App\Models\Inventory;
use App\Core\Database;

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
        
        $subModel = new SubCategory();
        $subcategories = $subModel->where("status = 'active'", [], 'name ASC');

        $suppModel = new Supplier();
        $suppliers = $suppModel->where("status = 'active'", [], 'id DESC');

        $materials = $productModel->getDistinctMaterials();

        $db = Database::getInstance();
        $allProducts = $db->query("SELECT p.*, c.name as category_name, s.name as subcategory_name, sup.name as supplier_name,
                                  (SELECT quantity FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock' ORDER BY id DESC LIMIT 1) as last_restocked_qty,
                                  (SELECT created_at FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock' ORDER BY id DESC LIMIT 1) as last_restocked_at,
                                  (SELECT COALESCE(SUM(quantity), 0) FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock') as total_restocked_qty,
                                  (SELECT COALESCE(SUM(ii.quantity), 0) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE ii.product_id = p.id AND inv.status != 'cancelled') as total_sold_qty,
                                  (SELECT COALESCE(SUM(ii.total_amount), 0) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE ii.product_id = p.id AND inv.status != 'cancelled') as total_sales_revenue,
                                  (SELECT COALESCE(SUM(it.quantity), 0) FROM inventory_transactions it WHERE it.product_id = p.id AND it.transaction_type = 'stock_in' AND it.reference_type IN ('opening_stock', 'restock', 'purchase')) as total_purchased_qty 
                                  FROM `products` p 
                                  LEFT JOIN `categories` c ON p.category_id = c.id 
                                  LEFT JOIN `subcategories` s ON p.subcategory_id = s.id 
                                  LEFT JOIN `suppliers` sup ON p.supplier_id = sup.id 
                                  ORDER BY p.id DESC");
        $allSuppliers = $db->query("SELECT s.*, 
                                   (SELECT COUNT(*) FROM `products` p WHERE p.supplier_id = s.id) as product_count,
                                   sp.paid_amount,
                                   sp.payment_status as saved_payment_status,
                                   sp.payment_method
                                   FROM `suppliers` s 
                                   LEFT JOIN `supplier_payments` sp ON s.id = sp.supplier_id
                                   ORDER BY s.id DESC");

        $suppModel = new Supplier();
        $recentInvoices = $suppModel->getSupplierInvoiceList();

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('products.index', [
            'pageTitle' => 'God Statues & Religious Catalog',
            'products' => $result['data'],
            'allProducts' => $allProducts,
            'allSuppliers' => $allSuppliers,
            'recentInvoices' => $recentInvoices,
            'pagination' => $result,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'suppliers' => $allSuppliers,
            'materials' => $materials,
            'filters' => $filters
        ]);
    }

    public function apiData(Request $request): void {
        $db = Database::getInstance();
        $products = $db->query("SELECT p.*, c.name as category_name, s.name as subcategory_name, sup.name as supplier_name,
                                (SELECT quantity FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock' ORDER BY id DESC LIMIT 1) as last_restocked_qty,
                                (SELECT created_at FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock' ORDER BY id DESC LIMIT 1) as last_restocked_at,
                                (SELECT COALESCE(SUM(quantity), 0) FROM inventory_transactions WHERE product_id = p.id AND reference_type = 'restock') as total_restocked_qty,
                                (SELECT COALESCE(SUM(ii.quantity), 0) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE ii.product_id = p.id AND inv.status != 'cancelled') as total_sold_qty,
                                (SELECT COALESCE(SUM(ii.total_amount), 0) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE ii.product_id = p.id AND inv.status != 'cancelled') as total_sales_revenue,
                                (SELECT COALESCE(SUM(it.quantity), 0) FROM inventory_transactions it WHERE it.product_id = p.id AND it.transaction_type = 'stock_in' AND it.reference_type IN ('opening_stock', 'restock', 'purchase')) as total_purchased_qty 
                                FROM `products` p 
                                LEFT JOIN `categories` c ON p.category_id = c.id 
                                LEFT JOIN `subcategories` s ON p.subcategory_id = s.id 
                                LEFT JOIN `suppliers` sup ON p.supplier_id = sup.id 
                                ORDER BY p.id DESC");
        $suppliers = $db->query("SELECT s.*, 
                                 (SELECT COUNT(*) FROM `products` p WHERE p.supplier_id = s.id) as product_count,
                                 sp.paid_amount,
                                 sp.payment_status as saved_payment_status,
                                 sp.payment_method
                                 FROM `suppliers` s 
                                 LEFT JOIN `supplier_payments` sp ON s.id = sp.supplier_id
                                 ORDER BY s.id DESC");
        $suppModel = new Supplier();
        $supplierInvoices = $suppModel->getSupplierInvoiceList();

        $categories = (new Category())->where("status = 'active'", [], 'name ASC');
        $subcategories = (new SubCategory())->where("status = 'active'", [], 'name ASC');

        Response::json([
            'success' => true,
            'products' => $products,
            'suppliers' => $suppliers,
            'invoices' => $supplierInvoices,
            'supplier_invoices' => $supplierInvoices,
            'categories' => $categories,
            'subcategories' => $subcategories
        ]);
    }

    public function saveDetail(Request $request): void {
        $this->requirePermission('products.create');

        $supplierId = (int)$request->input('prod_supplier_id', 0);
        if ($supplierId <= 0) {
            Response::error('Supplier is required. Please select an existing supplier.');
        }

        $suppModel = new Supplier();
        $supplier = $suppModel->find($supplierId);
        if (!$supplier) {
            Response::error('Selected supplier not found.');
        }

        $prodNames = $request->input('ap_product_name', []);
        if (empty($prodNames) || !is_array($prodNames)) {
            Response::error('At least one product entry is required.');
        }

        $db = Database::getInstance();
        $prodModel = new Product();
        $catModel = new Category();
        $subModel = new SubCategory();

        $prodCats = $request->input('ap_product_category', []);
        $prodSubcats = $request->input('ap_product_subcategory', []);
        $prodUnits = $request->input('ap_unit', []);
        $prodRates = $request->input('ap_piece_rate', []);
        $prodPurchases = $request->input('ap_purchase_price', []);
        $prodMargins = $request->input('ap_margin_percentage', []);
        $prodSellings = $request->input('ap_selling_price', []);
        $prodGsts = $request->input('ap_gst', []);
        $prodBarcodes = $request->input('ap_barcode', []);
        $prodIds = $request->input('ap_product_id', []);

        $prodStatus = $request->input('prod_status', 'Active');
        $statusVal = strtolower((string)$prodStatus) === 'inactive' ? 'inactive' : 'active';

        $db->beginTransaction();

        try {
            $savedProducts = [];

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

                // Selling price calculation based on dynamic margin percentage
                $rawMargin = isset($prodMargins[$i]) ? trim((string)$prodMargins[$i]) : '';
                $effectiveMargin = 60.0;
                $hasMarginInput = false;

                if ($rawMargin !== '') {
                    $hasMarginInput = true;
                    $parsedMargin = (float)$rawMargin;
                    if ($parsedMargin < 0) {
                        $parsedMargin = 60.0;
                    }
                    $effectiveMargin = $parsedMargin;
                }

                $sellingPrice = (float)($prodSellings[$i] ?? 0);
                if ($pieceRate > 0) {
                    $sellingPrice = round($pieceRate + ($pieceRate * ($effectiveMargin / 100.0)), 2);
                } else if ($sellingPrice <= 0 && $purchasePrice > 0) {
                    $sellingPrice = round($purchasePrice + ($purchasePrice * ($effectiveMargin / 100.0)), 2);
                }

                $gst = max(0, (float)($prodGsts[$i] ?? 0));
                $barcode = $prodModel->generateUniqueBarcode($prodBarcodes[$i] ?? null);
                $singleEditId = (int)$request->input('product_id', 0);
                $pId = !empty($prodIds[$i]) ? (int)$prodIds[$i] : ($i === 0 ? $singleEditId : 0);

                $existingProd = null;
                if ($pId > 0) {
                    $existingProd = $prodModel->find($pId);
                }
                if (!$existingProd) {
                    $existingProd = $prodModel->findForSupplierByName($supplierId, $pName);
                }

                if ($existingProd) {
                    $savedProdId = (int)$existingProd['id'];
                    $sku = $existingProd['sku'];
                    $barcode = $existingProd['barcode'];

                    $prevStock = (int)($existingProd['current_stock'] ?? 0);
                    if ($pId > 0) {
                        $newStock = $units;
                        $diffQty = $newStock - $prevStock;
                        $isRestock = false;
                    } else {
                        $newStock = $prevStock + $units;
                        $diffQty = $units;
                        $isRestock = true;
                    }

                    $effectiveStatus = $statusVal === 'inactive' ? 'inactive' : ($newStock > 0 ? 'active' : 'out_of_stock');

                    $marginToSave = $effectiveMargin;
                    if (!$hasMarginInput) {
                        if (isset($existingProd['margin_percentage']) && $existingProd['margin_percentage'] !== null) {
                            $marginToSave = (float)$existingProd['margin_percentage'];
                        }
                        if ($pieceRate <= 0 && isset($existingProd['selling_price'])) {
                            $sellingPrice = (float)$existingProd['selling_price'];
                        }
                    }

                    $prodModel->update($savedProdId, [
                        'supplier_id' => $supplierId,
                        'name' => $pName,
                        'category_id' => $catId,
                        'subcategory_id' => $subId,
                        'purchase_price' => $purchasePrice,
                        'piece_rate' => $pieceRate,
                        'margin_percentage' => $marginToSave,
                        'selling_price' => $sellingPrice,
                        'gst_percent' => $gst,
                        'current_stock' => $newStock,
                        'status' => $effectiveStatus,
                        'updated_by' => Auth::id() ?: 1
                    ]);

                    if ($diffQty > 0) {
                        $invModel = new Inventory();
                        $invModel->create([
                            'product_id' => $savedProdId,
                            'transaction_type' => 'stock_in',
                            'reference_type' => $isRestock ? 'restock' : 'stock_adjustment',
                            'reference_no' => ($isRestock ? 'RESTOCK-' : 'ADJ-') . ($sku ?: $savedProdId),
                            'previous_stock' => $prevStock,
                            'quantity' => $diffQty,
                            'new_stock' => $newStock,
                            'notes' => $isRestock 
                                ? "Restocked {$diffQty} units from supplier {$supplier['name']}" 
                                : "Stock updated in Product Details",
                            'user_id' => Auth::id() ?: 1
                        ]);
                    }
                } else {
                    $sku = $prodModel->generateUniqueSku();
                    $supplyDateInput = $request->input('prod_supply_date');
                    $supplyTimestamp = !empty($supplyDateInput) ? date('Y-m-d H:i:s', strtotime($supplyDateInput)) : date('Y-m-d H:i:s');
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
                        'margin_percentage' => $effectiveMargin,
                        'selling_price' => $sellingPrice,
                        'gst_percent' => $gst,
                        'current_stock' => $units,
                        'status' => $units > 0 ? $statusVal : 'out_of_stock',
                        'created_by' => Auth::id() ?: 1,
                        'created_at' => $supplyTimestamp
                    ]);

                    if ($units > 0) {
                        $invModel = new Inventory();
                        $invModel->create([
                            'product_id' => $savedProdId,
                            'transaction_type' => 'stock_in',
                            'reference_type' => 'opening_stock',
                            'reference_no' => 'INIT-' . $sku,
                            'previous_stock' => 0,
                            'quantity' => $units,
                            'new_stock' => $units,
                            'notes' => 'Product Details entry opening stock',
                            'user_id' => Auth::id() ?: 1
                        ]);
                    }
                }

                $savedProducts[] = $prodModel->getWithDetails($savedProdId);
            }

            if (empty($savedProducts)) {
                throw new \Exception('No valid product data provided.');
            }

            // Recalculate total purchase amount from all products of this supplier
            $allSuppProducts = $suppModel->getProducts($supplierId);
            $totalPurchaseAmount = 0.00;
            foreach ($allSuppProducts as $sp) {
                $u = (int)($sp['current_stock'] ?? 0);
                $r = (float)($sp['piece_rate'] ?? 0);
                $stored = (float)($sp['purchase_price'] ?? 0);
                $totalPurchaseAmount += ($stored > 0 ? $stored : ($u * $r));
            }
            $totalPurchaseAmount = round($totalPurchaseAmount, 2);

            // Fetch existing payment record
            $curPayRows = $db->query("SELECT * FROM `supplier_payments` WHERE supplier_id = ? LIMIT 1", [$supplierId]);
            $previouslyPaid = !empty($curPayRows) ? (float)$curPayRows[0]['paid_amount'] : 0.00;
            $prevMethod = !empty($curPayRows) ? $curPayRows[0]['payment_method'] : 'Cash';

            // Check if user entered an additional payment in Add Products Detail
            $prodCashAmt = max(0, (float)$request->input('prod_payment_amount_cash', 0));
            $prodUpiAmt = max(0, (float)$request->input('prod_payment_amount_upi', 0));
            $prodBankAmt = max(0, (float)$request->input('prod_payment_amount_bank', 0));
            $prodCreditAmt = max(0, (float)$request->input('prod_payment_amount_credit', 0));
            $prodMethodSum = round($prodCashAmt + $prodUpiAmt + $prodBankAmt + $prodCreditAmt, 2);

            $rawAddPaid = $request->input('prod_total_amount_paid') !== null
                ? $request->input('prod_total_amount_paid')
                : ($request->input('prod_total_payment_entered') !== null ? $request->input('prod_total_payment_entered') : 0);
            $additionalPaid = max(0.00, (float)$rawAddPaid);
            if ($prodMethodSum > 0 && ($additionalPaid <= 0 || abs($additionalPaid - $prodMethodSum) < 0.01)) {
                $additionalPaid = $prodMethodSum;
            }

            $newPaid = round($previouslyPaid + $additionalPaid, 2);
            if ($totalPurchaseAmount > 0 && $newPaid > $totalPurchaseAmount) {
                $newPaid = $totalPurchaseAmount;
            }

            if ($totalPurchaseAmount <= 0) {
                $newStatus = 'Unpaid';
                $newBalance = 0.00;
                $newPaid = 0.00;
            } elseif ($newPaid >= $totalPurchaseAmount) {
                $newStatus = 'Paid';
                $newBalance = 0.00;
            } elseif ($newPaid > 0) {
                $newStatus = 'Partially Paid';
                $newBalance = max(0.00, round($totalPurchaseAmount - $newPaid, 2));
            } else {
                $newStatus = 'Unpaid';
                $newBalance = $totalPurchaseAmount;
            }

            // Update supplier_payments table
            $db->query("INSERT INTO `supplier_payments` 
                        (`supplier_id`, `paid_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`)
                        VALUES (?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                            `paid_amount` = VALUES(`paid_amount`),
                            `payment_status` = VALUES(`payment_status`),
                            `updated_at` = CURRENT_TIMESTAMP",
                       [$supplierId, $newPaid, $newStatus, $prevMethod, date('Y-m-d'), 'Product addition payment sync']);

            if ($additionalPaid > 0) {
                $db->query("INSERT INTO `supplier_payment_history` 
                            (`supplier_id`, `amount_paid`, `total_amount`, `balance_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`, `created_by`)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                           [$supplierId, $additionalPaid, $totalPurchaseAmount, $newBalance, $newStatus, $prevMethod, date('Y-m-d'), 'Additional payment on product addition', Auth::id() ?: 1]);
            }

            // Update suppliers table outstanding_balance
            $suppModel->update($supplierId, ['outstanding_balance' => $newBalance]);

            $db->commit();
            $this->logActivity('create', 'products', $savedProducts[0]['id'], $savedProducts[0]['sku'], "Added " . count($savedProducts) . " product(s) under supplier {$supplier['name']}");

            Response::success('Product(s) added successfully!', [
                'products' => $savedProducts,
                'supplier_id' => $supplierId
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error('Failed to save products: ' . $e->getMessage());
        }
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

    public function template(Request $request): void {
        $this->requirePermission('products.import');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=god_statues_import_template.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Code', 'SKU', 'Barcode', 'Category ID', 'Product Name', 'God/Deity', 'Material', 'Height (in)', 'Weight (kg)', 'Finish', 'Selling Price', 'Wholesale Price', 'Stock', 'Status (Active/Inactive)']);
        // Add sample row
        fputcsv($output, ['PRD-GAN-001', 'SKU-GAN-BRS-18', '8901234567890', '1', 'Antique Brass Royal Ganesha 18 Inch', 'Ganesha', 'Brass (Antique)', '18.00', '12.50', 'Antique', '18900.00', '15000.00', '4', 'Active']);
        fclose($output);
        exit;
    }

    public function import(Request $request): void {
        $this->requirePermission('products.import');
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please select a valid CSV file.');
            header('Location: ' . url('products'));
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        if (!$handle) {
            Session::flash('error', 'Failed to read the file.');
            header('Location: ' . url('products'));
            exit;
        }

        $header = fgetcsv($handle); // Read header row
        
        $db = Database::getInstance();
        $db->beginTransaction();
        
        $productModel = new Product();
        $catModel = new Category();

        try {
            $count = 0;
            $rowNum = 1; // Header is row 1
            $seenSkus = [];
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rowNum++;
                // Check if row is empty
                if (empty(array_filter($data))) continue;
                
                // Map columns:
                // 0: Code, 1: SKU, 2: Barcode, 3: Category ID, 4: Name, 5: Deity, 6: Material, 7: Height, 8: Weight, 9: Finish, 10: Selling, 11: Wholesale, 12: Stock, 13: Status
                
                $sku = trim($data[1] ?? '');
                $providedCategoryId = trim($data[3] ?? '');
                $name = trim($data[4] ?? '');
                
                if (empty($sku)) {
                    throw new \Exception("Row {$rowNum}: SKU is required but missing.");
                }
                if (empty($name)) {
                    throw new \Exception("Row {$rowNum}: Product Name is required but missing.");
                }
                
                if (in_array($sku, $seenSkus)) {
                    throw new \Exception("Row {$rowNum}: Duplicate SKU '{$sku}' found within the CSV file itself.");
                }
                $seenSkus[] = $sku;
                
                // Determine category_id
                $categoryId = null;
                if (!empty($providedCategoryId) && is_numeric($providedCategoryId)) {
                    $categoryId = (int)$providedCategoryId;
                } else {
                    // Fallback: Try to find category by Deity Name
                    $deityName = trim($data[5] ?? '');
                    if (!empty($deityName)) {
                        $catResult = $db->query("SELECT id FROM categories WHERE name LIKE ?", ["%$deityName%"]);
                        if (!empty($catResult)) {
                            $categoryId = $catResult[0]['id'];
                        }
                    }
                }
                
                // Validate Category ID
                if (empty($categoryId)) {
                    throw new \Exception("Row {$rowNum}: Category is required. Invalid or missing Category ID for SKU '{$sku}'.");
                }
                
                $productData = [
                    'code' => trim($data[0] ?? ''),
                    'sku' => $sku,
                    'barcode' => trim($data[2] ?? ''),
                    'name' => $name,
                    'category_id' => $categoryId,
                    'god_name' => trim($data[5] ?? ''),
                    'material' => trim($data[6] ?? ''),
                    'height' => (float)($data[7] ?? 0),
                    'weight' => (float)($data[8] ?? 0),
                    'finish_type' => trim($data[9] ?? ''),
                    'selling_price' => (float)($data[10] ?? 0),
                    'wholesale_price' => (float)($data[11] ?? 0),
                    'current_stock' => (int)($data[12] ?? 0),
                    'status' => strtolower(trim($data[13] ?? '')) === 'inactive' ? 'inactive' : 'active'
                ];
                
                $existing = $productModel->findBySKU($sku);
                if ($existing) {
                    throw new \Exception("Row {$rowNum}: Product with SKU '{$sku}' already exists in the database. Duplicate imports are not allowed.");
                } else {
                    $productModel->create($productData);
                    $count++;
                }
            }
            
            fclose($handle);
            $db->commit();
            
            Session::flash('success', "Successfully imported $count new products.");
        } catch (\Exception $e) {
            $db->rollback();
            Session::flash('error', 'Import failed: ' . $e->getMessage());
        }
        
        header('Location: ' . url('products'));
        exit;
    }
}
