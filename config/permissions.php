<?php
/**
 * System Modules and Permission Definitions
 */

return [
    'modules' => [
        'users' => [
            'name' => 'User Management',
            'icon' => 'users',
            'permissions' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
            ]
        ],
        'roles' => [
            'name' => 'Roles & Permissions',
            'icon' => 'shield',
            'permissions' => [
                'roles.view' => 'View Roles',
                'roles.manage' => 'Manage Roles & Permissions',
            ]
        ],
        'departments' => [
            'name' => 'Department Management',
            'icon' => 'briefcase',
            'permissions' => [
                'departments.view' => 'View Departments',
                'departments.manage' => 'Manage Departments',
            ]
        ],
        'catalog' => [
            'name' => 'Categories & Catalog',
            'icon' => 'grid',
            'permissions' => [
                'categories.view' => 'View Categories',
                'categories.manage' => 'Manage Categories & Subcategories',
            ]
        ],
        'products' => [
            'name' => 'God Statues & Products',
            'icon' => 'box',
            'permissions' => [
                'products.view' => 'View Products',
                'products.create' => 'Create Products',
                'products.edit' => 'Edit Products',
                'products.delete' => 'Delete Products',
                'products.export' => 'Export Products',
            ]
        ],
        'inventory' => [
            'name' => 'Inventory & Stock Control',
            'icon' => 'archive',
            'permissions' => [
                'inventory.view' => 'View Stock Levels & History',
                'inventory.adjust' => 'Adjust Stock & Record Damage',
                'inventory.stock_in' => 'Manual Stock In',
            ]
        ],
        'customers' => [
            'name' => 'Customer CRM & Ledgers',
            'icon' => 'user-check',
            'permissions' => [
                'customers.view' => 'View Customers & Outstanding',
                'customers.create' => 'Create Customers',
                'customers.edit' => 'Edit Customers & Credit Limits',
                'customers.delete' => 'Delete Customers',
            ]
        ],
        'suppliers' => [
            'name' => 'Suppliers & Artisans',
            'icon' => 'truck',
            'permissions' => [
                'suppliers.view' => 'View Artisans & Suppliers',
                'suppliers.manage' => 'Manage Artisans & Payables',
            ]
        ],
        'purchases' => [
            'name' => 'Purchases & Procurement',
            'icon' => 'shopping-bag',
            'permissions' => [
                'purchases.view' => 'View Purchases',
                'purchases.create' => 'Create Purchase Orders',
                'purchases.approve' => 'Approve Purchases & Restock',
                'purchases.cancel' => 'Cancel Purchases',
            ]
        ],
        'billing' => [
            'name' => 'POS Billing & Sales',
            'icon' => 'shopping-cart',
            'permissions' => [
                'billing.pos' => 'Access POS Terminal',
                'billing.view' => 'View Sales Invoices',
                'billing.create' => 'Create Invoices',
                'billing.cancel' => 'Cancel Invoices',
                'billing.print' => 'Print Tax & Thermal Receipts',
                'billing.returns' => 'Process Sales Returns & Refunds',
            ]
        ],
        'accounts' => [
            'name' => 'Accounts & Financials',
            'icon' => 'dollar-sign',
            'permissions' => [
                'accounts.view' => 'View Cash/Bank Registers & Balances',
                'accounts.payments' => 'Record Payments & Receipts',
                'expenses.view' => 'View Expenses',
                'expenses.create' => 'Submit Expense Vouchers',
                'expenses.approve' => 'Approve Expense Payouts',
            ]
        ],
        'reports' => [
            'name' => 'Analytics & Reports',
            'icon' => 'bar-chart-2',
            'permissions' => [
                'reports.sales' => 'Sales & Revenue Reports',
                'reports.inventory' => 'Stock Valuation & Movement Reports',
                'reports.financial' => 'Financial P&L & Collection Reports',
            ]
        ],
        'settings' => [
            'name' => 'Settings & Configuration',
            'icon' => 'settings',
            'permissions' => [
                'settings.manage' => 'Manage Enterprise Settings',
            ]
        ],
        'audit' => [
            'name' => 'System Audit Logs',
            'icon' => 'activity',
            'permissions' => [
                'audit.view' => 'Inspect System Audit Logs',
            ]
        ]
    ]
];
