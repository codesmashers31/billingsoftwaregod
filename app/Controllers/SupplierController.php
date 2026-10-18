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
            $prodMargins = $request->input('sp_margin_percentage', []);
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

                    // Dynamic Margin Percentage & Selling Price Calculation
                    // Formula: Selling Price = One Piece Rate + (One Piece Rate × Margin Percentage / 100)
                    $rawMargin = isset($prodMargins[$i]) ? trim((string)$prodMargins[$i]) : '';
                    $submittedSelling = (float)($prodSellings[$i] ?? 0);
                    $pId = !empty($prodIds[$i]) ? (int)$prodIds[$i] : 0;

                    $effectiveMargin = 60.00;
                    $sellingPrice = 0.00;

                    if ($rawMargin !== '') {
                        if (!is_numeric($rawMargin) || (float)$rawMargin < 0) {
                            throw new \Exception("Margin percentage must be a non-negative number for product: {$pName}");
                        }
                        $effectiveMargin = round((float)$rawMargin, 2);
                        $sellingPrice = round($pieceRate + ($pieceRate * ($effectiveMargin / 100.0)), 2);
                    } else {
                        // Empty margin input -> Default 60%
                        if ($pId > 0 && ($existingProd = $prodModel->find($pId)) && ($existingProd['margin_percentage'] === null || $existingProd['margin_percentage'] === '')) {
                            // Historical product without saved margin: preserve current selling price if piece rate was not changed
                            if ($pieceRate > 0 && $pieceRate == (float)$existingProd['piece_rate'] && $submittedSelling > 0) {
                                $sellingPrice = $submittedSelling;
                                $effectiveMargin = null;
                            } else {
                                $effectiveMargin = 60.00;
                                $sellingPrice = round($pieceRate + ($pieceRate * 0.60), 2);
                            }
                        } else {
                            $effectiveMargin = 60.00;
                            $sellingPrice = round($pieceRate + ($pieceRate * 0.60), 2);
                        }
                    }

                    if ($sellingPrice <= 0 && $purchasePrice > 0) {
                        $sellingPrice = round($purchasePrice + ($purchasePrice * (($effectiveMargin ?? 60.00) / 100.0)), 2);
                    }

                    $gst = max(0, (float)($prodGsts[$i] ?? 0));
                    $barcode = $prodModel->generateUniqueBarcode($prodBarcodes[$i] ?? null);

                    $submittedId = $pId > 0 ? $pId : 0;
                    $existingProd = null;

                    if ($submittedId > 0) {
                        $existingProd = $prodModel->find($submittedId);
                    }
                    if (!$existingProd) {
                        $existingProd = $prodModel->findForSupplierByName($supplierId, $pName);
                    }

                    if ($existingProd) {
                        $savedProdId = (int)$existingProd['id'];
                        $sku = $existingProd['sku'];
                        $barcode = $existingProd['barcode'];

                        $prevStock = (int)($existingProd['current_stock'] ?? 0);
                        if ($submittedId > 0) {
                            $newStock = $units;
                            $diffQty = $newStock - $prevStock;
                        } else {
                            $newStock = $prevStock + $units;
                            $diffQty = $units;
                        }

                        $newStatus = $newStock > 0 ? 'active' : 'out_of_stock';

                        $prodModel->update($savedProdId, [
                            'supplier_id' => $supplierId,
                            'name' => $pName,
                            'category_id' => $catId,
                            'subcategory_id' => $subId,
                            'purchase_price' => $purchasePrice,
                            'piece_rate' => $pieceRate,
                            'margin_percentage' => $effectiveMargin,
                            'selling_price' => $sellingPrice,
                            'gst_percent' => $gst,
                            'current_stock' => $newStock,
                            'status' => $newStatus,
                            'updated_by' => \App\Core\Auth::id() ?: 1
                        ]);

                        if ($diffQty > 0) {
                            $invModel = new \App\Models\Inventory();
                            $invModel->create([
                                'product_id' => $savedProdId,
                                'transaction_type' => 'stock_in',
                                'reference_type' => 'restock',
                                'reference_no' => 'RESTOCK-' . ($sku ?: $savedProdId),
                                'previous_stock' => $prevStock,
                                'quantity' => $diffQty,
                                'new_stock' => $newStock,
                                'notes' => "Restocked {$diffQty} units from supplier {$name}",
                                'user_id' => \App\Core\Auth::id() ?: 1
                            ]);
                        }
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
                            'margin_percentage' => $effectiveMargin,
                            'selling_price' => $sellingPrice,
                            'gst_percent' => $gst,
                            'current_stock' => $units,
                            'status' => $units > 0 ? 'active' : 'out_of_stock',
                            'created_by' => \App\Core\Auth::id() ?: 1
                        ]);

                        if ($units > 0) {
                            $invModel = new \App\Models\Inventory();
                            $invModel->create([
                                'product_id' => $savedProdId,
                                'transaction_type' => 'stock_in',
                                'reference_type' => 'opening_stock',
                                'reference_no' => 'INIT-' . $sku,
                                'previous_stock' => 0,
                                'quantity' => $units,
                                'new_stock' => $units,
                                'notes' => "Initial stock from supplier {$name}",
                                'user_id' => \App\Core\Auth::id() ?: 1
                            ]);
                        }
                    }

                    $savedProducts[] = $prodModel->getWithDetails($savedProdId);
                }
            }

            // Calculate total purchase amount from all products of this supplier
            $allSuppProducts = $suppModel->getProducts($supplierId);
            $totalPurchaseAmount = 0.00;
            foreach ($allSuppProducts as $sp) {
                $u = (int)($sp['current_stock'] ?? 0);
                $r = (float)($sp['piece_rate'] ?? 0);
                $stored = (float)($sp['purchase_price'] ?? 0);
                $totalPurchaseAmount += ($stored > 0 ? $stored : ($u * $r));
            }
            $totalPurchaseAmount = round($totalPurchaseAmount, 2);

            // Read payment information from Section 3
            $cashAmt = max(0, (float)$request->input('payment_amount_cash', 0));
            $upiAmt = max(0, (float)$request->input('payment_amount_upi', 0));
            $bankAmt = max(0, (float)$request->input('payment_amount_bank', 0));
            $creditAmt = max(0, (float)$request->input('payment_amount_credit', 0));
            $methodSum = round($cashAmt + $upiAmt + $bankAmt + $creditAmt, 2);

            $rawPaidInput = $request->input('total_amount_paid') !== null 
                ? $request->input('total_amount_paid') 
                : ($request->input('total_payment_entered') !== null ? $request->input('total_payment_entered') : 0);
            $inputPaid = (float)$rawPaidInput;
            if ($methodSum > 0 && ($inputPaid <= 0 || abs($inputPaid - $methodSum) < 0.01)) {
                $inputPaid = $methodSum;
            }

            // Validate
            if ($inputPaid < 0) {
                throw new \Exception('Total amount paid cannot be negative.');
            }
            if ($totalPurchaseAmount > 0 && $inputPaid > $totalPurchaseAmount) {
                throw new \Exception("Total amount paid (₹{$inputPaid}) cannot exceed total purchase amount (₹{$totalPurchaseAmount}).");
            }

            // Derive payment status
            if ($totalPurchaseAmount <= 0) {
                $paymentStatus = 'Unpaid'; // Zero amount condition: do not mark as Paid
                $paidAmount = 0.00;
                $balanceAmount = 0.00;
            } elseif ($inputPaid >= $totalPurchaseAmount) {
                $paymentStatus = 'Paid';
                $paidAmount = $totalPurchaseAmount;
                $balanceAmount = 0.00;
            } elseif ($inputPaid > 0) {
                $paymentStatus = 'Partially Paid';
                $paidAmount = round($inputPaid, 2);
                $balanceAmount = max(0.00, round($totalPurchaseAmount - $paidAmount, 2));
            } else {
                $paymentStatus = 'Unpaid';
                $paidAmount = 0.00;
                $balanceAmount = $totalPurchaseAmount;
            }

            // Selected payment methods
            $rawMethods = $request->input('payment_methods', []);
            if (is_array($rawMethods)) {
                $rawMethods = array_values(array_filter($rawMethods));
                $methodString = !empty($rawMethods) ? implode(', ', $rawMethods) : ($paidAmount > 0 ? 'Cash' : 'Unpaid');
            } else {
                $methodString = trim((string)$rawMethods) ?: ($paidAmount > 0 ? 'Cash' : 'Unpaid');
            }

            $breakdownParts = [];
            if ($cashAmt > 0) $breakdownParts[] = "Cash: ₹" . number_format($cashAmt, 2);
            if ($upiAmt > 0) $breakdownParts[] = "UPI: ₹" . number_format($upiAmt, 2);
            if ($bankAmt > 0) $breakdownParts[] = "Bank Transfer: ₹" . number_format($bankAmt, 2);
            if ($creditAmt > 0) $breakdownParts[] = "Credit: ₹" . number_format($creditAmt, 2);
            $notesDetail = !empty($breakdownParts) ? implode(', ', $breakdownParts) : '';
            $payNotes = $notesDetail ? "Supplier registration payment ({$notesDetail})" : 'Supplier registration payment';
            $syncNotes = $notesDetail ? "Supplier creation / product sync ({$notesDetail})" : 'Supplier creation / product sync';

            // 1. Upsert into supplier_payments
            $upsertPaySql = "INSERT INTO `supplier_payments` 
                             (`supplier_id`, `paid_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`)
                             VALUES (?, ?, ?, ?, ?, ?)
                             ON DUPLICATE KEY UPDATE 
                                 `paid_amount` = VALUES(`paid_amount`),
                                 `payment_status` = VALUES(`payment_status`),
                                 `payment_method` = VALUES(`payment_method`),
                                 `payment_date` = VALUES(`payment_date`),
                                 `notes` = VALUES(`notes`),
                                 `updated_at` = CURRENT_TIMESTAMP";
            $db->query($upsertPaySql, [$supplierId, $paidAmount, $paymentStatus, $methodString, date('Y-m-d'), $syncNotes]);

            // 2. Insert into supplier_payment_history
            $histSql = "INSERT INTO `supplier_payment_history` 
                        (`supplier_id`, `amount_paid`, `total_amount`, `balance_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`, `created_by`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->query($histSql, [$supplierId, $paidAmount, $totalPurchaseAmount, $balanceAmount, $paymentStatus, $methodString, date('Y-m-d'), $payNotes, \App\Core\Auth::id() ?: 1]);

            // 3. Update suppliers table outstanding_balance
            $suppModel->update($supplierId, ['outstanding_balance' => $balanceAmount]);

            $db->commit();
            $this->logActivity($isEdit ? 'update' : 'create', 'suppliers', $supplierId, $code, ($isEdit ? 'Updated' : 'Added') . " supplier/artisan {$name} with " . count($savedProducts) . " products. Status: {$paymentStatus}");

            $savedSupplier = $suppModel->find($supplierId);
            $savedSupplier['product_count'] = count($suppModel->getProducts($supplierId));
            $savedSupplier['total_amount'] = $totalPurchaseAmount;
            $savedSupplier['paid_amount'] = $paidAmount;
            $savedSupplier['balance_amount'] = $balanceAmount;
            $savedSupplier['payment_status'] = $paymentStatus;

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
        $db->beginTransaction();
        try {
            // Remove associated products and purchases
            $db->query("DELETE FROM `products` WHERE supplier_id = ?", [$id]);
            $db->query("DELETE FROM `purchases` WHERE supplier_id = ?", [$id]);
            $db->query("DELETE FROM `supplier_payment_history` WHERE supplier_id = ?", [$id]);
            $db->query("DELETE FROM `supplier_payments` WHERE supplier_id = ?", [$id]);
            $suppModel->delete($id);
            $db->commit();

            $this->logActivity('delete', 'suppliers', $id, $supp['supplier_code'], "Deleted supplier {$supp['name']}");
            Response::success('Supplier deleted successfully.');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error('Failed to delete supplier: ' . $e->getMessage());
        }
    }
}
