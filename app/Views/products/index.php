<div class="space-y-6 max-w-7xl mx-auto relative">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Management</h1>
        <p class="text-sm text-slate-500 mt-1">Select an entry module below to manage your catalog and billing details</p>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Supplier Details Card -->
        <button onclick="toggleSection('supplier-section')" class="group relative bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 overflow-hidden flex flex-col items-center text-center w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-5 group-hover:-translate-y-1 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm border border-indigo-100 group-hover:border-indigo-600">
                <i class="fas fa-truck text-2xl"></i>
            </div>
            <h3 class="relative text-lg font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">Supplier Details</h3>
            <p class="relative text-xs text-slate-500 mt-2 leading-relaxed">Manage vendor profiles, track restock sources, and view supplier history.</p>
        </button>

        <!-- Product Details Card -->
        <button onclick="toggleSection('product-section')" class="group relative bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-300 overflow-hidden flex flex-col items-center text-center w-full focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-5 group-hover:-translate-y-1 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm border border-amber-100 group-hover:border-amber-500">
                <i class="fas fa-box-open text-2xl"></i>
            </div>
            <h3 class="relative text-lg font-bold text-slate-800 group-hover:text-amber-700 transition-colors">Product Details</h3>
            <p class="relative text-xs text-slate-500 mt-2 leading-relaxed">Update inventory items, modify pricing, and catalog idol specifications.</p>
        </button>

        <!-- Barcode Details Card -->
        <button onclick="toggleSection('barcode-section')" class="group relative bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 overflow-hidden flex flex-col items-center text-center w-full focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-5 group-hover:-translate-y-1 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm border border-emerald-100 group-hover:border-emerald-500">
                <i class="fas fa-barcode text-2xl"></i>
            </div>
            <h3 class="relative text-lg font-bold text-slate-800 group-hover:text-emerald-700 transition-colors">Barcode Details</h3>
            <p class="relative text-xs text-slate-500 mt-2 leading-relaxed">Generate labels, print tags, and configure SKU tracking parameters.</p>
        </button>

        <!-- Invoice Details Card -->
        <button onclick="toggleSection('invoice-section')" class="group relative bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-rose-300 transition-all duration-300 overflow-hidden flex flex-col items-center text-center w-full focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-5 group-hover:-translate-y-1 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 shadow-sm border border-rose-100 group-hover:border-rose-500">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <h3 class="relative text-lg font-bold text-slate-800 group-hover:text-rose-700 transition-colors">Invoice Details</h3>
            <p class="relative text-xs text-slate-500 mt-2 leading-relaxed">Review purchase orders, billing records, and transaction history.</p>
        </button>

    </div>

    <!-- Dynamic Sections Panel -->
    <div id="dynamic-sections" class="mt-8">
        
        <!-- Supplier Section with Table -->
        <div id="supplier-section" class="hidden animate-fade-in bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-slate-900">Supplier Management</h4>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Add new suppliers and manage product associations.</p>
                </div>
                <button onclick="openSupplierModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>

            <!-- Supplier Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Mobile</th>
                            <th class="py-3 px-4">Barcode No.</th>
                            <th class="py-3 px-4">Products</th>
                            <th class="py-3 px-4">GST Number</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplier-table-body" class="divide-y divide-slate-100">
                        <tr id="empty-state-row">
                            <td colspan="7" class="py-12 text-center text-slate-400">
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

            <!-- Product Table -->
            <div class="overflow-x-auto rounded-xl border border-amber-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-amber-100/50 text-amber-700 uppercase text-[10px] font-bold border-b border-amber-200">
                        <tr>
                            <th class="py-3 px-4">Product & SKU</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Prices (Purchase/Sell)</th>
                            <th class="py-3 px-4">GST</th>
                            <th class="py-3 px-4">Stock</th>
                            <th class="py-3 px-4">Offer</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
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
        </div>

        <!-- Barcode Section -->
        <div id="barcode-section" class="hidden animate-fade-in bg-emerald-50/50 border border-emerald-100 rounded-2xl p-6 shadow-inner">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-lg font-extrabold text-emerald-900">Barcode Management</h4>
                    <p class="text-xs text-emerald-600/80 mt-1 font-medium">Generate new barcode labels for products and shipments.</p>
                </div>
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-barcode"></i> Print All Barcodes
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-emerald-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-emerald-100/50 text-emerald-700 uppercase text-[10px] font-bold border-b border-emerald-200">
                        <tr>
                            <th class="py-3 px-4">Barcode No.</th>
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Supplier Name</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="barcode-table-body" class="divide-y divide-emerald-100 bg-white">
                        <tr id="barcode-empty-state-row">
                            <td colspan="5" class="py-12 text-center text-emerald-400">
                                <i class="fas fa-barcode text-3xl mb-3 block text-emerald-300"></i>
                                <p class="font-bold text-emerald-600 text-sm">No barcodes found</p>
                                <p class="text-xs mt-1">Add products or suppliers to generate barcodes.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Section -->
        <div id="invoice-section" class="hidden animate-fade-in bg-rose-50/50 border border-rose-100 rounded-2xl p-6 shadow-inner">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div>
                    <h4 class="text-lg font-extrabold text-rose-900">Invoice Management</h4>
                    <p class="text-xs text-rose-600/80 mt-1 font-medium">Create new purchase or sales invoices for transactions.</p>
                </div>
                <button class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Create New Invoice
                </button>
            </div>
        </div>

    </div>
