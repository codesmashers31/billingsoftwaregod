<!-- POS Billing Terminal Layout (Clean Professional Light Theme) -->
<div class="h-[calc(100vh-5.5rem)] flex flex-col lg:flex-row gap-4 overflow-hidden">
    <!-- LEFT SIDE: Catalog, Search, Barcode, Categories, Product Cards (65% width) -->
    <div class="flex-1 flex flex-col bg-white border border-slate-200 rounded-2xl p-4 shadow-sm min-w-0 overflow-hidden">
        <!-- Top Toolbar: Search & Barcode Scan -->
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 shrink-0">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-600 text-sm">
                    <i class="fas fa-barcode"></i>
                </div>
                <input type="text" id="pos-search-input" placeholder="Scan Barcode or Search Idol (Name, Code, Deity, Material) [F2]..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl pl-10 pr-4 py-2.5 text-xs font-medium text-slate-900 placeholder-slate-400 transition-all outline-none">
            </div>

            <!-- Suspended / Held Bills Trigger -->
            <button onclick="openHeldBillsModal()" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition-colors shrink-0">
                <i class="fas fa-pause-circle text-amber-600 text-sm"></i>
                <span>Held Bills</span>
                <span id="held-bills-badge" class="w-5 h-5 rounded-full bg-amber-600 text-white text-[10px] font-black flex items-center justify-center">0</span>
            </button>
        </div>

        <!-- Custom Category Dropdown Navigation -->
        <div class="py-3 flex items-center shrink-0 border-b border-slate-100 relative z-40">
            <div class="relative w-full sm:w-2/3 lg:w-1/2" id="custom-category-dropdown-container">
                
                <!-- Dropdown Button -->
                <button type="button" onclick="toggleCategoryDropdown()" 
                        class="relative w-full flex items-center bg-white border border-amber-500 shadow-md shadow-amber-500/20 hover:shadow-lg hover:shadow-amber-500/40 hover:border-amber-600 rounded-xl pl-10 pr-10 py-2.5 text-xs font-bold text-slate-800 cursor-pointer outline-none transition-all">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-600">
                        <i class="fas fa-layer-group text-sm"></i>
                    </span>
                    
                    <span id="custom-category-selected-text" class="block truncate">All Idols & Items Catalog</span>
                    
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-amber-600 transition-transform duration-200" id="custom-category-chevron">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </button>

                <!-- Dropdown Menu List -->
                <div id="custom-category-menu" class="hidden absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl shadow-slate-200/50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top">
                    <div class="max-h-60 overflow-y-auto py-1 custom-scrollbar">
                        
                        <!-- Option: All -->
                        <div onclick='selectCustomCategory("all", "All Idols & Items Catalog")' 
                             class="flex items-center px-4 py-3 text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-700 cursor-pointer transition-colors border-b border-slate-50">
                            <i class="fas fa-box-open w-6 text-amber-500 text-center"></i>
                            <span>All Idols & Items Catalog</span>
                        </div>
                        
                        <!-- Dynamic Categories -->
                        <?php foreach ($categories as $cat): ?>
                        <div onclick='selectCustomCategory(<?= $cat['id'] ?>, <?= htmlspecialchars(json_encode($cat['name']), ENT_QUOTES) ?>)' 
                             class="flex items-center px-4 py-3 text-xs font-semibold text-slate-600 hover:bg-amber-50 hover:text-amber-700 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                            <i class="fas fa-tag w-6 text-slate-400 text-center group-hover:text-amber-500 transition-colors"></i>
                            <span><?= sanitize($cat['name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                    </div>
                </div>
            </div>
            
            <div class="ml-4 hidden sm:block">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">
                    <i class="fas fa-filter text-slate-300 mr-1"></i> Filter Category
                </span>
            </div>
        </div>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f8fafc;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            @keyframes yellowWarningBlink {
                0%, 100% {
                    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
                    border-color: #f59e0b;
                    background-color: #fffbeb;
                }
                50% {
                    box-shadow: 0 0 12px 2px rgba(245, 158, 11, 0.7);
                    border-color: #d97706;
                    background-color: #fef3c7;
                }
            }
            .yellow-blink-warning {
                animation: yellowWarningBlink 1.4s ease-in-out infinite;
            }
            @keyframes redOutStockPulse {
                0%, 100% {
                    border-color: #ef4444;
                    background-color: #fef2f2;
                }
                50% {
                    border-color: #dc2626;
                    background-color: #fee2e2;
                }
            }
            .red-out-of-stock-row {
                animation: redOutStockPulse 2s ease-in-out infinite;
            }
        </style>

        <script>
            function toggleCategoryDropdown() {
                const menu = document.getElementById('custom-category-menu');
                const chevron = document.getElementById('custom-category-chevron');
                
                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    setTimeout(() => {
                        menu.classList.remove('opacity-0', 'scale-95');
                        menu.classList.add('opacity-100', 'scale-100');
                        chevron.classList.add('rotate-180');
                    }, 10);
                } else {
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95');
                    chevron.classList.remove('rotate-180');
                    setTimeout(() => {
                        menu.classList.add('hidden');
                    }, 200);
                }
            }

            function selectCustomCategory(id, name) {
                document.getElementById('custom-category-selected-text').textContent = name;
                toggleCategoryDropdown();
                
                const catId = id === 'all' ? null : id;
                if (typeof filterCategory === 'function') {
                    filterCategory(catId, null);
                }
            }

            document.addEventListener('click', function(event) {
                const container = document.getElementById('custom-category-dropdown-container');
                const menu = document.getElementById('custom-category-menu');
                if (container && !container.contains(event.target) && menu && !menu.classList.contains('hidden')) {
                    toggleCategoryDropdown();
                }
            });
        </script>

        <!-- Product Grid (Scrollable) -->
        <div id="pos-product-grid" class="flex-1 overflow-y-auto pt-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 pr-1 content-start pb-4">
            <?php foreach ($products as $p): ?>
                <?php 
                    $stock = (int)$p['current_stock'];
                    $isOutOfStock = $stock <= 0; 
                ?>
                <div data-product-id="<?= $p['id'] ?>"
                     data-max-stock="<?= $stock ?>"
                     data-name="<?= escapeHtml($p['name']) ?>"
                     data-price="<?= $p['selling_price'] ?>"
                     data-discount="<?= $p['discount_percent'] ?? 0 ?>"
                     data-gst="<?= $p['gst_percent'] ?? 12 ?>"
                     data-code="<?= escapeHtml($p['code'] ?: $p['sku']) ?>"
                     data-disabled="false"
                     onclick="handleProductCardClick(this)"
                     class="pos-product-card h-[144px] <?= $isOutOfStock ? 'pos-card-out-of-stock bg-red-50 border-2 border-red-500 cursor-pointer shadow-xs' : 'bg-white border border-slate-100 hover:border-amber-300 hover:shadow-md cursor-pointer' ?> shadow-sm rounded-2xl p-3.5 flex flex-col group transition-all select-none">
                    <div>
                        <div class="flex items-center justify-between gap-1">
                            <span class="pos-code-badge text-[10px] font-bold <?= $isOutOfStock ? 'text-red-800 bg-red-100 border-red-300' : 'text-amber-700 bg-[#fff8ef] border-amber-200' ?> px-2 py-1 rounded-md border whitespace-nowrap truncate"><?= $p['code'] ?: $p['sku'] ?></span>
                            <?php if ($isOutOfStock): ?>
                                <span class="pos-stock-badge bg-red-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full border border-red-700 shadow-xs whitespace-nowrap shrink-0 flex items-center gap-1">
                                    <i class="fas fa-ban text-[9px]"></i> Out of Stock
                                </span>
                            <?php else: ?>
                                <span class="pos-stock-badge bg-[#f0fdf4] text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-[#bbf7d0] whitespace-nowrap shrink-0">
                                    <?= $stock ?> in stock
                                </span>
                            <?php endif; ?>
                        </div>
                        <h4 class="pos-prod-title text-[13px] font-extrabold <?= $isOutOfStock ? 'text-red-950' : 'text-slate-900 group-hover:text-amber-700' ?> transition-colors line-clamp-2 h-[36px] leading-snug mt-2">
                            <?= sanitize($p['name']) ?>
                        </h4>
                    </div>
                    <div class="mt-auto pt-3 flex items-center justify-between">
                        <div class="flex items-baseline">
                            <span class="text-[10px] text-slate-400 font-medium">Rate:</span>
                            <span class="pos-price-val text-[14px] font-black <?= $isOutOfStock ? 'text-red-900' : 'text-slate-900' ?> font-mono ml-1"><?= formatCurrency($p['selling_price']) ?></span>
                        </div>
                        <button type="button" class="pos-add-btn w-7 h-7 rounded-lg <?= $isOutOfStock ? 'bg-red-600 text-white hover:bg-red-700 cursor-pointer shadow-sm' : 'bg-[#fef3c7] text-amber-700 group-hover:bg-amber-500 group-hover:text-white cursor-pointer' ?> flex items-center justify-center transition-all shadow-sm">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RIGHT SIDE: Reactive Cart & Fast Checkout (35% width) -->
    <div class="w-full lg:w-[420px] xl:w-[450px] flex flex-col bg-white border border-slate-200 rounded-2xl p-4 shadow-sm shrink-0 overflow-hidden">
        <!-- Customer Live Direct Filling Banner (Mandatory for Billing) -->
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl mb-3 shrink-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 overflow-hidden">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <span class="font-extrabold text-slate-800 text-xs tracking-tight flex items-center gap-1">
                        <span>Customer Details</span>
                        <span class="text-rose-600 text-[10px] font-black uppercase tracking-wider">(Required for Bill)</span>
                    </span>
                </div>
                <button type="button" onclick="openCustomerModal()" class="px-2.5 py-1 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 shadow-xs">
                    <i class="fas fa-search text-[10px] text-amber-600"></i> Search [F4]
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Mobile No <span class="text-rose-600 font-black">*</span>
                    </label>
                    <input type="tel" id="pos-customer-mobile" value="" placeholder="10-digit Mobile Number *" 
                           maxlength="10"
                           oninput="onCustomerInputsChanged()"
                           class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-slate-900 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Customer Name <span class="text-rose-600 font-black">*</span>
                    </label>
                    <input type="text" id="pos-customer-name" value="" placeholder="Customer Full Name *" 
                           oninput="onCustomerInputsChanged()"
                           class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Cart Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider shrink-0">
            <span>Billing Cart (<span id="cart-items-count" class="text-amber-700">0</span>)</span>
            <button onclick="clearCart()" class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold lowercase">
                <i class="fas fa-trash-alt mr-1"></i> clear cart
            </button>
        </div>

        <!-- Cart Items Scrollable List -->
        <div id="pos-cart-items" class="flex-1 overflow-y-auto py-2 pr-1 space-y-2">
            <!-- Rendered by pos.js -->
        </div>

        <!-- Totals & Checkout Bottom Panel -->
        <div class="pt-3 border-t border-slate-200 bg-white shrink-0 space-y-2 text-xs">
            <!-- Overall Discount Input -->
            <div class="flex items-center justify-between text-slate-600 font-medium">
                <span>Overall Discount (₹):</span>
                <input type="text" inputmode="decimal" id="pos-overall-discount" placeholder="0.00" 
                       oninput="renderCart()"
                       class="w-24 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-900 font-mono text-right focus:border-amber-500 outline-none">
            </div>

            <!-- Dynamic Bill GST Tax Input -->
            <div class="flex items-center justify-between text-slate-600 font-medium">
                <span class="flex items-center gap-1.5">
                    <span>GST Tax Rate:</span>
                    <span class="text-[9px] uppercase tracking-wider font-extrabold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Dynamic</span>
                </span>
                <div class="flex items-center gap-1.5">
                    <select id="pos-tax-preset" onchange="changeBillGstPreset(this.value)" 
                            class="bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-800 font-semibold outline-none focus:border-amber-500 cursor-pointer">
                        <option value="item">As per Item</option>
                        <option value="0">0% (Nil)</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18">18%</option>
                        <option value="20">20%</option>
                        <option value="28">28%</option>
                        <option value="custom">Custom %</option>
                    </select>
                    <input type="number" id="pos-custom-tax-rate" min="0" max="100" step="0.5" placeholder="%" 
                           oninput="onCustomGstInput(this.value)"
                           class="hidden w-14 bg-slate-50 border border-amber-400 rounded-lg px-1.5 py-1 text-xs text-slate-900 font-mono font-bold text-right outline-none">
                </div>
            </div>

            <!-- Calculation Lines -->
            <div class="space-y-1 text-slate-600 text-xs font-mono">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal:</span>
                    <span id="summary-subtotal" class="font-bold text-slate-800">₹0.00</span>
                </div>
                <div class="flex justify-between text-rose-600 font-semibold">
                    <span class="text-slate-500">Discount:</span>
                    <span id="summary-discount">-₹0.00</span>
                </div>
                <div class="flex justify-between text-blue-600 font-semibold">
                    <span class="text-slate-500" id="summary-tax-label">GST Tax (12%):</span>
                    <span id="summary-tax">₹0.00</span>
                </div>
                <div class="flex justify-between text-slate-400 text-[11px]">
                    <span>Round-off:</span>
                    <span id="summary-roundoff">₹0.00</span>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">Total Amount</span>
                    <span class="text-[10px] text-amber-800 font-semibold">Includes Taxes</span>
                </div>
                <span id="summary-grandtotal" class="text-2xl font-black font-mono text-slate-900">₹0.00</span>
            </div>

            <!-- Action Buttons: Hold / Pay -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <button onclick="holdCurrentBill()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors border border-slate-200">
                    <i class="fas fa-pause text-amber-600"></i> Hold [F9]
                </button>
                <button id="btn-pos-pay" onclick="openPaymentModal()" class="col-span-2 gold-btn font-extrabold py-2.5 rounded-xl text-sm shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Complete Bill [F8]
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Customer Selection / Quick Add -->
<div id="pos-customer-modal" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs border border-amber-200">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Select or Add Customer</h3>
            </div>
            <button onclick="closeModal('pos-customer-modal')" class="text-slate-400 hover:text-slate-700 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4">
            <!-- Search Customer Box -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="cust-search-input" placeholder="Type customer name or mobile..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl pl-8 pr-8 py-2 text-xs text-slate-900 outline-none transition-all">
                <button type="button" id="btn-clear-cust-search" onclick="clearCustomerSearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times-circle text-xs"></i>
                </button>
            </div>

            <!-- Customer Search Results List -->
            <div>
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-0.5">
                    <span>Available Customers</span>
                    <span id="cust-list-status" class="text-slate-400 font-normal">All</span>
                </div>
                <div id="pos-customer-list" class="max-h-56 overflow-y-auto space-y-1.5 pr-0.5">
                    <!-- Loaded via JS -->
                </div>
            </div>

            <!-- Quick Add Customer Form -->
            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                        <i class="fas fa-user-plus text-amber-600"></i> Quick Register Customer
                    </h5>
                    <span class="text-[10px] text-slate-400">Instant Link to Bill</span>
                </div>
                <form id="pos-quick-customer-form" class="space-y-2 text-xs">
                    <?= csrf_field() ?>
                    <input type="text" id="pos-quick-cust-name" name="name" required placeholder="Customer Full Name" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <input type="tel" id="pos-quick-cust-mobile" name="mobile" required placeholder="Mobile Number (10 digits)" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:bg-white focus:border-amber-500 outline-none transition-all">
                    <button type="submit" id="btn-save-quick-cust" class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-check-circle"></i> Save & Select Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Multi-Payment Checkout -->
<div id="pos-payment-modal" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Complete Sales Payment</h3>
            <button onclick="closeModal('pos-payment-modal')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <div class="space-y-4 text-xs">
            <!-- Selected Customer Badge in Payment Modal -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl px-3.5 py-2.5 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 overflow-hidden">
                    <div class="w-6 h-6 rounded-lg bg-amber-200/70 text-amber-800 flex items-center justify-center text-[10px] shrink-0 font-bold">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block leading-tight">Customer</span>
                        <span id="modal-pay-customer-name" class="font-bold text-slate-900 truncate block">Walk-in Retail Customer</span>
                    </div>
                </div>
                <span id="modal-pay-customer-mobile" class="font-mono text-slate-700 text-xs font-semibold bg-white px-2 py-0.5 rounded border border-amber-200/60 shrink-0">9999999999</span>
            </div>

            <!-- Grand Total Display -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Invoice Net Payable</span>
                <h2 id="modal-pay-grandtotal" class="text-3xl font-black font-mono text-slate-900 mt-1">₹0.00</h2>
            </div>

            <!-- Select Payment Method: Cash and UPI Only -->
            <div>
                <label class="block font-bold text-slate-700 mb-2">Select Payment Method</label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Cash -->
                    <div class="p-3.5 bg-white border border-slate-200 hover:border-emerald-500 rounded-xl flex flex-col gap-2 text-slate-700 shadow-sm transition-all">
                        <label class="cursor-pointer flex items-center gap-2 select-none w-full">
                            <input type="checkbox" name="payment_methods[]" value="cash" id="pos_chk_cash" checked onchange="togglePosPaymentAmount(this, 'pos_payment_amount_cash')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-money-bill-wave text-emerald-600"></i> Cash Payment
                            </span>
                        </label>
                        <input type="number" step="0.01" min="0" name="pos_payment_amount_cash" id="pos_payment_amount_cash" oninput="calculatePosTotalPaidFromMethods()" placeholder="Amount (₹)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono font-bold text-slate-800">
                    </div>

                    <!-- UPI -->
                    <div class="p-3.5 bg-white border border-slate-200 hover:border-emerald-500 rounded-xl flex flex-col gap-2 text-slate-700 shadow-sm transition-all">
                        <label class="cursor-pointer flex items-center gap-2 select-none w-full">
                            <input type="checkbox" name="payment_methods[]" value="upi" id="pos_chk_upi" onchange="togglePosPaymentAmount(this, 'pos_payment_amount_upi')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-qrcode text-indigo-600"></i> UPI (Scan QR)
                            </span>
                        </label>
                        <input type="number" step="0.01" min="0" name="pos_payment_amount_upi" id="pos_payment_amount_upi" oninput="calculatePosTotalPaidFromMethods()" placeholder="Amount (₹)" class="hidden w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono font-bold text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Dynamic UPI QR Code Container -->
            <div id="dynamic-upi-container" class="hidden mt-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-center flex-col items-center justify-center">
                <p class="text-xs font-bold text-slate-700 mb-2">Scan QR to Pay: <span id="upi-exact-amount" class="text-amber-700 font-extrabold text-sm"></span></p>
                <div class="bg-white p-2 rounded-lg shadow-sm mb-2 inline-block">
                    <img id="dynamic-upi-qr" src="" alt="UPI QR Code" class="w-40 h-40 object-contain mx-auto">
                </div>
                <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider"><i class="fab fa-google-pay text-xs mr-1 text-slate-600"></i> <i class="fas fa-rupee-sign text-xs mr-1 text-slate-600"></i> UPI Payments</p>
            </div>
            
            <input type="hidden" id="payment-account-select" value="<?= !empty($accounts[0]['id']) ? $accounts[0]['id'] : 1 ?>">
            
            <!-- Total Amount Paid & Balance to Pay (matching Image 2) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Total Amount Paid (₹)</label>
                    <input type="number" step="0.01" min="0" id="modal-pay-amount" oninput="calculatePosBalance()" class="w-full bg-emerald-50/40 border border-emerald-400 text-emerald-900 font-bold rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-all font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Balance to Pay (₹)</label>
                    <input type="number" step="0.01" id="modal-balance-amount" readonly class="w-full bg-rose-50 border border-rose-200 text-rose-700 font-bold rounded-xl px-3 py-2 text-sm outline-none cursor-not-allowed font-mono" value="0.00">
                    <p class="text-[10px] text-slate-400 mt-1">Formula: Balance = Total Amount - Total Amount Paid</p>
                </div>
            </div>

            <!-- Receipt Delivery Option -->
            <div class="pt-1">
                <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-paper-plane text-amber-600 text-xs"></i>
                        <span>Bill Delivery Method (Customer Preference)</span>
                    </span>
                    <span class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">Select delivery</span>
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer border-2 border-slate-200 rounded-xl p-2.5 flex flex-col items-center justify-center gap-1.5 hover:border-amber-400 bg-white select-none transition-all has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:shadow-xs">
                        <input type="radio" name="pos_receipt_mode" value="thermal" checked class="hidden">
                        <i class="fas fa-receipt text-lg text-amber-600"></i>
                        <span class="text-xs font-bold text-slate-800">Thermal Print</span>
                    </label>
                    <label class="cursor-pointer border-2 border-slate-200 rounded-xl p-2.5 flex flex-col items-center justify-center gap-1.5 hover:border-emerald-400 bg-white select-none transition-all has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:shadow-xs">
                        <input type="radio" name="pos_receipt_mode" value="whatsapp" class="hidden">
                        <i class="fab fa-whatsapp text-xl text-emerald-600"></i>
                        <span class="text-xs font-bold text-slate-800">WhatsApp Bill</span>
                    </label>
                    <label class="cursor-pointer border-2 border-slate-200 rounded-xl p-2.5 flex flex-col items-center justify-center gap-1.5 hover:border-indigo-400 bg-white select-none transition-all has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:shadow-xs">
                        <input type="radio" name="pos_receipt_mode" value="both" class="hidden">
                        <i class="fas fa-print text-lg text-indigo-600"></i>
                        <span class="text-xs font-bold text-slate-800">Print + WhatsApp</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Invoice Notes</label>
                <input type="text" id="modal-pay-notes" placeholder="e.g. Consecrated with holy ash, velvet packaging included..." 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('pos-payment-modal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="button" id="btn-show-qr" class="hidden bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md items-center gap-2 transition-all">
                    <i class="fas fa-qrcode"></i> Show QR
                </button>
                <button type="button" onclick="processCheckout()" id="btn-done-qr" class="hidden bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md items-center gap-2 transition-all">
                    <i class="fas fa-check"></i> Done
                </button>
                <button type="button" onclick="processCheckout()" id="btn-confirm-checkout" class="gold-btn px-6 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Complete Transaction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Suspended Held Bills -->
<div id="pos-held-modal" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Suspended / Held Bills</h3>
            <button onclick="closeModal('pos-held-modal')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="held-bills-list" class="space-y-2 text-xs"></div>
    </div>
</div>

<!-- Modal: Transaction Completed & Print Prompt -->
<div id="pos-complete-modal" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-8 w-full max-w-md shadow-2xl relative text-center">
        <!-- Close (X) Button Top-Right -->
        <button type="button" onclick="closeModal('pos-complete-modal')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors" title="Close [Esc]">
            <i class="fas fa-times text-sm"></i>
        </button>

        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-4 border border-emerald-200">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900">Invoice Generated!</h3>
        <p class="text-xs text-slate-500 mt-1">Invoice Number: <span id="completed-invoice-no" class="text-amber-800 font-mono font-bold text-sm"></span></p>
        <div class="mt-2 py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl inline-flex items-center gap-2 text-xs font-mono text-slate-700">
            <span>Customer: <strong id="completed-customer-name" class="text-slate-900 font-bold">Walk-in</strong></span>
            <span class="text-slate-300">•</span>
            <span>Mobile: <strong id="completed-customer-mobile" class="text-amber-700 font-bold">9999999999</strong></span>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-6">
            <!-- Thermal 80mm Print -->
            <button id="btn-print-thermal" class="py-3.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex flex-col items-center justify-center gap-1.5 transition-all border border-slate-200 shadow-sm cursor-pointer hover:border-amber-400">
                <i class="fas fa-receipt text-xl text-amber-600"></i>
                <span>Thermal Print (80mm)</span>
            </button>
            
            <!-- WhatsApp Share Bill -->
            <button id="btn-share-whatsapp" class="py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex flex-col items-center justify-center gap-1.5 transition-all shadow-md shadow-emerald-600/20 cursor-pointer">
                <i class="fab fa-whatsapp text-2xl text-white"></i>
                <span>Share Bill on WhatsApp</span>
            </button>
        </div>

        <div class="mt-5 flex items-center gap-2">
            <button onclick="closeModal('pos-complete-modal')" class="flex-1 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition-colors">
                New Bill [F2]
            </button>
            <button id="btn-view-invoice" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                View Bill
            </button>
        </div>
    </div>
</div>

<script>
    const SYSTEM_UPI_ID = "<?= sanitize(getSetting('company_upi_id', 'sasthasasti@sbi')) ?>";
    const SYSTEM_COMPANY_NAME = "<?= sanitize(getSetting('company_name', 'Sastha Sasti')) ?>";
</script>

<!-- Load POS Controller JS -->
<script src="<?= asset('js/pos_v2.js?v=' . time()) ?>"></script>












