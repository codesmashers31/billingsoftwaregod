<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Expense;
use App\Models\Account;

class ExpenseController extends Controller {
    public function index(Request $request): void {
        $this->requirePermission('expenses.view');
        $expModel = new Expense();
        $accModel = new Account();
        $db = Database::getInstance();

        $page = (int)$request->input('page', 1);
        $filters = [
            'search' => (string)$request->input('search', ''),
            'category_id' => $request->input('category_id'),
            'status' => (string)$request->input('status', ''),
            'date_from' => (string)$request->input('date_from', ''),
            'date_to' => (string)$request->input('date_to', ''),
        ];

        $result = $expModel->getPaginatedList($page, 15, $filters);
        $categories = $db->query("SELECT * FROM `expense_categories` WHERE status = 'active' ORDER BY name ASC");
        $accounts = $accModel->where("status = 'active' AND type IN ('cash', 'bank')", [], 'name ASC');

        if ($request->isAjax() && $request->input('ajax_table')) {
            Response::json($result);
        }

        $this->render('expenses.index', [
            'pageTitle' => 'Expense Management & Vouchers',
            'expenses' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'accounts' => $accounts,
            'filters' => $filters
        ]);
    }

    public function store(Request $request): void {
        $this->requirePermission('expenses.create');
        $title = trim($request->input('title', ''));
        $categoryId = (int)$request->input('category_id');
        $accountId = (int)$request->input('account_id');
        $amount = (float)$request->input('amount');
        $date = $request->input('expense_date', date('Y-m-d'));
        $method = $request->input('payment_method', 'cash');
        $desc = trim($request->input('description', ''));

        if (empty($title) || $categoryId <= 0 || $accountId <= 0 || $amount <= 0) {
            Response::error('Please fill in all required expense fields.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $expModel = new Expense();
            $accModel = new Account();
            $expenseNo = $expModel->generateExpenseNo();

            // Auto-approve if user has approve permission, otherwise pending
            $canApprove = Auth::hasPermission('expenses.approve');
            $status = $canApprove ? 'approved' : 'pending';
            $approvedBy = $canApprove ? (Auth::id() ?: 1) : null;

            $expenseId = $expModel->create([
                'expense_no' => $expenseNo,
                'category_id' => $categoryId,
                'account_id' => $accountId,
                'title' => $title,
                'amount' => $amount,
                'payment_method' => $method,
                'expense_date' => $date,
                'description' => $desc,
                'approved_by' => $approvedBy,
                'user_id' => Auth::id() ?: 1,
                'status' => $status
            ]);

            // If approved, deduct from Cash/Bank account
            if ($status === 'approved') {
                $accModel->recordTransaction(
                    $accountId,
                    'credit', // Paying out reduces asset
                    $amount,
                    'expense',
                    (int)$expenseId,
                    $expenseNo,
                    "Expense voucher: {$title}",
                    $date,
                    Auth::id() ?: 1
                );
            }

            $db->commit();
            $this->logActivity('create', 'accounts', (int)$expenseId, $expenseNo, "Logged expense {$title} for " . formatCurrency($amount));

            Response::success('Expense voucher created successfully!', [
                'expense_id' => $expenseId,
                'redirect' => url('expenses')
            ]);
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }

    public function approve(Request $request, array $params): void {
        $this->requirePermission('expenses.approve');
        $id = (int)($params['id'] ?? 0);
        $expModel = new Expense();
        $expense = $expModel->find($id);

        if (!$expense) {
            Response::error('Expense not found.');
        }

        if ($expense['status'] === 'approved') {
            Response::error('Expense is already approved.');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $accModel = new Account();
            $expModel->update($id, [
                'status' => 'approved',
                'approved_by' => Auth::id() ?: 1
            ]);

            $accModel->recordTransaction(
                (int)$expense['account_id'],
                'credit',
                (float)$expense['amount'],
                'expense',
                $id,
                $expense['expense_no'],
                "Expense voucher approval: {$expense['title']}",
                $expense['expense_date'],
                Auth::id() ?: 1
            );

            $db->commit();
            $this->logActivity('approve', 'accounts', $id, $expense['expense_no'], "Approved expense voucher {$expense['expense_no']}");
            Response::success('Expense approved and payment ledger updated!');
        } catch (\Throwable $e) {
            $db->rollback();
            Response::error($e->getMessage());
        }
    }
}