</div>


<!-- Supplier Form Modal -->
<div id="supplier-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="flex min-h-full items-start sm:items-center justify-center p-4 py-10">
        <div class="bg-white rounded-2xl w-full max-w-5xl shadow-2xl relative animate-fade-in">
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
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4"><i class="fas fa-user-tag text-indigo-500 mr-2"></i>1. Supplier Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Supplier Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="supplier_name" id="supplier_name" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-supplier_name">This field is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-mobile_number">Valid mobile number is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Address <span class="text-rose-500">*</span></label>
                        <input type="text" name="address" id="address" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-address">Address is required</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                        <input type="text" name="state" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">GST Number</label>
                        <input type="text" name="gst_number" id="gst_number" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 uppercase transition-all">
                        <span class="text-[10px] text-rose-500 hidden mt-1" id="err-gst_number">Invalid GST format</span>
                    </div>

                </div>
            </div>

            <!-- 2. Product Information -->
            <div class="bg-amber-50/30 p-5 rounded-xl border border-amber-100">
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
            <div class="bg-emerald-50/30 p-5 rounded-xl border border-emerald-100">
                <h4 class="text-sm font-bold text-slate-800 border-b border-emerald-200 pb-2 mb-4"><i class="fas fa-credit-card text-emerald-500 mr-2"></i>3. Payment Information</h4>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Purchased (₹)</label>
                    <input type="number" step="0.01" min="0" name="total_amount_purchased" id="total_amount_purchased" class="w-full md:w-1/3 bg-slate-100 border border-slate-200 text-slate-700 font-bold rounded-xl px-3 py-2 text-sm outline-none" readonly>
                    <span class="text-[10px] text-rose-500 hidden mt-1" id="err-total_amount_purchased">Cannot be negative</span>
                </div>

                <label class="block text-xs font-bold text-slate-700 mb-3">Select Payment Methods</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Cash -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="Cash" name="payment_methods[]" onchange="togglePaymentAmount(this, 'payment_amount_cash')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Cash</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_cash" id="payment_amount_cash" oninput="calculateTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- UPI -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="UPI" name="payment_methods[]" onchange="togglePaymentAmount(this, 'payment_amount_upi')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">UPI</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_upi" id="payment_amount_upi" oninput="calculateTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- Bank Transfer -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="Bank Transfer" name="payment_methods[]" onchange="togglePaymentAmount(this, 'payment_amount_bank')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Bank Transfer</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_bank" id="payment_amount_bank" oninput="calculateTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>

                    <!-- Credit -->
                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-2 transition-all">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="Credit" name="payment_methods[]" onchange="togglePaymentAmount(this, 'payment_amount_credit')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-bold text-slate-700">Credit</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="payment_amount_credit" id="payment_amount_credit" oninput="calculateTotalPaid()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-emerald-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount Paid (₹)</label>
                        <input type="number" step="0.01" min="0" id="total_payment_entered" class="w-full bg-emerald-100 border border-emerald-300 text-emerald-900 font-bold rounded-xl px-3 py-2 text-sm outline-none" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Balance to Pay (₹)</label>
                        <input type="number" step="0.01" name="balance_amount" id="balance_amount" class="w-full bg-rose-50 border border-rose-200 text-rose-800 font-bold rounded-xl px-3 py-2 text-sm outline-none" readonly>
                    </div>
                </div>
            </div>

            <!-- 4. Additional Information -->
            <div class="bg-rose-50/30 p-5 rounded-xl border border-rose-100">
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
    <div class="flex min-h-full items-start sm:items-center justify-center p-4 py-10">
        <div class="bg-white rounded-2xl w-full max-w-5xl shadow-2xl relative animate-fade-in">
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Supplier Name <span class="text-rose-500">*</span></label>
                            <select name="prod_supplier_id" id="prod_supplier_id" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                <option value="">Select Supplier</option>
                                <!-- Populated dynamically -->
                            </select>
                            <span class="text-[10px] text-rose-500 hidden mt-1" id="err-prod_supplier_id">Supplier is required</span>
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

