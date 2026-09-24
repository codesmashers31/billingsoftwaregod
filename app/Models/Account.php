<?php
namespace App\Models;

use App\Core\Model;
use Exception;

class Account extends Model {
    protected string $table = 'accounts';
    protected array $fillable = [
        'account_code', 'name', 'type', 'bank_name', 'account_number', 
        'ifsc_code', 'opening_balance', 'current_balance', 'status'
    ];

    /**
     * Post a debit or credit entry to an account with updated balance.
     */
    public function recordTransaction(int $accountId, string $type, float $amount, string $refType, ?int $refId, ?string $refNo, string $description, string $date, int $userId = 1): bool {
        $account = $this->find($accountId);
        if (!$account) {
            throw new Exception("Account ID {$accountId} not found.");
        }

        $currentBal = (float)$account['current_balance'];
        if ($type === 'debit') {
            // For Cash/Bank assets, debit increases balance (receipt)
            // For general assets, debit increases; for liabilities/income debit decreases
            $newBal = in_array($account['type'], ['cash', 'bank', 'asset', 'expense']) ? $currentBal + $amount : $currentBal - $amount;
        } else {
            // Credit
            $newBal = in_array($account['type'], ['cash', 'bank', 'asset', 'expense']) ? $currentBal - $amount : $currentBal + $amount;
        }

        // Insert transaction record
        $sql = "INSERT INTO `account_transactions` (`account_id`, `transaction_type`, `amount`, `balance_after`, `reference_type`, `reference_id`, `reference_no`, `description`, `transaction_date`, `created_by`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $accountId, $type, $amount, $newBal, $refType, $refId, $refNo, $description, $date, $userId
        ]);

        // Update account balance
        $this->update($accountId, ['current_balance' => $newBal]);
        return true;
    }

    public function getTransactions(int $accountId, int $limit = 50): array {
        $sql = "SELECT at.*, u.name as user_name 
                FROM `account_transactions` at 
                LEFT JOIN `users` u ON at.created_by = u.id 
                WHERE at.account_id = ? 
                ORDER BY at.id DESC 
                LIMIT {$limit}";
        return $this->db->query($sql, [$accountId]);
    }
}
