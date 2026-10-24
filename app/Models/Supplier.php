<?php
namespace App\Models;

use App\Core\Model;

class Supplier extends Model {
    protected string $table = 'suppliers';
    protected array $fillable = [
        'supplier_code', 'name', 'company_name', 'mobile', 'email', 
        'address', 'city', 'state', 'gst_number', 'bank_name', 
        'bank_account_no', 'bank_ifsc', 'outstanding_balance', 'status'
    ];

    public function getPurchases(int $supplierId, int $limit = 20): array {
        $sql = "SELECT * FROM `purchases` WHERE supplier_id = ? ORDER BY id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$supplierId]);
    }

    public function getPayments(int $supplierId, int $limit = 20): array {
        $sql = "SELECT p.*, a.name as account_name FROM `payments` p 
                LEFT JOIN `accounts` a ON p.account_id = a.id 
                WHERE p.supplier_id = ? ORDER BY p.id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$supplierId]);
    }

    public function getProducts(int $supplierId): array {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM `products` p 
                LEFT JOIN `categories` c ON p.category_id = c.id 
                LEFT JOIN `subcategories` s ON p.subcategory_id = s.id 
                WHERE p.supplier_id = ? 
                ORDER BY p.id DESC";
        return $this->db->query($sql, [$supplierId]);
    }

    /**
     * Get all suppliers with dynamically calculated product purchase totals and payment details.
     */
    public function getSupplierInvoiceList(?string $search = null, ?string $paymentStatus = null): array {
        $sql = "SELECT s.id, s.supplier_code, s.name, s.company_name, s.mobile, s.email, s.status,
                       sp.paid_amount, sp.payment_status as saved_payment_status, sp.payment_method, sp.payment_date, sp.notes
                FROM `suppliers` s
                LEFT JOIN `supplier_payments` sp ON s.id = sp.supplier_id
                ORDER BY s.id DESC";
        $suppliers = $this->db->query($sql);

        $results = [];
        $searchLower = $search !== null ? trim(mb_strtolower($search)) : '';
        $filterStatusLower = $paymentStatus !== null ? trim(mb_strtolower($paymentStatus)) : '';

        foreach ($suppliers as $supp) {
            $suppId = (int)$supp['id'];
            $products = $this->getProducts($suppId);

            $totalPurchaseAmount = 0.00;
            $productItems = [];
            foreach ($products as $p) {
                $piecesCount = (int)($p['current_stock'] ?? 0);
                $pieceRate = (float)($p['piece_rate'] ?? 0);
                $storedPurchase = (float)($p['purchase_price'] ?? 0);
                $itemTotal = $storedPurchase > 0 ? $storedPurchase : ($piecesCount * $pieceRate);
                $totalPurchaseAmount += $itemTotal;

                $productItems[] = [
                    'id' => (int)$p['id'],
                    'product_name' => $p['name'],
                    'pieces_count' => $piecesCount,
                    'piece_rate' => $pieceRate,
                    'total_purchase_price' => round($itemTotal, 2),
                    'code' => $p['code'] ?? '',
                    'sku' => $p['sku'] ?? '',
                    'category_name' => $p['category_name'] ?? 'General',
                ];
            }

            $totalPurchaseAmount = round($totalPurchaseAmount, 2);
            if ($totalPurchaseAmount <= 0) {
                $lastHist = $this->db->query("SELECT total_amount FROM `supplier_payment_history` WHERE supplier_id = ? ORDER BY id DESC LIMIT 1", [$suppId]);
                if (!empty($lastHist) && isset($lastHist[0]['total_amount']) && (float)$lastHist[0]['total_amount'] > 0) {
                    $totalPurchaseAmount = (float)$lastHist[0]['total_amount'];
                }
            }
            $rawPaid = isset($supp['paid_amount']) && $supp['paid_amount'] !== null ? (float)$supp['paid_amount'] : 0.00;
            $paidAmount = min($totalPurchaseAmount, max(0.00, round($rawPaid, 2)));
            $balanceAmount = max(0.00, round($totalPurchaseAmount - $paidAmount, 2));

            // Derive payment status
            if ($totalPurchaseAmount <= 0) {
                $status = 'Unpaid'; // Zero amount condition: do not mark as Paid
                $balanceAmount = 0.00;
                $paidAmount = 0.00;
            } elseif ($paidAmount >= $totalPurchaseAmount) {
                $status = 'Paid';
                $balanceAmount = 0.00;
            } elseif ($paidAmount <= 0) {
                $status = 'Unpaid';
                $balanceAmount = $totalPurchaseAmount;
            } else {
                $status = 'Partially Paid';
            }

            $item = [
                'id' => $suppId,
                'supplier_id' => $supp['supplier_code'] ?: ('SUP-' . str_pad((string)$suppId, 4, '0', STR_PAD_LEFT)),
                'supplier_code' => $supp['supplier_code'] ?: ('SUP-' . str_pad((string)$suppId, 4, '0', STR_PAD_LEFT)),
                'supplier_name' => $supp['name'],
                'company_name' => $supp['company_name'] ?? '',
                'mobile' => $supp['mobile'] ?? '',
                'amount' => $totalPurchaseAmount,
                'total_amount' => $totalPurchaseAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $status,
                'payment_method' => $supp['payment_method'] ?: 'Cash',
                'payment_date' => $supp['payment_date'] ?: date('Y-m-d'),
                'notes' => $supp['notes'] ?? '',
                'products' => $productItems,
                'product_count' => count($productItems)
            ];

            // Search filter
            if ($searchLower !== '') {
                $matchesCode = mb_stripos($item['supplier_id'], $searchLower) !== false;
                $matchesName = mb_stripos($item['supplier_name'], $searchLower) !== false;
                $matchesCompany = mb_stripos($item['company_name'], $searchLower) !== false;
                $matchesMobile = mb_stripos($item['mobile'], $searchLower) !== false;
                if (!$matchesCode && !$matchesName && !$matchesCompany && !$matchesMobile) {
                    continue;
                }
            }

            // Payment status filter
            if ($filterStatusLower !== '') {
                $statusClean = strtolower(str_replace(' ', '', $status));
                $filterClean = strtolower(str_replace(' ', '', $filterStatusLower));
                if ($statusClean !== $filterClean && strtolower($status) !== $filterStatusLower) {
                    continue;
                }
            }

            $results[] = $item;
        }

        return $results;
    }

    /**
     * Get single supplier invoice and purchase details.
     */
    public function getSupplierInvoiceWithDetails(int $supplierId): ?array {
        $supp = $this->find($supplierId);
        if (!$supp) return null;

        $products = $this->getProducts($supplierId);
        $totalPurchaseAmount = 0.00;
        $productItems = [];
        foreach ($products as $p) {
            $piecesCount = (int)($p['current_stock'] ?? 0);
            $pieceRate = (float)($p['piece_rate'] ?? 0);
            $storedPurchase = (float)($p['purchase_price'] ?? 0);
            $itemTotal = $storedPurchase > 0 ? $storedPurchase : ($piecesCount * $pieceRate);
            $totalPurchaseAmount += $itemTotal;

            $productItems[] = [
                'id' => (int)$p['id'],
                'product_name' => $p['name'],
                'pieces_count' => $piecesCount,
                'piece_rate' => $pieceRate,
                'total_purchase_price' => round($itemTotal, 2),
                'code' => $p['code'] ?? '',
                'sku' => $p['sku'] ?? '',
                'category_name' => $p['category_name'] ?? 'General',
            ];
        }

        $totalPurchaseAmount = round($totalPurchaseAmount, 2);

        $paySql = "SELECT * FROM `supplier_payments` WHERE supplier_id = ? LIMIT 1";
        $payRows = $this->db->query($paySql, [$supplierId]);
        $sp = !empty($payRows) ? $payRows[0] : null;

        $rawPaid = $sp ? (float)$sp['paid_amount'] : 0.00;
        $paidAmount = min($totalPurchaseAmount, max(0.00, round($rawPaid, 2)));
        $balanceAmount = max(0.00, round($totalPurchaseAmount - $paidAmount, 2));

        if ($totalPurchaseAmount <= 0) {
            $status = 'Unpaid'; // Zero amount condition: do not mark as Paid
            $balanceAmount = 0.00;
            $paidAmount = 0.00;
        } elseif ($paidAmount >= $totalPurchaseAmount) {
            $status = 'Paid';
            $balanceAmount = 0.00;
        } elseif ($paidAmount <= 0) {
            $status = 'Unpaid';
            $balanceAmount = $totalPurchaseAmount;
        } else {
            $status = 'Partially Paid';
        }

        return [
            'id' => (int)$supp['id'],
            'supplier_id' => $supp['supplier_code'] ?: ('SUP-' . str_pad((string)$supp['id'], 4, '0', STR_PAD_LEFT)),
            'supplier_code' => $supp['supplier_code'] ?: ('SUP-' . str_pad((string)$supp['id'], 4, '0', STR_PAD_LEFT)),
            'supplier_name' => $supp['name'],
            'company_name' => $supp['company_name'] ?? '',
            'mobile' => $supp['mobile'] ?? '',
            'email' => $supp['email'] ?? '',
            'address' => $supp['address'] ?? '',
            'city' => $supp['city'] ?? '',
            'state' => $supp['state'] ?? '',
            'gst_number' => $supp['gst_number'] ?? '',
            'amount' => $totalPurchaseAmount,
            'total_amount' => $totalPurchaseAmount,
            'paid_amount' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'payment_status' => $status,
            'payment_method' => $sp['payment_method'] ?? 'Cash',
            'payment_date' => $sp['payment_date'] ?? date('Y-m-d'),
            'notes' => $sp['notes'] ?? '',
            'products' => $productItems,
            'product_count' => count($productItems)
        ];
    }

    /**
     * Update supplier payment in MySQL with transaction.
     * Supports both new total paid amount or additional payment increment.
     */
    public function updateSupplierPayment(int $supplierId, float $inputPaid, string $inputStatus, string $method, ?string $date = null, ?string $notes = null, ?int $userId = null, float $additionalAmount = 0.00): array {
        $supp = $this->find($supplierId);
        if (!$supp) {
            throw new \Exception('Supplier not found.');
        }

        // Calculate live total purchase amount from supplier products
        $products = $this->getProducts($supplierId);
        $totalPurchaseAmount = 0.00;
        foreach ($products as $p) {
            $piecesCount = (int)($p['current_stock'] ?? 0);
            $pieceRate = (float)($p['piece_rate'] ?? 0);
            $storedPurchase = (float)($p['purchase_price'] ?? 0);
            $itemTotal = $storedPurchase > 0 ? $storedPurchase : ($piecesCount * $pieceRate);
            $totalPurchaseAmount += $itemTotal;
        }
        $totalPurchaseAmount = round($totalPurchaseAmount, 2);

        $normStatus = trim($inputStatus);
        $payDate = !empty($date) ? $date : date('Y-m-d');
        $payMethod = !empty($method) ? $method : 'Cash';

        // Check existing payment record in DB to get previously paid amount
        $curPayRows = $this->db->query("SELECT * FROM `supplier_payments` WHERE supplier_id = ? LIMIT 1", [$supplierId]);
        $previouslyPaid = !empty($curPayRows) ? (float)$curPayRows[0]['paid_amount'] : 0.00;

        $historyAmountPaid = 0.00;
        if ($additionalAmount > 0) {
            // Additional payment scenario
            $paidAmount = round($previouslyPaid + $additionalAmount, 2);
            $historyAmountPaid = $additionalAmount;
            if ($totalPurchaseAmount > 0 && $paidAmount > $totalPurchaseAmount) {
                throw new \Exception("Additional payment (₹{$additionalAmount}) makes total paid (₹{$paidAmount}) exceed total purchase amount (₹{$totalPurchaseAmount}).");
            }
            if ($totalPurchaseAmount <= 0) {
                $finalStatus = 'Unpaid';
                $balanceAmount = 0.00;
            } elseif ($paidAmount >= $totalPurchaseAmount) {
                $finalStatus = 'Paid';
                $paidAmount = $totalPurchaseAmount;
                $balanceAmount = 0.00;
            } else {
                $finalStatus = 'Partially Paid';
                $balanceAmount = max(0.00, round($totalPurchaseAmount - $paidAmount, 2));
            }
        } elseif (strcasecmp($normStatus, 'Paid') === 0) {
            $paidAmount = $totalPurchaseAmount;
            $balanceAmount = 0.00;
            $finalStatus = $totalPurchaseAmount > 0 ? 'Paid' : 'Unpaid';
            $historyAmountPaid = max(0.00, round($paidAmount - $previouslyPaid, 2));
        } elseif (strcasecmp($normStatus, 'Unpaid') === 0) {
            $paidAmount = 0.00;
            $balanceAmount = $totalPurchaseAmount;
            $finalStatus = 'Unpaid';
            $historyAmountPaid = 0.00;
        } else {
            // Partially Paid or derived from direct paid amount
            $paidAmount = round(max(0.00, $inputPaid), 2);
            if ($totalPurchaseAmount > 0 && $paidAmount > $totalPurchaseAmount) {
                throw new \Exception("Paid amount (₹{$paidAmount}) cannot exceed total purchase amount (₹{$totalPurchaseAmount}).");
            }
            $historyAmountPaid = $paidAmount >= $previouslyPaid ? round($paidAmount - $previouslyPaid, 2) : $paidAmount;
            if ($totalPurchaseAmount <= 0) {
                $finalStatus = 'Unpaid';
                $balanceAmount = 0.00;
                $paidAmount = 0.00;
            } elseif ($paidAmount >= $totalPurchaseAmount) {
                $finalStatus = 'Paid';
                $balanceAmount = 0.00;
            } elseif ($paidAmount <= 0) {
                $finalStatus = 'Unpaid';
                $balanceAmount = $totalPurchaseAmount;
            } else {
                $finalStatus = 'Partially Paid';
                $balanceAmount = max(0.00, round($totalPurchaseAmount - $paidAmount, 2));
            }
        }

        $this->db->beginTransaction();
        try {
            // 1. Upsert into supplier_payments
            $upsertSql = "INSERT INTO `supplier_payments` 
                          (`supplier_id`, `paid_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`)
                          VALUES (?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE 
                              `paid_amount` = VALUES(`paid_amount`),
                              `payment_status` = VALUES(`payment_status`),
                              `payment_method` = VALUES(`payment_method`),
                              `payment_date` = VALUES(`payment_date`),
                              `notes` = VALUES(`notes`),
                              `updated_at` = CURRENT_TIMESTAMP";
            $this->db->query($upsertSql, [$supplierId, $paidAmount, $finalStatus, $payMethod, $payDate, $notes]);

            // 2. Insert into supplier_payment_history
            $histAmount = $historyAmountPaid > 0 ? $historyAmountPaid : $paidAmount;
            $histSql = "INSERT INTO `supplier_payment_history` 
                        (`supplier_id`, `amount_paid`, `total_amount`, `balance_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`, `created_by`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($histSql, [$supplierId, $histAmount, $totalPurchaseAmount, $balanceAmount, $finalStatus, $payMethod, $payDate, $notes, $userId ?: 1]);

            // 3. Update suppliers table outstanding_balance
            $this->update($supplierId, ['outstanding_balance' => $balanceAmount]);

            $this->db->commit();

            return [
                'supplier_id' => $supp['supplier_code'] ?: ('SUP-' . str_pad((string)$supplierId, 4, '0', STR_PAD_LEFT)),
                'supplier_name' => $supp['name'],
                'total_amount' => $totalPurchaseAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $finalStatus,
                'payment_method' => $payMethod,
                'payment_date' => $payDate
            ];
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
