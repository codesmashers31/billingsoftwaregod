<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\RoleController;
use App\Controllers\DepartmentController;
use App\Controllers\CategoryController;
use App\Controllers\SubCategoryController;
use App\Controllers\ProductController;
use App\Controllers\InventoryController;
use App\Controllers\CustomerController;
use App\Controllers\SupplierController;
use App\Controllers\PurchaseController;
use App\Controllers\BillingController;
use App\Controllers\SalesReturnController;
use App\Controllers\AccountController;
use App\Controllers\ExpenseController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\AuditController;

// 1. Authentication Routes
Router::get('/', [DashboardController::class, 'index']);
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);
Router::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Router::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Router::get('/reset-password/{token}', [AuthController::class, 'showResetPassword']);
Router::post('/reset-password', [AuthController::class, 'processResetPassword']);

// 2. Dashboard
Router::get('/dashboard', [DashboardController::class, 'index']);

// 3. User Management
Router::get('/profile', [UserController::class, 'profile']);
Router::post('/profile', [UserController::class, 'updateProfile']);
Router::get('/users', [UserController::class, 'index']);
Router::post('/users/create', [UserController::class, 'store']);
Router::post('/users/edit/{id}', [UserController::class, 'update']);
Router::post('/users/toggle/{id}', [UserController::class, 'toggleStatus']);
Router::post('/users/delete/{id}', [UserController::class, 'delete']);
Router::get('/users/{id}/permissions', [UserController::class, 'getPermissions']);
Router::post('/users/{id}/permissions', [UserController::class, 'updatePermissions']);

// 4. Role & Permissions
Router::get('/roles', [RoleController::class, 'index']);
Router::post('/roles/create', [RoleController::class, 'store']);
Router::get('/roles/{id}/permissions', [RoleController::class, 'getPermissions']);
Router::post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions']);

// 5. Departments
Router::get('/departments', [DepartmentController::class, 'index']);
Router::post('/departments/create', [DepartmentController::class, 'store']);
Router::post('/departments/edit/{id}', [DepartmentController::class, 'update']);
Router::post('/departments/delete/{id}', [DepartmentController::class, 'delete']);

// 6. Categories & Subcategories
Router::get('/categories', [CategoryController::class, 'index']);
Router::post('/categories/create', [CategoryController::class, 'store']);
Router::post('/categories/edit/{id}', [CategoryController::class, 'update']);
Router::post('/categories/delete/{id}', [CategoryController::class, 'delete']);

Router::get('/subcategories', [SubCategoryController::class, 'index']);
Router::get('/subcategories/by-category/{id}', [SubCategoryController::class, 'getByCategory']);
Router::post('/subcategories/create', [SubCategoryController::class, 'store']);
Router::post('/subcategories/edit/{id}', [SubCategoryController::class, 'update']);
Router::post('/subcategories/delete/{id}', [SubCategoryController::class, 'delete']);

// 7. Products (God Statues)
Router::get('/products', [ProductController::class, 'index']);
Router::get('/products/create', [ProductController::class, 'create']);
Router::post('/products/create', [ProductController::class, 'store']);
Router::get('/products/edit/{id}', [ProductController::class, 'edit']);
Router::post('/products/edit/{id}', [ProductController::class, 'update']);
Router::get('/products/view/{id}', [ProductController::class, 'view']);
Router::post('/products/delete/{id}', [ProductController::class, 'delete']);
Router::get('/products/export', [ProductController::class, 'export']);

// 8. Inventory
Router::get('/inventory', [InventoryController::class, 'index']);
Router::post('/inventory/adjust', [InventoryController::class, 'adjust']);
Router::get('/inventory/history/{id}', [InventoryController::class, 'history']);

// 9. Customers
Router::get('/customers', [CustomerController::class, 'index']);
Router::get('/customers/search', [CustomerController::class, 'search']);
Router::post('/customers/create', [CustomerController::class, 'store']);
Router::post('/customers/edit/{id}', [CustomerController::class, 'update']);
Router::get('/customers/profile/{id}', [CustomerController::class, 'profile']);

// 10. Suppliers & Artisans
Router::get('/suppliers', [SupplierController::class, 'index']);
Router::post('/suppliers/create', [SupplierController::class, 'store']);
Router::post('/suppliers/edit/{id}', [SupplierController::class, 'update']);

// 11. Purchases
Router::get('/purchases', [PurchaseController::class, 'index']);
Router::get('/purchases/create', [PurchaseController::class, 'create']);
Router::post('/purchases/create', [PurchaseController::class, 'store']);
Router::get('/purchases/view/{id}', [PurchaseController::class, 'view']);
Router::post('/purchases/approve/{id}', [PurchaseController::class, 'approve']);

// 12. POS Billing & Invoicing
Router::get('/billing', [BillingController::class, 'pos']);
Router::get('/pos', [BillingController::class, 'pos']);
Router::get('/billing/search-products', [BillingController::class, 'searchProducts']);
Router::post('/billing/scan', [BillingController::class, 'scanBarcode']);
Router::post('/billing/checkout', [BillingController::class, 'checkout']);
Router::get('/billing/invoices', [BillingController::class, 'invoices']);
Router::get('/billing/invoice/{id}', [BillingController::class, 'viewInvoice']);
Router::get('/billing/invoice/{id}/print', [BillingController::class, 'printInvoice']);
Router::post('/billing/invoice/{id}/cancel', [BillingController::class, 'cancel']);

// 13. Sales Returns
Router::get('/sales-returns', [SalesReturnController::class, 'index']);
Router::get('/sales-returns/create', [SalesReturnController::class, 'create']);
Router::get('/sales-returns/lookup-invoice', [SalesReturnController::class, 'lookupInvoice']);
Router::post('/sales-returns/create', [SalesReturnController::class, 'store']);

// 14. Accounts & Financials
Router::get('/accounts', [AccountController::class, 'index']);
Router::get('/accounts/payments', [AccountController::class, 'payments']);
Router::post('/accounts/customer-payment', [AccountController::class, 'recordCustomerPayment']);
Router::post('/accounts/supplier-payment', [AccountController::class, 'recordSupplierPayment']);
Router::get('/accounts/ledger/{id}', [AccountController::class, 'ledger']);

// 15. Expenses
Router::get('/expenses', [ExpenseController::class, 'index']);
Router::post('/expenses/create', [ExpenseController::class, 'store']);
Router::post('/expenses/approve/{id}', [ExpenseController::class, 'approve']);

// 16. Reports
Router::get('/reports/sales', [ReportController::class, 'sales']);
Router::get('/reports/inventory', [ReportController::class, 'inventory']);
Router::get('/reports/financial', [ReportController::class, 'financial']);

// 17. Settings & Audit
Router::get('/settings', [SettingsController::class, 'index']);
Router::post('/settings/update', [SettingsController::class, 'update']);
Router::get('/audit', [AuditController::class, 'index']);
