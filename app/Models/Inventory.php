<?php
namespace App\Models;

use App\Core\Model;
use Exception;

class Inventory extends Model {
    protected string $table = 'inventory_transactions';
    protected array $fillable = [
        'product_id', 'transaction_type', 'reference_type', 'reference_id', 
        'reference_no', 'previous_stock', 'quantity', 'new_stock', 'notes', 'user_id'
    ];

    /**
     * Record inventory transaction and atomically update product stock.
     */
    public function adjustStock(int $productId, int $qtyChange, string $txType, ?string $refType = null, ?int $refId = null, ?string $refNo = null, ?string $notes = null, int $userId = 1): bool {
        // Fetch current stock
        $prodModel = new Product();
        $product = $prodModel->find($productId);
        if (!$product) {
            throw new Exception("Product ID {$productId} not found.");
        }

        $prevStock = (int)$product['current_stock'];
        $newStock = $prevStock + $qtyChange;

        if ($newStock < 0) {
            throw new Exception("Insufficient stock for {$product['name']}. Available: {$prevStock}, Requested deduction: " . abs($qtyChange));
        }

        // Insert inventory transaction
        $this->create([
            'product_id' => $productId,
            'transaction_type' => $txType,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'reference_no' => $refNo,
            'previous_stock' => $prevStock,
            'quantity' => $qtyChange,
            'new_stock' => $newStock,
            'notes' => $notes,
            'user_id' => $userId
        ]);

        // Update product stock and status
        $status = $newStock <= 0 ? 'out_of_stock' : ($product['status'] === 'out_of_stock' ? 'active' : $product['status']);
        $prodModel->update($productId, [
            'current_stock' => $newStock,
            'status' => $status
        ]);

        return true;
    }

    public function getProductHistory(int $productId, int $limit = 50): array {
        $sql = "SELECT it.*, u.name as user_name 
                FROM `inventory_transactions` it 
                LEFT JOIN `users` u ON it.user_id = u.id 
                WHERE it.product_id = ? 
                ORDER BY it.id DESC 
                LIMIT {$limit}";
        return $this->db->query($sql, [$productId]);
    }

    public function getRecentTransactions(int $limit = 20): array {
        $sql = "SELECT it.*, p.name as product_name, p.code as product_code, p.sku, u.name as user_name 
                FROM `inventory_transactions` it 
                JOIN `products` p ON it.product_id = p.id 
                LEFT JOIN `users` u ON it.user_id = u.id 
                ORDER BY it.id DESC 
                LIMIT {$limit}";
        return $this->db->query($sql);
    }
}