<!-- Supplier View Modal -->
<div id="supplier-view-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl relative animate-fade-in">
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
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl relative animate-fade-in">
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
</div>

<script>
    // --- SECTION TOGGLE LOGIC ---
    function toggleSection(sectionId) {
        const sections = ['supplier-section', 'product-section', 'barcode-section', 'invoice-section'];
        let wasHidden = document.getElementById(sectionId).classList.contains('hidden');
        
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
            }
        });

        if (wasHidden) {
            const target = document.getElementById(sectionId);
            if (target) {
                target.classList.remove('hidden');
            }
        }
    }

    // --- SUPPLIER MANAGEMENT LOGIC ---
    let suppliers = [];
    let currentEditId = null;

    function openSupplierModal() {
        document.getElementById('supplier-form').reset();
        clearErrors();
        currentEditId = null;
        document.getElementById('supplier-products-container').innerHTML = '';
        addSupplierProductField(); // Add at least one empty product field
        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-truck-loading text-indigo-600"></i> Add Supplier';
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplier-modal').classList.add('hidden');
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
        div.innerHTML = `
            <button type="button" onclick="removeSupplierProductField(this)" class="absolute -top-2 -right-2 bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors shadow-sm">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="sp_product_name[]" value="${productData ? productData.prod_name : ''}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <select name="sp_product_category[]" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">Select</option>
                    <option value="Statues" ${productData && productData.prod_category === 'Statues' ? 'selected' : ''}>Statues</option>
                    <option value="Accessories" ${productData && productData.prod_category === 'Accessories' ? 'selected' : ''}>Accessories</option>
                    <option value="Metals" ${productData && productData.prod_category === 'Metals' ? 'selected' : ''}>Metals</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Subcategory</label>
                <input type="text" name="sp_product_subcategory[]" value="${productData ? (productData.prod_subcategory || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. Chola Style">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Pieces Count</label>
                <input type="number" min="0" name="sp_unit[]" value="${productData ? (productData.prod_unit || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 10" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">One Piece Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_piece_rate[]" value="${productData ? (productData.prod_piece_rate || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Total Purchase Price (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0" name="sp_purchase_price[]" value="${productData ? productData.prod_purchase_price : ''}" required class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-lg px-2 py-1.5 text-xs outline-none" readonly>
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
                <input type="text" name="sp_barcode[]" value="${productData && productData.barcode ? productData.barcode : generateSupplierBarcode()}" class="w-full bg-slate-100 border border-slate-200 rounded-lg px-2 py-1.5 text-xs text-slate-500 font-mono outline-none" readonly>
            </div>
            <input type="hidden" name="sp_product_id[]" value="${productData ? productData.id : ''}">
        `;
        container.appendChild(div);
    }

    function generateSupplierBarcode() {
        return 'BAR-' + Math.floor(1000000000 + Math.random() * 9000000000);
    }

    function calculateSupplierPurchase(element) {
        const parentBlock = element.closest('.supplier-product-block');
        const unitInput = parentBlock.querySelector('input[name="sp_unit[]"]');
        const pieceRateInput = parentBlock.querySelector('input[name="sp_piece_rate[]"]');
        const purchaseInput = parentBlock.querySelector('input[name="sp_purchase_price[]"]');
        const sellingInput = parentBlock.querySelector('input[name="sp_selling_price[]"]');

        const units = parseFloat(unitInput.value) || 0;
        const pieceRate = parseFloat(pieceRateInput.value) || 0;
        
        if (units > 0 && pieceRate >= 0) {
            const purchasePrice = units * pieceRate;
            purchaseInput.value = purchasePrice.toFixed(2);
            
            // Auto-calculate selling price with default 60% margin based on one piece rate
            const sellingPrice = pieceRate + (pieceRate * 0.60);
            if (sellingInput) {
                sellingInput.value = sellingPrice.toFixed(2);
            }
        } else {
            purchaseInput.value = '';
            if (sellingInput) sellingInput.value = '';
        }
        
        calculateTotalSupplierPurchase();
    }

    function calculateTotalSupplierPurchase() {
        const purchaseInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
        let total = 0;
        purchaseInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalAmountInput = document.getElementById('total_amount_purchased');
        if (totalAmountInput) {
            totalAmountInput.value = total > 0 ? total.toFixed(2) : '';
        }
        
        // Sync balance when total purchase changes
        calculateTotalPaid();
    }

    function removeSupplierProductField(btn) {
        btn.parentElement.remove();
        calculateTotalSupplierPurchase();
    }

    function togglePaymentAmount(checkbox, inputId) {
        const input = document.getElementById(inputId);
        if (checkbox.checked) {
            input.classList.remove('hidden');
        } else {
            input.classList.add('hidden');
            input.value = '';
        }
        calculateTotalPaid();
    }

    function calculateTotalPaid() {
        const cash = parseFloat(document.getElementById('payment_amount_cash').value) || 0;
        const upi = parseFloat(document.getElementById('payment_amount_upi').value) || 0;
        const bank = parseFloat(document.getElementById('payment_amount_bank').value) || 0;
        const credit = parseFloat(document.getElementById('payment_amount_credit').value) || 0;
        
        const totalPaid = cash + upi + bank + credit;
        const totalPaidInput = document.getElementById('total_payment_entered');
        
        if (totalPaidInput) {
            totalPaidInput.value = totalPaid > 0 ? totalPaid.toFixed(2) : '';
        }

        // Calculate Balance
        const totalAmountPurchasedInput = document.getElementById('total_amount_purchased');
        const balanceInput = document.getElementById('balance_amount');
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


    function handleSupplierSubmit(event) {
        event.preventDefault();
        clearErrors();
        let isValid = true;
        
        const formData = new FormData(event.target);
        
        // Build supplier object
        const data = {
            supplier_name: formData.get('supplier_name'),
            mobile_number: formData.get('mobile_number'),
            alt_mobile_number: formData.get('alt_mobile_number'),
            email: formData.get('email'),
            address: formData.get('address'),
            city: formData.get('city'),
            state: formData.get('state'),
            pincode: formData.get('pincode'),
            gst_number: formData.get('gst_number'),
            pan_number: formData.get('pan_number'),
            payment_method: formData.get('payment_method'),
            total_amount_purchased: formData.get('total_amount_purchased'),
            notes: formData.get('notes'),
            status: formData.get('status')
        };

        // 1. Validate Required Fields for Supplier
        const requiredFields = ['supplier_name', 'mobile_number', 'address'];
        requiredFields.forEach(field => {
            if (!data[field] || data[field].trim() === '') {
                showError(field, 'Required');
                isValid = false;
            }
        });

        // Add to array
        let supplierId;
        if (currentEditId) {
            supplierId = currentEditId;
            const index = suppliers.findIndex(s => s.id === currentEditId);
            if (index > -1) {
                data.barcode = suppliers[index].barcode || Math.floor(100000 + Math.random() * 900000).toString();
                suppliers[index] = { ...data, id: currentEditId };
            }
        } else {
            supplierId = Date.now();
            data.id = supplierId;
            data.barcode = Math.floor(100000 + Math.random() * 900000).toString();
            suppliers.push(data);
        }

        // Process products
        const sp_names = formData.getAll('sp_product_name[]');
        const sp_cats = formData.getAll('sp_product_category[]');
        const sp_units = formData.getAll('sp_unit[]');
        const sp_piece_rates = formData.getAll('sp_piece_rate[]');
        const sp_purchases = formData.getAll('sp_purchase_price[]');
        const sp_sells = formData.getAll('sp_selling_price[]');
        const sp_gsts = formData.getAll('sp_gst[]');

        const sp_barcodes = formData.getAll('sp_barcode[]');
        const sp_ids = formData.getAll('sp_product_id[]');
        
        // Remove old products linked to this supplier if editing (to replace with the new list)
        if (currentEditId) {
            products = products.filter(p => p.prod_supplier_id !== currentEditId);
        }

        // Add new product list
        for (let i = 0; i < sp_names.length; i++) {
            if (sp_names[i].trim() !== '') {
                let prodId = sp_ids[i] ? parseInt(sp_ids[i]) : Date.now() + i;
                products.push({
                    id: prodId,
                    prod_supplier_id: supplierId,
                    prod_name: sp_names[i],
                    prod_sku: 'SKU-' + Math.floor(Math.random() * 90000),
                    prod_barcode: sp_barcodes[i] || ('BAR-' + Math.floor(1000000000 + Math.random() * 9000000000)),
                    prod_category: sp_cats[i],
                    prod_unit: sp_units[i],
                    prod_piece_rate: sp_piece_rates[i],
                    prod_purchase_price: sp_purchases[i],
                    prod_selling_price: sp_sells[i],
                    prod_gst: sp_gsts[i],
                    prod_opening_stock: 0,
                    prod_current_stock: 0,
                    prod_status: 'Active'
                });
            }
        }
        
        showToast('success', currentEditId ? 'Supplier and associated products updated.' : 'Supplier and products saved.');
        
        closeSupplierModal();
        renderSupplierTable();
        renderProductTable();
        renderBarcodeTable();
    }

    function renderBarcodeTable() {
        const tbody = document.getElementById('barcode-table-body');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr id="barcode-empty-state-row">
                    <td colspan="5" class="py-12 text-center text-emerald-400">
                        <i class="fas fa-barcode text-3xl mb-3 block text-emerald-300"></i>
                        <p class="font-bold text-emerald-600 text-sm">No barcodes found</p>
                        <p class="text-xs mt-1">Add products or suppliers to generate barcodes.</p>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = '';
        products.forEach(p => {
            const supplier = suppliers.find(s => s.id === p.prod_supplier_id);
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/50 transition-colors';
            
            tr.innerHTML = `
                <td class="py-3 px-4 font-mono text-[13px] font-bold text-emerald-700 bg-emerald-50/30 tracking-wider">
                    ${p.prod_barcode || 'N/A'}
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${p.prod_name}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-[10px] font-bold border border-slate-200">
                        ${p.prod_category || 'N/A'}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-indigo-700">${supplier ? supplier.supplier_name : 'N/A'}</div>
                </td>
                <td class="py-3 px-4 text-right">
                    <button class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm flex items-center gap-2 ml-auto">
                        <i class="fas fa-print"></i> Print
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderSupplierTable() {
        const tbody = document.getElementById('supplier-table-body');
        if (suppliers.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-slate-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No suppliers found</p>
                        <p class="text-xs mt-1">Click "Add Supplier" to create your first entry.</p>
                    </td>
                </tr>`;
            return;
        }
        
        tbody.innerHTML = '';
        
        suppliers.forEach(s => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            
            // Fetch products for this supplier
            const supplierProducts = products.filter(p => p.prod_supplier_id === s.id);
            
            tr.innerHTML = `
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${s.supplier_name}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5"><i class="fas fa-envelope text-slate-400 mr-1"></i>${s.email || 'N/A'}</div>
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-700">${s.mobile_number}</div>
                </td>
                <td class="py-3 px-4 font-mono text-[12px] font-bold text-indigo-700 bg-indigo-50/50 rounded-lg text-center tracking-wider">
                    ${s.barcode || 'N/A'}
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-indigo-700 text-xs">${supplierProducts.length} Products</div>
                </td>
                <td class="py-3 px-4 font-mono text-[11px] text-slate-600 font-semibold">${s.gst_number || 'N/A'}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${s.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${s.status || 'Active'}
                    </span>
                </td>
                <td class="py-3 px-4 text-right space-x-1">
                    <button onclick="viewSupplier(${s.id})" title="View" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-indigo-600 text-xs transition-colors"><i class="fas fa-eye"></i></button>
                    <button onclick="editSupplier(${s.id})" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-600 text-xs transition-colors"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteSupplier(${s.id})" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs transition-colors"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function deleteSupplier(id) {
        if (confirm('Are you sure you want to delete this supplier? All associated products will also be removed.')) {
            suppliers = suppliers.filter(s => s.id !== id);
            products = products.filter(p => p.prod_supplier_id !== id);
            renderSupplierTable();
            renderProductTable();
        }
    }

    function closeSupplierViewModal() {
        document.getElementById('supplier-view-modal').classList.add('hidden');
    }

    function viewSupplier(id) {
        const s = suppliers.find(sup => sup.id === id);
        if (!s) return;
        
        const supplierProducts = products.filter(p => p.prod_supplier_id === id);
        
        let productsHtml = '';
        if(supplierProducts.length > 0) {
            productsHtml = supplierProducts.map(p => `
                <div class="flex items-center justify-between border-b border-slate-100 last:border-0 py-2">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">${p.prod_name}</p>
                        <p class="text-[10px] text-slate-500">${p.prod_category} | ${p.prod_barcode || p.prod_sku}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-emerald-600 text-sm">₹${parseFloat(p.prod_purchase_price).toFixed(2)}</p>
                        <p class="text-[10px] text-slate-500">${p.prod_unit} units</p>
                    </div>
                </div>
            `).join('');
        } else {
            productsHtml = '<p class="text-sm text-slate-500 italic py-2">No products linked.</p>';
        }

        const html = `
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Supplier Name</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${s.supplier_name}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mobile Number</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${s.mobile_number}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${s.email || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Alt Mobile</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${s.alt_mobile_number || 'N/A'}</p>
                    </div>
                </div>
                
                <!-- Tax & Address Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST Number</p>
                        <p class="font-mono text-indigo-700 text-sm font-semibold mt-0.5">${s.gst_number || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PAN Number</p>
                        <p class="font-mono text-indigo-700 text-sm font-semibold mt-0.5">${s.pan_number || 'N/A'}</p>
                    </div>
                    <div class="col-span-2 mt-2 border-t border-slate-200/50 pt-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Address</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${s.address || 'N/A'}</p>
                        <p class="text-xs text-slate-600">${s.city || ''} ${s.state ? ', ' + s.state : ''} ${s.pincode ? '- ' + s.pincode : ''}</p>
                    </div>
                </div>

                <!-- Products Supplied List -->
                <div>
                    <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 border-b border-slate-200 pb-2">Products Supplied</h5>
                    <div class="bg-white rounded-xl border border-slate-200 p-3 max-h-[200px] overflow-y-auto">
                        ${productsHtml}
                    </div>
                </div>
            </div>
        `;
        document.getElementById('supplier-view-content').innerHTML = html;
        document.getElementById('supplier-view-modal').classList.remove('hidden');
    }

    function editSupplier(id) {
        const s = suppliers.find(sup => sup.id === id);
        if (!s) return;
        
        currentEditId = id;
        clearErrors();
        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Supplier';
        
        const form = document.getElementById('supplier-form');
        
        // Load basic supplier fields
        Object.keys(s).forEach(key => {
            const input = form.elements[key];
            if (input && input.name && !input.name.includes('[]')) input.value = s[key];
        });
        
        // Load associated products
        document.getElementById('supplier-products-container').innerHTML = '';
        const supplierProducts = products.filter(p => p.prod_supplier_id === id);
        
        if (supplierProducts.length > 0) {
            supplierProducts.forEach(p => addSupplierProductField(p));
        } else {
            addSupplierProductField();
        }
        
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    // --- PRODUCT MANAGEMENT LOGIC ---
    let products = [];
    let currentProductEditId = null;

    function openProductModal() {
        document.getElementById('product-form').reset();
        clearProductErrors();
        currentProductEditId = null;
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-box-open text-amber-600"></i> Add Products Detail';
        
        // Populate supplier dropdown
        const supplierSelect = document.getElementById('prod_supplier_id');
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.supplier_name;
            supplierSelect.appendChild(opt);
        });

        document.getElementById('product-products-container').innerHTML = '';
        addProductProductField();

        toggleProductOffer();
        document.getElementById('product-modal').classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
    }

    function closeProductViewModal() {
        document.getElementById('product-view-modal').classList.add('hidden');
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

    function handleProductSubmit(event) {
        event.preventDefault();
        clearProductErrors();
        
        const formData = new FormData(event.target);
        const prod_supplier_id = formData.get('prod_supplier_id');
        const prod_tax_inclusive = formData.get('prod_tax_inclusive');
        const prod_offer_available = formData.get('prod_offer_available');
        const prod_offer_type = formData.get('prod_offer_type');
        const prod_offer_amount = formData.get('prod_offer_amount');

        const ap_names = formData.getAll('ap_product_name[]');
        const ap_cats = formData.getAll('ap_product_category[]');
        const ap_subcats = formData.getAll('ap_product_subcategory[]');
        const ap_units = formData.getAll('ap_unit[]');
        const ap_piece_rates = formData.getAll('ap_piece_rate[]');
        const ap_purchases = formData.getAll('ap_purchase_price[]');
        const ap_sells = formData.getAll('ap_selling_price[]');
        const ap_gsts = formData.getAll('ap_gst[]');
        const ap_barcodes = formData.getAll('ap_barcode[]');
        const ap_ids = formData.getAll('ap_product_id[]');
        
        for (let i = 0; i < ap_names.length; i++) {
            if (ap_names[i].trim() !== '') {
                let prodId = ap_ids[i] ? parseInt(ap_ids[i]) : Date.now() + i;
                
                let data = {
                    id: prodId,
                    prod_supplier_id: prod_supplier_id,
                    prod_name: ap_names[i],
                    prod_sku: 'SKU-' + Math.floor(Math.random() * 90000),
                    prod_barcode: ap_barcodes[i] || ('BAR-' + Math.floor(1000000000 + Math.random() * 9000000000)),
                    prod_category: ap_cats[i],
                    prod_subcategory: ap_subcats[i],
                    prod_unit: ap_units[i],
                    prod_piece_rate: ap_piece_rates[i],
                    prod_purchase_price: ap_purchases[i],
                    prod_selling_price: ap_sells[i],
                    prod_gst: ap_gsts[i],
                    prod_tax_inclusive: prod_tax_inclusive,
                    prod_offer_available: prod_offer_available,
                    prod_offer_type: prod_offer_type,
                    prod_offer_amount: prod_offer_amount,
                    prod_opening_stock: 0,
                    prod_current_stock: 0,
                    prod_status: 'Active'
                };
                
                if (currentProductEditId) {
                    const index = products.findIndex(p => p.id === currentProductEditId);
                    if (index > -1) {
                        data.prod_barcode = products[index].prod_barcode || data.prod_barcode;
                        data.prod_sku = products[index].prod_sku || data.prod_sku;
                        products[index] = data;
                    }
                } else {
                    products.push(data);
                }
            }
        }
        
        showToast('success', currentProductEditId ? 'Product updated successfully.' : 'Product(s) added successfully.');
        
        closeProductModal();
        renderProductTable();
        renderSupplierTable(); // Update the supplier product counts
    }

    function renderProductTable() {
        const tbody = document.getElementById('product-table-body');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr id="product-empty-state-row">
                    <td colspan="10" class="py-12 text-center text-amber-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-amber-300"></i>
                        <p class="font-bold text-amber-600 text-sm">No products found</p>
                        <p class="text-xs mt-1">Click "Add Products Detail" to create your first entry.</p>
                    </td>
                </tr>`;
            return;
        }
        
        tbody.innerHTML = '';
        
        products.forEach(p => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-amber-50/30 transition-colors';
            
            let offerDisplay = 'N/A';
            if (p.prod_offer_available === 'Yes' && p.prod_offer_amount) {
                offerDisplay = p.prod_offer_type === '%' ? `${p.prod_offer_amount}%` : `₹${parseFloat(p.prod_offer_amount).toFixed(2)}`;
            }
            
            let supplierName = 'Unknown';
            const sup = suppliers.find(s => s.id == p.prod_supplier_id);
            if(sup) supplierName = sup.supplier_name;

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${p.prod_name}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5"><i class="fas fa-barcode text-slate-400 mr-1"></i>${p.prod_sku} / ${p.prod_barcode}</div>
                </td>
                <td class="py-3 px-4">
                    <div class="text-[10px] text-amber-700 bg-amber-50 inline-block px-2 py-0.5 mt-0.5 rounded-md border border-amber-100 font-medium">${p.prod_category}</div>
                </td>
                <td class="py-3 px-4 font-bold text-slate-700 text-xs">${supplierName}</td>
                <td class="py-3 px-4 font-mono">
                    <div class="text-slate-900 font-bold text-xs">P: ₹${parseFloat(p.prod_purchase_price).toFixed(2)}</div>
                    <div class="text-[10px] text-emerald-600 font-bold">S: ₹${parseFloat(p.prod_selling_price).toFixed(2)}</div>
                </td>
                <td class="py-3 px-4 font-mono text-[11px] text-slate-600 font-semibold">${p.prod_gst}%</td>
                <td class="py-3 px-4 font-bold ${p.prod_current_stock <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-700'} text-xs">${p.prod_current_stock} ${p.prod_unit}</td>
                <td class="py-3 px-4 font-bold text-fuchsia-600 text-xs">${offerDisplay}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${p.prod_status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${p.prod_status}
                    </span>
                </td>
                <td class="py-3 px-4 text-right space-x-1">
                    <button onclick="viewProduct(${p.id})" title="View" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-600 text-xs transition-colors"><i class="fas fa-eye"></i></button>
                    <button onclick="editProduct(${p.id})" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-indigo-600 text-xs transition-colors"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteProduct(${p.id})" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs transition-colors"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            products = products.filter(p => p.id !== id);
            renderProductTable();
            renderSupplierTable(); // Update the supplier product counts
        }
    }

    function viewProduct(id) {
        const p = products.find(prod => prod.id === id);
        if (!p) return;
        
        let supplierName = 'Unknown';
        const sup = suppliers.find(s => s.id == p.prod_supplier_id);
        if(sup) supplierName = sup.supplier_name;

        let finalPrice = parseFloat(p.prod_selling_price);
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
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_sku} / ${p.prod_barcode || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Category / Brand</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_category} ${p.prod_brand ? '- ' + p.prod_brand : ''}</p>
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
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup ? sup.mobile_number : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup && sup.email ? sup.email : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST Number</p>
                        <p class="font-mono text-slate-700 text-xs font-semibold mt-0.5">${sup && sup.gst_number ? sup.gst_number : 'N/A'}</p>
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
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${parseFloat(p.prod_purchase_price).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Selling Price</p>
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${parseFloat(p.prod_selling_price).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Final Price (Offer)</p>
                        <p class="font-bold text-indigo-600 text-sm mt-0.5">₹${finalPrice.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_gst}% (${p.prod_tax_inclusive === 'Yes' ? 'Inclusive' : 'Exclusive'})</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">HSN/SAC Code</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_hsn || 'N/A'}</p>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Current Stock</p>
                        <p class="font-bold ${p.prod_current_stock <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-800'} text-lg mt-0.5">${p.prod_current_stock} ${p.prod_unit}</p>
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
        document.getElementById('product-view-modal').classList.remove('hidden');
    }

    function editProduct(id) {
        const p = products.find(prod => prod.id === id);
        if (!p) return;
        
        currentProductEditId = id;
        clearProductErrors();
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Product';
        
        const supplierSelect = document.getElementById('prod_supplier_id');
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.supplier_name;
            supplierSelect.appendChild(opt);
        });

        const form = document.getElementById('product-form');
        if(form.elements['prod_supplier_id']) form.elements['prod_supplier_id'].value = p.prod_supplier_id || '';
        if(form.elements['prod_tax_inclusive']) form.elements['prod_tax_inclusive'].value = p.prod_tax_inclusive || 'No';
        if(form.elements['prod_offer_available']) form.elements['prod_offer_available'].value = p.prod_offer_available || 'No';
        if(form.elements['prod_offer_type']) form.elements['prod_offer_type'].value = p.prod_offer_type || '%';
        if(form.elements['prod_offer_amount']) form.elements['prod_offer_amount'].value = p.prod_offer_amount || '';
        
        document.getElementById('product-products-container').innerHTML = '';
        addProductProductField(p);
        
        toggleProductOffer();
        document.getElementById('product-modal').classList.remove('hidden');
    }

    // Dynamic Product Methods for Add Product form
    function addProductProductField(productData = null) {
        const container = document.getElementById('product-products-container');
        const div = document.createElement('div');
        div.className = 'product-product-block relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 border border-amber-200 rounded-lg bg-white shadow-sm';
        div.innerHTML = `
            <button type="button" onclick="removeProductProductField(this)" class="absolute -top-2 -right-2 bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors shadow-sm">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="ap_product_name[]" value="${productData ? productData.prod_name : ''}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <select name="ap_product_category[]" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">Select</option>
                    <option value="Statues" ${productData && productData.prod_category === 'Statues' ? 'selected' : ''}>Statues</option>
                    <option value="Accessories" ${productData && productData.prod_category === 'Accessories' ? 'selected' : ''}>Accessories</option>
                    <option value="Metals" ${productData && productData.prod_category === 'Metals' ? 'selected' : ''}>Metals</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Subcategory</label>
                <input type="text" name="ap_product_subcategory[]" value="${productData ? (productData.prod_subcategory || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. Chola Style">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Pieces Count</label>
                <input type="number" min="0" name="ap_unit[]" value="${productData ? (productData.prod_unit || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 10" oninput="calculateProductPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">One Piece Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="ap_piece_rate[]" value="${productData ? (productData.prod_piece_rate || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="calculateProductPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Total Purchase Price (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0" name="ap_purchase_price[]" value="${productData ? productData.prod_purchase_price : ''}" required class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-lg px-2 py-1.5 text-xs outline-none" readonly>
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
                <input type="text" name="ap_barcode[]" value="${productData && productData.prod_barcode ? productData.prod_barcode : generateSupplierBarcode()}" class="w-full bg-slate-100 border border-slate-200 rounded-lg px-2 py-1.5 text-xs text-slate-500 font-mono outline-none" readonly>
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

    function calculateProductPurchase(element) {
        const parentBlock = element.closest('.product-product-block');
        const unitInput = parentBlock.querySelector('input[name="ap_unit[]"]');
        const pieceRateInput = parentBlock.querySelector('input[name="ap_piece_rate[]"]');
        const purchaseInput = parentBlock.querySelector('input[name="ap_purchase_price[]"]');
        const sellingInput = parentBlock.querySelector('input[name="ap_selling_price[]"]');

        const units = parseFloat(unitInput.value) || 0;
        const pieceRate = parseFloat(pieceRateInput.value) || 0;
        
        if (units > 0 && pieceRate >= 0) {
            const purchasePrice = units * pieceRate;
            purchaseInput.value = purchasePrice.toFixed(2);
            
            // Auto-calculate selling price with default 60% margin based on one piece rate
            const sellingPrice = pieceRate + (pieceRate * 0.60);
            if (sellingInput) {
                sellingInput.value = sellingPrice.toFixed(2);
            }
        } else {
            purchaseInput.value = '';
            if (sellingInput) sellingInput.value = '';
        }
        
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
</script>
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
