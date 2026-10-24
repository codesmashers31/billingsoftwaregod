<div class="space-y-4 max-w-7xl mx-auto relative">
    <!-- Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Product Management</h1>
            <p class="text-xs text-slate-500">Select an entry module below to manage your catalog and billing details</p>
        </div>
    </div>

    <!-- Compact Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Supplier Details Card -->
        <button id="btn-supplier-section" onclick="toggleSection('supplier-section')" class="active-tab group relative bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-xs hover:shadow-md hover:border-indigo-300 transition-all duration-200 flex items-center gap-3 text-left w-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-xs">
                <i class="fas fa-truck text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition-colors truncate">Supplier Details</h3>
                <p class="text-[11px] text-slate-400 truncate mt-0.5">Vendors & products</p>
            </div>
        </button>

        <!-- Product Details Card -->
        <button id="btn-product-section" onclick="toggleSection('product-section')" class="group relative bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-xs hover:shadow-md hover:border-amber-300 transition-all duration-200 flex items-center gap-3 text-left w-full focus:outline-none focus:ring-2 focus:ring-amber-500">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-all shadow-xs">
                <i class="fas fa-box-open text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Product Details</h3>
                <p class="text-[11px] text-slate-400 truncate mt-0.5">Catalog & inventory</p>
            </div>
        </button>

        <!-- Barcode Details Card -->
        <button id="btn-barcode-section" onclick="toggleSection('barcode-section')" class="group relative bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-xs hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3 text-left w-full focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-xs">
                <i class="fas fa-barcode text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Barcode Details</h3>
                <p class="text-[11px] text-slate-400 truncate mt-0.5">Print labels & tags</p>
            </div>
        </button>

        <!-- Invoice Details Card -->
        <button id="btn-invoice-section" onclick="toggleSection('invoice-section')" class="group relative bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-xs hover:shadow-md hover:border-rose-300 transition-all duration-200 flex items-center gap-3 text-left w-full focus:outline-none focus:ring-2 focus:ring-rose-500">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 group-hover:bg-rose-500 group-hover:text-white transition-all shadow-xs">
                <i class="fas fa-file-invoice-dollar text-base"></i>
            </div>
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-rose-700 transition-colors truncate">Invoice Details</h3>
                <p class="text-[11px] text-slate-400 truncate mt-0.5">Billing & payables</p>
            </div>
        </button>

    </div>

    <!-- Dynamic Sections Panel -->
    <!-- Dynamic Sections Panel -->
    <div id="dynamic-sections" class="mt-8">
        
        <!-- Supplier Section with Table -->
        <div id="supplier-section" class="animate-fade-in bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-slate-900">Supplier Management</h4>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Add new suppliers and manage product associations.</p>
                </div>
                <button onclick="openSupplierModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>

            <!-- Supplier Search & Filter Controls -->
            <div class="mb-5 bg-slate-50/80 p-4 rounded-xl border border-slate-200/90 shadow-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    <!-- Search Input -->
                    <div class="lg:col-span-5 relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="supplier-search-input" oninput="filterSupplierTable()" placeholder="Search by supplier name, mobile, barcode, city, GST..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-medium">
                        <button type="button" onclick="clearSupplierSearch()" id="supplier-search-clear" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-2">
                        <select id="supplier-status-filter" onchange="filterSupplierTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-medium">
                            <option value="">All Statuses</option>
                            <option value="Active">Active Only</option>
                            <option value="Inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Products Association Filter -->
                    <div class="lg:col-span-3">
                        <select id="supplier-products-filter" onchange="filterSupplierTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-medium">
                            <option value="">All Suppliers</option>
                            <option value="with_products">Has Products (&gt; 0)</option>
                            <option value="no_products">No Products Associated (0)</option>
                            <option value="has_sales">Has Sales Activity</option>
                        </select>
                    </div>

                    <!-- Reset & Summary -->
                    <div class="lg:col-span-2 flex items-center justify-between sm:justify-end gap-2">
                        <button type="button" onclick="resetSupplierFilters()" title="Reset Filters" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-all shadow-xs flex items-center gap-1.5">
                            <i class="fas fa-undo text-[10px]"></i> Reset
                        </button>
                        <span id="supplier-filter-count-badge" class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1.5 rounded-lg border border-indigo-100 whitespace-nowrap">
                            0 Suppliers
                        </span>
                    </div>
                </div>
            </div>

            <!-- Supplier Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Mobile</th>
                            <th class="py-3 px-4">Products</th>
                            <th class="py-3 px-4">GST Number</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplier-table-body" class="divide-y divide-slate-100">
                        <tr id="empty-state-row">
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-3xl mb-3 block text-slate-300"></i>
                                <p class="font-bold text-slate-600 text-sm">No suppliers found</p>
                                <p class="text-xs mt-1">Click "Add Supplier" to create your first entry.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product Section -->
        <div id="product-section" class="hidden animate-fade-in bg-amber-50/50 border border-amber-100 rounded-2xl p-6 shadow-inner">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-amber-900">Product Management</h4>
                    <p class="text-xs text-amber-600/80 mt-1 font-medium">Add new products to your catalog and manage inventory.</p>
                </div>
                <button onclick="openProductModal()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add Products Detail
                </button>
            </div>

            <!-- Product Search & Filter Controls -->
            <div class="mb-5 bg-white p-4 rounded-xl border border-amber-200/90 shadow-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    <!-- Search Input -->
                    <div class="lg:col-span-4 relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="product-search-input" oninput="filterProductTable()" placeholder="Search product name, SKU, barcode, supplier..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium">
                        <button type="button" onclick="clearProductSearch()" id="product-search-clear" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    <!-- Category Filter -->
                    <div class="lg:col-span-3">
                        <select id="product-category-filter" onchange="filterProductTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium">
                            <option value="">All Categories</option>
                        </select>
                    </div>

                    <!-- Supplier Filter -->
                    <div class="lg:col-span-3">
                        <select id="product-supplier-filter" onchange="filterProductTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div class="lg:col-span-2">
                        <select id="product-stock-filter" onchange="filterProductTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all font-medium">
                            <option value="">All Stock</option>
                            <option value="in_stock">In Stock (&gt; 0)</option>
                            <option value="low_stock">Low Stock (≤ Min)</option>
                            <option value="out_of_stock">Out of Stock (0)</option>
                        </select>
                    </div>
                </div>

                <!-- Secondary row: Reset & Counter -->
                <div class="mt-3 pt-3 border-t border-amber-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="resetProductFilters()" title="Reset Filters" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-all shadow-xs flex items-center gap-1.5">
                            <i class="fas fa-undo text-[10px]"></i> Reset Filters
                        </button>
                        <span id="product-filter-count-badge" class="text-[11px] font-bold text-amber-900 bg-amber-100/80 px-2.5 py-1 rounded-lg border border-amber-200 whitespace-nowrap">
                            0 Products
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        <i class="fas fa-mouse-pointer text-amber-500 mr-1"></i>Click any product row or number to copy
                    </div>
                </div>
            </div>

            <!-- Product Table -->
            <div class="overflow-x-auto rounded-xl border border-amber-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-amber-100/60 text-amber-800 uppercase text-[10px] font-extrabold border-b border-amber-200 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 whitespace-nowrap min-w-[200px]">Product & SKU</th>
                            <th class="py-3.5 px-4 whitespace-nowrap min-w-[120px]">Category</th>
                            <th class="py-3.5 px-4 whitespace-nowrap min-w-[140px]">Supplier</th>
                            <th class="py-3.5 px-4 whitespace-nowrap min-w-[160px]">Prices (Purchase / Sell)</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[70px]">GST</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[100px]">Stock</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[80px]">Offer</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[90px]">Status</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[120px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body" class="divide-y divide-amber-100 bg-white">
                        <tr id="product-empty-state-row">
                            <td colspan="9" class="py-12 text-center text-amber-400">
                                <i class="fas fa-box-open text-3xl mb-3 block text-amber-300"></i>
                                <p class="font-bold text-amber-600 text-sm">No products found</p>
                                <p class="text-xs mt-1">Click "Add Products Detail" to create your first entry.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Product Pagination Controls -->
            <div class="mt-4 pt-3 border-t border-amber-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs" id="product-pagination-container">
                <div class="flex items-center gap-2 text-slate-500 font-medium">
                    <span id="product-page-info">Showing 0 to 0 of 0 entries</span>
                    <span class="mx-1">•</span>
                    <label class="flex items-center gap-1.5">
                        <span>Show</span>
                        <select id="product-per-page-select" onchange="changeProductPerPage(this.value)" class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs outline-none focus:border-amber-500 font-bold text-slate-700">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>per page</span>
                    </label>
                </div>
                <div class="flex items-center gap-1.5" id="product-page-buttons">
                    <!-- Page buttons injected via JS -->
                </div>
            </div>
        </div>

        <!-- Barcode Section -->
        <div id="barcode-section" class="hidden animate-fade-in bg-white border border-emerald-200/90 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-emerald-950">Barcode Management</h4>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Generate new barcode labels for products and shipments.</p>
                </div>
                <button type="button" onclick="printAllBarcodes()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-barcode"></i> Print All Barcodes
                </button>
            </div>

            <!-- Barcode Search & Filter Controls -->
            <div class="mb-5 bg-slate-50/80 p-4 rounded-xl border border-slate-200/90 shadow-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    <!-- Search Input -->
                    <div class="lg:col-span-5 relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="barcode-search-input" oninput="filterBarcodeTable()" placeholder="Search Barcode No., Product Name, SKU..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-medium">
                        <button type="button" onclick="clearBarcodeSearch()" id="barcode-search-clear" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    <!-- Category Filter -->
                    <div class="lg:col-span-3">
                        <select id="barcode-category-filter" onchange="filterBarcodeTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-medium">
                            <option value="">All Categories</option>
                        </select>
                    </div>

                    <!-- Supplier Filter -->
                    <div class="lg:col-span-2">
                        <select id="barcode-supplier-filter" onchange="filterBarcodeTable()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-medium">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>

                    <!-- Reset & Summary -->
                    <div class="lg:col-span-2 flex items-center justify-between sm:justify-end gap-2">
                        <button type="button" onclick="resetBarcodeFilters()" title="Reset Filters" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-all shadow-xs flex items-center gap-1.5">
                            <i class="fas fa-undo text-[10px]"></i> Reset
                        </button>
                        <span id="barcode-count-badge" class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200 whitespace-nowrap">
                            0 Barcodes
                        </span>
                    </div>
                </div>
            </div>

            <!-- Barcode Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-xs">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/90 text-slate-700 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 min-w-[140px]">Barcode No.</th>
                            <th class="py-3 px-4 min-w-[220px]">Product Name & SKU</th>
                            <th class="py-3 px-4 min-w-[130px]">Category</th>
                            <th class="py-3 px-4 min-w-[140px]">Supplier Name</th>
                            <th class="py-3 px-4 text-center min-w-[80px]">Stock</th>
                            <th class="py-3 px-4 text-right min-w-[110px]">Selling Price</th>
                            <th class="py-3 px-4 text-right min-w-[100px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="barcode-table-body" class="divide-y divide-slate-100 bg-white">
                        <tr id="barcode-empty-state-row">
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-barcode text-3xl mb-3 block text-slate-300"></i>
                                <p class="font-bold text-slate-600 text-sm">No barcodes found</p>
                                <p class="text-xs mt-1">Add products or suppliers to generate barcodes.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Barcode Pagination Controls -->
            <div class="mt-4 pt-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs" id="barcode-pagination-container">
                <div class="flex items-center gap-2 text-slate-500 font-medium">
                    <span id="barcode-page-info">Showing 0 to 0 of 0 entries</span>
                    <span class="mx-1">•</span>
                    <label class="flex items-center gap-1.5">
                        <span>Show</span>
                        <select id="barcode-per-page-select" onchange="changeBarcodePerPage(this.value)" class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs outline-none focus:border-emerald-500 font-bold text-slate-700">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>per page</span>
                    </label>
                </div>
                <div class="flex items-center gap-1.5" id="barcode-page-buttons">
                    <!-- Page buttons injected via JS -->
                </div>
            </div>
        </div>

        <!-- Supplier Invoice Section (Supplier Purchase and Payment Management) -->
        <div id="invoice-section" class="hidden animate-fade-in bg-white border border-rose-200/80 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6 pb-5 border-b border-rose-100">
                <div>
                    <div class="flex items-center gap-2 justify-center sm:justify-start">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-bold shadow-sm">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </span>
                        <h4 class="text-xl font-extrabold text-slate-900">Invoice Management</h4>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Manage supplier purchase amounts, track payments, and update settlement status.</p>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap justify-center">
                    <a href="<?= url('billing/invoices') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-list-ul"></i> All Invoices
                    </a>
                </div>
            </div>

            <!-- Quick Supplier Invoice Stats Bar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
                <div class="bg-rose-50/60 border border-rose-100 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Total Suppliers</p>
                        <p id="inv-stat-count" class="text-lg font-black text-rose-950 mt-0.5">0</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Total Purchase Amount</p>
                        <p id="inv-stat-total" class="text-lg font-black text-indigo-950 mt-0.5">₹0.00</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Total Paid</p>
                        <p id="inv-stat-paid" class="text-lg font-black text-emerald-950 mt-0.5">₹0.00</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
                <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Total Balance Due</p>
                        <p id="inv-stat-due" class="text-lg font-black text-amber-950 mt-0.5">₹0.00</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-4">
                <div class="relative w-full sm:w-72">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="inv-search-input" oninput="filterInvoicesTable()" placeholder="Search Supplier ID, Name..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 outline-none focus:border-rose-400 focus:bg-white transition-all">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select id="inv-status-filter" onchange="filterInvoicesTable()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-rose-400">
                        <option value="">All Payment Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Partially Paid">Partially Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>
                </div>
            </div>

            <!-- Invoices Table: STRICTLY 5 COLUMNS -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 font-bold text-slate-700">Supplier ID</th>
                            <th class="py-3 px-4 font-bold text-slate-700">Supplier Name</th>
                            <th class="py-3 px-4 font-bold text-slate-700">Amount</th>
                            <th class="py-3 px-4 font-bold text-slate-700">Payment Status</th>
                            <th class="py-3 px-4 font-bold text-slate-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoice-table-body" class="divide-y divide-slate-100 bg-white">
                        <tr id="invoice-empty-state-row">
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fas fa-truck-loading text-3xl mb-3 block text-rose-300"></i>
                                <p class="font-bold text-slate-600 text-sm">No supplier invoices found</p>
                                <p class="text-xs mt-1">Add suppliers and products to manage supplier purchase invoices.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: View Supplier Purchase Details (Tab Context) -->
        <div id="prod-modal-view-details" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-3xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-truck-loading text-amber-600"></i> Supplier Purchase & Payment Summary
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5" id="pv_sub_heading">Supplier Overview</p>
                    </div>
                    <button type="button" onclick="closeSupplierInvoiceViewModal()" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Supplier ID</span>
                            <span id="pv_supplier_id" class="font-mono font-bold text-slate-900 text-sm"></span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 sm:col-span-2">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Supplier Name</span>
                            <span id="pv_supplier_name" class="font-bold text-slate-900 text-sm"></span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Payment Status</span>
                            <span id="pv_payment_status" class="inline-block mt-0.5"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-3">
                            <span class="text-[10px] font-bold uppercase text-blue-700 block">Total Purchase Amount</span>
                            <span id="pv_total_amount" class="font-mono font-black text-blue-900 text-base">₹0.00</span>
                        </div>
                        <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-3">
                            <span class="text-[10px] font-bold uppercase text-emerald-700 block">Paid Amount</span>
                            <span id="pv_paid_amount" class="font-mono font-black text-emerald-900 text-base">₹0.00</span>
                        </div>
                        <div class="bg-rose-50/50 border border-rose-200 rounded-xl p-3">
                            <span class="text-[10px] font-bold uppercase text-rose-700 block">Balance Amount</span>
                            <span id="pv_balance_amount" class="font-mono font-black text-rose-900 text-base">₹0.00</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Associated Products Purchased</span>
                            <span id="pv_product_count_badge" class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold">0 products</span>
                        </h4>
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs text-slate-600">
                                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">#</th>
                                        <th class="py-2.5 px-3">Product Name</th>
                                        <th class="py-2.5 px-3 text-center">Pieces Count (Qty)</th>
                                        <th class="py-2.5 px-3 text-right">One Piece Rate</th>
                                        <th class="py-2.5 px-3 text-right">Total Purchase Price</th>
                                    </tr>
                                </thead>
                                <tbody id="pv_products_tbody" class="divide-y divide-slate-100 font-mono">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="button" onclick="closeSupplierInvoiceViewModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Edit / Update Supplier Payment (Tab Context) -->
        <div id="prod-modal-edit-payment" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md shadow-2xl p-6 relative">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-money-check-alt text-amber-600"></i> Update Supplier Payment
                    </h3>
                    <button type="button" onclick="closeSupplierInvoiceEditModal()" class="text-slate-400 hover:text-slate-700">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="prod-edit-payment-form" onsubmit="submitSupplierInvoicePayment(event)" class="space-y-4">
                    <input type="hidden" id="pe_supplier_id">
                    <input type="hidden" id="pe_total_purchase_amount" value="0">

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Supplier:</span>
                            <span id="pe_supp_name" class="font-bold text-slate-900"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Supplier ID:</span>
                            <span id="pe_supp_id_text" class="font-mono font-bold text-slate-700"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-1.5">
                            <span class="text-slate-600 font-semibold">Total Purchase Amount:</span>
                            <span id="pe_total_amount_display" class="font-mono font-black text-slate-900">₹0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Previously Paid:</span>
                            <span id="pe_previously_paid_display" class="font-mono font-bold text-emerald-700">₹0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 font-semibold">Remaining Balance:</span>
                            <span id="pe_balance_amount_display" class="font-mono font-black text-rose-600">₹0.00</span>
                        </div>
                    </div>

                    <!-- Additional Payment Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Additional Payment (₹)</label>
                        <input type="number" step="0.01" min="0" id="pe_additional_amount" oninput="onSupplierAdditionalAmountInput()" placeholder="0.00" 
                               class="w-full bg-emerald-50/50 border border-emerald-300 rounded-xl px-3 py-2 text-sm text-emerald-900 font-mono font-bold outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Enter payment received now (auto-increments total paid)</p>
                    </div>

                    <!-- Total Paid Amount Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Paid Amount (₹) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" id="pe_paid_amount" oninput="onSupplierPaidAmountInput()" required 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono font-bold outline-none focus:border-amber-500">
                        <p class="text-[11px] text-slate-400 mt-1">Formula: Balance Amount = Total Amount - Paid Amount</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Payment Status <span class="text-rose-500">*</span></label>
                        <select id="pe_payment_status" onchange="onSupplierPaymentStatusChange()" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-amber-500">
                            <option value="Unpaid">Unpaid</option>
                            <option value="Partially Paid">Partially Paid</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                            <select id="pe_payment_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="UPI">UPI / QR</option>
                                <option value="Card">Card</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Payment Date</label>
                            <input type="date" id="pe_payment_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Transaction Ref (Optional)</label>
                        <input type="text" id="pe_notes" placeholder="e.g. UTR # / Cheque No / Cash voucher" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeSupplierInvoiceEditModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Cancel</button>
                        <button type="submit" id="btn-save-supp-payment" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-all">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Quick Add Category Modal -->
<div id="quick-add-category-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden p-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 class="text-base font-bold text-slate-900"><i class="fas fa-folder-plus text-amber-600 mr-2"></i> Add Product Category</h3>
            <button type="button" onclick="document.getElementById('quick-add-category-modal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" id="quick-cat-name" onkeydown="if(event.key==='Enter'){event.preventDefault();submitQuickCategory();}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. Hindu God Statues">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select id="quick-cat-status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="active">Available</option>
                    <option value="inactive">Not Available</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('quick-add-category-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
            <button type="button" onclick="submitQuickCategory()" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-transform transform hover:-translate-y-0.5">Save Category</button>
        </div>
    </div>
</div>

<!-- Quick Add Subcategory Modal -->
<div id="quick-add-subcategory-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden p-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 class="text-base font-bold text-slate-900"><i class="fas fa-folder-plus text-amber-600 mr-2"></i> Add Subcategory</h3>
            <button type="button" onclick="document.getElementById('quick-add-subcategory-modal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Parent Category <span class="text-rose-500">*</span></label>
                <select id="quick-subcat-parent" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <!-- Populated via JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory Name <span class="text-rose-500">*</span></label>
                <input type="text" id="quick-subcat-name" onkeydown="if(event.key==='Enter'){event.preventDefault();submitQuickSubcategory();}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. Lord Ganesha (Vinayagar)">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select id="quick-subcat-status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="active">Available</option>
                    <option value="inactive">Not Available</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('quick-add-subcategory-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
            <button type="button" onclick="submitQuickSubcategory()" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-transform transform hover:-translate-y-0.5">Save Subcategory</button>
        </div>
    </div>
</div>

<!-- Supplier Form Modal -->
<div id="supplier-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-full w-full flex items-center justify-center p-4 sm:p-6 py-10">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl relative animate-fade-in mx-auto">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2" id="supplier-modal-title">
                <i class="fas fa-truck-loading text-indigo-600"></i> Add Supplier
            </h3>
            <button type="button" onclick="closeSupplierModal()" class="text-slate-400 hover:text-rose-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form Content -->
        <form id="supplier-form" class="p-6 space-y-8" onsubmit="handleSupplierSubmit(event)">
            
            <!-- 1. Supplier Information -->
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4"><i class="fas fa-user-tag text-indigo-500 mr-2"></i>Supplier Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Supplier Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="supplier_name" id="supplier_name" placeholder="Enter supplier name (e.g. Swamimalai Bronze Heritage Works)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-supplier_name">This field is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" placeholder="Enter mobile number (e.g. 9876543210)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-mobile_number">Valid mobile number is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="e.g. supplier@domain.com (Optional)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" id="address" placeholder="Enter street address / workshop location" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-address">Address is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" placeholder="e.g. Kumbakonam / Thanjavur / Madurai" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                        <input type="text" name="state" value="Tamil Nadu" placeholder="e.g. Tamil Nadu" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">GST Number</label>
                        <input type="text" name="gst_number" id="gst_number" placeholder="e.g. 33AABCF9876K1Z2 (Optional)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 uppercase transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-gst_number">Invalid GST format</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Supplier Status</label>
                        <select name="status" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- 2. Product Information -->
            <div class="hidden">
                <div class="flex items-center justify-between border-b border-amber-200 pb-2 mb-4">
                    <h4 class="text-sm font-bold text-slate-800"><i class="fas fa-box-open text-amber-500 mr-2"></i>2. Products Supplied</h4>
                    <button type="button" onclick="addSupplierProductField()" class="text-xs bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg font-bold hover:bg-amber-200 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Another Product
                    </button>
                </div>
                
                <div id="supplier-products-container" class="space-y-4">
                    <!-- Dynamic Product Blocks go here -->
                </div>
            </div>

            <!-- 3. Payment Information -->
            <div class="hidden">
                <div class="flex items-center justify-between border-b border-emerald-200 pb-2 mb-4">
                    <h4 class="text-sm font-bold text-slate-800"><i class="fas fa-credit-card text-emerald-500 mr-2"></i>2. Payment Information</h4>
                    <span id="supplier_status_badge" class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-600 border border-slate-200">Status: Unpaid</span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Purchased (₹)</label>
                    <input type="number" step="0.01" min="0" name="total_amount_purchased" id="total_amount_purchased" oninput="onTotalPurchasedInput()" placeholder="0.00 (e.g. 50000.00)" class="w-full md:w-1/2 bg-white border border-slate-300 text-slate-900 font-bold rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" value="0.00">
                    <span class="text-[10px] text-rose-500 hidden mt-1" id="err-total_amount_purchased">Cannot be negative</span>
                </div>

                <label class="block text-xs font-bold text-slate-700 mb-3">Select Payment Methods</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Cash -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 hover:border-emerald-300 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer w-full">
                            <input type="checkbox" value="Cash" name="payment_methods[]" onchange="toggleSupplierPaymentAmount(this, 'payment_amount_cash')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Cash</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_cash" id="payment_amount_cash" oninput="calculateSupplierTotalPaid()" placeholder="Cash Amount (₹) e.g. 10000" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- UPI -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 hover:border-emerald-300 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer w-full">
                            <input type="checkbox" value="UPI" name="payment_methods[]" onchange="toggleSupplierPaymentAmount(this, 'payment_amount_upi')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">UPI</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_upi" id="payment_amount_upi" oninput="calculateSupplierTotalPaid()" placeholder="UPI Amount (₹) e.g. 10000" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- Bank Transfer -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 hover:border-emerald-300 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer w-full">
                            <input type="checkbox" value="Bank Transfer" name="payment_methods[]" onchange="toggleSupplierPaymentAmount(this, 'payment_amount_bank')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Bank Transfer</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_bank" id="payment_amount_bank" oninput="calculateSupplierTotalPaid()" placeholder="Bank Amount (₹) e.g. 10000" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- Credit -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 hover:border-emerald-300 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer w-full">
                            <input type="checkbox" value="Credit" name="payment_methods[]" onchange="toggleSupplierPaymentAmount(this, 'payment_amount_credit')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Credit</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_credit" id="payment_amount_credit" oninput="calculateSupplierTotalPaid()" placeholder="Credit Amount (₹) e.g. 10000" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-emerald-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Paid (₹)</label>
                        <input type="number" step="0.01" min="0" name="total_amount_paid" id="total_payment_entered" oninput="onTotalPaidInput()" placeholder="0.00 (e.g. 25000.00)" class="w-full bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-500 transition-all">
                        <span class="text-[11px] text-rose-500 hidden mt-1" id="err-total_paid"></span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Balance to Pay (₹)</label>
                        <input type="number" step="0.01" name="balance_amount" id="balance_amount" placeholder="0.00" class="w-full bg-rose-50 border border-rose-200 text-rose-800 font-bold rounded-xl px-3 py-2 text-sm outline-none cursor-not-allowed" readonly value="0.00">
                        <span class="text-[10px] text-slate-400 mt-1 block">Formula: Balance = Total Amount Purchased - Total Amount Paid</span>
                    </div>
                </div>

                <input type="hidden" name="payment_status" id="supplier_payment_status" value="Unpaid">
            </div>

            <!-- 4. Additional Information -->
            <div class="hidden">
                <h4 class="text-sm font-bold text-slate-800 border-b border-rose-200 pb-2 mb-4"><i class="fas fa-info-circle text-rose-500 mr-2"></i>4. Additional Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Supplier Status</label>
                        <select name="status" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

        </form>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center justify-end gap-3 sticky bottom-0 z-10">
            <button type="button" onclick="closeSupplierModal()" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 text-sm font-bold transition-all shadow-sm">
                Cancel
            </button>
            <button type="submit" form="supplier-form" class="px-5 py-2.5 rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <i class="fas fa-save"></i> Save Supplier
            </button>
        </div>
    </div>
    </div>
</div>

<!-- Product Form Modal -->
<div id="product-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-full w-full flex items-center justify-center p-4 sm:p-6 py-10">
        <div class="bg-white rounded-2xl w-full max-w-5xl shadow-2xl relative animate-fade-in mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2" id="product-modal-title">
                    <i class="fas fa-plus text-amber-600"></i> Add Products Detail
                </h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="product-form" class="p-6 space-y-8" onsubmit="handleProductSubmit(event)">
                
                <!-- 1. Supplier Information -->
                <div class="bg-indigo-50/30 p-5 rounded-xl border border-indigo-100">
                    <h4 class="text-sm font-bold text-slate-800 border-b border-indigo-200 pb-2 mb-4"><i class="fas fa-truck text-indigo-500 mr-2"></i>1. Supplier Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">Supplier Name <span class="text-rose-500">*</span></label>
                                <button type="button" onclick="openQuickAddSupplierModal()" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 hover:underline">
                                    <i class="fas fa-plus-circle text-xs"></i> + New Supplier
                                </button>
                            </div>

                            <!-- Native hidden select for form submission & validation compatibility -->
                            <select name="prod_supplier_id" id="prod_supplier_id" class="hidden">
                                <option value="">Select Supplier</option>
                            </select>

                            <!-- Custom Searchable Combobox -->
                            <div class="relative" id="supplier-combobox-wrapper">
                                <!-- Trigger Display Button -->
                                <button type="button" id="supplier-combobox-trigger" onclick="toggleSupplierCombobox()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs sm:text-sm text-left flex items-center justify-between outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all hover:border-slate-300 shadow-xs">
                                    <span id="supplier-combobox-selected-text" class="text-slate-400 font-medium truncate">
                                        <i class="fas fa-search text-slate-300 mr-1.5"></i>Select or search supplier...
                                    </span>
                                    <div class="flex items-center gap-1.5 ml-2 text-slate-400 shrink-0">
                                        <span id="supplier-combobox-clear-btn" onclick="event.stopPropagation(); clearSupplierComboboxSelection();" class="hidden hover:text-rose-500 p-0.5 cursor-pointer" title="Clear selection">
                                            <i class="fas fa-times-circle text-xs"></i>
                                        </span>
                                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="supplier-combobox-arrow"></i>
                                    </div>
                                </button>

                                <!-- Dropdown Box with Live Search -->
                                <div id="supplier-combobox-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden animate-fade-in">
                                    <!-- Search Input inside dropdown -->
                                    <div class="p-2 border-b border-slate-100 bg-slate-50 relative">
                                        <i class="fas fa-search absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" id="supplier-combobox-search-input" oninput="filterSupplierCombobox(this.value)" placeholder="Search supplier by name, mobile, city..." class="w-full bg-white border border-slate-200 rounded-lg pl-8 pr-7 py-1.5 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-medium" autocomplete="off">
                                        <button type="button" onclick="document.getElementById('supplier-combobox-search-input').value=''; filterSupplierCombobox('');" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 text-xs">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Scrollable Suppliers List -->
                                    <div id="supplier-combobox-list" class="max-h-56 overflow-y-auto divide-y divide-slate-100">
                                        <!-- Dynamically populated -->
                                    </div>

                                    <!-- Quick Add Option at Bottom -->
                                    <div class="p-2 border-t border-slate-100 bg-slate-50">
                                        <button type="button" onclick="openQuickAddSupplierModal()" class="w-full text-left px-3 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 rounded-lg transition-colors flex items-center justify-between border border-indigo-200 shadow-xs">
                                            <span class="flex items-center gap-1.5 truncate">
                                                <i class="fas fa-user-plus text-indigo-600"></i>
                                                <span>+ Add New Supplier<span id="quick-add-typed-name"></span></span>
                                            </span>
                                            <span class="text-[10px] bg-white text-indigo-600 px-1.5 py-0.5 rounded font-mono uppercase tracking-wider font-black">Quick Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] text-rose-500 hidden mt-1" id="err-prod_supplier_id">Supplier is required</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Supply / Purchase Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="prod_supply_date" id="prod_supply_date" value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- 2. Products Supplied -->
                <div class="bg-slate-50/50 p-5 rounded-xl border border-slate-100 mb-6">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2 mb-4">
                        <h4 class="text-sm font-bold text-slate-800"><i class="fas fa-box-open text-amber-500 mr-2"></i>2. Products Supplied</h4>
                        <button type="button" onclick="addProductProductField()" class="text-xs bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg font-bold hover:bg-amber-200 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add Another Product
                        </button>
                    </div>
                    <div id="product-products-container" class="space-y-4">
                        <!-- Dynamic fields inserted here -->
                    </div>
                </div>



                <!-- 3. Payment Information -->
                <div class="bg-emerald-50/30 p-5 rounded-xl border border-emerald-100 mb-6">
                    <h4 class="text-sm font-bold text-slate-800 border-b border-emerald-200 pb-2 mb-4"><i class="fas fa-credit-card text-emerald-500 mr-2"></i>3. Payment Information</h4>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Purchased (₹)</label>
                        <input type="number" step="0.01" min="0" name="prod_total_amount_purchased" id="prod_total_amount_purchased" oninput="calculateProdTotalPaid()" class="w-full md:w-1/3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <label class="block text-xs font-bold text-slate-700 mb-3">Select Payment Methods</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <!-- Cash -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="Cash" name="prod_payment_methods[]" onchange="toggleProdPaymentAmount(this, 'prod_payment_amount_cash')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                <span class="text-sm font-bold text-slate-700">Cash</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="prod_payment_amount_cash" id="prod_payment_amount_cash" oninput="calculateProdTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>

                        <!-- UPI -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="UPI" name="prod_payment_methods[]" onchange="toggleProdPaymentAmount(this, 'prod_payment_amount_upi')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                <span class="text-sm font-bold text-slate-700">UPI</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="prod_payment_amount_upi" id="prod_payment_amount_upi" oninput="calculateProdTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>

                        <!-- Bank Transfer -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="Bank Transfer" name="prod_payment_methods[]" onchange="toggleProdPaymentAmount(this, 'prod_payment_amount_bank')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                <span class="text-sm font-bold text-slate-700">Bank Transfer</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="prod_payment_amount_bank" id="prod_payment_amount_bank" oninput="calculateProdTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>

                        <!-- Credit -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="Credit" name="prod_payment_methods[]" onchange="toggleProdPaymentAmount(this, 'prod_payment_amount_credit')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                <span class="text-sm font-bold text-slate-700">Credit</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="prod_payment_amount_credit" id="prod_payment_amount_credit" oninput="calculateProdTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-emerald-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Paid (₹)</label>
                            <input type="number" step="0.01" min="0" id="prod_total_payment_entered" class="w-full bg-emerald-100 border border-emerald-300 text-emerald-900 font-bold rounded-xl px-3 py-2 text-sm outline-none" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Balance to Pay (₹)</label>
                            <input type="number" step="0.01" name="prod_balance_amount" id="prod_balance_amount" class="w-full bg-rose-50 border border-rose-200 text-rose-800 font-bold rounded-xl px-3 py-2 text-sm outline-none" readonly>
                        </div>
                    </div>
                </div>

                <!-- 4. GST & Tax -->
                <div class="bg-blue-50/30 p-5 rounded-xl border border-blue-100">
                    <h4 class="text-sm font-bold text-slate-800 border-b border-blue-200 pb-2 mb-4"><i class="fas fa-file-invoice-dollar text-blue-500 mr-2"></i>4. GST and Tax Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">GST Percentage (%) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.1" min="0" max="100" name="prod_gst" id="prod_gst" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <span class="text-[10px] text-rose-500 hidden mt-1" id="err-prod_gst">Valid GST required</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tax Inclusive</label>
                            <select name="prod_tax_inclusive" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 5. Offers & Discounts -->
                <div class="bg-fuchsia-50/30 p-5 rounded-xl border border-fuchsia-100">
                    <h4 class="text-sm font-bold text-slate-800 border-b border-fuchsia-200 pb-2 mb-4"><i class="fas fa-gift text-fuchsia-500 mr-2"></i>5. Offers and Discounts</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer Available</label>
                            <select name="prod_offer_available" id="prod_offer_available" onchange="toggleProductOffer()" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="prod-offer-field opacity-50 pointer-events-none">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer Type</label>
                            <select name="prod_offer_type" id="prod_offer_type" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
                                <option value="%">Percentage (%)</option>
                                <option value="₹">Fixed Amount (₹)</option>
                            </select>
                        </div>
                        <div class="prod-offer-field opacity-50 pointer-events-none">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer Amount</label>
                            <input type="number" step="0.01" min="0" name="prod_offer_amount" id="prod_offer_amount" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
                            <span class="text-[10px] text-rose-500 hidden mt-1" id="err-prod_offer_amount">Invalid offer amount</span>
                        </div>
                        <div class="prod-offer-field opacity-50 pointer-events-none">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer Start Date</label>
                            <input type="date" name="prod_offer_start" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
                        </div>
                        <div class="prod-offer-field opacity-50 pointer-events-none">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer End Date</label>
                            <input type="date" name="prod_offer_end" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500">
                        </div>
                    </div>
                </div>



                <!-- 7. Additional Information -->
                <div class="bg-rose-50/30 p-5 rounded-xl border border-rose-100">
                    <h4 class="text-sm font-bold text-slate-800 border-b border-rose-200 pb-2 mb-4"><i class="fas fa-info-circle text-rose-500 mr-2"></i>7. Additional Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Product Status</label>
                            <select name="prod_status" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>


                    </div>
                </div>

            </form>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center justify-end gap-3 sticky bottom-0 z-10">
                <button type="button" onclick="closeProductModal()" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 text-sm font-bold transition-all shadow-sm">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('product-form').reset(); clearProductErrors(); toggleProductOffer();" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 text-sm font-bold transition-all shadow-sm">
                    Reset
                </button>
                <button type="submit" form="product-form" class="px-5 py-2.5 rounded-xl text-white bg-amber-600 hover:bg-amber-700 text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-save"></i> Save Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Products Detailed Sheet Modal (Full Screen & Drag to Scroll) -->
<div id="supplier-products-modal" onclick="if(event.target === this) closeSupplierProductsModal()" class="hidden fixed inset-0 z-[105] bg-slate-900/80 backdrop-blur-sm overflow-hidden p-2 sm:p-3 flex items-center justify-center">
    <div class="bg-white rounded-2xl w-full h-[96vh] max-w-[98vw] shadow-2xl relative animate-fade-in mx-auto flex flex-col overflow-hidden border border-slate-200">
        <!-- Sticky Header: Supplier Identity & Compact Single-Line KPI Badges -->
        <div class="px-5 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-white shrink-0 z-20 shadow-xs">
            <!-- Supplier Profile -->
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 truncate max-w-xs" id="sp-modal-supplier-name">Supplier Name</h3>
                        <span id="sp-modal-supplier-code" class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">SUP-0000</span>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate max-w-md" id="sp-modal-supplier-info">Phone • Location • GST</p>
                </div>
            </div>

            <!-- Single-line Compact KPI Badges -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center gap-2 shadow-xs">
                    <i class="fas fa-cubes text-indigo-600 text-xs"></i>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase block leading-none">Products</span>
                        <span class="text-xs font-black text-indigo-900 font-mono leading-none" id="sp-kpi-total-products">0</span>
                    </div>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center gap-2 shadow-xs">
                    <i class="fas fa-shopping-bag text-emerald-600 text-xs"></i>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase block leading-none">Sold</span>
                        <span class="text-xs font-black text-emerald-900 font-mono leading-none" id="sp-kpi-units-sold">0 Units</span>
                    </div>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-100 flex items-center gap-2 shadow-xs">
                    <i class="fas fa-layer-group text-amber-600 text-xs"></i>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase block leading-none">In Stock</span>
                        <span class="text-xs font-black text-amber-900 font-mono leading-none" id="sp-kpi-stock-remaining">0 Units</span>
                    </div>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-purple-50 border border-purple-100 flex items-center gap-2 shadow-xs">
                    <i class="fas fa-receipt text-purple-600 text-xs"></i>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase block leading-none">Sales Revenue</span>
                        <span class="text-xs font-black text-purple-900 font-mono leading-none" id="sp-kpi-sales-revenue">₹0.00</span>
                    </div>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center gap-2">
                <!-- Drag helper & Left/Right Nudge buttons -->
                <div class="hidden sm:flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button type="button" onclick="scrollSheetLeft()" title="Scroll Left" class="w-6 h-6 rounded-lg bg-white hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 flex items-center justify-center text-[10px] shadow-xs transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="px-2 text-[10px] font-bold text-slate-600 flex items-center gap-1 select-none">
                        <i class="fas fa-hand-paper text-indigo-500 animate-pulse"></i> Drag Left/Right
                    </span>
                    <button type="button" onclick="scrollSheetRight()" title="Scroll Right" class="w-6 h-6 rounded-lg bg-white hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 flex items-center justify-center text-[10px] shadow-xs transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <button type="button" onclick="printSupplierProductsSheet()" title="Print / Export Sheet" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" onclick="closeSupplierProductsModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>
        </div>

        <!-- Filter Sub-bar -->
        <div class="px-5 py-2 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                <div class="relative flex-1 max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="sp-sheet-search" oninput="renderSupplierProductsSheet()" placeholder="Search product name, SKU..." class="w-full bg-white border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 transition-all font-medium">
                </div>
                <select id="sp-sheet-status-filter" onchange="renderSupplierProductsSheet()" class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-700 outline-none focus:border-indigo-500 font-medium">
                    <option value="">All Product Statuses</option>
                    <option value="partially_sold">Partially Sold</option>
                    <option value="sold_out">Sold Out</option>
                    <option value="in_stock">In Stock (Unsold)</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-500 font-medium hidden md:inline">
                    <i class="fas fa-mouse-pointer text-indigo-400 mr-1"></i>Click & drag table horizontally to explore columns
                </span>
                <button type="button" onclick="openAddProductForSupplier(currentSheetSupplierId)" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap shadow-xs">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>

        <!-- Printable & Draggable Table Area (Full Flex Height) -->
        <div class="flex-1 overflow-hidden flex flex-col relative" id="supplier-sheet-printable-area">
            <!-- Table Container with Mouse Drag Scroll -->
            <div id="sp-table-scroll-container" class="flex-1 overflow-x-auto overflow-y-auto cursor-grab select-none bg-slate-50/20">
                <table class="w-max min-w-full text-left text-xs text-slate-600 border-collapse table-fixed">
                    <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-black border-b border-slate-200 tracking-wider sticky top-0 z-10 shadow-xs h-10">
                        <tr>
                            <th class="w-12 min-w-[48px] max-w-[48px] px-2 text-center bg-slate-100 truncate">#</th>
                            <th class="w-64 min-w-[260px] max-w-[260px] px-3 bg-slate-100 truncate">Product & SKU</th>
                            <th class="w-36 min-w-[140px] max-w-[140px] px-3 bg-slate-100 truncate">Category</th>
                            <th class="w-28 min-w-[110px] max-w-[110px] px-3 text-center bg-slate-100 truncate">Supply Date</th>
                            <th class="w-28 min-w-[110px] max-w-[110px] px-3 text-right bg-slate-100 truncate">Piece Rate</th>
                            <th class="w-24 min-w-[90px] max-w-[90px] px-2 text-center bg-slate-100 truncate">Margin %</th>
                            <th class="w-32 min-w-[120px] max-w-[120px] px-3 text-right bg-slate-100 truncate">Selling Price</th>
                            <th class="w-24 min-w-[90px] max-w-[90px] px-2 text-center bg-slate-100 truncate">Sold</th>
                            <th class="w-28 min-w-[100px] max-w-[100px] px-2 text-center bg-slate-100 truncate">In Stock</th>
                            <th class="w-36 min-w-[130px] max-w-[130px] px-2 text-center bg-slate-100 truncate">Sales Status</th>
                            <th class="w-36 min-w-[140px] max-w-[140px] px-3 text-right bg-slate-100 truncate">Total Sold Value</th>
                        </tr>
                    </thead>
                    <tbody id="sp-sheet-table-body" class="divide-y divide-slate-100 bg-white">
                        <!-- Populated dynamically with fixed height & widths -->
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold sticky bottom-0 z-10 shadow-xs h-12">
                        <tr id="sp-sheet-footer-row">
                            <!-- Populated dynamically -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="px-5 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0 z-20">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <i class="fas fa-hand-paper text-indigo-500"></i>
                <span class="truncate">Grab anywhere on table to drag left/right. Full screen view eliminates vertical page scrolling.</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="closeSupplierProductsModal()" class="px-4 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all shadow-xs">
                    Close
                </button>
                <button type="button" onclick="openAddProductForSupplier(currentSheetSupplierId)" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Supplier Modal -->
<div id="quick-add-supplier-modal" class="hidden fixed inset-0 z-[120] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative animate-fade-in border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-indigo-50/50">
            <h3 class="text-sm font-extrabold text-indigo-950 flex items-center gap-2">
                <i class="fas fa-truck text-indigo-600"></i> Quick Register Supplier
            </h3>
            <button type="button" onclick="closeQuickAddSupplierModal()" class="text-slate-400 hover:text-rose-500 p-1 rounded-lg transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="quick-add-supplier-form" onsubmit="handleQuickAddSupplier(event)" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Supplier / Artisan Name <span class="text-rose-500">*</span></label>
                <input type="text" id="quick-supp-name" required placeholder="e.g. Swamimalai Bronze Works" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                <input type="tel" id="quick-supp-mobile" required placeholder="e.g. 9876543210" maxlength="15" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">City / Town</label>
                    <input type="text" id="quick-supp-city" placeholder="Swamimalai" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GST Number (Optional)</label>
                    <input type="text" id="quick-supp-gst" placeholder="GSTIN (optional)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 font-mono uppercase">
                </div>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeQuickAddSupplierModal()" class="px-3 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="quick-supp-save-btn" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-check"></i> Save & Select Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Supplier View Modal -->
<div id="supplier-view-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-full w-full flex items-center justify-center p-4 sm:p-6 py-10">
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl relative animate-fade-in mx-auto">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-eye text-indigo-600"></i> View Supplier Details
                </h3>
                <button onclick="closeSupplierViewModal()" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="supplier-view-content">
                <!-- Injected via JS -->
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center justify-end">
                <button onclick="closeSupplierViewModal()" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 text-sm font-bold transition-all shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product View Modal -->
<div id="product-view-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-full w-full flex items-center justify-center p-4 sm:p-6 py-10">
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl relative animate-fade-in mx-auto">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-eye text-amber-600"></i> View Product Details
                </h3>
                <button onclick="closeProductViewModal()" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="product-view-content">
                <!-- Injected via JS -->
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center justify-end">
                <button onclick="closeProductViewModal()" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 text-sm font-bold transition-all shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
<!-- Interactive Confirmation Modal (Self-Contained) -->
<div id="interactive-confirm-modal" class="hidden fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200">
    <div id="interactive-confirm-box" class="bg-white border border-slate-200 rounded-2xl w-full max-w-sm sm:max-w-md shadow-2xl p-6 relative transform scale-95 opacity-0 transition-all duration-200 text-center">
        <div id="interactive-confirm-icon-wrap" class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm bg-rose-50 border border-rose-100 text-rose-500">
            <i id="interactive-confirm-icon" class="fas fa-trash-alt"></i>
        </div>
        <h3 id="interactive-confirm-title" class="text-lg font-extrabold text-slate-900 tracking-tight mb-2">Delete Confirmation</h3>
        <div id="interactive-confirm-message" class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6 font-medium">Are you sure you want to proceed?</div>
        <div class="flex items-center justify-center gap-3">
            <button type="button" id="interactive-confirm-cancel-btn" class="w-1/2 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-bold transition-all shadow-sm cursor-pointer">
                Cancel
            </button>
            <button type="button" id="interactive-confirm-ok-btn" class="w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-rose-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-trash-alt mr-1"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
    // --- INTERACTIVE CONFIRMATION DIALOG (PROMISE-BASED) ---
    function showConfirmDialog(options = {}) {
        return new Promise((resolve) => {
            const modal = document.getElementById('interactive-confirm-modal');
            const box = document.getElementById('interactive-confirm-box');
            const titleEl = document.getElementById('interactive-confirm-title');
            const msgEl = document.getElementById('interactive-confirm-message');
            const iconWrap = document.getElementById('interactive-confirm-icon-wrap');
            const iconEl = document.getElementById('interactive-confirm-icon');
            const okBtn = document.getElementById('interactive-confirm-ok-btn');
            const cancelBtn = document.getElementById('interactive-confirm-cancel-btn');

            if (!modal || !box) {
                resolve(true);
                return;
            }

            const title = options.title || 'Delete Confirmation';
            const message = typeof options === 'string' ? options : (options.message || 'Are you sure you want to proceed?');
            const confirmText = options.confirmText || 'Yes, Delete';
            const cancelText = options.cancelText || 'Cancel';
            const type = options.type || 'danger';

            titleEl.textContent = title;
            msgEl.innerHTML = message;
            okBtn.innerHTML = `<i class="fas fa-trash-alt mr-1.5"></i> ${confirmText}`;
            cancelBtn.textContent = cancelText;

            if (type === 'danger') {
                iconWrap.className = 'w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
                iconEl.className = 'fas fa-trash-alt text-rose-500';
                okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-rose-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 cursor-pointer';
            } else if (type === 'warning') {
                iconWrap.className = 'w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
                iconEl.className = 'fas fa-exclamation-triangle text-amber-500';
                okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-amber-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 cursor-pointer';
            } else {
                iconWrap.className = 'w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
                iconEl.className = 'fas fa-info-circle text-indigo-500';
                okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 cursor-pointer';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);

            function cleanup(result) {
                box.classList.remove('scale-100', 'opacity-100');
                box.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    resolve(result);
                }, 180);
                document.removeEventListener('keydown', handleKey);
            }

            function handleKey(e) {
                if (e.key === 'Escape') cleanup(false);
            }

            okBtn.onclick = () => cleanup(true);
            cancelBtn.onclick = () => cleanup(false);
            modal.onclick = (e) => {
                if (e.target === modal) cleanup(false);
            };
            document.addEventListener('keydown', handleKey);
        });
    }
    window.showConfirmDialog = showConfirmDialog;

    // Toast notification fallback if app.js is not loaded
    if (typeof window.showToast !== 'function') {
        window.showToast = function(type, message) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-5 right-5 z-[10001] flex flex-col space-y-3 pointer-events-none';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-center p-4 min-w-[300px] max-w-md rounded-xl shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 border ' + 
                (type === 'success' ? 'bg-slate-900 text-amber-300 border-amber-500/40' : 'bg-slate-900 text-rose-300 border-rose-500/40');
            toast.innerHTML = `<div class="mr-3 text-lg"><i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-triangle text-rose-400'}"></i></div><div class="flex-1 text-xs font-bold">${message}</div>`;
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
            setTimeout(() => { toast.classList.add('opacity-0'); setTimeout(() => toast.remove(), 300); }, 3500);
        };
    }

    // --- SECTION TOGGLE LOGIC ---
    function toggleSection(sectionId) {
        const sections = ['supplier-section', 'product-section', 'barcode-section', 'invoice-section'];
        
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
            }
            const btn = document.getElementById('btn-' + id);
            if (btn) {
                btn.classList.remove('active-tab');
            }
        });

        const target = document.getElementById(sectionId);
        if (target) {
            target.classList.remove('hidden');
        }
        const activeBtn = document.getElementById('btn-' + sectionId);
        if (activeBtn) {
            activeBtn.classList.add('active-tab');
        }

        if (sectionId === 'invoice-section' && typeof renderInvoiceTable === 'function') {
            renderInvoiceTable();
        }
        if (sectionId === 'product-section' && typeof renderProductTable === 'function') {
            renderProductTable();
        }
        if (sectionId === 'barcode-section' && typeof renderBarcodeTable === 'function') {
            renderBarcodeTable();
        }
        if (sectionId === 'supplier-section' && typeof renderSupplierTable === 'function') {
            renderSupplierTable();
        }
    }

    // --- URL HELPER ---
    function getAppUrl(path = '') {
        const base = (window.APP_URL || '').replace(/\/+$/, '');
        const cleanPath = (path || '').replace(/^\/+/, '');
        return cleanPath ? base + '/' + cleanPath : base;
    }

    // --- QUICK ADD CATEGORY LOGIC ---
    window.CATEGORIES_DATA = <?= json_encode($categories ?? []) ?>;
    
    function renderCategoryDropdown(inputElement, filterText = '') {
        const container = inputElement.parentElement;
        const dropdown = container.querySelector('.category-dropdown');
        if (!dropdown) return;
        dropdown.innerHTML = '';
        
        let filtered = window.CATEGORIES_DATA || [];
        if (filterText) {
            filtered = filtered.filter(c => (c.name || '').toLowerCase().includes(filterText.toLowerCase()));
        }
        
        if (filtered.length > 0) {
            filtered.forEach(c => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 text-xs text-slate-700 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 flex items-center justify-between transition-colors';
                item.textContent = c.name;
                item.onmousedown = function(e) {
                    e.preventDefault();
                    inputElement.value = c.name;
                    const hidden = container.querySelector('input[type="hidden"]');
                    if (hidden) hidden.value = c.name;
                    dropdown.classList.add('hidden');
                    onCategoryChanged(inputElement);
                };
                dropdown.appendChild(item);
            });

            // Option to add category at bottom
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'w-full text-left px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition-colors border-t border-slate-100 flex items-center gap-1.5';
            addBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i> Add Category';
            addBtn.onmousedown = function(e) {
                e.preventDefault();
                openQuickAddCategoryModal(inputElement);
                dropdown.classList.add('hidden');
            };
            dropdown.appendChild(addBtn);
        } else {
            const noData = document.createElement('div');
            noData.className = 'px-3 py-2 text-xs text-slate-500 italic border-b border-slate-100 bg-slate-50';
            noData.textContent = 'No category found';
            dropdown.appendChild(noData);
            
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'w-full text-left px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition-colors flex items-center gap-1.5';
            addBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i> Add Category';
            addBtn.onmousedown = function(e) {
                e.preventDefault();
                openQuickAddCategoryModal(inputElement);
                dropdown.classList.add('hidden');
            };
            dropdown.appendChild(addBtn);
        }
        dropdown.classList.remove('hidden');
    }

    function showCategoryDropdown(el) {
        renderCategoryDropdown(el, el.value.trim());
    }

    function filterCategoryDropdown(el) {
        renderCategoryDropdown(el, el.value.trim());
        const hidden = el.parentElement.querySelector('input[type="hidden"]');
        if (hidden) hidden.value = el.value.trim();
        onCategoryChanged(el);
    }

    function onCategoryChanged(categoryInput) {
        const parentBlock = categoryInput.closest('.supplier-product-block') || categoryInput.closest('.product-product-block');
        if (!parentBlock) return;
        const subcatInput = parentBlock.querySelector('.subcat-search-input');
        const subcatHidden = parentBlock.querySelector('input[name="sp_product_subcategory[]"]') || parentBlock.querySelector('input[name="ap_product_subcategory[]"]');
        if (!subcatInput) return;
        
        const catName = (categoryInput.value || '').trim().toLowerCase();
        const catObj = (window.CATEGORIES_DATA || []).find(c => (c.name || '').toLowerCase() === catName);
        
        const curSubVal = (subcatInput.value || '').trim().toLowerCase();
        if (curSubVal && catObj) {
            // Only reset subcategory if it belongs to a known DIFFERENT category
            const knownSub = (window.SUBCATEGORIES_DATA || []).find(s => (s.name || '').toLowerCase() === curSubVal);
            if (knownSub && String(knownSub.category_id) !== String(catObj.id)) {
                subcatInput.value = '';
                if (subcatHidden) subcatHidden.value = '';
            }
        }
    }
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.category-dropdown') && !e.target.classList.contains('cat-search-input')) {
            document.querySelectorAll('.category-dropdown').forEach(d => d.classList.add('hidden'));
        }
        if (!e.target.closest('.subcategory-dropdown') && !e.target.classList.contains('subcat-search-input')) {
            document.querySelectorAll('.subcategory-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    function openQuickAddCategoryModal(inputElement) {
        window.currentCategoryInput = inputElement;
        const nameInput = document.getElementById('quick-cat-name');
        nameInput.value = inputElement ? inputElement.value : ''; 
        document.getElementById('quick-cat-status').value = 'active';
        document.getElementById('quick-add-category-modal').classList.remove('hidden');
        setTimeout(() => nameInput.focus(), 50);
    }

    async function submitQuickCategory() {
        const nameInput = document.getElementById('quick-cat-name');
        const name = nameInput.value.trim();
        const status = document.getElementById('quick-cat-status').value;
        
        if (!name) {
            showToast('error', 'Category Name is required.');
            nameInput.focus();
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || ''); 
            formData.append('name', name);
            formData.append('status', status);
            
            const res = await fetch(getAppUrl('categories/create'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            if (data.success) {
                const catId = (data.data && data.data.id) ? data.data.id : (data.id || Date.now());
                const newCat = { id: catId, name: name, status: status };
                if (!window.CATEGORIES_DATA) window.CATEGORIES_DATA = [];
                const existIdx = window.CATEGORIES_DATA.findIndex(c => (c.name || '').toLowerCase() === name.toLowerCase());
                if (existIdx >= 0) {
                    window.CATEGORIES_DATA[existIdx] = { ...window.CATEGORIES_DATA[existIdx], ...newCat };
                } else {
                    window.CATEGORIES_DATA.push(newCat);
                }
                
                if (window.currentCategoryInput) {
                    window.currentCategoryInput.value = name;
                    const hidden = window.currentCategoryInput.parentElement.querySelector('input[type="hidden"]');
                    if (hidden) hidden.value = name;
                    onCategoryChanged(window.currentCategoryInput);
                }
                showToast('success', data.message || 'Category added successfully!');
                document.getElementById('quick-add-category-modal').classList.add('hidden');
                nameInput.value = '';
            } else {
                showToast('error', data.message || 'Error saving category');
            }
        } catch(e) {
            console.error(e);
            showToast('error', 'Network error occurred.');
        }
    }

    // --- QUICK ADD SUBCATEGORY LOGIC ---
    window.SUBCATEGORIES_DATA = <?= json_encode($subcategories ?? []) ?>;
    
    function renderSubcategoryDropdown(inputElement, filterText = '') {
        const container = inputElement.parentElement;
        const dropdown = container.querySelector('.subcategory-dropdown');
        if (!dropdown) return;
        dropdown.innerHTML = '';
        
        // Check if parent category is selected in the same product block (supports both Add Supplier & Add Products Detail)
        const parentBlock = inputElement.closest('.supplier-product-block') || inputElement.closest('.product-product-block');
        let selectedCatName = '';
        if (parentBlock) {
            const catInput = parentBlock.querySelector('input[name="sp_product_category[]"]') || parentBlock.querySelector('input[name="ap_product_category[]"]') || parentBlock.querySelector('.cat-search-input');
            if (catInput) selectedCatName = (catInput.value || '').trim();
        }

        let catObj = null;
        if (selectedCatName && window.CATEGORIES_DATA) {
            catObj = window.CATEGORIES_DATA.find(c => (c.name || '').toLowerCase() === selectedCatName.toLowerCase());
        }

        let filtered = window.SUBCATEGORIES_DATA || [];
        if (catObj) {
            filtered = filtered.filter(c => String(c.category_id) === String(catObj.id));
        }

        if (filterText) {
            filtered = filtered.filter(c => (c.name || '').toLowerCase().includes(filterText.toLowerCase()));
        }
        
        if (filtered.length > 0) {
            filtered.forEach(c => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 text-xs text-slate-700 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 flex items-center justify-between transition-colors';
                item.textContent = c.name;
                item.onmousedown = function(e) {
                    e.preventDefault();
                    inputElement.value = c.name;
                    const hidden = container.querySelector('input[type="hidden"]');
                    if (hidden) hidden.value = c.name;
                    dropdown.classList.add('hidden');
                };
                dropdown.appendChild(item);
            });

            // Option to add subcategory at bottom
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'w-full text-left px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition-colors border-t border-slate-100 flex items-center gap-1.5';
            addBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i> Add Subcategory';
            addBtn.onmousedown = function(e) {
                e.preventDefault();
                openQuickAddSubcategoryModal(inputElement);
                dropdown.classList.add('hidden');
            };
            dropdown.appendChild(addBtn);
        } else {
            const noData = document.createElement('div');
            noData.className = 'px-3 py-2 text-xs text-slate-500 italic border-b border-slate-100 bg-slate-50';
            noData.textContent = catObj ? 'No subcategory found for ' + catObj.name : 'No subcategory found';
            dropdown.appendChild(noData);
            
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'w-full text-left px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 transition-colors flex items-center gap-1.5';
            addBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i> Add Subcategory';
            addBtn.onmousedown = function(e) {
                e.preventDefault();
                openQuickAddSubcategoryModal(inputElement);
                dropdown.classList.add('hidden');
            };
            dropdown.appendChild(addBtn);
        }
        dropdown.classList.remove('hidden');
    }

    function showSubcategoryDropdown(el) {
        renderSubcategoryDropdown(el, el.value.trim());
    }

    function filterSubcategoryDropdown(el) {
        renderSubcategoryDropdown(el, el.value.trim());
        const hidden = el.parentElement.querySelector('input[type="hidden"]');
        if (hidden) hidden.value = el.value.trim(); 
    }

    function openQuickAddSubcategoryModal(inputElement) {
        window.currentSubcategoryInput = inputElement;
        
        const parentSelect = document.getElementById('quick-subcat-parent');
        parentSelect.innerHTML = '<option value="">Select Parent Category</option>';
        (window.CATEGORIES_DATA || []).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.name;
            parentSelect.appendChild(opt);
        });

        // Pre-select category if entered in the adjacent category field
        const parentBlock = inputElement ? (inputElement.closest('.supplier-product-block') || inputElement.closest('.product-product-block')) : null;
        if (parentBlock) {
            const catInput = parentBlock.querySelector('input[name="sp_product_category[]"]') || parentBlock.querySelector('input[name="ap_product_category[]"]') || parentBlock.querySelector('.cat-search-input');
            const selectedCatName = catInput ? (catInput.value || '').trim() : '';
            if (selectedCatName) {
                const foundCat = (window.CATEGORIES_DATA || []).find(c => (c.name || '').toLowerCase() === selectedCatName.toLowerCase());
                if (foundCat) parentSelect.value = foundCat.id;
            }
        }

        const nameInput = document.getElementById('quick-subcat-name');
        nameInput.value = inputElement ? inputElement.value : ''; 
        document.getElementById('quick-subcat-status').value = 'active';
        document.getElementById('quick-add-subcategory-modal').classList.remove('hidden');
        setTimeout(() => nameInput.focus(), 50);
    }

    async function submitQuickSubcategory() {
        const nameInput = document.getElementById('quick-subcat-name');
        const name = nameInput.value.trim();
        const parentSelect = document.getElementById('quick-subcat-parent');
        const parentId = parentSelect.value;
        const status = document.getElementById('quick-subcat-status').value;
        
        if (!parentId) {
            showToast('error', 'Parent Category is required.');
            parentSelect.focus();
            return;
        }

        if (!name) {
            showToast('error', 'Subcategory Name is required.');
            nameInput.focus();
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || ''); 
            formData.append('name', name);
            formData.append('category_id', parentId);
            formData.append('status', status);
            
            const res = await fetch(getAppUrl('subcategories/create'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            if (data.success) {
                const subId = (data.data && data.data.id) ? data.data.id : (data.id || Date.now());
                const newSub = { id: subId, name: name, category_id: parentId, status: status };
                if (!window.SUBCATEGORIES_DATA) window.SUBCATEGORIES_DATA = [];
                const existIdx = window.SUBCATEGORIES_DATA.findIndex(s => String(s.category_id) === String(parentId) && (s.name || '').toLowerCase() === name.toLowerCase());
                if (existIdx >= 0) {
                    window.SUBCATEGORIES_DATA[existIdx] = { ...window.SUBCATEGORIES_DATA[existIdx], ...newSub };
                } else {
                    window.SUBCATEGORIES_DATA.push(newSub);
                }

                if (window.currentSubcategoryInput) {
                    window.currentSubcategoryInput.value = name;
                    const hidden = window.currentSubcategoryInput.parentElement.querySelector('input[type="hidden"]');
                    if (hidden) hidden.value = name;
                }
                showToast('success', data.message || 'Subcategory added successfully!');
                document.getElementById('quick-add-subcategory-modal').classList.add('hidden');
                nameInput.value = '';
            } else {
                showToast('error', data.message || 'Error saving subcategory');
            }
        } catch(e) {
            console.error(e);
            showToast('error', 'Network error occurred.');
        }
    }

    // --- SUPPLIER & PRODUCT DATA PERSISTENCE ---
    window.DB_SUPPLIERS = <?= json_encode($allSuppliers ?? $suppliers ?? []) ?>;
    window.DB_PRODUCTS = <?= json_encode($allProducts ?? []) ?>;
    window.DB_INVOICES = <?= json_encode($recentInvoices ?? []) ?>;
    let suppliers = [];
    let products = [];
    let invoices = [];
    let currentEditId = null;
    let currentProductEditId = null;

    function mapSupplierFromDb(s) {
        return {
            id: s.id,
            supplier_name: s.name || s.supplier_name || '',
            mobile_number: s.mobile || s.mobile_number || s.phone || '',
            purchase_date: s.created_at ? s.created_at.split(' ')[0] : (s.purchase_date || new Date().toISOString().split('T')[0]),
            email: s.email || '',
            alt_mobile_number: s.alt_phone || s.alt_mobile_number || '',
            address: s.address || '',
            city: s.city || '',
            state: s.state || 'Tamil Nadu',
            gst_number: s.gst_number || s.tax_number || '',
            barcode: s.supplier_code || s.barcode || ('SUP-' + String(s.id).padStart(4, '0')),
            payment_methods: s.payment_methods || s.payment_method || 'Cash',
            payment_amount_cash: s.payment_amount_cash || '',
            payment_amount_upi: s.payment_amount_upi || '',
            payment_amount_bank: s.payment_amount_bank || '',
            payment_amount_credit: s.payment_amount_credit || '',
            total_amount_purchased: s.total_amount_purchased || s.total_amount || s.amount || '0.00',
            paid_amount: s.paid_amount !== undefined ? s.paid_amount : (s.total_amount_paid !== undefined ? s.total_amount_paid : (s.total_paid || '0.00')),
            total_amount_paid: s.paid_amount !== undefined ? s.paid_amount : (s.total_amount_paid !== undefined ? s.total_amount_paid : (s.total_paid || '0.00')),
            balance_amount: s.balance_amount !== undefined ? s.balance_amount : '0.00',
            payment_status: s.payment_status || s.saved_payment_status || 'Unpaid',
            status: (s.status && s.status.toLowerCase() === 'inactive') ? 'Inactive' : 'Active'
        };
    }

    function mapProductFromDb(p) {
        return {
            id: p.id,
            prod_supplier_id: p.supplier_id || p.prod_supplier_id || null,
            prod_name: p.name || p.prod_name || '',
            prod_sku: p.sku || p.prod_sku || ('SKU-' + p.id),
            prod_barcode: p.barcode || p.prod_barcode || ('BAR-' + p.id),
            prod_category: p.category_name || p.prod_category || 'General',
            prod_subcategory: p.subcategory_name || p.prod_subcategory || '',
            prod_category_id: p.category_id || null,
            prod_subcategory_id: p.subcategory_id || null,
            prod_unit: p.unit || 'Pieces',
            prod_piece_rate: parseFloat(p.piece_rate || p.prod_piece_rate || 0),
            prod_margin_percentage: (p.margin_percentage !== undefined && p.margin_percentage !== null) ? p.margin_percentage : (p.prod_margin_percentage !== undefined ? p.prod_margin_percentage : null),
            prod_purchase_price: parseFloat(p.purchase_price || p.prod_purchase_price || 0),
            prod_selling_price: parseFloat(p.selling_price || p.prod_selling_price || 0),
            prod_gst: parseFloat(p.gst_percent !== undefined ? p.gst_percent : (p.gst_rate !== undefined ? p.gst_rate : (p.prod_gst || 0))),
            prod_tax_inclusive: p.tax_inclusive || p.prod_tax_inclusive || 'No',
            prod_offer_available: p.offer_available || p.prod_offer_available || 'No',
            prod_offer_type: p.offer_type || p.prod_offer_type || '%',
            prod_offer_amount: p.offer_amount || p.prod_offer_amount || '',
            prod_opening_stock: parseInt(p.current_stock !== undefined ? p.current_stock : (p.stock_quantity !== undefined ? p.stock_quantity : (p.prod_opening_stock || 0))),
            prod_current_stock: parseInt(p.current_stock !== undefined ? p.current_stock : (p.stock_quantity !== undefined ? p.stock_quantity : (p.prod_current_stock || 0))),
            prod_status: (p.status && (p.status.toLowerCase() === 'inactive' || p.status.toLowerCase() === 'out_of_stock')) ? (p.status.toLowerCase() === 'out_of_stock' ? 'Out of Stock' : 'Inactive') : 'Active',
            last_restocked_qty: p.last_restocked_qty ? parseInt(p.last_restocked_qty) : 0,
            last_restocked_at: p.last_restocked_at || '',
            total_restocked_qty: p.total_restocked_qty ? parseInt(p.total_restocked_qty) : 0,
            total_sold_qty: parseInt(p.total_sold_qty || 0),
            created_at: p.created_at || ''
        };
    }

    function saveState() {
        try {
            localStorage.setItem('god_erp_suppliers', JSON.stringify(suppliers));
            localStorage.setItem('god_erp_products', JSON.stringify(products));
        } catch (e) {
            console.error('Error saving state to localStorage', e);
        }
    }

    function loadState() {
        if (window.DB_SUPPLIERS && window.DB_SUPPLIERS.length > 0) {
            suppliers = window.DB_SUPPLIERS.map(mapSupplierFromDb);
        } else {
            const savedSuppliers = localStorage.getItem('god_erp_suppliers');
            if (savedSuppliers) {
                try { suppliers = JSON.parse(savedSuppliers) || []; } catch(e) {}
            }
        }

        if (window.DB_PRODUCTS && window.DB_PRODUCTS.length > 0) {
            products = window.DB_PRODUCTS.map(mapProductFromDb);
        } else {
            const savedProducts = localStorage.getItem('god_erp_products');
            if (savedProducts) {
                try { products = JSON.parse(savedProducts) || []; } catch(e) {}
            }
        }

        if (window.DB_INVOICES && Array.isArray(window.DB_INVOICES)) {
            invoices = window.DB_INVOICES.slice();
        }

        updateSupplierDropdowns();
    }

    async function refreshStateFromDb() {
        try {
            const res = await fetch(getAppUrl('products/api-data'), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                window.CATEGORIES_DATA = data.categories || [];
                window.SUBCATEGORIES_DATA = data.subcategories || [];
                suppliers = (data.suppliers || []).map(mapSupplierFromDb);
                products = (data.products || []).map(mapProductFromDb);
                if (data.invoices && Array.isArray(data.invoices)) {
                    invoices = data.invoices;
                    renderInvoiceTable();
                }
                saveState();
                renderSupplierTable();
                renderProductTable();
                renderBarcodeTable();
                updateSupplierDropdowns();
            }
        } catch (err) {
            console.error('Error refreshing state from DB:', err);
        }
    }

    function updateSupplierDropdowns() {
        const sel = document.getElementById('prod_supplier_id');
        if (!sel) return;
        const curVal = sel.value;
        sel.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.supplier_name;
            if (String(s.id) === String(curVal)) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    function openSupplierModal() {
        document.getElementById('supplier-form').reset();
        clearErrors();
        currentEditId = null;
        document.getElementById('supplier-products-container').innerHTML = '';
        // Standalone supplier mode: no product required
        
        // Hide payment amount inputs
        ['payment_amount_cash', 'payment_amount_upi', 'payment_amount_bank', 'payment_amount_credit'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.classList.add('hidden'); el.value = ''; }
        });
        const totalPurchased = document.getElementById('total_amount_purchased');
        if (totalPurchased) totalPurchased.value = '0.00';
        const totalPaid = document.getElementById('total_payment_entered');
        if (totalPaid) totalPaid.value = '';
        const balance = document.getElementById('balance_amount');
        if (balance) balance.value = '0.00';
        const badge = document.getElementById('supplier_status_badge');
        if (badge) {
            badge.className = 'text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-600 border border-slate-200';
            badge.textContent = 'Status: Unpaid';
        }
        const statusHidden = document.getElementById('supplier_payment_status');
        if (statusHidden) statusHidden.value = 'Unpaid';
        const errPaid = document.getElementById('err-total_paid');
        if (errPaid) { errPaid.textContent = ''; errPaid.classList.add('hidden'); }

        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-truck-loading text-indigo-600"></i> Add Supplier';
        const modal = document.getElementById('supplier-modal');
        modal.classList.remove('hidden');
        modal.classList.remove('flex');
    }

    function closeSupplierModal() {
        currentEditId = null;
        const modal = document.getElementById('supplier-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function clearErrors() {
        const errorSpans = document.querySelectorAll('span[id^="err-"]');
        errorSpans.forEach(span => span.classList.add('hidden'));
        const inputs = document.querySelectorAll('#supplier-form input, #supplier-form select, #supplier-form textarea');
        inputs.forEach(input => input.classList.remove('border-rose-500'));
    }

    function showError(fieldId, message = null) {
        const span = document.getElementById('err-' + fieldId);
        const input = document.getElementById(fieldId);
        if (span) {
            if (message) span.textContent = message;
            span.classList.remove('hidden');
        }
        if (input) {
            input.classList.add('border-rose-500');
        }
    }

    


    function addSupplierProductField(productData = null) {
        const container = document.getElementById('supplier-products-container');
        const div = document.createElement('div');
        div.className = 'supplier-product-block relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 border border-amber-200 rounded-lg bg-white shadow-sm';
        
        let marginVal = '';
        if (productData) {
            if (productData.margin_percentage !== undefined && productData.margin_percentage !== null && productData.margin_percentage !== '') {
                marginVal = productData.margin_percentage;
            } else if (productData.prod_margin_percentage !== undefined && productData.prod_margin_percentage !== null && productData.prod_margin_percentage !== '') {
                marginVal = productData.prod_margin_percentage;
            }
        }

        div.innerHTML = `
            <button type="button" onclick="removeSupplierProductField(this)" class="absolute -top-2 -right-2 bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors shadow-sm">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Product Name</label>
                <input type="text" name="sp_product_name[]" value="${productData ? (productData.prod_name || '') : ''}" placeholder="e.g. Brass Ganesha Idol" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="checkProductRestockMatch(this)">
                <div class="restock-hint-container mt-1 hidden"></div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Category</label>
                <input type="text" autocomplete="off" onfocus="showCategoryDropdown(this)" onclick="showCategoryDropdown(this)" oninput="filterCategoryDropdown(this)" onchange="const h=this.parentElement.querySelector('input[type=hidden]');if(h)h.value=this.value.trim();" placeholder="Search category or Add category" class="cat-search-input w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" value="${productData ? (productData.prod_category || '') : ''}">
                <input type="hidden" name="sp_product_category[]" value="${productData ? (productData.prod_category || '') : ''}">
                <div class="category-dropdown absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-xl hidden max-h-48 overflow-y-auto mt-1" style="top: 100%;"></div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Subcategory</label>
                <input type="text" autocomplete="off" onfocus="showSubcategoryDropdown(this)" onclick="showSubcategoryDropdown(this)" oninput="filterSubcategoryDropdown(this)" onchange="const h=this.parentElement.querySelector('input[type=hidden]');if(h)h.value=this.value.trim();" placeholder="Search subcategory or Add subcategory" class="subcat-search-input w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" value="${productData ? (productData.prod_subcategory || '') : ''}">
                <input type="hidden" name="sp_product_subcategory[]" value="${productData ? (productData.prod_subcategory || '') : ''}">
                <div class="subcategory-dropdown absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-xl hidden max-h-48 overflow-y-auto mt-1" style="top: 100%;"></div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Pieces Count</label>
                <input type="number" min="0" name="sp_unit[]" value="${productData ? (productData.prod_current_stock !== undefined ? productData.prod_current_stock : (productData.prod_unit || '')) : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 10" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">One Piece Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_piece_rate[]" value="${productData ? (productData.prod_piece_rate || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 100" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Total Purchase Price (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_purchase_price[]" value="${productData ? (productData.prod_purchase_price || '') : ''}" class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-lg px-2 py-1.5 text-xs outline-none cursor-not-allowed" readonly>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Margin Percentage (%)</label>
                <input type="number" step="0.01" min="0" name="sp_margin_percentage[]" value="${marginVal}" placeholder="Enter Margin % (Default: 60%)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" oninput="calculateSupplierSellingPrice(this)">
                <span class="text-[10px] text-rose-500 hidden mt-0.5 err-margin-msg"></span>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Selling Price (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_selling_price[]" value="${productData ? (productData.prod_selling_price || '') : ''}" class="w-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">GST (%)</label>
                <input type="number" step="0.1" min="0" max="100" name="sp_gst[]" value="${productData ? (productData.prod_gst || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Barcode (Auto)</label>
                <input type="text" name="sp_barcode[]" value="${productData && (productData.barcode || productData.prod_barcode) ? (productData.barcode || productData.prod_barcode) : generateSupplierBarcode()}" class="w-full bg-slate-100 border border-slate-200 rounded-lg px-2 py-1.5 text-xs text-slate-500 font-mono outline-none" readonly>
            </div>
            <input type="hidden" name="sp_product_id[]" value="${productData ? productData.id : ''}">
        `;
        container.appendChild(div);
    }

    function generateSupplierBarcode() {
        return 'BAR-' + Math.floor(1000000000 + Math.random() * 9000000000);
    }

    function calculateSupplierSellingPrice(element) {
        const parentBlock = element.closest('.supplier-product-block');
        if (!parentBlock) return;
        const pieceRateInput = parentBlock.querySelector('input[name="sp_piece_rate[]"]');
        const marginInput = parentBlock.querySelector('input[name="sp_margin_percentage[]"]');
        const sellingInput = parentBlock.querySelector('input[name="sp_selling_price[]"]');
        const errSpan = parentBlock.querySelector('.err-margin-msg');

        const pieceRate = parseFloat(pieceRateInput ? pieceRateInput.value : 0) || 0;
        const rawMargin = marginInput ? marginInput.value.trim() : '';

        let effectiveMargin = 60.0; // Default when empty

        if (rawMargin === '') {
            // Empty value means default 60%
            effectiveMargin = 60.0;
            if (errSpan) {
                errSpan.textContent = '';
                errSpan.classList.add('hidden');
            }
            if (marginInput) marginInput.classList.remove('border-rose-500');
        } else {
            const parsedMargin = parseFloat(rawMargin);
            if (isNaN(parsedMargin) || !isFinite(parsedMargin) || parsedMargin < 0) {
                if (errSpan) {
                    errSpan.textContent = 'Margin % must be 0 or greater';
                    errSpan.classList.remove('hidden');
                }
                if (marginInput) marginInput.classList.add('border-rose-500');
                return;
            } else {
                if (errSpan) {
                    errSpan.textContent = '';
                    errSpan.classList.add('hidden');
                }
                if (marginInput) marginInput.classList.remove('border-rose-500');
                effectiveMargin = parsedMargin;
            }
        }

        // Selling Price = One Piece Rate + (One Piece Rate × Margin Percentage / 100)
        if (pieceRate >= 0 && pieceRateInput && pieceRateInput.value !== '') {
            const sellingPrice = pieceRate + (pieceRate * (effectiveMargin / 100.0));
            if (sellingInput) {
                sellingInput.value = sellingPrice.toFixed(2);
            }
        } else {
            if (sellingInput) sellingInput.value = '';
        }
    }

    function calculateSupplierPurchase(element) {
        const parentBlock = element.closest('.supplier-product-block');
        if (!parentBlock) return;
        const unitInput = parentBlock.querySelector('input[name="sp_unit[]"]');
        const pieceRateInput = parentBlock.querySelector('input[name="sp_piece_rate[]"]');
        const purchaseInput = parentBlock.querySelector('input[name="sp_purchase_price[]"]');

        const units = parseFloat(unitInput ? unitInput.value : 0) || 0;
        const pieceRate = parseFloat(pieceRateInput ? pieceRateInput.value : 0) || 0;
        
        if (units > 0 && pieceRate >= 0) {
            const purchasePrice = units * pieceRate;
            if (purchaseInput) purchaseInput.value = purchasePrice.toFixed(2);
        } else {
            if (purchaseInput) purchaseInput.value = '';
        }
        
        // Auto-calculate selling price using dynamic margin percentage
        calculateSupplierSellingPrice(element);
        
        const totalPurchasedInput = document.getElementById('total_amount_purchased');
        if (totalPurchasedInput) {
            let totalVal = (typeof s !== 'undefined' && s) ? (parseFloat(s.total_amount_purchased !== undefined ? s.total_amount_purchased : (s.total_amount || 0)) || 0) : 0;
            const pInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
            if (pInputs.length > 0) {
                let pSum = 0;
                pInputs.forEach(inp => pSum += (parseFloat(inp.value) || 0));
                if (pSum > 0) totalVal = pSum;
            }
            if (totalVal <= 0 && typeof supplierProducts !== 'undefined' && supplierProducts && supplierProducts.length > 0) {
                let calcSum = 0;
                supplierProducts.forEach(p => {
                    const u = parseFloat(p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)) || 0;
                    const r = parseFloat(p.prod_piece_rate || 0) || 0;
                    const stored = parseFloat(p.prod_purchase_price || 0) || 0;
                    calcSum += (stored > 0 ? stored : (u * r));
                });
                if (calcSum > 0) totalVal = calcSum;
            }
            totalPurchasedInput.value = totalVal > 0 ? totalVal.toFixed(2) : '0.00';
            calculateSupplierPaymentStatus();
        }
    }

    function calculateTotalSupplierPurchase() {
        const purchaseInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
        let total = 0;
        purchaseInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalAmountInput = document.getElementById('total_amount_purchased');
        if (totalAmountInput) {
            totalAmountInput.value = total > 0 ? total.toFixed(2) : '0.00';
        }
        
        // Sync balance and status when total purchase changes
        calculateSupplierPaymentStatus();
    }

    function removeSupplierProductField(btn) {
        btn.parentElement.remove();
        const totalPurchasedInput = document.getElementById('total_amount_purchased');
        if (totalPurchasedInput) {
            let totalVal = (typeof s !== 'undefined' && s) ? (parseFloat(s.total_amount_purchased !== undefined ? s.total_amount_purchased : (s.total_amount || 0)) || 0) : 0;
            const pInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
            if (pInputs.length > 0) {
                let pSum = 0;
                pInputs.forEach(inp => pSum += (parseFloat(inp.value) || 0));
                if (pSum > 0) totalVal = pSum;
            }
            if (totalVal <= 0 && typeof supplierProducts !== 'undefined' && supplierProducts && supplierProducts.length > 0) {
                let calcSum = 0;
                supplierProducts.forEach(p => {
                    const u = parseFloat(p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)) || 0;
                    const r = parseFloat(p.prod_piece_rate || 0) || 0;
                    const stored = parseFloat(p.prod_purchase_price || 0) || 0;
                    calcSum += (stored > 0 ? stored : (u * r));
                });
                if (calcSum > 0) totalVal = calcSum;
            }
            totalPurchasedInput.value = totalVal > 0 ? totalVal.toFixed(2) : '0.00';
            calculateSupplierPaymentStatus();
        }
    }

    function toggleSupplierPaymentAmount(checkbox, inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (checkbox.checked) {
            input.classList.remove('hidden');
            // If empty, auto-fill remaining balance
            if (input.value === '' || parseFloat(input.value) === 0) {
                const totalPurchased = parseFloat(document.getElementById('total_amount_purchased').value) || 0;
                const otherPaid = getSupplierOtherMethodsPaid(inputId);
                const remaining = Math.max(0, totalPurchased - otherPaid);
                if (remaining > 0) {
                    input.value = remaining.toFixed(2);
                }
            }
            input.focus();
        } else {
            input.classList.add('hidden');
            input.value = '';
        }
        calculateSupplierTotalPaid();
    }

    function getSupplierOtherMethodsPaid(excludeInputId) {
        let sum = 0;
        const methodIds = ['payment_amount_cash', 'payment_amount_upi', 'payment_amount_bank', 'payment_amount_credit'];
        methodIds.forEach(id => {
            if (id !== excludeInputId) {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) {
                    sum += (parseFloat(el.value) || 0);
                }
            }
        });
        return sum;
    }

    function calculateSupplierTotalPaid() {
        const cashInput = document.getElementById('payment_amount_cash');
        const upiInput = document.getElementById('payment_amount_upi');
        const bankInput = document.getElementById('payment_amount_bank');
        const creditInput = document.getElementById('payment_amount_credit');

        const cash = (cashInput && !cashInput.classList.contains('hidden')) ? (parseFloat(cashInput.value) || 0) : 0;
        const upi = (upiInput && !upiInput.classList.contains('hidden')) ? (parseFloat(upiInput.value) || 0) : 0;
        const bank = (bankInput && !bankInput.classList.contains('hidden')) ? (parseFloat(bankInput.value) || 0) : 0;
        const credit = (creditInput && !creditInput.classList.contains('hidden')) ? (parseFloat(creditInput.value) || 0) : 0;

        const totalPaid = cash + upi + bank + credit;
        const paidInput = document.getElementById('total_payment_entered');
        const checkedCount = document.querySelectorAll('#supplier-form input[name="payment_methods[]"]:checked').length;

        if (paidInput) {
            if (checkedCount > 0 || totalPaid > 0) {
                paidInput.value = totalPaid > 0 ? totalPaid.toFixed(2) : '0.00';
            } else {
                paidInput.value = '';
            }
        }

        const errSpan = document.getElementById('err-total_paid');
        const totalPurchased = parseFloat(document.getElementById('total_amount_purchased').value) || 0;
        if (errSpan) {
            if (totalPurchased > 0 && totalPaid > totalPurchased) {
                errSpan.textContent = `Paid amount (₹${totalPaid.toFixed(2)}) cannot exceed total purchase amount (₹${totalPurchased.toFixed(2)})`;
                errSpan.classList.remove('hidden');
            } else {
                errSpan.textContent = '';
                errSpan.classList.add('hidden');
            }
        }

        calculateSupplierPaymentStatus();
    }

    function onTotalPurchasedInput() {
        calculateSupplierPaymentStatus();
    }

    function onPaymentMethodToggle() {
        calculateSupplierTotalPaid();
    }

    function onTotalPaidInput() {
        const totalInput = document.getElementById('total_amount_purchased');
        const paidInput = document.getElementById('total_payment_entered');
        const errSpan = document.getElementById('err-total_paid');
        const total = parseFloat(totalInput ? totalInput.value : 0) || 0;
        let paid = parseFloat(paidInput ? paidInput.value : 0);

        if (isNaN(paid)) paid = 0;

        // If exactly one payment method is checked, sync this paid value into that method's input
        const checkedCheckboxes = Array.from(document.querySelectorAll('#supplier-form input[name="payment_methods[]"]:checked'));
        if (checkedCheckboxes.length === 1) {
            const methodMap = {
                'Cash': 'payment_amount_cash',
                'UPI': 'payment_amount_upi',
                'Bank Transfer': 'payment_amount_bank',
                'Credit': 'payment_amount_credit'
            };
            const activeInputId = methodMap[checkedCheckboxes[0].value];
            const activeInput = document.getElementById(activeInputId);
            if (activeInput) {
                activeInput.value = paid > 0 ? paid.toFixed(2) : '';
            }
        }

        if (paid < 0) {
            if (errSpan) {
                errSpan.textContent = 'Paid amount cannot be negative';
                errSpan.classList.remove('hidden');
            }
        } else if (total > 0 && paid > total) {
            if (errSpan) {
                errSpan.textContent = `Paid amount (₹${paid.toFixed(2)}) cannot exceed total purchase amount (₹${total.toFixed(2)})`;
                errSpan.classList.remove('hidden');
            }
        } else {
            if (errSpan) {
                errSpan.textContent = '';
                errSpan.classList.add('hidden');
            }
        }

        calculateSupplierPaymentStatus();
    }

    function calculateSupplierPaymentStatus() {
        const totalInput = document.getElementById('total_amount_purchased');
        const paidInput = document.getElementById('total_payment_entered');
        const balanceInput = document.getElementById('balance_amount');
        const statusInput = document.getElementById('supplier_payment_status');
        const badge = document.getElementById('supplier_status_badge');

        const total = parseFloat(totalInput ? totalInput.value : 0) || 0;
        let paidRaw = parseFloat(paidInput ? paidInput.value : 0);
        if (isNaN(paidRaw) || paidRaw < 0) paidRaw = 0;

        // Valid clamped paid for balance computation
        const validPaid = (total > 0 && paidRaw > total) ? total : paidRaw;
        const balance = Math.max(0, total - validPaid);

        if (balanceInput) {
            balanceInput.value = balance.toFixed(2);
        }

        // Automatic Status calculation
        let status = 'Unpaid';
        if (total <= 0) {
            // Zero Amount Condition: do not mark as Paid
            status = 'Unpaid';
            if (badge) {
                badge.className = 'text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-600 border border-slate-200';
                badge.textContent = 'Status: Unpaid (Draft)';
            }
        } else if (paidRaw >= total) {
            // Condition 1: Paid
            status = 'Paid';
            if (balanceInput) balanceInput.value = '0.00';
            if (badge) {
                badge.className = 'text-xs px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
                badge.textContent = 'Status: Paid';
            }
        } else if (paidRaw > 0) {
            // Condition 2: Partially Paid
            status = 'Partially Paid';
            if (badge) {
                badge.className = 'text-xs px-2.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800 border border-amber-300';
                badge.textContent = 'Status: Partially Paid';
            }
        } else {
            // Condition 3: Unpaid
            status = 'Unpaid';
            if (badge) {
                badge.className = 'text-xs px-2.5 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800 border border-rose-300';
                badge.textContent = 'Status: Unpaid';
            }
        }

        if (statusInput) {
            statusInput.value = status;
        }
    }

    function toggleProdPaymentAmount(checkbox, inputId) {
        const input = document.getElementById(inputId);
        if (checkbox.checked) {
            input.classList.remove('hidden');
        } else {
            input.classList.add('hidden');
            input.value = '';
        }
        calculateProdTotalPaid();
    }

    function calculateProdTotalPaid() {
        const cash = parseFloat(document.getElementById('prod_payment_amount_cash').value) || 0;
        const upi = parseFloat(document.getElementById('prod_payment_amount_upi').value) || 0;
        const bank = parseFloat(document.getElementById('prod_payment_amount_bank').value) || 0;
        const credit = parseFloat(document.getElementById('prod_payment_amount_credit').value) || 0;
        
        const totalPaid = cash + upi + bank + credit;
        const totalPaidInput = document.getElementById('prod_total_payment_entered');
        
        if (totalPaidInput) {
            totalPaidInput.value = totalPaid > 0 ? totalPaid.toFixed(2) : '';
        }

        // Calculate Balance
        const totalAmountPurchasedInput = document.getElementById('prod_total_amount_purchased');
        const balanceInput = document.getElementById('prod_balance_amount');
        if (totalAmountPurchasedInput && balanceInput) {
            const totalPurchased = parseFloat(totalAmountPurchasedInput.value) || 0;
            let balance = totalPurchased - totalPaid;
            // Prevent negative balance display, or allow it to show overpayment
            balanceInput.value = balance !== 0 ? balance.toFixed(2) : '0.00';
            
            if (balance < 0) {
                balanceInput.classList.replace('text-rose-800', 'text-emerald-800');
                balanceInput.classList.replace('bg-rose-50', 'bg-emerald-50');
            } else {
                balanceInput.classList.replace('text-emerald-800', 'text-rose-800');
                balanceInput.classList.replace('bg-emerald-50', 'bg-rose-50');
            }
        }
    }


    async function handleSupplierSubmit(event) {
        if (event) event.preventDefault();
        clearErrors();
        
        const form = document.getElementById('supplier-form');

        // Sync visible category and subcategory inputs to hidden inputs
        form.querySelectorAll('.supplier-product-block').forEach(block => {
            const catVis = block.querySelector('.cat-search-input');
            const catHid = block.querySelector('input[name="sp_product_category[]"]');
            if (catVis && catHid) {
                catHid.value = catVis.value.trim();
            }
            const subVis = block.querySelector('.subcat-search-input');
            const subHid = block.querySelector('input[name="sp_product_subcategory[]"]');
            if (subVis && subHid) {
                subHid.value = subVis.value.trim();
            }
        });

        const formData = new FormData(form);
        
        const supplierName = (formData.get('supplier_name') || '').trim();
        const mobileNumber = (formData.get('mobile_number') || '').trim();
        
        let isValid = true;
        if (!supplierName) {
            showError('supplier_name', 'Supplier name is required');
            isValid = false;
        }
        if (!mobileNumber) {
            showError('mobile_number', 'Mobile number is required');
            isValid = false;
        }

        if (!isValid) return;

        // Validate Margin Percentages
        let marginValid = true;
        form.querySelectorAll('.supplier-product-block').forEach(block => {
            const marginInput = block.querySelector('input[name="sp_margin_percentage[]"]');
            const errSpan = block.querySelector('.err-margin-msg');
            if (marginInput) {
                const val = marginInput.value.trim();
                if (val !== '') {
                    const parsed = parseFloat(val);
                    if (isNaN(parsed) || !isFinite(parsed) || parsed < 0) {
                        marginValid = false;
                        if (errSpan) {
                            errSpan.textContent = 'Margin % must be 0 or greater';
                            errSpan.classList.remove('hidden');
                        }
                        marginInput.classList.add('border-rose-500');
                    }
                }
            }
        });
        if (!marginValid) {
            showToast('error', 'Please correct the invalid margin percentage(s).');
            return;
        }

        const totalPurchased = parseFloat(formData.get('total_amount_purchased') || 0) || 0;
        const totalPaid = parseFloat(formData.get('total_amount_paid') || 0) || 0;
        if (totalPaid < 0) {
            const errSpan = document.getElementById('err-total_paid');
            if (errSpan) {
                errSpan.textContent = 'Paid amount cannot be negative';
                errSpan.classList.remove('hidden');
            }
            showToast('error', 'Paid amount cannot be negative');
            return;
        }
        if (totalPurchased > 0 && totalPaid > totalPurchased) {
            const errSpan = document.getElementById('err-total_paid');
            if (errSpan) {
                errSpan.textContent = `Paid amount (₹${totalPaid.toFixed(2)}) cannot exceed total purchase amount (₹${totalPurchased.toFixed(2)})`;
                errSpan.classList.remove('hidden');
            }
            showToast('error', `Paid amount cannot exceed total purchase amount (₹${totalPurchased.toFixed(2)})`);
            return;
        }

        // Payment methods
        const paymentMethods = formData.getAll('payment_methods[]').join(', ') || 'Cash';
        formData.set('payment_methods', paymentMethods);
        formData.append('_csrf_token', window.CSRF_TOKEN || '');
        if (currentEditId) {
            formData.append('supplier_id', currentEditId);
        }

        const submitBtn = form.querySelector('button[type="submit"]') || document.querySelector('button[form="supplier-form"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Saving...';
        }

        try {
            const res = await fetch(getAppUrl('suppliers/save-with-products'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                showToast('success', data.message || (currentEditId ? 'Supplier updated successfully.' : 'Supplier and products saved.'));
                closeSupplierModal();
                currentEditId = null;
                await refreshStateFromDb();
            } else {
                showToast('error', data.message || 'Failed to save supplier.');
            }
        } catch (err) {
            console.error('Error saving supplier:', err);
            showToast('error', 'Network error occurred while saving supplier.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-1.5"></i> Save Supplier';
            }
        }
    }

    let barcodeCurrentPage = 1;
    let barcodePerPage = 10;
    let barcodeSearchQuery = '';
    let barcodeCategoryFilter = '';
    let barcodeSupplierFilter = '';

    function filterBarcodeTable() {
        barcodeSearchQuery = (document.getElementById('barcode-search-input')?.value || '').toLowerCase().trim();
        barcodeCategoryFilter = (document.getElementById('barcode-category-filter')?.value || '').trim();
        barcodeSupplierFilter = (document.getElementById('barcode-supplier-filter')?.value || '').trim();
        barcodeCurrentPage = 1;

        const clearBtn = document.getElementById('barcode-search-clear');
        if (clearBtn) {
            if (barcodeSearchQuery.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderBarcodeTable();
    }

    function clearBarcodeSearch() {
        const inp = document.getElementById('barcode-search-input');
        if (inp) inp.value = '';
        filterBarcodeTable();
    }

    function resetBarcodeFilters() {
        const sInp = document.getElementById('barcode-search-input');
        if (sInp) sInp.value = '';
        const cSel = document.getElementById('barcode-category-filter');
        if (cSel) cSel.value = '';
        const supSel = document.getElementById('barcode-supplier-filter');
        if (supSel) supSel.value = '';
        barcodeSearchQuery = '';
        barcodeCategoryFilter = '';
        barcodeSupplierFilter = '';
        barcodeCurrentPage = 1;
        filterBarcodeTable();
    }

    function changeBarcodePerPage(val) {
        barcodePerPage = parseInt(val) || 10;
        barcodeCurrentPage = 1;
        renderBarcodeTable();
    }

    function goToBarcodePage(page) {
        barcodeCurrentPage = page;
        renderBarcodeTable();
    }

    function populateBarcodeDropdowns() {
        // Categories
        const catSelect = document.getElementById('barcode-category-filter');
        if (catSelect && catSelect.options.length <= 1) {
            const curVal = catSelect.value;
            const uniqueCats = [...new Set(products.map(p => p.prod_category).filter(Boolean))].sort();
            uniqueCats.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                catSelect.appendChild(opt);
            });
            if (curVal) catSelect.value = curVal;
        }

        // Suppliers
        const supSelect = document.getElementById('barcode-supplier-filter');
        if (supSelect && supSelect.options.length <= 1) {
            const curVal = supSelect.value;
            suppliers.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.supplier_name;
                supSelect.appendChild(opt);
            });
            if (curVal) supSelect.value = curVal;
        }
    }

    function renderBarcodeTable() {
        populateBarcodeDropdowns();

        const tbody = document.getElementById('barcode-table-body');
        if (!tbody) return;

        // Filter products for barcode table
        const filtered = products.filter(p => {
            const barcodeNo = (p.prod_barcode || '').toLowerCase();
            const pName = (p.prod_name || '').toLowerCase();
            const pSku = (p.prod_sku || '').toLowerCase();
            const pCat = (p.prod_category || '').toLowerCase();
            const supplier = suppliers.find(s => String(s.id) === String(p.prod_supplier_id));
            const suppName = (supplier ? supplier.supplier_name : '').toLowerCase();

            if (barcodeSearchQuery) {
                const matches = barcodeNo.includes(barcodeSearchQuery) ||
                                pName.includes(barcodeSearchQuery) ||
                                pSku.includes(barcodeSearchQuery) ||
                                pCat.includes(barcodeSearchQuery) ||
                                suppName.includes(barcodeSearchQuery);
                if (!matches) return false;
            }

            if (barcodeCategoryFilter) {
                if (pCat !== barcodeCategoryFilter.toLowerCase()) return false;
            }

            if (barcodeSupplierFilter) {
                if (String(p.prod_supplier_id) !== String(barcodeSupplierFilter)) return false;
            }

            return true;
        });

        const badge = document.getElementById('barcode-count-badge');
        if (badge) {
            badge.textContent = `${filtered.length} Barcodes`;
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr id="barcode-empty-state-row">
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="fas fa-barcode text-3xl mb-3 block text-slate-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No barcodes found</p>
                        <p class="text-xs mt-1">Try adjusting your search query or filters.</p>
                    </td>
                </tr>`;
            renderBarcodePagination(0);
            return;
        }

        // Pagination calculations
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / barcodePerPage) || 1;
        if (barcodeCurrentPage > totalPages) barcodeCurrentPage = totalPages;
        if (barcodeCurrentPage < 1) barcodeCurrentPage = 1;

        const startIdx = (barcodeCurrentPage - 1) * barcodePerPage;
        const pageItems = filtered.slice(startIdx, startIdx + barcodePerPage);

        tbody.innerHTML = '';
        pageItems.forEach(p => {
            const supplier = suppliers.find(s => String(s.id) === String(p.prod_supplier_id));
            const stock = p.prod_current_stock || 0;
            const price = parseFloat(p.prod_selling_price || 0);

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/40 transition-colors text-xs border-b border-slate-100';
            
            tr.innerHTML = `
                <td class="py-3 px-4 font-mono text-[13px] font-bold text-emerald-800 bg-emerald-50/50 tracking-wider whitespace-nowrap cursor-pointer hover:bg-emerald-100/60 transition-colors" data-copy="${p.prod_barcode || ''}" title="Click to copy barcode: ${p.prod_barcode || 'N/A'}">
                    <i class="fas fa-barcode text-emerald-500 mr-1.5"></i>${p.prod_barcode || 'N/A'}
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800 text-xs hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${p.prod_name}" title="Click to copy product name">${p.prod_name}</div>
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5 hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${p.prod_sku || ''}" title="Click to copy SKU">${p.prod_sku || 'N/A'}</div>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[10px] font-bold border border-slate-200 hover:bg-slate-200 cursor-pointer transition-colors" data-copy="${p.prod_category || 'General'}" title="Click to copy category">
                        ${p.prod_category || 'General'}
                    </span>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-semibold text-slate-700 hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${supplier ? (supplier.supplier_name || supplier.name) : 'Unnamed Supplier'}" title="Click to copy supplier name">${supplier ? (supplier.supplier_name || supplier.name) : 'Unnamed Supplier'}</div>
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                    <span class="font-mono font-bold text-xs ${stock > 0 ? 'text-emerald-700' : 'text-rose-600'} cursor-pointer" data-copy="${stock}" title="Click to copy stock units">
                        ${stock} Units
                    </span>
                </td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap cursor-pointer hover:text-indigo-600" data-copy="${price.toFixed(2)}" title="Click to copy price">
                    ₹${price.toFixed(2)}
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                    <button type="button" onclick="printSingleBarcode('${p.id}')" class="bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs inline-flex items-center gap-1.5">
                        <i class="fas fa-print text-[11px]"></i> Print Label
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        renderBarcodePagination(totalItems);
    }

    function renderBarcodePagination(totalItems) {
        const pageInfo = document.getElementById('barcode-page-info');
        const btnContainer = document.getElementById('barcode-page-buttons');
        if (!pageInfo || !btnContainer) return;

        if (totalItems === 0) {
            pageInfo.textContent = 'Showing 0 to 0 of 0 entries';
            btnContainer.innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(totalItems / barcodePerPage) || 1;
        const startItem = (barcodeCurrentPage - 1) * barcodePerPage + 1;
        const endItem = Math.min(barcodeCurrentPage * barcodePerPage, totalItems);

        pageInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} entries`;

        let btnsHtml = '';

        // Previous button
        btnsHtml += `
            <button type="button" onclick="goToBarcodePage(${barcodeCurrentPage - 1})" ${barcodeCurrentPage <= 1 ? 'disabled' : ''} class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs font-semibold shadow-xs">
                <i class="fas fa-chevron-left mr-1"></i> Prev
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 7) {
                if (i !== 1 && i !== totalPages && Math.abs(i - barcodeCurrentPage) > 2) {
                    if (i === 2 || i === totalPages - 1) {
                        btnsHtml += '<span class="px-1 text-slate-400">...</span>';
                    }
                    continue;
                }
            }
            const isActive = i === barcodeCurrentPage;
            btnsHtml += `
                <button type="button" onclick="goToBarcodePage(${i})" class="w-7 h-7 rounded-lg text-xs font-bold transition-colors ${isActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'}">
                    ${i}
                </button>
            `;
        }

        // Next button
        btnsHtml += `
            <button type="button" onclick="goToBarcodePage(${barcodeCurrentPage + 1})" ${barcodeCurrentPage >= totalPages ? 'disabled' : ''} class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs font-semibold shadow-xs">
                Next <i class="fas fa-chevron-right ml-1"></i>
            </button>
        `;

        btnContainer.innerHTML = btnsHtml;
    }

    function printSingleBarcode(productId) {
        const p = products.find(prod => String(prod.id) === String(productId));
        if (!p) {
            showToast('error', 'Product not found.');
            return;
        }

        const win = window.open('', '_blank');
        win.document.write(`
            <html>
                <head>
                    <title>Barcode Label - ${p.prod_name}</title>
                    <style>
                        body { font-family: monospace; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
                        .label-box { border: 2px dashed #333; padding: 16px; border-radius: 8px; text-align: center; width: 280px; }
                        .brand { font-size: 11px; text-transform: uppercase; font-weight: bold; color: #555; }
                        .title { font-size: 14px; font-weight: bold; margin: 6px 0; }
                        .barcode-val { font-size: 20px; font-weight: 900; letter-spacing: 4px; margin: 10px 0; }
                        .price { font-size: 16px; font-weight: bold; color: #047857; margin-top: 6px; }
                    </style>
                </head>
                <body>
                    <div class="label-box">
                        <div class="brand">God Statue Billing</div>
                        <div class="title">${p.prod_name}</div>
                        <div class="barcode-val">${p.prod_barcode || 'BAR-' + p.id}</div>
                        <div class="price">MRP: ₹${parseFloat(p.prod_selling_price || 0).toFixed(2)}</div>
                    </div>
                </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); win.close(); }, 500);
    }

    function printAllBarcodes() {
        if (products.length === 0) {
            showToast('error', 'No products to print.');
            return;
        }

        const win = window.open('', '_blank');
        const cardsHtml = products.map(p => `
            <div style="border: 1px solid #ccc; padding: 12px; border-radius: 6px; text-align: center; width: 200px; page-break-inside: avoid; display: inline-block; margin: 6px;">
                <div style="font-size: 9px; font-weight: bold; color: #666;">God Statue Billing</div>
                <div style="font-size: 12px; font-weight: bold; margin: 4px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${p.prod_name}</div>
                <div style="font-size: 16px; font-weight: 900; letter-spacing: 2px; margin: 6px 0;">${p.prod_barcode || 'BAR-' + p.id}</div>
                <div style="font-size: 13px; font-weight: bold; color: #047857;">₹${parseFloat(p.prod_selling_price || 0).toFixed(2)}</div>
            </div>
        `).join('');

        win.document.write(`
            <html>
                <head>
                    <title>All Barcode Labels</title>
                    <style>
                        body { font-family: monospace; padding: 15px; }
                        @media print { body { padding: 0; } }
                    </style>
                </head>
                <body>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        ${cardsHtml}
                    </div>
                </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); win.close(); }, 500);
    }
    function filterSupplierTable() {
        const search = (document.getElementById('supplier-search-input')?.value || '').toLowerCase().trim();
        const statusFilter = (document.getElementById('supplier-status-filter')?.value || '').trim();
        const prodFilter = (document.getElementById('supplier-products-filter')?.value || '').trim();

        const clearBtn = document.getElementById('supplier-search-clear');
        if (clearBtn) {
            if (search.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderSupplierTable(search, statusFilter, prodFilter);
    }

    function clearSupplierSearch() {
        const inp = document.getElementById('supplier-search-input');
        if (inp) inp.value = '';
        filterSupplierTable();
    }

    function resetSupplierFilters() {
        const sInp = document.getElementById('supplier-search-input');
        if (sInp) sInp.value = '';
        const sStat = document.getElementById('supplier-status-filter');
        if (sStat) sStat.value = '';
        const sProd = document.getElementById('supplier-products-filter');
        if (sProd) sProd.value = '';
        filterSupplierTable();
    }

    function renderSupplierTable(search = '', statusFilter = '', prodFilter = '') {
        const tbody = document.getElementById('supplier-table-body');
        if (!tbody) return;

        // Group/deduplicate suppliers uniquely if any repeat
        const uniqueSuppliersMap = new Map();
        suppliers.forEach(s => {
            const key = String(s.id);
            if (!uniqueSuppliersMap.has(key)) {
                uniqueSuppliersMap.set(key, s);
            }
        });
        const supplierList = Array.from(uniqueSuppliersMap.values());

        // Apply filters
        const filtered = supplierList.filter(s => {
            const suppName = (s.supplier_name || s.name || '').toLowerCase();
            const suppMobile = (s.mobile_number || s.mobile || s.phone || '').toLowerCase();
            const suppCode = (s.barcode || s.supplier_code || '').toLowerCase();
            const suppCity = (s.city || '').toLowerCase();
            const suppGst = (s.gst_number || '').toLowerCase();
            const suppStatus = (s.status || 'Active');

            if (search) {
                const matches = suppName.includes(search) || 
                                suppMobile.includes(search) || 
                                suppCode.includes(search) || 
                                suppCity.includes(search) || 
                                suppGst.includes(search);
                if (!matches) return false;
            }

            if (statusFilter) {
                if (suppStatus.toLowerCase() !== statusFilter.toLowerCase()) return false;
            }

            const suppProds = products.filter(p => String(p.prod_supplier_id) === String(s.id));
            const hasSales = suppProds.some(p => (p.total_sold_qty || 0) > 0);

            if (prodFilter === 'with_products' && suppProds.length === 0) return false;
            if (prodFilter === 'no_products' && suppProds.length > 0) return false;
            if (prodFilter === 'has_sales' && !hasSales) return false;

            return true;
        });

        const badge = document.getElementById('supplier-filter-count-badge');
        if (badge) {
            badge.textContent = `${filtered.length} of ${supplierList.length} Suppliers`;
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="fas fa-search text-3xl mb-3 block text-slate-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No matching suppliers found</p>
                        <p class="text-xs mt-1">Try adjusting your search query or filters above.</p>
                    </td>
                </tr>`;
            return;
        }
        
        tbody.innerHTML = '';
        
        filtered.forEach(s => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            
            // Fetch products for this supplier using loose string comparison
            const supplierProducts = products.filter(p => String(p.prod_supplier_id) === String(s.id));
            const suppName = s.supplier_name || s.name || 'Unnamed Supplier';
            const suppMobile = s.mobile_number || s.mobile || s.phone || 'N/A';
            const suppCode = s.barcode || s.supplier_code || ('SUP-' + s.id);
            const suppGst = s.gst_number || s.tax_number || 'N/A';
            const suppStatus = s.status || 'Active';
            
            let productButtonHtml = '';
            if (supplierProducts.length > 0) {
                productButtonHtml = `
                    <button type="button" onclick="openSupplierProductsModal('${s.id}')" title="Click to view all ${supplierProducts.length} products & detailed sales sheet" class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white transition-all font-bold text-xs shadow-xs border border-indigo-200 hover:border-indigo-600">
                        <i class="fas fa-boxes text-indigo-500 group-hover:text-white transition-colors"></i>
                        <span>${supplierProducts.length} Products</span>
                        <span class="text-[9px] bg-white text-indigo-700 group-hover:bg-indigo-500 group-hover:text-white px-1.5 py-0.5 rounded-md border border-indigo-200 group-hover:border-indigo-400 font-extrabold uppercase tracking-wider transition-colors">View Sheet</span>
                    </button>
                `;
            } else {
                productButtonHtml = `
                    <button type="button" onclick="openAddProductForSupplier('${s.id}')" title="No products associated yet. Click to add products" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-800 transition-all font-semibold text-xs border border-slate-200">
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>0 Products</span>
                    </button>
                `;
            }

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800 hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${suppName}" title="Click to copy supplier name">${suppName}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-2">
                        <span class="hover:text-indigo-600 cursor-pointer" data-copy="${s.city || 'Tamil Nadu'}" title="Click to copy city"><i class="fas fa-map-marker-alt text-slate-400 mr-1"></i>${s.city || 'Tamil Nadu'}</span>
                        ${s.email ? `<span class="hover:text-indigo-600 cursor-pointer" data-copy="${s.email}" title="Click to copy email">• <i class="fas fa-envelope text-slate-400 mx-1"></i>${s.email}</span>` : ''}
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-700 font-mono hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${suppMobile}" title="Click to copy mobile number">${suppMobile}</div>
                </td>
                
                <td class="py-3 px-4">
                    ${productButtonHtml}
                </td>
                <td class="py-3 px-4 font-mono text-[11px] text-slate-600 font-semibold hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${suppGst}" title="Click to copy GST number">${suppGst}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${suppStatus.toLowerCase() === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${suppStatus}
                    </span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-1.5">
                        <button type="button" onclick="openSupplierProductsModal('${s.id}')" title="View Products & Sales Sheet" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-indigo-600 hover:text-white text-indigo-600 text-xs flex items-center justify-center transition-colors shadow-xs"><i class="fas fa-table"></i></button>
                        <button type="button" onclick="viewSupplier('${s.id}')" title="View Supplier Profile" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-indigo-50 text-indigo-600 text-xs flex items-center justify-center transition-colors shadow-xs"><i class="fas fa-eye"></i></button>
                        <button type="button" onclick="editSupplier('${s.id}')" title="Edit Supplier" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-600 text-xs flex items-center justify-center transition-colors shadow-xs"><i class="fas fa-edit"></i></button>
                        <button type="button" onclick="deleteSupplier('${s.id}')" title="Delete Supplier" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs flex items-center justify-center transition-colors shadow-xs"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    let currentSheetSupplierId = null;
    let isSheetDragging = false;
    let sheetStartX = 0;
    let sheetScrollLeft = 0;

    function initSheetDragScroll() {
        const slider = document.getElementById('sp-table-scroll-container');
        if (!slider) return;

        slider.onmousedown = (e) => {
            if (e.target.closest('button, input, select, a')) return;
            isSheetDragging = true;
            window.sheetDragHasMoved = false;
            sheetStartX = e.pageX - slider.offsetLeft;
            sheetScrollLeft = slider.scrollLeft;
        };

        slider.onmouseleave = () => {
            isSheetDragging = false;
            slider.style.cursor = 'grab';
        };

        slider.onmouseup = () => {
            isSheetDragging = false;
            slider.style.cursor = 'grab';
        };

        slider.onmousemove = (e) => {
            if (!isSheetDragging) return;
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - sheetStartX) * 1.5;
            if (Math.abs(walk) > 5) {
                window.sheetDragHasMoved = true;
                slider.style.cursor = 'grabbing';
                slider.scrollLeft = sheetScrollLeft - walk;
            }
        };

        // Touch support
        slider.ontouchstart = (e) => {
            sheetStartX = e.touches[0].pageX - slider.offsetLeft;
            sheetScrollLeft = slider.scrollLeft;
        };
        slider.ontouchmove = (e) => {
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - sheetStartX) * 1.5;
            slider.scrollLeft = sheetScrollLeft - walk;
        };
    }

    function scrollSheetLeft() {
        const slider = document.getElementById('sp-table-scroll-container');
        if (slider) slider.scrollBy({ left: -320, behavior: 'smooth' });
    }

    function scrollSheetRight() {
        const slider = document.getElementById('sp-table-scroll-container');
        if (slider) slider.scrollBy({ left: 320, behavior: 'smooth' });
    }

    function openSupplierProductsModal(supplierId) {
        currentSheetSupplierId = supplierId;
        const s = suppliers.find(sup => String(sup.id) === String(supplierId));
        if (!s) {
            showToast('error', 'Supplier not found.');
            return;
        }

        const suppName = s.supplier_name || s.name || 'Unnamed Supplier';
        const suppMobile = s.mobile_number || s.mobile || s.phone || 'N/A';
        const suppCode = s.barcode || s.supplier_code || ('SUP-' + s.id);
        const suppCity = s.city ? `${s.city}, ${s.state || 'Tamil Nadu'}` : (s.state || 'Tamil Nadu');

        const nameEl = document.getElementById('sp-modal-supplier-name');
        if (nameEl) nameEl.textContent = suppName;
        const codeEl = document.getElementById('sp-modal-supplier-code');
        if (codeEl) codeEl.textContent = suppCode;
        const infoEl = document.getElementById('sp-modal-supplier-info');
        if (infoEl) infoEl.textContent = `${suppMobile} • ${suppCity} • ${s.gst_number ? 'GST: ' + s.gst_number : 'GST: N/A'}`;

        // Reset search and filter inputs
        const sInp = document.getElementById('sp-sheet-search');
        if (sInp) sInp.value = '';
        const sFilter = document.getElementById('sp-sheet-status-filter');
        if (sFilter) sFilter.value = '';

        renderSupplierProductsSheet();

        const modal = document.getElementById('supplier-products-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }

        setTimeout(() => {
            initSheetDragScroll();
        }, 100);
    }

    function closeSupplierProductsModal() {
        currentSheetSupplierId = null;
        const modal = document.getElementById('supplier-products-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function openAddProductForSupplier(supplierId) {
        closeSupplierProductsModal();
        openProductModal(supplierId);
    }

    function renderSupplierProductsSheet() {
        if (!currentSheetSupplierId) return;
        const supplierId = currentSheetSupplierId;
        const suppProds = products.filter(p => String(p.prod_supplier_id) === String(supplierId));

        const search = (document.getElementById('sp-sheet-search')?.value || '').toLowerCase().trim();
        const statusFilter = (document.getElementById('sp-sheet-status-filter')?.value || '').trim();

        // Overarching calculations
        let totalItemsSold = 0;
        let totalStockRemaining = 0;
        let totalSalesRevenue = 0;

        suppProds.forEach(p => {
            const sold = p.total_sold_qty || 0;
            const stock = p.prod_current_stock || 0;
            const sp = p.prod_selling_price || 0;

            totalItemsSold += sold;
            totalStockRemaining += stock;
            totalSalesRevenue += (sold * sp);
        });

        const kpiProds = document.getElementById('sp-kpi-total-products');
        if (kpiProds) kpiProds.textContent = suppProds.length;
        const kpiSold = document.getElementById('sp-kpi-units-sold');
        if (kpiSold) kpiSold.textContent = `${totalItemsSold} Units`;
        const kpiStock = document.getElementById('sp-kpi-stock-remaining');
        if (kpiStock) kpiStock.textContent = `${totalStockRemaining} Units`;
        const kpiRev = document.getElementById('sp-kpi-sales-revenue');
        if (kpiRev) kpiRev.textContent = `₹${totalSalesRevenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        // Filter products for the table
        const filteredProds = suppProds.filter(p => {
            const pName = (p.prod_name || '').toLowerCase();
            const pSku = (p.prod_sku || '').toLowerCase();
            const pCat = (p.prod_category || '').toLowerCase();
            const pSub = (p.prod_subcategory || '').toLowerCase();

            if (search) {
                if (!pName.includes(search) && !pSku.includes(search) && !pCat.includes(search) && !pSub.includes(search)) {
                    return false;
                }
            }

            const sold = p.total_sold_qty || 0;
            const stock = p.prod_current_stock || 0;

            let saleStatus = 'in_stock';
            if (sold > 0 && stock === 0) saleStatus = 'sold_out';
            else if (sold > 0 && stock > 0) saleStatus = 'partially_sold';
            else if (sold === 0 && stock > 0) saleStatus = 'in_stock';
            else if (sold === 0 && stock === 0) saleStatus = 'out_of_stock';

            if (statusFilter && saleStatus !== statusFilter) {
                return false;
            }

            return true;
        });

        const tbody = document.getElementById('sp-sheet-table-body');
        const footerRow = document.getElementById('sp-sheet-footer-row');
        if (!tbody) return;

        if (filteredProds.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="py-12 text-center text-slate-400">
                        <i class="fas fa-boxes text-3xl mb-3 block text-slate-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No products found</p>
                        <p class="text-xs mt-1 text-slate-400">No products match your filter criteria or none added yet.</p>
                        <button type="button" onclick="openAddProductForSupplier('${supplierId}')" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            <i class="fas fa-plus mr-1.5"></i> Add Products For This Supplier
                        </button>
                    </td>
                </tr>`;
            if (footerRow) footerRow.innerHTML = '';
            return;
        }

        let filteredSoldUnits = 0;
        let filteredStockUnits = 0;
        let filteredRevenue = 0;

        tbody.innerHTML = '';
        filteredProds.forEach((p, index) => {
            const sold = p.total_sold_qty || 0;
            const stock = p.prod_current_stock || 0;
            const pieceRate = p.prod_piece_rate > 0 ? p.prod_piece_rate : (p.prod_purchase_price || 0);
            const margin = (p.prod_margin_percentage !== undefined && p.prod_margin_percentage !== null) ? p.prod_margin_percentage : '60.00';
            const sellingPrice = p.prod_selling_price || 0;
            const productSalesVal = sold * sellingPrice;

            filteredSoldUnits += sold;
            filteredStockUnits += stock;
            filteredRevenue += productSalesVal;

            // Determine status badge
            let statusBadge = '';
            if (sold > 0 && stock === 0) {
                statusBadge = '<span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 whitespace-nowrap"><i class="fas fa-check-double mr-1"></i>Sold Out</span>';
            } else if (sold > 0 && stock > 0) {
                statusBadge = '<span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap"><i class="fas fa-chart-pie mr-1"></i>Partially Sold</span>';
            } else if (sold === 0 && stock > 0) {
                statusBadge = '<span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap"><i class="fas fa-box mr-1"></i>In Stock</span>';
            } else {
                statusBadge = '<span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">Out of Stock</span>';
            }

            const supplyDate = p.created_at ? p.created_at.split(' ')[0] : 'N/A';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-indigo-50/40 transition-colors text-xs border-b border-slate-100 h-14 max-h-14';
            tr.innerHTML = `
                <td class="w-12 min-w-[48px] max-w-[48px] px-2 text-slate-400 font-mono text-center font-bold truncate cursor-pointer hover:text-indigo-600" data-copy="${index + 1}" title="Click to copy index: ${index + 1}">${index + 1}</td>
                <td class="w-64 min-w-[260px] max-w-[260px] px-3 font-semibold text-slate-800">
                    <div class="font-bold text-slate-900 truncate leading-snug cursor-pointer hover:text-indigo-600" data-copy="${p.prod_name}" title="Click to copy product name">${p.prod_name}</div>
                    <div class="text-[10px] text-slate-500 font-mono truncate cursor-pointer hover:text-indigo-600" data-copy="${p.prod_sku || p.prod_barcode || ''}" title="Click to copy SKU">${p.prod_sku || p.prod_barcode || 'SKU-N/A'}</div>
                </td>
                <td class="w-36 min-w-[140px] max-w-[140px] px-3">
                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 truncate max-w-[130px] leading-tight cursor-pointer hover:bg-slate-200" data-copy="${p.prod_category || 'General'}" title="Click to copy category">
                        ${p.prod_category || 'General'}
                    </span>
                    ${p.prod_subcategory ? `<div class="text-[9px] text-slate-400 truncate max-w-[130px] font-medium leading-tight mt-0.5 cursor-pointer hover:text-indigo-600" data-copy="${p.prod_subcategory}" title="Click to copy subcategory">${p.prod_subcategory}</div>` : ''}
                </td>
                <td class="w-28 min-w-[110px] max-w-[110px] px-3 font-mono text-slate-600 text-center whitespace-nowrap truncate cursor-pointer hover:text-indigo-600" data-copy="${supplyDate}" title="Click to copy supply date">
                    <i class="far fa-calendar-alt text-slate-400 mr-1 text-[11px]"></i>${supplyDate}
                </td>
                <td class="w-28 min-w-[110px] max-w-[110px] px-3 font-mono font-bold text-slate-800 text-right whitespace-nowrap cursor-pointer hover:text-indigo-600" data-copy="${pieceRate.toFixed(2)}" title="Click to copy piece rate">
                    ₹${pieceRate.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </td>
                <td class="w-24 min-w-[90px] max-w-[90px] px-2 font-mono text-center font-bold text-indigo-700 bg-indigo-50/50 whitespace-nowrap cursor-pointer hover:bg-indigo-100/60" data-copy="${margin}%" title="Click to copy margin %">
                    ${margin}%
                </td>
                <td class="w-32 min-w-[120px] max-w-[120px] px-3 font-mono font-bold text-emerald-700 text-right whitespace-nowrap cursor-pointer hover:text-emerald-900" data-copy="${sellingPrice.toFixed(2)}" title="Click to copy selling price">
                    ₹${sellingPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </td>
                <td class="w-24 min-w-[90px] max-w-[90px] px-2 text-center whitespace-nowrap">
                    <span class="inline-block font-mono font-extrabold px-2.5 py-0.5 rounded-lg text-xs cursor-pointer ${sold > 0 ? 'bg-emerald-100 text-emerald-800' : 'text-slate-400'}" data-copy="${sold}" title="Click to copy sold units">
                        ${sold}
                    </span>
                </td>
                <td class="w-28 min-w-[100px] max-w-[100px] px-2 text-center whitespace-nowrap">
                    <span class="inline-block font-mono font-extrabold px-2.5 py-0.5 rounded-lg text-xs cursor-pointer ${stock > 0 ? 'bg-amber-100 text-amber-900' : 'bg-rose-100 text-rose-800'}" data-copy="${stock}" title="Click to copy stock units">
                        ${stock}
                    </span>
                </td>
                <td class="w-36 min-w-[130px] max-w-[130px] px-2 text-center whitespace-nowrap cursor-pointer">
                    ${statusBadge}
                </td>
                <td class="w-36 min-w-[140px] max-w-[140px] px-3 font-mono font-extrabold text-slate-900 text-right whitespace-nowrap cursor-pointer hover:text-indigo-600" data-copy="${productSalesVal.toFixed(2)}" title="Click to copy total sold value">
                    ₹${productSalesVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </td>
            `;
            tbody.appendChild(tr);
        });

        if (footerRow) {
            footerRow.innerHTML = `
                <td colspan="7" class="py-3 px-3 font-bold text-slate-700 uppercase tracking-wider text-right text-xs">Sheet Summary:</td>
                <td class="py-3 px-2 text-center font-mono font-black text-emerald-800 text-xs bg-emerald-50 whitespace-nowrap">${filteredSoldUnits} Sold</td>
                <td class="py-3 px-2 text-center font-mono font-black text-amber-900 text-xs bg-amber-50 whitespace-nowrap">${filteredStockUnits} In Stock</td>
                <td class="py-3 px-2 text-center text-xs text-slate-400">—</td>
                <td class="py-3 px-3 text-right font-mono font-black text-indigo-900 text-xs bg-indigo-50 whitespace-nowrap">₹${filteredRevenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            `;
        }
    }
    function printSupplierProductsSheet() {
        if (!currentSheetSupplierId) return;
        const s = suppliers.find(sup => String(sup.id) === String(currentSheetSupplierId));
        const suppName = s ? (s.supplier_name || s.name) : 'Supplier';
        const printContent = document.getElementById('supplier-sheet-printable-area').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`
            <html>
                <head>
                    <title>${suppName} - Products & Sales Sheet</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body { font-family: sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
                        th { background-color: #f8fafc; font-size: 11px; }
                        td { font-size: 11px; }
                        @media print { button { display: none !important; } }
                    </style>
                </head>
                <body>
                    <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 4px;">${suppName} - Products & Sales Sheet</h2>
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">Generated on ${new Date().toLocaleDateString('en-GB')}</p>
                    ${printContent}
                </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); win.close(); }, 500);
    }

    async function deleteSupplier(id) {
        const confirmed = await showConfirmDialog({
            title: 'Delete Supplier',
            message: 'Are you sure you want to delete this supplier?<br><span class="text-rose-500 text-xs font-semibold mt-1 inline-block">All associated products will also be removed.</span>',
            confirmText: 'Yes, Delete',
            type: 'danger'
        });
        if (!confirmed) return;

        try {
            const formData = new FormData();
            formData.append('_csrf_token', window.CSRF_TOKEN || '');
            const res = await fetch(getAppUrl('suppliers/delete/' + id), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                showToast('success', data.message || 'Supplier deleted successfully.');
                await refreshStateFromDb();
            } else {
                showToast('error', data.message || 'Failed to delete supplier.');
            }
        } catch (err) {
            console.error('Error deleting supplier:', err);
            showToast('error', 'Network error occurred while deleting supplier.');
        }
    }

    function closeSupplierViewModal() {
        const modal = document.getElementById('supplier-view-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function viewSupplier(id) {
        const s = suppliers.find(sup => String(sup.id) === String(id));
        if (!s) {
            showToast('error', 'Supplier details not found.');
            return;
        }
        
        const supplierProducts = products.filter(p => String(p.prod_supplier_id) === String(id));
        
        let productsHtml = '';
        if (supplierProducts.length > 0) {
            productsHtml = supplierProducts.map(p => {
                const u = parseFloat(p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)) || 0;
                const r = parseFloat(p.prod_piece_rate || 0) || 0;
                const sp = parseFloat(p.prod_selling_price || 0) || 0;
                const margin = (p.prod_margin_percentage !== undefined && p.prod_margin_percentage !== null && p.prod_margin_percentage !== '') ? p.prod_margin_percentage : (p.margin_percentage !== undefined && p.margin_percentage !== null ? p.margin_percentage : '');
                const stored = parseFloat(p.prod_purchase_price || 0) || 0;
                const itemTotal = stored > 0 ? stored : (u * r);
                return `
                <div class="flex items-center justify-between border-b border-slate-100 last:border-0 py-2.5">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">${p.prod_name}</p>
                        <p class="text-[10px] text-slate-500">${p.prod_category || 'General'} ${p.prod_subcategory ? ' - ' + p.prod_subcategory : ''} | ${p.prod_barcode || p.prod_sku || 'N/A'}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-emerald-600 text-sm">₹${itemTotal.toFixed(2)}</p>
                        <p class="text-[10px] text-slate-500">${u} units @ ₹${r.toFixed(2)}${sp > 0 ? ' | SP: ₹' + sp.toFixed(2) + (margin !== '' ? ' (' + margin + '%)' : '') : ''}</p>
                    </div>
                </div>
            `}).join('');
        } else {
            productsHtml = '<p class="text-sm text-slate-500 italic py-2">No products linked.</p>';
        }

        const suppName = s.supplier_name || s.name || 'Unnamed Supplier';
        const suppMobile = s.mobile_number || s.mobile || s.phone || 'N/A';
        const suppAltMobile = s.alt_mobile_number || 'N/A';
        const suppEmail = s.email || 'N/A';
        const suppGst = s.gst_number || s.tax_number || 'N/A';
        const suppMethods = s.payment_methods || 'N/A';
        const suppAddress = s.address || 'N/A';
        const suppCity = s.city || '';
        const suppState = s.state || '';

        // Calculate purchase and payment values
        let calculatedTotalPurchase = 0;
        supplierProducts.forEach(p => {
            const u = parseFloat(p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)) || 0;
            const r = parseFloat(p.prod_piece_rate || 0) || 0;
            const stored = parseFloat(p.prod_purchase_price || 0) || 0;
            calculatedTotalPurchase += (stored > 0 ? stored : (u * r));
        });
        const totalPurchaseVal = calculatedTotalPurchase > 0 ? calculatedTotalPurchase : (parseFloat(s.total_amount_purchased || 0) || 0);
        const paidVal = parseFloat(s.paid_amount || s.total_amount_paid || 0) || 0;
        const balanceVal = Math.max(0, totalPurchaseVal - paidVal);

        let statusVal = 'Unpaid';
        let statusBadgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
        if (totalPurchaseVal <= 0) {
            statusVal = 'Unpaid';
            statusBadgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
        } else if (paidVal >= totalPurchaseVal) {
            statusVal = 'Paid';
            statusBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
        } else if (paidVal > 0) {
            statusVal = 'Partially Paid';
            statusBadgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
        }

        const html = `
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Supplier Name</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${suppName}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mobile Number</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${suppMobile}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${suppEmail}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Alt Mobile</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${suppAltMobile}</p>
                    </div>
                </div>
                
                <!-- Tax & Address Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST Number</p>
                        <p class="font-mono text-indigo-700 text-sm font-semibold mt-0.5">${suppGst}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Payment Methods</p>
                        <p class="font-mono text-indigo-700 text-sm font-semibold mt-0.5">${suppMethods}</p>
                    </div>
                    <div class="col-span-2 mt-2 border-t border-slate-200/50 pt-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Address</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${suppAddress}</p>
                        <p class="text-xs text-slate-600">${suppCity} ${suppState ? ', ' + suppState : ''}</p>
                    </div>
                </div>

                <!-- Purchase & Payment Summary -->
                <div class="bg-indigo-50/40 p-3.5 rounded-xl border border-indigo-100">
                    <div class="flex items-center justify-between mb-3 border-b border-indigo-100 pb-2">
                        <h5 class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Payment Summary</h5>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border ${statusBadgeClass}">
                            Status: ${statusVal}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Purchased</p>
                            <p class="text-sm font-black text-slate-800 mt-0.5">₹${totalPurchaseVal.toFixed(2)}</p>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-emerald-100">
                            <p class="text-[10px] text-emerald-600 font-bold uppercase">Amount Paid</p>
                            <p class="text-sm font-black text-emerald-700 mt-0.5">₹${paidVal.toFixed(2)}</p>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-rose-100">
                            <p class="text-[10px] text-rose-500 font-bold uppercase">Balance to Pay</p>
                            <p class="text-sm font-black text-rose-700 mt-0.5">₹${balanceVal.toFixed(2)}</p>
                        </div>
                    </div>
                </div>

                <!-- Products Supplied List -->
                <div>
                    <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 border-b border-slate-200 pb-2">Products Supplied (${supplierProducts.length})</h5>
                    <div class="bg-white rounded-xl border border-slate-200 p-3 max-h-[200px] overflow-y-auto">
                        ${productsHtml}
                    </div>
                </div>
            </div>
        `;
        document.getElementById('supplier-view-content').innerHTML = html;
        const modal = document.getElementById('supplier-view-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function editSupplier(id) {
        const s = suppliers.find(sup => String(sup.id) === String(id));
        if (!s) {
            showToast('error', 'Supplier not found.');
            return;
        }
        
        currentEditId = id;
        clearErrors();
        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Supplier';
        
        const form = document.getElementById('supplier-form');
        form.reset();
        
        // Load basic supplier fields
        Object.keys(s).forEach(key => {
            const input = form.elements[key];
            if (input && input.name && !input.name.includes('[]')) input.value = s[key] || '';
        });

        // Ensure name and mobile are populated even with alternative keys
        if (form.elements['supplier_name'] && !form.elements['supplier_name'].value) {
            form.elements['supplier_name'].value = s.supplier_name || s.name || '';
        }
        if (form.elements['mobile_number'] && !form.elements['mobile_number'].value) {
            form.elements['mobile_number'].value = s.mobile_number || s.mobile || s.phone || '';
        }

        // Restore payment method checkboxes and amount inputs
        const methodInputMap = {
            'Cash': 'payment_amount_cash',
            'UPI': 'payment_amount_upi',
            'Bank Transfer': 'payment_amount_bank',
            'Credit': 'payment_amount_credit'
        };

        const paymentMethods = (s.payment_methods || s.payment_method || '').toLowerCase();
        const pVal = parseFloat(s.paid_amount !== undefined ? s.paid_amount : (s.total_amount_paid !== undefined ? s.total_amount_paid : (s.total_paid || 0))) || 0;
        
        let checkedMethodInputs = [];
        ['Cash', 'UPI', 'Bank Transfer', 'Credit'].forEach(methodName => {
            const chk = form.querySelector(`input[name="payment_methods[]"][value="${methodName}"]`);
            const amtInput = document.getElementById(methodInputMap[methodName]);
            const isChecked = paymentMethods.includes(methodName.toLowerCase());
            if (chk) chk.checked = isChecked;
            if (amtInput) {
                if (isChecked) {
                    amtInput.classList.remove('hidden');
                    checkedMethodInputs.push(amtInput);
                } else {
                    amtInput.classList.add('hidden');
                    amtInput.value = '';
                }
            }
        });

        // Load associated products
        document.getElementById('supplier-products-container').innerHTML = '';
        const supplierProducts = products.filter(p => String(p.prod_supplier_id) === String(id));
        
        if (supplierProducts.length > 0) {
            supplierProducts.forEach(p => addSupplierProductField(p));
        }
        
        // Recalculate purchase amounts
        const totalPurchasedInput = document.getElementById('total_amount_purchased');
        if (totalPurchasedInput) {
            let totalVal = (typeof s !== 'undefined' && s) ? (parseFloat(s.total_amount_purchased !== undefined ? s.total_amount_purchased : (s.total_amount || 0)) || 0) : 0;
            const pInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
            if (pInputs.length > 0) {
                let pSum = 0;
                pInputs.forEach(inp => pSum += (parseFloat(inp.value) || 0));
                if (pSum > 0) totalVal = pSum;
            }
            if (totalVal <= 0 && typeof supplierProducts !== 'undefined' && supplierProducts && supplierProducts.length > 0) {
                let calcSum = 0;
                supplierProducts.forEach(p => {
                    const u = parseFloat(p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)) || 0;
                    const r = parseFloat(p.prod_piece_rate || 0) || 0;
                    const stored = parseFloat(p.prod_purchase_price || 0) || 0;
                    calcSum += (stored > 0 ? stored : (u * r));
                });
                if (calcSum > 0) totalVal = calcSum;
            }
            totalPurchasedInput.value = totalVal > 0 ? totalVal.toFixed(2) : '0.00';
            calculateSupplierPaymentStatus();
        }

        // Populate paid amount into method input if single method checked
        if (checkedMethodInputs.length === 1 && pVal > 0) {
            checkedMethodInputs[0].value = pVal.toFixed(2);
        }

        // Populate paid amount from supplier record
        const paidInput = document.getElementById('total_payment_entered');
        if (paidInput) {
            paidInput.value = pVal > 0 ? pVal.toFixed(2) : '';
        }

        // Recalculate payment status and balance
        calculateSupplierPaymentStatus();

        const modal = document.getElementById('supplier-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.remove('flex');
        }
    }

    // --- PRODUCT MANAGEMENT LOGIC ---
    // =========================================================================
    // SEARCHABLE SUPPLIER COMBOBOX & QUICK-ADD SUPPLIER LOGIC
    // =========================================================================
    let currentSelectedSupplierId = null;
    let supplierComboboxSearchTerm = '';

    function toggleSupplierCombobox() {
        const dropdown = document.getElementById('supplier-combobox-dropdown');
        const arrow = document.getElementById('supplier-combobox-arrow');
        if (!dropdown) return;

        const isOpening = dropdown.classList.contains('hidden');
        if (isOpening) {
            dropdown.classList.remove('hidden');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
            const searchInput = document.getElementById('supplier-combobox-search-input');
            if (searchInput) {
                searchInput.value = '';
                filterSupplierCombobox('');
                setTimeout(() => searchInput.focus(), 50);
            }
        } else {
            dropdown.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    }

    function closeSupplierCombobox() {
        const dropdown = document.getElementById('supplier-combobox-dropdown');
        const arrow = document.getElementById('supplier-combobox-arrow');
        if (dropdown) dropdown.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }

    // Close combobox when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('supplier-combobox-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeSupplierCombobox();
        }
    });

    function filterSupplierCombobox(query) {
        supplierComboboxSearchTerm = (query || '').toLowerCase().trim();
        const list = document.getElementById('supplier-combobox-list');
        const typedNameSpan = document.getElementById('quick-add-typed-name');
        if (typedNameSpan) {
            typedNameSpan.textContent = supplierComboboxSearchTerm ? ': "' + (query.trim()) + '"' : '';
        }

        if (!list) return;

        const filtered = suppliers.filter(s => {
            if (!supplierComboboxSearchTerm) return true;
            const name = (s.supplier_name || s.name || '').toLowerCase();
            const mobile = (s.mobile_number || s.mobile || s.phone || '').toLowerCase();
            const code = (s.barcode || s.supplier_code || ('SUP-' + s.id)).toLowerCase();
            const city = (s.city || '').toLowerCase();
            const gst = (s.gst_number || '').toLowerCase();
            return name.includes(supplierComboboxSearchTerm) ||
                   mobile.includes(supplierComboboxSearchTerm) ||
                   code.includes(supplierComboboxSearchTerm) ||
                   city.includes(supplierComboboxSearchTerm) ||
                   gst.includes(supplierComboboxSearchTerm);
        });

        if (filtered.length === 0) {
            list.innerHTML = `
                <div class="p-4 text-center text-slate-400">
                    <i class="fas fa-search text-slate-300 text-lg mb-1 block"></i>
                    <p class="text-xs font-semibold text-slate-600">No matching suppliers found</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Click "+ Add New Supplier" below to create it instantly.</p>
                </div>
            `;
            return;
        }

        list.innerHTML = '';
        filtered.forEach(s => {
            const isSelected = String(s.id) === String(currentSelectedSupplierId);
            const suppName = s.supplier_name || s.name || 'Unnamed Supplier';
            const suppMobile = s.mobile_number || s.mobile || s.phone || 'N/A';
            const suppCode = s.barcode || s.supplier_code || ('SUP-' + s.id);
            const suppCity = s.city || 'Tamil Nadu';

            const item = document.createElement('div');
            item.className = `p-2.5 hover:bg-indigo-50/80 cursor-pointer flex items-center justify-between transition-colors ${isSelected ? 'bg-indigo-50 text-indigo-900 font-bold border-l-4 border-indigo-600' : 'text-slate-700'}`;
            item.onclick = () => {
                selectSupplierCombobox(s.id);
                closeSupplierCombobox();
            };

            item.innerHTML = `
                <div class="min-w-0 pr-2">
                    <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5 truncate">
                        <span>${suppName}</span>
                        ${isSelected ? '<i class="fas fa-check text-indigo-600 text-[10px]"></i>' : ''}
                    </div>
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5 flex items-center gap-2 truncate">
                        <span><i class="fas fa-phone-alt text-[9px] text-slate-400 mr-1"></i>${suppMobile}</span>
                        <span>•</span>
                        <span><i class="fas fa-map-marker-alt text-[9px] text-slate-400 mr-1"></i>${suppCity}</span>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        ${suppCode}
                    </span>
                </div>
            `;
            list.appendChild(item);
        });
    }

    function selectSupplierCombobox(id) {
        currentSelectedSupplierId = id ? String(id) : null;
        const select = document.getElementById('prod_supplier_id');
        if (select) {
            select.value = currentSelectedSupplierId || '';
            select.dispatchEvent(new Event('change'));
        }

        const displaySpan = document.getElementById('supplier-combobox-selected-text');
        const clearBtn = document.getElementById('supplier-combobox-clear-btn');
        const errSpan = document.getElementById('err-prod_supplier_id');
        if (errSpan) errSpan.classList.add('hidden');

        if (!currentSelectedSupplierId) {
            if (displaySpan) {
                displaySpan.innerHTML = '<i class="fas fa-search text-slate-300 mr-1.5"></i>Select or search supplier...';
                displaySpan.className = 'text-slate-400 font-medium truncate';
            }
            if (clearBtn) clearBtn.classList.add('hidden');
            return;
        }

        const s = suppliers.find(sup => String(sup.id) === String(currentSelectedSupplierId));
        if (s && displaySpan) {
            const suppName = s.supplier_name || s.name || 'Unnamed Supplier';
            const suppMobile = s.mobile_number || s.mobile || s.phone || '';
            const suppCode = s.barcode || s.supplier_code || ('SUP-' + s.id);

            displaySpan.innerHTML = `
                <div class="flex items-center gap-2 truncate">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs shrink-0 font-bold">
                        <i class="fas fa-truck text-[11px]"></i>
                    </span>
                    <span class="font-bold text-slate-900 text-xs truncate">${suppName}</span>
                    ${suppMobile ? `<span class="text-[11px] text-slate-500 font-mono hidden sm:inline">• ${suppMobile}</span>` : ''}
                    <span class="text-[10px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200 shrink-0">${suppCode}</span>
                </div>
            `;
            displaySpan.className = 'text-slate-800 font-medium truncate';
            if (clearBtn) clearBtn.classList.remove('hidden');
        }
    }

    function clearSupplierComboboxSelection() {
        selectSupplierCombobox(null);
        closeSupplierCombobox();
    }

    function syncSupplierCombobox(selectedId = null) {
        const select = document.getElementById('prod_supplier_id');
        if (select) {
            select.innerHTML = '<option value="">Select Supplier</option>';
            suppliers.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.supplier_name || s.name;
                if (selectedId && String(s.id) === String(selectedId)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
            if (selectedId) select.value = String(selectedId);
        }

        selectSupplierCombobox(selectedId);
        filterSupplierCombobox('');
    }

    function openQuickAddSupplierModal() {
        closeSupplierCombobox();
        const modal = document.getElementById('quick-add-supplier-modal');
        if (!modal) return;

        const nameInput = document.getElementById('quick-supp-name');
        if (nameInput) {
            nameInput.value = supplierComboboxSearchTerm ? supplierComboboxSearchTerm.trim() : '';
        }
        const mobileInput = document.getElementById('quick-supp-mobile');
        if (mobileInput) mobileInput.value = '';
        const cityInput = document.getElementById('quick-supp-city');
        if (cityInput) cityInput.value = 'Tamil Nadu';
        const gstInput = document.getElementById('quick-supp-gst');
        if (gstInput) gstInput.value = '';

        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        setTimeout(() => {
            if (nameInput && !nameInput.value) nameInput.focus();
            else if (mobileInput) mobileInput.focus();
        }, 100);
    }

    function closeQuickAddSupplierModal() {
        const modal = document.getElementById('quick-add-supplier-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    async function handleQuickAddSupplier(e) {
        e.preventDefault();
        const btn = document.getElementById('quick-supp-save-btn');
        const name = (document.getElementById('quick-supp-name')?.value || '').trim();
        const mobile = (document.getElementById('quick-supp-mobile')?.value || '').trim();
        const city = (document.getElementById('quick-supp-city')?.value || '').trim();
        const gst = (document.getElementById('quick-supp-gst')?.value || '').trim();

        if (!name || !mobile) {
            showToast('error', 'Supplier name and mobile number are required.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('supplier_name', name);
            formData.append('mobile', mobile);
            formData.append('mobile_number', mobile);
            formData.append('city', city || 'Tamil Nadu');
            formData.append('state', 'Tamil Nadu');
            if (gst) formData.append('gst_number', gst);

            const res = await fetch(getAppUrl('suppliers/create'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.success) {
                const newSuppId = data.id || (data.data ? data.data.id : Date.now());
                const newSupplierObj = {
                    id: newSuppId,
                    name: name,
                    supplier_name: name,
                    mobile: mobile,
                    mobile_number: mobile,
                    city: city || 'Tamil Nadu',
                    state: 'Tamil Nadu',
                    gst_number: gst || '',
                    barcode: 'SUP-' + newSuppId,
                    supplier_code: 'SUP-' + newSuppId,
                    status: 'Active'
                };

                // Add to local array
                suppliers.unshift(newSupplierObj);

                // Re-render supplier table and barcode dropdowns
                if (typeof renderSupplierTable === 'function') renderSupplierTable();
                if (typeof populateBarcodeDropdowns === 'function') populateBarcodeDropdowns();

                // Select in searchable combobox & native select
                syncSupplierCombobox(newSuppId);

                closeQuickAddSupplierModal();
                showToast('success', `Supplier "${name}" registered & selected! You can now add products.`);
            } else {
                showToast('error', data.message || 'Failed to save supplier.');
            }
        } catch (err) {
            console.error('Quick Add Supplier Error:', err);
            showToast('error', 'Network error occurred while saving supplier.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Save & Select Supplier';
        }
    }

    // =========================================================================
    // UNIVERSAL CLICK-TO-COPY & TABLE NUMBER POINTER LOGIC
    // =========================================================================
    function copyTableText(text, targetEl = null) {
        if (!text) return;
        text = String(text).trim();
        if (!text) return;

        // Clipboard API write
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).catch(() => fallbackClipboardCopy(text));
        } else {
            fallbackClipboardCopy(text);
        }

        // Visual feedback on element
        if (targetEl) {
            targetEl.classList.remove('copy-pulse-success');
            void targetEl.offsetWidth; // trigger reflow
            targetEl.classList.add('copy-pulse-success');
            setTimeout(() => targetEl.classList.remove('copy-pulse-success'), 500);
        }

        // Show micro toast
        showTableCopyToast(text);
    }

    function fallbackClipboardCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        ta.style.top = '-9999px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
    }

    let copyToastTimerInstance = null;
    function showTableCopyToast(text) {
        let toast = document.getElementById('table-copy-micro-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'table-copy-micro-toast';
            toast.className = 'fixed bottom-5 right-5 z-[10000] bg-slate-900/95 backdrop-blur-sm text-white px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-semibold border border-slate-700 transition-all duration-300 pointer-events-none opacity-0 translate-y-3';
            document.body.appendChild(toast);
        }
        const displayText = text.length > 35 ? text.substring(0, 32) + '...' : text;
        toast.innerHTML = `
            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fas fa-check text-[10px]"></i>
            </span>
            <span class="text-slate-300">Copied:</span>
            <span class="font-mono text-emerald-300 font-bold max-w-[220px] truncate">${displayText}</span>
        `;
        toast.classList.remove('opacity-0', 'translate-y-3');
        toast.classList.add('opacity-100', 'translate-y-0');

        clearTimeout(copyToastTimerInstance);
        copyToastTimerInstance = setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-3');
        }, 1800);
    }

    // Global Click-to-Copy Listener on Tables
    window.sheetDragHasMoved = false;
    document.addEventListener('click', function(e) {
        // If user was dragging sheet horizontally, do not copy
        if (window.sheetDragHasMoved) {
            window.sheetDragHasMoved = false;
            return;
        }

        // Ignore interactive buttons, inputs, links, dropdowns
        if (e.target.closest('button, input, select, a, textarea')) return;

        const copyEl = e.target.closest('[data-copy]');
        const cell = e.target.closest('td');
        if (!copyEl && !cell) return;
        if (cell && cell.querySelector('button, a') && !copyEl) return;

        let val = '';
        let target = null;
        if (copyEl) {
            val = copyEl.getAttribute('data-copy');
            target = copyEl;
        } else if (cell) {
            val = cell.innerText.trim();
            target = cell;
        }

        if (val) {
            copyTableText(val, target);
        }
    });

    function openProductModal(forSupplierId = null) {
        document.getElementById('product-form').reset();
        clearProductErrors();
        currentProductEditId = null;
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-box-open text-amber-600"></i> Add Products Detail';
        
        // Sync searchable combobox & native select
        syncSupplierCombobox(forSupplierId);

        const dateInp = document.getElementById('prod_supply_date');
        if (dateInp) {
            dateInp.value = new Date().toISOString().split('T')[0];
        }

        document.getElementById('product-products-container').innerHTML = '';
        addProductProductField();

        toggleProductOffer();
        const modal = document.getElementById('product-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.remove('flex');
        }
    }

    function closeProductModal() {
        const modal = document.getElementById('product-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function closeProductViewModal() {
        const modal = document.getElementById('product-view-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function clearProductErrors() {
        const errorSpans = document.querySelectorAll('span[id^="err-prod_"]');
        errorSpans.forEach(span => span.classList.add('hidden'));
        const inputs = document.querySelectorAll('#product-form input, #product-form select, #product-form textarea');
        inputs.forEach(input => input.classList.remove('border-rose-500'));
    }

    function showProductError(fieldId, message = null) {
        const span = document.getElementById('err-' + fieldId);
        const input = document.getElementById(fieldId);
        if (span) {
            if (message) span.textContent = message;
            span.classList.remove('hidden');
        }
        if (input) {
            input.classList.add('border-rose-500');
        }
    }

    function calculateSellingPrice() {
        // Obsolete function since selling price is calculated in calculateProductPurchase
    }

    function toggleProductOffer() {
        const offerAvailable = document.getElementById('prod_offer_available').value;
        const offerFields = document.querySelectorAll('.prod-offer-field');
        if (offerAvailable === 'Yes') {
            offerFields.forEach(f => {
                f.classList.remove('opacity-50', 'pointer-events-none');
                const input = f.querySelector('input, select');
                if (input) input.disabled = false;
            });
        } else {
            offerFields.forEach(f => {
                f.classList.add('opacity-50', 'pointer-events-none');
                const input = f.querySelector('input, select');
                if (input) {
                    input.disabled = true;
                    if (input.tagName === 'INPUT') input.value = '';
                }
            });
        }
    }

    async function handleProductSubmit(event) {
        if (event) event.preventDefault();
        clearProductErrors();
        
        const form = document.getElementById('product-form');

        // Sync visible category and subcategory inputs to hidden inputs
        form.querySelectorAll('.product-product-block').forEach(block => {
            const catVis = block.querySelector('.cat-search-input');
            const catHid = block.querySelector('input[name="ap_product_category[]"]');
            if (catVis && catHid) {
                catHid.value = catVis.value.trim();
            }
            const subVis = block.querySelector('.subcat-search-input');
            const subHid = block.querySelector('input[name="ap_product_subcategory[]"]');
            if (subVis && subHid) {
                subHid.value = subVis.value.trim();
            }
        });

        const formData = new FormData(form);
        const prod_supplier_id = formData.get('prod_supplier_id');
        
        if (!prod_supplier_id) {
            showProductError('prod_supplier_id', 'Supplier is required');
            return;
        }

        // Check that at least one product has a name
        const productBlocks = document.querySelectorAll('.product-product-block');
        let hasValidProduct = false;
        let hasMissingCat = false;

        productBlocks.forEach(block => {
            const name = block.querySelector('input[name="ap_product_name[]"]')?.value?.trim();
            if (name) {
                hasValidProduct = true;
                const catVal = block.querySelector('input[name="ap_product_category[]"]')?.value?.trim() || block.querySelector('.cat-search-input')?.value?.trim();
                if (!catVal) {
                    hasMissingCat = true;
                }
            }
        });

        if (!hasValidProduct) {
            showToast('error', 'Please enter at least one product name.');
            return;
        }

        if (hasMissingCat) {
            showToast('error', 'Please select or enter a category for each product.');
            return;
        }

        formData.append('_csrf_token', window.CSRF_TOKEN || '');
        if (currentProductEditId) {
            formData.append('product_id', currentProductEditId);
        }

        const submitBtn = form.querySelector('button[type="submit"]') || document.querySelector('button[form="product-form"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Saving...';
        }

        try {
            const res = await fetch(getAppUrl('products/save-detail'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                showToast('success', data.message || 'Product(s) saved successfully.');
                closeProductModal();
                currentProductEditId = null;
                await refreshStateFromDb();
            } else {
                showToast('error', data.message || 'Failed to save product(s).');
            }
        } catch (err) {
            console.error('Error saving product detail:', err);
            showToast('error', 'Network error occurred while saving product(s).');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-1.5"></i> Save Product';
            }
        }
    }

    // --- PRODUCT SEARCH, FILTER & PAGINATION STATE ---
    let productCurrentPage = 1;
    let productPerPage = 10;
    let productSearchQuery = '';
    let productCategoryFilter = '';
    let productSupplierFilter = '';
    let productStockFilter = '';

    function filterProductTable() {
        productSearchQuery = (document.getElementById('product-search-input')?.value || '').toLowerCase().trim();
        productCategoryFilter = (document.getElementById('product-category-filter')?.value || '').trim();
        productSupplierFilter = (document.getElementById('product-supplier-filter')?.value || '').trim();
        productStockFilter = (document.getElementById('product-stock-filter')?.value || '').trim();
        productCurrentPage = 1;

        const clearBtn = document.getElementById('product-search-clear');
        if (clearBtn) {
            if (productSearchQuery.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderProductTable();
    }

    function clearProductSearch() {
        const inp = document.getElementById('product-search-input');
        if (inp) inp.value = '';
        filterProductTable();
    }

    function resetProductFilters() {
        const sInp = document.getElementById('product-search-input');
        if (sInp) sInp.value = '';
        const cSel = document.getElementById('product-category-filter');
        if (cSel) cSel.value = '';
        const supSel = document.getElementById('product-supplier-filter');
        if (supSel) supSel.value = '';
        const stSel = document.getElementById('product-stock-filter');
        if (stSel) stSel.value = '';

        productSearchQuery = '';
        productCategoryFilter = '';
        productSupplierFilter = '';
        productStockFilter = '';
        productCurrentPage = 1;
        filterProductTable();
    }

    function changeProductPerPage(val) {
        productPerPage = parseInt(val) || 10;
        productCurrentPage = 1;
        renderProductTable();
    }

    function goToProductPage(page) {
        productCurrentPage = page;
        renderProductTable();
    }

    function populateProductFilterDropdowns() {
        // Categories
        const catSelect = document.getElementById('product-category-filter');
        if (catSelect && catSelect.options.length <= 1) {
            const curVal = catSelect.value;
            const uniqueCats = [...new Set(products.map(p => p.prod_category).filter(Boolean))].sort();
            uniqueCats.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                catSelect.appendChild(opt);
            });
            if (curVal) catSelect.value = curVal;
        }

        // Suppliers
        const supSelect = document.getElementById('product-supplier-filter');
        if (supSelect && supSelect.options.length <= 1) {
            const curVal = supSelect.value;
            suppliers.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.supplier_name || s.name || ('Supplier #' + s.id);
                supSelect.appendChild(opt);
            });
            if (curVal) supSelect.value = curVal;
        }
    }

    function renderProductTable() {
        populateProductFilterDropdowns();

        const tbody = document.getElementById('product-table-body');
        if (!tbody) return;

        // Apply filters
        const filtered = products.filter(p => {
            const pName = (p.prod_name || '').toLowerCase();
            const pSku = (p.prod_sku || '').toLowerCase();
            const pBarcode = (p.prod_barcode || '').toLowerCase();
            const pBrand = (p.prod_brand || '').toLowerCase();
            const pCat = (p.prod_category || '').toLowerCase();
            const sup = suppliers.find(s => String(s.id) === String(p.prod_supplier_id));
            const suppName = (sup ? (sup.supplier_name || sup.name || '') : '').toLowerCase();

            if (productSearchQuery) {
                const matches = pName.includes(productSearchQuery) ||
                                pSku.includes(productSearchQuery) ||
                                pBarcode.includes(productSearchQuery) ||
                                pBrand.includes(productSearchQuery) ||
                                pCat.includes(productSearchQuery) ||
                                suppName.includes(productSearchQuery);
                if (!matches) return false;
            }

            if (productCategoryFilter) {
                if (pCat !== productCategoryFilter.toLowerCase()) return false;
            }

            if (productSupplierFilter) {
                if (String(p.prod_supplier_id) !== String(productSupplierFilter)) return false;
            }

            const stock = p.prod_current_stock !== undefined ? p.prod_current_stock : (parseInt(p.prod_unit) || 0);
            const minStock = p.prod_min_stock || 0;

            if (productStockFilter === 'in_stock' && stock <= 0) return false;
            if (productStockFilter === 'out_of_stock' && stock > 0) return false;
            if (productStockFilter === 'low_stock' && (stock <= 0 || stock > minStock)) return false;

            return true;
        });

        // Update badge
        const badge = document.getElementById('product-filter-count-badge');
        if (badge) {
            badge.textContent = `${filtered.length} of ${products.length} Products`;
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr id="product-empty-state-row">
                    <td colspan="9" class="py-12 text-center text-amber-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-amber-300"></i>
                        <p class="font-bold text-amber-600 text-sm">No products found</p>
                        <p class="text-xs mt-1 text-slate-500">Try adjusting your search query or filters above.</p>
                    </td>
                </tr>`;
            renderProductPagination(0);
            return;
        }

        // Pagination calculations
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / productPerPage) || 1;
        if (productCurrentPage > totalPages) productCurrentPage = totalPages;
        if (productCurrentPage < 1) productCurrentPage = 1;

        const startIdx = (productCurrentPage - 1) * productPerPage;
        const pageItems = filtered.slice(startIdx, startIdx + productPerPage);

        tbody.innerHTML = '';
        pageItems.forEach(p => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-amber-50/30 transition-colors text-xs border-b border-amber-100/60';
            
            let offerDisplay = 'N/A';
            if (p.prod_offer_available === 'Yes' && p.prod_offer_amount) {
                offerDisplay = p.prod_offer_type === '%' ? `${p.prod_offer_amount}%` : `₹${parseFloat(p.prod_offer_amount).toFixed(2)}`;
            }
            
            let supplierName = 'Unknown';
            const sup = suppliers.find(s => String(s.id) === String(p.prod_supplier_id));
            if (sup) supplierName = sup.supplier_name || sup.name || 'Unknown';

            const purchasePrice = parseFloat(p.prod_purchase_price) || 0;
            const sellingPrice = parseFloat(p.prod_selling_price) || 0;
            const gst = parseFloat(p.prod_gst) || 0;
            const stock = p.prod_current_stock !== undefined ? p.prod_current_stock : (parseInt(p.prod_unit) || 0);
            const unit = p.prod_unit && isNaN(p.prod_unit) ? p.prod_unit : 'Pieces';

            tr.innerHTML = `
                <td class="py-3.5 px-4 min-w-[200px]">
                    <div class="font-extrabold text-slate-800 text-sm leading-snug hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${p.prod_name}" title="Click to copy product name">${p.prod_name}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5"><i class="fas fa-barcode text-slate-400"></i> <span class="hover:text-indigo-600 cursor-pointer" data-copy="${p.prod_sku || ''}" title="Click to copy SKU">${p.prod_sku || 'N/A'}</span> <span class="text-slate-300">/</span> <span class="hover:text-indigo-600 cursor-pointer" data-copy="${p.prod_barcode || ''}" title="Click to copy barcode">${p.prod_barcode || 'N/A'}</span></div>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">
                    <span class="text-[10px] text-amber-800 bg-amber-50 inline-block px-2.5 py-1 rounded-md border border-amber-200/80 font-bold cursor-pointer hover:bg-amber-100" data-copy="${p.prod_category || 'General'}" title="Click to copy category">${p.prod_category || 'General'}</span>
                </td>
                <td class="py-3.5 px-4 font-semibold text-slate-700 text-xs whitespace-nowrap hover:text-indigo-600 transition-colors cursor-pointer" data-copy="${supplierName}" title="Click to copy supplier name">${supplierName}</td>
                <td class="py-3.5 px-4 font-mono whitespace-nowrap">
                    <div class="text-slate-900 font-bold text-xs flex items-center gap-1 hover:text-indigo-600 cursor-pointer" data-copy="${purchasePrice.toFixed(2)}" title="Click to copy purchase price"><span class="text-[10px] font-semibold text-slate-400">P:</span> ₹${purchasePrice.toFixed(2)}</div>
                    <div class="text-[11px] text-emerald-600 font-bold flex items-center gap-1 mt-0.5 hover:text-emerald-800 cursor-pointer" data-copy="${sellingPrice.toFixed(2)}" title="Click to copy selling price"><span class="text-[10px] font-semibold text-emerald-500">S:</span> ₹${sellingPrice.toFixed(2)}</div>
                </td>
                <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-600 font-bold whitespace-nowrap hover:text-indigo-600 cursor-pointer" data-copy="${gst}%" title="Click to copy GST %">${gst}%</td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                    <div class="font-bold ${stock <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-800'} text-xs cursor-pointer" data-copy="${stock}" title="Click to copy stock units">${stock} <span class="text-[10px] font-normal text-slate-400">${unit}</span></div>
                    ${p.last_restocked_qty > 0 ? `
                        <div class="mt-0.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Last restock: +${p.last_restocked_qty}">
                            <i class="fas fa-arrow-up text-[8px]"></i> +${p.last_restocked_qty}
                        </div>` : ''}
                </td>
                <td class="py-3.5 px-4 text-center font-bold text-fuchsia-600 text-xs whitespace-nowrap">${offerDisplay}</td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${(p.prod_status || 'Active') === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ((p.prod_status || '') === 'Out of Stock' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200')}">
                        ${p.prod_status || 'Active'}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-1.5">
                        <button onclick="viewProduct('${p.id}')" title="View Product" class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs flex items-center justify-center transition-colors border border-amber-200 shadow-xs"><i class="fas fa-eye"></i></button>
                        <button onclick="editProduct('${p.id}')" title="Edit Product" class="w-7 h-7 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs flex items-center justify-center transition-colors border border-indigo-200 shadow-xs"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteProduct('${p.id}')" title="Delete Product" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs flex items-center justify-center transition-colors border border-rose-200 shadow-xs"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        renderProductPagination(totalItems);
    }

    function renderProductPagination(totalItems) {
        const pageInfo = document.getElementById('product-page-info');
        const buttonsContainer = document.getElementById('product-page-buttons');
        if (!pageInfo || !buttonsContainer) return;

        if (totalItems === 0) {
            pageInfo.textContent = 'Showing 0 to 0 of 0 entries';
            buttonsContainer.innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(totalItems / productPerPage) || 1;
        const startItem = (productCurrentPage - 1) * productPerPage + 1;
        const endItem = Math.min(productCurrentPage * productPerPage, totalItems);

        pageInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} entries`;

        let btnsHtml = '';

        // Prev Button
        btnsHtml += `
            <button type="button" onclick="goToProductPage(${productCurrentPage - 1})" ${productCurrentPage <= 1 ? 'disabled class="px-2.5 py-1 rounded-lg text-slate-300 border border-slate-200 cursor-not-allowed"' : 'class="px-2.5 py-1 rounded-lg text-slate-700 hover:bg-amber-100 hover:text-amber-800 border border-slate-200 transition-colors font-bold"'}>
                <i class="fas fa-chevron-left text-[10px]"></i> Prev
            </button>
        `;

        // Page Numbers (up to 5 around current)
        let startPage = Math.max(1, productCurrentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        for (let i = startPage; i <= endPage; i++) {
            const isCur = i === productCurrentPage;
            btnsHtml += `
                <button type="button" onclick="goToProductPage(${i})" class="w-7 h-7 rounded-lg text-xs font-bold transition-colors ${isCur ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-100 border border-slate-200'}">
                    ${i}
                </button>
            `;
        }

        // Next Button
        btnsHtml += `
            <button type="button" onclick="goToProductPage(${productCurrentPage + 1})" ${productCurrentPage >= totalPages ? 'disabled class="px-2.5 py-1 rounded-lg text-slate-300 border border-slate-200 cursor-not-allowed"' : 'class="px-2.5 py-1 rounded-lg text-slate-700 hover:bg-amber-100 hover:text-amber-800 border border-slate-200 transition-colors font-bold"'}>
                Next <i class="fas fa-chevron-right text-[10px]"></i>
            </button>
        `;

        buttonsContainer.innerHTML = btnsHtml;
    }

        function viewProduct(id) {
        const p = products.find(prod => String(prod.id) === String(id));
        if (!p) {
            showToast('error', 'Product not found.');
            return;
        }
        
        let supplierName = 'Unknown';
        const sup = suppliers.find(s => String(s.id) === String(p.prod_supplier_id));
        if(sup) supplierName = sup.supplier_name || sup.name || 'Unknown';

        let finalPrice = parseFloat(p.prod_selling_price) || 0;
        if (p.prod_offer_available === 'Yes' && p.prod_offer_amount) {
            if (p.prod_offer_type === '%') {
                finalPrice = finalPrice - (finalPrice * parseFloat(p.prod_offer_amount) / 100);
            } else {
                finalPrice = finalPrice - parseFloat(p.prod_offer_amount);
            }
        }

        const html = `
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Product Name</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_name}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">SKU / Barcode</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_sku || 'N/A'} / ${p.prod_barcode || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Category / Brand</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_category || 'General'} ${p.prod_brand ? '- ' + p.prod_brand : ''}</p>
                    </div>
                </div>
                
                <!-- Supplier Information -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 border-b border-slate-100 pb-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Supplier Name</p>
                        <p class="font-bold text-indigo-900 text-sm mt-0.5">${supplierName}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mobile Number</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup ? (sup.mobile_number || sup.mobile || sup.phone || 'N/A') : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup && sup.email ? sup.email : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST Number</p>
                        <p class="font-mono text-slate-700 text-xs font-semibold mt-0.5">${sup && (sup.gst_number || sup.tax_number) ? (sup.gst_number || sup.tax_number) : 'N/A'}</p>
                    </div>
                    <div class="col-span-2 lg:col-span-4 mt-2 border-t border-slate-200/50 pt-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Address</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup && sup.address ? sup.address : 'N/A'}${sup && sup.city ? ', ' + sup.city : ''}${sup && sup.state ? ', ' + sup.state : ''}</p>
                    </div>
                </div>

                <!-- Pricing & Tax -->
                <div class="grid grid-cols-3 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Purchase Price</p>
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${(parseFloat(p.prod_purchase_price) || 0).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Selling Price</p>
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${(parseFloat(p.prod_selling_price) || 0).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Final Price (Offer)</p>
                        <p class="font-bold text-indigo-600 text-sm mt-0.5">₹${finalPrice.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_gst || 0}% (${p.prod_tax_inclusive === 'Yes' ? 'Inclusive' : 'Exclusive'})</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">HSN/SAC Code</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_hsn || 'N/A'}</p>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Current Stock</p>
                        <p class="font-bold ${(p.prod_current_stock !== undefined ? p.prod_current_stock : 0) <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-800'} text-lg mt-0.5">${p.prod_current_stock !== undefined ? p.prod_current_stock : (p.prod_unit || 0)} ${p.prod_unit || 'Pieces'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Restocked Count</p>
                        ${p.last_restocked_qty > 0 ? `
                            <p class="font-bold text-emerald-700 text-sm mt-0.5 flex items-center gap-1.5">
                                <i class="fas fa-boxes text-emerald-500"></i> +${p.last_restocked_qty} ${p.prod_unit || 'Pieces'}
                            </p>
                            <p class="text-[9px] text-slate-400 mt-0.5">${p.last_restocked_at ? 'On ' + p.last_restocked_at : ''} (Total: ${p.total_restocked_qty || p.last_restocked_qty})</p>
                        ` : `
                            <p class="font-semibold text-slate-500 text-xs mt-0.5">Initial Batch</p>
                        `}
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Min / Max Stock</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_min_stock || 0} / ${p.prod_max_stock || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Storage Location</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_location || 'N/A'}</p>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('product-view-content').innerHTML = html;
        const modal = document.getElementById('product-view-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.remove('flex');
        }
    }

    function editProduct(id) {
        const p = products.find(prod => String(prod.id) === String(id));
        if (!p) {
            showToast('error', 'Product not found.');
            return;
        }
        
        currentProductEditId = id;
        clearProductErrors();
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Product';
        
        const form = document.getElementById('product-form');
        syncSupplierCombobox(p.prod_supplier_id || '');
        if(form.elements['prod_tax_inclusive']) form.elements['prod_tax_inclusive'].value = p.prod_tax_inclusive || 'No';
        if(form.elements['prod_offer_available']) form.elements['prod_offer_available'].value = p.prod_offer_available || 'No';
        if(form.elements['prod_offer_type']) form.elements['prod_offer_type'].value = p.prod_offer_type || '%';
        if(form.elements['prod_offer_amount']) form.elements['prod_offer_amount'].value = p.prod_offer_amount || '';
        
        document.getElementById('product-products-container').innerHTML = '';
        addProductProductField(p);
        calculateTotalProductPurchase();
        calculateProdTotalPaid();
        
        toggleProductOffer();
        const modal = document.getElementById('product-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.remove('flex');
        }
    }

    // Dynamic Product Methods for Add Product form
    function addProductProductField(productData = null) {
        const container = document.getElementById('product-products-container');
        const div = document.createElement('div');
        div.className = 'product-product-block relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 border border-amber-200 rounded-lg bg-white shadow-sm';
        
        let marginVal = '';
        if (productData) {
            if (productData.margin_percentage !== undefined && productData.margin_percentage !== null && productData.margin_percentage !== '') {
                marginVal = productData.margin_percentage;
            } else if (productData.prod_margin_percentage !== undefined && productData.prod_margin_percentage !== null && productData.prod_margin_percentage !== '') {
                marginVal = productData.prod_margin_percentage;
            }
        }

        div.innerHTML = `
            <button type="button" onclick="removeProductProductField(this)" class="absolute -top-2 -right-2 bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors shadow-sm">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="ap_product_name[]" value="${productData ? (productData.prod_name || '') : ''}" placeholder="e.g. Brass Ganesha Idol" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="checkProductRestockMatch(this)">
                <div class="restock-hint-container mt-1 hidden"></div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <input type="text" autocomplete="off" onfocus="showCategoryDropdown(this)" onclick="showCategoryDropdown(this)" oninput="filterCategoryDropdown(this)" onchange="const h=this.parentElement.querySelector('input[type=hidden]');if(h)h.value=this.value.trim();" placeholder="Search category or Add category" class="cat-search-input w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" value="${productData ? (productData.prod_category || '') : ''}">
                <input type="hidden" name="ap_product_category[]" value="${productData ? (productData.prod_category || '') : ''}">
                <div class="category-dropdown absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-xl hidden max-h-48 overflow-y-auto mt-1" style="top: 100%;"></div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Subcategory</label>
                <input type="text" autocomplete="off" onfocus="showSubcategoryDropdown(this)" onclick="showSubcategoryDropdown(this)" oninput="filterSubcategoryDropdown(this)" onchange="const h=this.parentElement.querySelector('input[type=hidden]');if(h)h.value=this.value.trim();" placeholder="Search subcategory or Add subcat" class="subcat-search-input w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" value="${productData ? (productData.prod_subcategory || '') : ''}">
                <input type="hidden" name="ap_product_subcategory[]" value="${productData ? (productData.prod_subcategory || '') : ''}">
                <div class="subcategory-dropdown absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-xl hidden max-h-48 overflow-y-auto mt-1" style="top: 100%;"></div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Pieces Count</label>
                <input type="number" min="0" name="ap_unit[]" value="${productData ? (productData.prod_current_stock !== undefined ? productData.prod_current_stock : (productData.prod_unit || '')) : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 10" oninput="calculateProductPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">One Piece Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="ap_piece_rate[]" value="${productData ? (productData.prod_piece_rate || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="calculateProductPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Total Purchase Price (₹)</label>
                <input type="number" step="0.01" min="0" name="ap_purchase_price[]" value="${productData ? (productData.prod_purchase_price || '') : ''}" class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-lg px-2 py-1.5 text-xs outline-none cursor-not-allowed" readonly>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Margin Percentage (%)</label>
                <input type="number" step="0.01" min="0" name="ap_margin_percentage[]" value="${marginVal}" placeholder="Enter Margin % (Default: 60%)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" oninput="calculateProductSellingPrice(this)">
                <span class="text-[10px] text-rose-500 hidden mt-0.5 err-margin-msg"></span>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Selling Price (₹)</label>
                <input type="number" step="0.01" min="0" name="ap_selling_price[]" value="${productData ? (productData.prod_selling_price || '') : ''}" class="w-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">GST (%)</label>
                <input type="number" step="0.1" min="0" max="100" name="ap_gst[]" value="${productData ? (productData.prod_gst || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Barcode (Auto)</label>
                <input type="text" name="ap_barcode[]" value="${productData && (productData.prod_barcode || productData.barcode) ? (productData.prod_barcode || productData.barcode) : generateSupplierBarcode()}" class="w-full bg-slate-100 border border-slate-200 rounded-lg px-2 py-1.5 text-xs text-slate-500 font-mono outline-none" readonly>
            </div>
            <input type="hidden" name="ap_product_id[]" value="${productData ? productData.id : ''}">
        `;
        container.appendChild(div);
    }

    function removeProductProductField(button) {
        const block = button.closest('.product-product-block');
        block.remove();
        calculateTotalProductPurchase();
    }

    function checkProductRestockMatch(input) {
        const block = input.closest('.product-product-block') || input.closest('.supplier-product-block');
        if (!block) return;
        const nameVal = input.value.trim().toLowerCase();
        const hintDiv = block.querySelector('.restock-hint-container');
        if (!hintDiv) return;

        if (!nameVal) {
            hintDiv.classList.add('hidden');
            hintDiv.innerHTML = '';
            return;
        }

        let suppId = null;
        const prodSuppSelect = document.getElementById('prod_supplier_id');
        if (prodSuppSelect && prodSuppSelect.value) {
            suppId = prodSuppSelect.value;
        } else if (typeof currentEditId !== 'undefined' && currentEditId) {
            suppId = currentEditId;
        }

        const match = products.find(p => {
            const pName = (p.prod_name || '').trim().toLowerCase();
            if (pName !== nameVal) return false;
            if (suppId) {
                return String(p.prod_supplier_id) === String(suppId);
            }
            return true;
        });

        if (match) {
            hintDiv.classList.remove('hidden');
            hintDiv.innerHTML = `
                <div class="flex items-center gap-1.5 px-2 py-1 bg-emerald-50 border border-emerald-200 rounded-md text-[10px] text-emerald-800 font-medium">
                    <i class="fas fa-sync-alt text-emerald-600"></i>
                    <span><strong>Existing Product:</strong> ${match.prod_sku} (Stock: ${match.prod_current_stock}). Pieces count will <strong>restock</strong> this item.</span>
                </div>`;

            const hidId = block.querySelector('input[name="ap_product_id[]"]') || block.querySelector('input[name="sp_product_id[]"]');
            if (hidId && !hidId.value) {
                hidId.value = match.id;
            }
            const catInput = block.querySelector('.cat-search-input');
            const catHid = block.querySelector('input[name="ap_product_category[]"]') || block.querySelector('input[name="sp_product_category[]"]');
            if (catInput && !catInput.value && match.prod_category) {
                catInput.value = match.prod_category;
                if (catHid) catHid.value = match.prod_category;
            }
            const rateInput = block.querySelector('input[name="ap_piece_rate[]"]') || block.querySelector('input[name="sp_piece_rate[]"]');
            if (rateInput && (!rateInput.value || parseFloat(rateInput.value) <= 0) && match.prod_piece_rate) {
                rateInput.value = match.prod_piece_rate;
            }
            const barInput = block.querySelector('input[name="ap_barcode[]"]') || block.querySelector('input[name="sp_barcode[]"]');
            if (barInput && match.prod_barcode) {
                barInput.value = match.prod_barcode;
            }
            if (typeof calculateProductPurchase === 'function' && block.classList.contains('product-product-block')) {
                const piecesInput = block.querySelector('input[name="ap_unit[]"]');
                if (piecesInput) calculateProductPurchase(piecesInput);
            } else if (typeof calculateSupplierPurchase === 'function' && block.classList.contains('supplier-product-block')) {
                const piecesInput = block.querySelector('input[name="sp_unit[]"]');
                if (piecesInput) calculateSupplierPurchase(piecesInput);
            }
        } else {
            hintDiv.classList.add('hidden');
            hintDiv.innerHTML = '';
        }
    }

    function calculateProductSellingPrice(element) {
        const parentBlock = element.closest('.product-product-block');
        if (!parentBlock) return;
        const pieceRateInput = parentBlock.querySelector('input[name="ap_piece_rate[]"]');
        const marginInput = parentBlock.querySelector('input[name="ap_margin_percentage[]"]');
        const sellingInput = parentBlock.querySelector('input[name="ap_selling_price[]"]');
        const errSpan = parentBlock.querySelector('.err-margin-msg');

        const pieceRate = parseFloat(pieceRateInput ? pieceRateInput.value : 0) || 0;
        const rawMargin = marginInput ? marginInput.value.trim() : '';

        let effectiveMargin = 60.0;

        if (rawMargin === '') {
            effectiveMargin = 60.0;
            if (errSpan) {
                errSpan.textContent = '';
                errSpan.classList.add('hidden');
            }
            if (marginInput) marginInput.classList.remove('border-rose-500');
        } else {
            const parsedMargin = parseFloat(rawMargin);
            if (isNaN(parsedMargin) || !isFinite(parsedMargin) || parsedMargin < 0) {
                if (errSpan) {
                    errSpan.textContent = 'Margin % must be 0 or greater';
                    errSpan.classList.remove('hidden');
                }
                if (marginInput) marginInput.classList.add('border-rose-500');
                return;
            } else {
                if (errSpan) {
                    errSpan.textContent = '';
                    errSpan.classList.add('hidden');
                }
                if (marginInput) marginInput.classList.remove('border-rose-500');
                effectiveMargin = parsedMargin;
            }
        }

        if (pieceRate >= 0 && pieceRateInput && pieceRateInput.value !== '') {
            const sellingPrice = pieceRate + (pieceRate * (effectiveMargin / 100.0));
            if (sellingInput) {
                sellingInput.value = sellingPrice.toFixed(2);
            }
        } else {
            if (sellingInput) sellingInput.value = '';
        }
    }

    function calculateProductPurchase(element) {
        const parentBlock = element.closest('.product-product-block');
        const unitInput = parentBlock.querySelector('input[name="ap_unit[]"]');
        const pieceRateInput = parentBlock.querySelector('input[name="ap_piece_rate[]"]');
        const purchaseInput = parentBlock.querySelector('input[name="ap_purchase_price[]"]');

        const units = parseFloat(unitInput.value) || 0;
        const pieceRate = parseFloat(pieceRateInput.value) || 0;
        
        if (units > 0 && pieceRate >= 0) {
            const purchasePrice = units * pieceRate;
            purchaseInput.value = purchasePrice.toFixed(2);
        } else {
            purchaseInput.value = '';
        }
        
        // Auto-calculate selling price using dynamic margin percentage
        calculateProductSellingPrice(element);
        
        calculateTotalProductPurchase();
    }

    function calculateTotalProductPurchase() {
        const purchaseInputs = document.querySelectorAll('input[name="ap_purchase_price[]"]');
        let total = 0;
        purchaseInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalAmountInput = document.getElementById('prod_total_amount_purchased');
        if (totalAmountInput) {
            totalAmountInput.value = total > 0 ? total.toFixed(2) : '';
        }
        
        calculateProdTotalPaid();
    }

    // --- TOAST NOTIFICATION SYSTEM ---
    function showToast(type, message) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-4 right-4 z-[9999] flex flex-col gap-2';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `min-w-[300px] px-4 py-3 rounded-xl shadow-lg border flex items-center gap-3 transform translate-y-10 opacity-0 transition-all duration-300`;
        
        if (type === 'success') {
            toast.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
            toast.innerHTML = `<i class="fas fa-check-circle text-emerald-500 text-lg"></i> <span class="font-bold text-sm">${message}</span>`;
        } else if (type === 'error') {
            toast.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-800');
            toast.innerHTML = `<i class="fas fa-exclamation-circle text-rose-500 text-lg"></i> <span class="font-bold text-sm">${message}</span>`;
        }

        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Animate out
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // --- SUPPLIER INVOICE TABLE RENDERING & FILTERING ---
    function renderInvoiceTable(filteredList = null) {
        const tbody = document.getElementById('invoice-table-body');
        if (!tbody) return;

        const allInvoices = invoices || [];
        const countEl = document.getElementById('inv-stat-count');
        const totalEl = document.getElementById('inv-stat-total');
        const paidEl = document.getElementById('inv-stat-paid');
        const dueEl = document.getElementById('inv-stat-due');

        if (countEl) countEl.textContent = allInvoices.length;
        if (totalEl) {
            const sumTotal = allInvoices.reduce((acc, i) => acc + (parseFloat(i.amount || i.total_amount) || 0), 0);
            totalEl.textContent = '₹' + sumTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (paidEl) {
            const sumPaid = allInvoices.reduce((acc, i) => acc + (parseFloat(i.paid_amount) || 0), 0);
            paidEl.textContent = '₹' + sumPaid.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (dueEl) {
            const sumDue = allInvoices.reduce((acc, i) => acc + (parseFloat(i.balance_amount) || 0), 0);
            dueEl.textContent = '₹' + sumDue.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const displayList = (filteredList !== null) ? filteredList : allInvoices;

        if (displayList.length === 0) {
            tbody.innerHTML = `
                <tr id="invoice-empty-state-row">
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        <i class="fas fa-truck-loading text-3xl mb-3 block text-rose-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No supplier invoices found</p>
                        <p class="text-xs mt-1">Add suppliers and products to manage supplier purchase invoices.</p>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = '';
        displayList.forEach(inv => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';

            const suppId = inv.supplier_id || inv.supplier_code || ('SUP-' + inv.id);
            const suppName = inv.supplier_name || inv.name || '';
            const amountVal = parseFloat(inv.amount || inv.total_amount || 0);
            const status = inv.payment_status || 'Unpaid';

            let badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
            if (status.toLowerCase() === 'paid') {
                badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } else if (status.toLowerCase() === 'partially paid' || status.toLowerCase() === 'partial') {
                badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
            }

            // Expected Table Format: Supplier ID | Supplier Name | Amount | Payment Status | Actions
            tr.innerHTML = `
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                    ${suppId}
                </td>
                <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 text-sm">${suppName}</span>
                    ${inv.company_name ? `<span class="text-[11px] text-slate-400 block">${inv.company_name}</span>` : ''}
                </td>
                <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm">
                    ₹${amountVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                </td>
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${badgeClass}">
                        ${status}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                    <button type="button" onclick="openSupplierInvoiceViewModal(${inv.id})" title="View" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-eye text-xs"></i> View
                    </button>
                    <button type="button" onclick='openSupplierInvoiceEditModal(${JSON.stringify(inv).replace(/'/g, "&#39;")})' title="Edit / Update Payment" class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold inline-flex items-center gap-1.5 border border-amber-200 transition-colors">
                        <i class="fas fa-edit text-xs"></i> Edit
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterInvoicesTable() {
        const searchInput = document.getElementById('inv-search-input');
        const statusSelect = document.getElementById('inv-status-filter');
        
        const q = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const status = statusSelect ? statusSelect.value.trim().toLowerCase() : '';

        const filtered = (invoices || []).filter(inv => {
            const sid = (inv.supplier_id || inv.supplier_code || '').toLowerCase();
            const sname = (inv.supplier_name || inv.name || '').toLowerCase();
            const scomp = (inv.company_name || '').toLowerCase();
            const payStatus = (inv.payment_status || 'unpaid').toLowerCase();

            const matchesSearch = !q || sid.includes(q) || sname.includes(q) || scomp.includes(q);
            const matchesStatus = !status || payStatus === status.toLowerCase();

            return matchesSearch && matchesStatus;
        });

        renderInvoiceTable(filtered);
    }

    // --- TAB CONTEXT SUPPLIER INVOICE MODALS ---
    async function openSupplierInvoiceViewModal(supplierId) {
        try {
            const res = await fetch(getAppUrl('billing/invoices/supplier/' + supplierId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success || !json.data) {
                showToast('error', 'Failed to load supplier details.');
                return;
            }

            const data = json.data;
            document.getElementById('pv_supplier_id').textContent = data.supplier_id || ('SUP-' + data.id);
            document.getElementById('pv_supplier_name').textContent = data.supplier_name;
            document.getElementById('pv_sub_heading').textContent = (data.company_name ? data.company_name + ' • ' : '') + (data.mobile || '');
            
            const formatInrVal = val => '₹' + (parseFloat(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('pv_total_amount').textContent = formatInrVal(data.total_amount);
            document.getElementById('pv_paid_amount').textContent = formatInrVal(data.paid_amount);
            document.getElementById('pv_balance_amount').textContent = formatInrVal(data.balance_amount);

            let badgeHtml = '';
            if (data.payment_status === 'Paid') {
                badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">Paid</span>';
            } else if (data.payment_status === 'Partially Paid') {
                badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-amber-50 text-amber-800 border-amber-200">Partially Paid</span>';
            } else {
                badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-rose-50 text-rose-700 border-rose-200">Unpaid</span>';
            }
            document.getElementById('pv_payment_status').innerHTML = badgeHtml;

            const tbody = document.getElementById('pv_products_tbody');
            tbody.innerHTML = '';
            const prods = data.products || [];
            document.getElementById('pv_product_count_badge').textContent = prods.length + ' products';

            if (prods.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-slate-400 font-sans">No products linked to this supplier yet.</td></tr>';
            } else {
                prods.forEach((p, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="py-2.5 px-3 text-slate-400 font-sans">${idx + 1}</td>
                        <td class="py-2.5 px-3 font-sans">
                            <div class="font-bold text-slate-900">${p.product_name}</div>
                            <div class="text-[10px] text-slate-400 font-mono">${p.code || p.sku || ''} • ${p.category_name || ''}</div>
                        </td>
                        <td class="py-2.5 px-3 text-center font-bold text-slate-800">${p.pieces_count}</td>
                        <td class="py-2.5 px-3 text-right text-slate-700">${formatInrVal(p.piece_rate)}</td>
                        <td class="py-2.5 px-3 text-right font-black text-slate-900">${formatInrVal(p.total_purchase_price)}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            document.getElementById('prod-modal-view-details').classList.remove('hidden');
        } catch(err) {
            console.error(err);
            showToast('error', 'Error loading supplier details.');
        }
    }

    function closeSupplierInvoiceViewModal() {
        document.getElementById('prod-modal-view-details').classList.add('hidden');
    }

    let peCurrentPrevPaid = 0.00;

    function openSupplierInvoiceEditModal(inv) {
        document.getElementById('pe_supplier_id').value = inv.id;
        document.getElementById('pe_supp_name').textContent = inv.supplier_name;
        document.getElementById('pe_supp_id_text').textContent = inv.supplier_id || ('SUP-' + inv.id);

        const totalAmt = parseFloat(inv.amount || inv.total_amount || 0);
        const paidAmt = parseFloat(inv.paid_amount || 0);
        peCurrentPrevPaid = paidAmt;
        const balAmt = Math.max(0, totalAmt - paidAmt);

        const formatInrVal = val => '₹' + (parseFloat(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('pe_total_purchase_amount').value = totalAmt;
        document.getElementById('pe_total_amount_display').textContent = formatInrVal(totalAmt);
        document.getElementById('pe_previously_paid_display').textContent = formatInrVal(paidAmt);
        document.getElementById('pe_balance_amount_display').textContent = formatInrVal(balAmt);

        document.getElementById('pe_additional_amount').value = '';
        document.getElementById('pe_payment_status').value = inv.payment_status || 'Unpaid';
        document.getElementById('pe_paid_amount').value = paidAmt.toFixed(2);
        document.getElementById('pe_paid_amount').max = totalAmt > 0 ? totalAmt : '';
        document.getElementById('pe_payment_method').value = inv.payment_method || 'Cash';
        document.getElementById('pe_payment_date').value = inv.payment_date || new Date().toISOString().split('T')[0];
        document.getElementById('pe_notes').value = inv.notes || '';

        onSupplierPaymentStatusChange();
        document.getElementById('prod-modal-edit-payment').classList.remove('hidden');
    }

    function closeSupplierInvoiceEditModal() {
        document.getElementById('prod-modal-edit-payment').classList.add('hidden');
    }

    function onSupplierPaymentStatusChange() {
        const status = document.getElementById('pe_payment_status').value;
        const total = parseFloat(document.getElementById('pe_total_purchase_amount').value) || 0;
        const paidInput = document.getElementById('pe_paid_amount');
        const balDisplay = document.getElementById('pe_balance_amount_display');
        const addInput = document.getElementById('pe_additional_amount');
        const formatInrVal = val => '₹' + (parseFloat(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (status === 'Paid') {
            paidInput.value = total.toFixed(2);
            if (addInput) addInput.value = Math.max(0, total - peCurrentPrevPaid).toFixed(2);
            balDisplay.textContent = formatInrVal(0);
        } else if (status === 'Unpaid') {
            paidInput.value = '0.00';
            if (addInput) addInput.value = '0.00';
            balDisplay.textContent = formatInrVal(total);
        } else if (status === 'Partially Paid') {
            let curr = parseFloat(paidInput.value) || 0;
            if (curr <= 0 && total > 0) {
                curr = total / 2;
                paidInput.value = curr.toFixed(2);
            }
            if (addInput) addInput.value = Math.max(0, curr - peCurrentPrevPaid).toFixed(2);
            const bal = Math.max(0, total - curr);
            balDisplay.textContent = formatInrVal(bal);
        }
    }

    function onSupplierAdditionalAmountInput() {
        const total = parseFloat(document.getElementById('pe_total_purchase_amount').value) || 0;
        const addInput = document.getElementById('pe_additional_amount');
        const paidInput = document.getElementById('pe_paid_amount');
        const formatInrVal = val => '₹' + (parseFloat(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        let additional = parseFloat(addInput ? addInput.value : 0);
        if (isNaN(additional) || additional < 0) additional = 0;

        let newTotalPaid = peCurrentPrevPaid + additional;
        if (total > 0 && newTotalPaid > total) {
            newTotalPaid = total;
            additional = Math.max(0, total - peCurrentPrevPaid);
            if (addInput) addInput.value = additional.toFixed(2);
        }

        if (paidInput) {
            paidInput.value = newTotalPaid.toFixed(2);
        }

        const bal = Math.max(0, total - newTotalPaid);
        document.getElementById('pe_balance_amount_display').textContent = formatInrVal(bal);

        const statusSelect = document.getElementById('pe_payment_status');
        if (total <= 0) {
            statusSelect.value = 'Unpaid';
        } else if (newTotalPaid >= total) {
            statusSelect.value = 'Paid';
        } else if (newTotalPaid <= 0) {
            statusSelect.value = 'Unpaid';
        } else {
            statusSelect.value = 'Partially Paid';
        }
    }

    function onSupplierPaidAmountInput() {
        const total = parseFloat(document.getElementById('pe_total_purchase_amount').value) || 0;
        const paidInput = document.getElementById('pe_paid_amount');
        const addInput = document.getElementById('pe_additional_amount');
        let paid = parseFloat(paidInput.value) || 0;
        const formatInrVal = val => '₹' + (parseFloat(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (total > 0 && paid > total) {
            paid = total;
            paidInput.value = total.toFixed(2);
        }
        if (paid < 0) {
            paid = 0;
            paidInput.value = '0.00';
        }

        if (addInput) {
            addInput.value = Math.max(0, paid - peCurrentPrevPaid).toFixed(2);
        }

        const bal = Math.max(0, total - paid);
        document.getElementById('pe_balance_amount_display').textContent = formatInrVal(bal);

        const statusSelect = document.getElementById('pe_payment_status');
        if (total <= 0) {
            statusSelect.value = 'Unpaid';
        } else if (paid >= total) {
            statusSelect.value = 'Paid';
        } else if (paid <= 0) {
            statusSelect.value = 'Unpaid';
        } else {
            statusSelect.value = 'Partially Paid';
        }
    }

    async function submitSupplierInvoicePayment(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-supp-payment');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const supplierId = document.getElementById('pe_supplier_id').value;
        const paidAmount = document.getElementById('pe_paid_amount').value;
        const additionalAmount = document.getElementById('pe_additional_amount') ? document.getElementById('pe_additional_amount').value : 0;
        const status = document.getElementById('pe_payment_status').value;
        const method = document.getElementById('pe_payment_method').value;
        const date = document.getElementById('pe_payment_date').value;
        const notes = document.getElementById('pe_notes').value;

        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        formData.append('supplier_id', supplierId);
        formData.append('paid_amount', paidAmount);
        if (parseFloat(additionalAmount) > 0) {
            formData.append('additional_amount', additionalAmount);
        }
        formData.append('payment_status', status);
        formData.append('payment_method', method);
        formData.append('payment_date', date);
        formData.append('notes', notes);

        try {
            const res = await fetch(getAppUrl('billing/invoices/update-payment'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                closeSupplierInvoiceEditModal();
                showToast('success', data.message || 'Payment updated successfully.');
                await refreshStateFromDb();
            } else {
                showToast('error', data.message || 'Failed to update payment.');
            }
        } catch(err) {
            console.error(err);
            showToast('error', 'Network error while updating payment.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Changes';
        }
    }

    // --- INITIALIZE TABLES ON PAGE LOAD ---
    function initModuleState() {
        loadState();
        renderSupplierTable();
        renderProductTable();
        renderBarcodeTable();
        renderInvoiceTable();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModuleState);
    } else {
        initModuleState();
    }
</script>
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Active Tab Styles */
    .active-tab {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
    }
    .active-tab .bg-gradient-to-br {
        opacity: 1 !important;
    }
    .active-tab .relative.w-16 {
        transform: translateY(-0.25rem) !important;
    }
    
    #btn-supplier-section.active-tab { border-color: #a5b4fc !important; }
    #btn-supplier-section.active-tab .relative.w-16 { background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: white !important; }
    #btn-supplier-section.active-tab h3 { color: #4338ca !important; }
    
    #btn-product-section.active-tab { border-color: #fcd34d !important; }
    #btn-product-section.active-tab .relative.w-16 { background-color: #f59e0b !important; border-color: #f59e0b !important; color: white !important; }
    #btn-product-section.active-tab h3 { color: #b45309 !important; }
    
    #btn-barcode-section.active-tab { border-color: #6ee7b7 !important; }
    #btn-barcode-section.active-tab .relative.w-16 { background-color: #10b981 !important; border-color: #10b981 !important; color: white !important; }
    #btn-barcode-section.active-tab h3 { color: #047857 !important; }
    
    #btn-invoice-section.active-tab { border-color: #fda4af !important; }
    #btn-invoice-section.active-tab .relative.w-16 { background-color: #f43f5e !important; border-color: #f43f5e !important; color: white !important; }
    #btn-invoice-section.active-tab h3 { color: #be123c !important; }

    /* Universal Pointer Cursor and Styling for Text & Numbers in Tables for Copying */
    #supplier-table-body td:not(:has(button, a)),
    #barcode-table-body td:not(:has(button, a)),
    #product-table-body td:not(:has(button, a)),
    #sp-sheet-table-body td:not(:has(button, a)),
    .copyable-cell,
    [data-copy] {
        cursor: pointer !important;
        user-select: text !important;
        -webkit-user-select: text !important;
        transition: color 0.15s ease, background-color 0.15s ease;
    }

    #supplier-table-body td:not(:has(button, a)):hover,
    #barcode-table-body td:not(:has(button, a)):hover,
    #product-table-body td:not(:has(button, a)):hover,
    #sp-sheet-table-body td:not(:has(button, a)):hover {
        background-color: rgba(99, 102, 241, 0.04);
    }

    [data-copy]:hover {
        cursor: pointer !important;
        color: #4338ca !important;
    }

    @keyframes copyFlashAnim {
        0% { outline: 2px solid #10b981; background-color: rgba(16, 185, 129, 0.25); }
        50% { outline: 2px solid #10b981; background-color: rgba(16, 185, 129, 0.35); }
        100% { outline: 2px solid transparent; background-color: transparent; }
    }

    .copy-pulse-success {
        animation: copyFlashAnim 0.45s ease-out;
        border-radius: 6px;
    }
</style>
