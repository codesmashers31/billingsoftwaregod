<?php
$currentUri = (new \App\Core\Request())->uri();
?>
<!-- Sidebar Component (Clean Professional Light Theme) -->
<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 overflow-y-auto z-30 transition-all duration-300 shadow-sm">
    <!-- Brand Logo -->
    <div>
        <div class="p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-14 h-14 bg-transparent flex items-center justify-center shrink-0">
                <img src="<?= asset('images/logo.png') ?>" alt="Sastha Sasti" class="w-full h-full object-contain scale-150 transform mix-blend-multiply">
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-slate-900 leading-tight truncate"><?= sanitize(getSetting('company_name', 'Sastha Sasti')) ?></h1>
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-700 block">Enterprise ERP</span>
            </div>
        </div>

        <!-- Navigation Menus -->
        <nav class="p-3 space-y-1 text-xs font-semibold text-slate-600">
            <!-- Dashboard -->
            <a href="<?= url('dashboard') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all <?= $currentUri === '/dashboard' || $currentUri === '/' ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-sm' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-th-large text-sm w-4 <?= $currentUri === '/dashboard' || $currentUri === '/' ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Executive Dashboard</span>
            </a>

            <!-- POS BILLING TERMINAL -->
            <?php if (hasPermission('billing.pos')): ?>
            <a href="<?= url('billing') ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all <?= isActiveRoute('/billing', $currentUri) && !isActiveRoute('/billing/invoices', $currentUri) ? 'bg-amber-600 text-white font-bold shadow-md' : 'bg-amber-50 text-amber-900 hover:bg-amber-100 border border-amber-200 font-bold' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cash-register text-sm w-4"></i>
                    <span>POS Billing Terminal</span>
                </div>
                <span class="text-[9px] uppercase px-1.5 py-0.5 rounded <?= isActiveRoute('/billing', $currentUri) && !isActiveRoute('/billing/invoices', $currentUri) ? 'bg-white/20 text-white' : 'bg-amber-200 text-amber-900' ?> font-extrabold">Fast</span>
            </a>
            <?php endif; ?>

            <!-- Catalog & Products -->
            <?php if (hasPermission('products.view') || hasPermission('categories.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Catalog & Idols</div>
            <?php if (hasPermission('products.view')): ?>
            <a href="<?= url('products') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/products', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-dharmachakra text-sm w-4 <?= isActiveRoute('/products', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>God Statues Catalog</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('categories.view')): ?>
            <a href="<?= url('categories') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/categories', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-tags text-sm w-4 <?= isActiveRoute('/categories', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Categories</span>
            </a>
            <a href="<?= url('subcategories') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/subcategories', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-sitemap text-sm w-4 <?= isActiveRoute('/subcategories', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Subcategories (Deities)</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Inventory & Stock Control -->
            <?php if (hasPermission('inventory.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory & Logistics</div>
            <a href="<?= url('inventory') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/inventory', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-boxes text-sm w-4 <?= isActiveRoute('/inventory', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Stock Control & Register</span>
            </a>
            <?php endif; ?>

            <!-- Sales & Invoices -->
            <?php if (hasPermission('billing.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Sales & Billing</div>
            <a href="<?= url('billing/invoices') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/billing/invoices', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-file-invoice-dollar text-sm w-4 <?= isActiveRoute('/billing/invoices', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Invoices & History</span>
            </a>
            <?php if (hasPermission('billing.returns')): ?>
            <a href="<?= url('sales-returns') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/sales-returns', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-undo-alt text-sm w-4 <?= isActiveRoute('/sales-returns', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Sales Returns</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Sourcing & Sthapathis -->
            <?php if (hasPermission('purchases.view') || hasPermission('suppliers.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Procurement & Artisans</div>
            <?php if (hasPermission('purchases.view')): ?>
            <a href="<?= url('purchases') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/purchases', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-truck-loading text-sm w-4 <?= isActiveRoute('/purchases', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Sourcing Purchases</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('suppliers.view')): ?>
            <a href="<?= url('suppliers') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/suppliers', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-hammer text-sm w-4 <?= isActiveRoute('/suppliers', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Artisans & Foundries</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Customers & CRM -->
            <?php if (hasPermission('customers.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer CRM</div>
            <a href="<?= url('customers') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/customers', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-users text-sm w-4 <?= isActiveRoute('/customers', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Customers & Ledgers</span>
            </a>
            <?php endif; ?>

            <!-- Accounts & Finance -->
            <?php if (hasPermission('accounts.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Accounts & Ledgers</div>
            <a href="<?= url('accounts') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= $currentUri === '/accounts' ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-university text-sm w-4 <?= $currentUri === '/accounts' ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Accounts & Cash Book</span>
            </a>
            <a href="<?= url('accounts/payments') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/accounts/payments', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-money-check-alt text-sm w-4 <?= isActiveRoute('/accounts/payments', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Receipts & Payouts</span>
            </a>
            <?php if (hasPermission('expenses.view')): ?>
            <a href="<?= url('expenses') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/expenses', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-receipt text-sm w-4 <?= isActiveRoute('/expenses', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Expense Vouchers</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Analytics & Reports -->
            <?php if (hasPermission('reports.sales') || hasPermission('reports.inventory') || hasPermission('reports.financial')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Reports & Analytics</div>
            <?php if (hasPermission('reports.sales')): ?>
            <a href="<?= url('reports/sales') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/reports/sales', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-chart-line text-sm w-4 <?= isActiveRoute('/reports/sales', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Sales Analytics</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('reports.inventory')): ?>
            <a href="<?= url('reports/inventory') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/reports/inventory', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-warehouse text-sm w-4 <?= isActiveRoute('/reports/inventory', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Stock Valuation</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('reports.financial')): ?>
            <a href="<?= url('reports/financial') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/reports/financial', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-balance-scale text-sm w-4 <?= isActiveRoute('/reports/financial', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>P&L Statement</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Administration & Settings -->
            <?php if (hasPermission('users.view') || hasPermission('settings.view') || hasPermission('audit.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Administration</div>
            <?php if (hasPermission('users.view')): ?>
            <a href="<?= url('users') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/users', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-user-cog text-sm w-4 <?= isActiveRoute('/users', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Staff & Roles</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('settings.view')): ?>
            <a href="<?= url('settings') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/settings', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-cog text-sm w-4 <?= isActiveRoute('/settings', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Settings</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('audit.view')): ?>
            <a href="<?= url('audit') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/audit', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-shield-alt text-sm w-4 <?= isActiveRoute('/audit', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Audit Trail</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Permissions & Credits -->
            <?php if (hasPermission('roles.view')): ?>
            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Permissions and Credits</div>
            <a href="<?= url('roles') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all <?= isActiveRoute('/roles', $currentUri) ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                <i class="fas fa-key text-sm w-4 <?= isActiveRoute('/roles', $currentUri) ? 'text-amber-700' : 'text-slate-400' ?>"></i>
                <span>Permissions</span>
            </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- User Profile Footer -->
    <div class="p-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
        <div class="flex items-center gap-2.5 overflow-hidden">
            <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                <?= strtoupper(substr(auth('name', 'U'), 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-slate-800 truncate"><?= sanitize(auth('name', 'Staff')) ?></div>
                <div class="text-[10px] text-amber-700 font-semibold truncate"><?= sanitize(auth('role_name', 'User')) ?></div>
            </div>
        </div>
        <a href="<?= url('logout') ?>" title="Sign Out" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>

