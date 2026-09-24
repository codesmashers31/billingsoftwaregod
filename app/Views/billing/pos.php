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

        <!-- Category Filter Pills -->
        <div class="py-2.5 flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0 border-b border-slate-100">
            <button onclick="filterCategory(null, this)" class="cat-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-amber-600 text-white shadow-sm shrink-0">
                All Idols & Items
            </button>
            <?php foreach ($categories as $cat): ?>
                <button onclick="filterCategory(<?= $cat['id'] ?>, this)" class="cat-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 shrink-0">
                    <?= sanitize($cat['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Product Grid (Scrollable) -->
        <div id="pos-product-grid" class="flex-1 overflow-y-auto pt-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 pr-1">
            <?php foreach ($products as $p): ?>
                <?php $isOutOfStock = $p['current_stock'] <= 0; ?>
                <div onclick="addToCartById(<?= $p['id'] ?>, '<?= escapeHtml($p['name']) ?>', <?= $p['selling_price'] ?>, <?= $p['discount_percent'] ?? 0 ?>, <?= $p['gst_percent'] ?? 12 ?>, <?= $p['current_stock'] ?>)" 
                     class="pos-product-card bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer group hover:bg-white hover:border-amber-400 <?= $isOutOfStock ? 'opacity-50 pointer-events-none' : '' ?>">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-mono text-amber-800 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200"><?= $p['code'] ?: $p['sku'] ?></span>
                            <?php if ($isOutOfStock): ?>
                                <span class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-200">Out of Stock</span>
                            <?php else: ?>
                                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200"><?= $p['current_stock'] ?> in stock</span>
                            <?php endif; ?>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 leading-snug">
                            <?= sanitize($p['name']) ?>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fas fa-om text-amber-600 text-[10px]"></i> <?= sanitize($p['material'] ?? '') ?> <?= $p['height'] ? '• ' . $p['height'] . '"' : '' ?>
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400">Rate:</span>
                            <span class="text-sm font-black text-slate-900 font-mono ml-1"><?= formatCurrency($p['selling_price']) ?></span>
                        </div>
                        <button class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center transition-all font-bold">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RIGHT SIDE: Reactive Cart & Fast Checkout (35% width) -->
    <div class="w-full lg:w-[420px] xl:w-[450px] flex flex-col bg-white border border-slate-200 rounded-2xl p-4 shadow-sm shrink-0 overflow-hidden">
        <!-- Customer Banner -->
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between mb-3 shrink-0">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200">
                    <i class="fas fa-user"></i>
                </div>
                <div class="overflow-hidden">
                    <div id="selected-customer-name" class="font-bold text-slate-900 text-xs truncate">Walk-in Retail Customer</div>
                    <div id="selected-customer-mobile" class="text-[10px] text-slate-500 font-mono">9999999999 • Regular</div>
                </div>
            </div>
            <button onclick="openCustomerModal()" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold transition-all shrink-0">
                Change [F4]
            </button>
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
                <span>Overall Discount (%):</span>
                <input type="number" id="pos-overall-discount" value="0" min="0" max="100" step="0.5" 
                       class="w-20 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-900 font-mono text-right focus:border-amber-500 outline-none">
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
                    <span class="text-slate-500">GST Tax:</span>
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
                <button id="btn-pos-pay" onclick="openPaymentModal()" disabled class="col-span-2 gold-btn font-extrabold py-2.5 rounded-xl text-sm shadow-md flex items-center justify-center gap-2">
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
            <h3 class="text-base font-bold text-slate-900">Select or Add Customer</h3>
            <button onclick="closeModal('pos-customer-modal')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <div class="space-y-4">
            <input type="text" id="cust-search-input" oninput="searchCustomerPos(this.value)" placeholder="Type customer name or mobile..." 
                   class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">

            <div id="pos-customer-list" class="max-h-56 overflow-y-auto space-y-1.5">
                <!-- Loaded via JS -->
            </div>

            <!-- Quick Add Customer Form -->
            <div class="pt-3 border-t border-slate-100">
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Quick Register Customer</h5>
                <form action="<?= url('customers/create') ?>" method="POST" data-ajax="true" class="space-y-2 text-xs">
                    <?= csrf_field() ?>
                    <input type="text" name="name" required placeholder="Customer Full Name" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900">
                    <input type="text" name="mobile" required placeholder="Mobile Number" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono">
                    <button type="submit" class="w-full py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm">
                        Save & Select Customer
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
            <!-- Grand Total Display -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Invoice Net Payable</span>
                <h2 id="modal-pay-grandtotal" class="text-3xl font-black font-mono text-slate-900 mt-1">₹0.00</h2>
            </div>

            <!-- Payment Method Radio Buttons -->
            <div>
                <label class="block font-bold text-slate-700 mb-2">Select Payment Mode</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="p-3 bg-slate-50 border border-slate-200 hover:border-amber-500 rounded-xl cursor-pointer flex flex-col items-center justify-center gap-1.5 text-slate-700 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 font-semibold transition-all">
                        <input type="radio" name="payment_method_radio" value="cash" checked class="hidden">
                        <i class="fas fa-money-bill-wave text-base"></i>
                        <span class="text-xs">Cash</span>
                    </label>
                    <label class="p-3 bg-slate-50 border border-slate-200 hover:border-amber-500 rounded-xl cursor-pointer flex flex-col items-center justify-center gap-1.5 text-slate-700 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 font-semibold transition-all">
                        <input type="radio" name="payment_method_radio" value="upi" class="hidden">
                        <i class="fas fa-qrcode text-base"></i>
                        <span class="text-xs">UPI / QR</span>
                    </label>
                    <label class="p-3 bg-slate-50 border border-slate-200 hover:border-amber-500 rounded-xl cursor-pointer flex flex-col items-center justify-center gap-1.5 text-slate-700 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 font-semibold transition-all">
                        <input type="radio" name="payment_method_radio" value="card" class="hidden">
                        <i class="fas fa-credit-card text-base"></i>
                        <span class="text-xs">Card / POS</span>
                    </label>
                    <label class="p-3 bg-slate-50 border border-slate-200 hover:border-amber-500 rounded-xl cursor-pointer flex flex-col items-center justify-center gap-1.5 text-slate-700 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 font-semibold transition-all">
                        <input type="radio" name="payment_method_radio" value="bank_transfer" class="hidden">
                        <i class="fas fa-university text-base"></i>
                        <span class="text-xs">Bank Transfer</span>
                    </label>
                    <label class="p-3 bg-slate-50 border border-slate-200 hover:border-amber-500 rounded-xl cursor-pointer flex flex-col items-center justify-center gap-1.5 text-slate-700 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 font-semibold transition-all">
                        <input type="radio" name="payment_method_radio" value="credit" class="hidden">
                        <i class="fas fa-book text-base"></i>
                        <span class="text-xs">Ledger Credit</span>
                    </label>
                </div>
            </div>

            <!-- Cash Drawer / Bank Account Destination -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deposit Account</label>
                    <select id="payment-account-select" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <?php foreach ($accounts as $acc): ?>
                            <option value="<?= $acc['id'] ?>"><?= sanitize($acc['name']) ?> (<?= ucfirst($acc['type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Paid Amount (₹)</label>
                    <input type="number" step="0.01" id="modal-pay-amount" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono font-bold focus:border-amber-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Invoice Notes</label>
                <input type="text" id="modal-pay-notes" placeholder="e.g. Consecrated with holy ash, velvet packaging included..." 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('pos-payment-modal')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
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
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-4 border border-emerald-200">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900">Invoice Generated!</h3>
        <p class="text-xs text-slate-500 mt-1">Invoice Number: <span id="completed-invoice-no" class="text-amber-800 font-mono font-bold text-sm"></span></p>

        <div class="grid grid-cols-2 gap-3 mt-6">
            <button id="btn-print-thermal" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex flex-col items-center justify-center gap-1.5 transition-colors border border-slate-200">
                <i class="fas fa-receipt text-lg text-amber-600"></i>
                <span>Thermal 80mm</span>
            </button>
            <button id="btn-print-a4" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex flex-col items-center justify-center gap-1.5 transition-colors border border-slate-200">
                <i class="fas fa-file-invoice text-lg text-amber-600"></i>
                <span>Tax Invoice A4</span>
            </button>
        </div>

        <div class="mt-4 flex gap-2">
            <button onclick="closeModal('pos-complete-modal')" class="flex-1 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm">
                New Bill [F2]
            </button>
            <button id="btn-view-invoice" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                View Bill
            </button>
        </div>
    </div>
</div>

<!-- Load POS Controller JS -->
<script src="<?= asset('js/pos.js') ?>"></script>
