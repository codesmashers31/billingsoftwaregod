<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="<?= url('billing/invoices') ?>" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 shadow-sm hover:bg-slate-50 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900"><?= $isEdit ? 'Edit Invoice' : 'Create New Invoice' ?></h1>
                    <p class="text-xs text-slate-500">Generate GST compliant tax invoices, proforma bills, and manage customer billing</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="saveInvoice('draft')" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all">
                <i class="fas fa-save text-slate-500"></i> Save as Draft
            </button>
            <button type="button" onclick="saveInvoice('completed')" class="gold-btn px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-check-circle"></i> <?= $isEdit ? 'Update & Finalize' : 'Finalize & Issue Invoice' ?>
            </button>
        </div>
    </div>

    <!-- Main Invoice Form -->
    <form id="invoice-form" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="status" id="invoice_status" value="<?= $invoice['status'] ?? 'completed' ?>">
        <input type="hidden" name="customer_id" id="cust_id" value="<?= $invoice['customer_id'] ?? '' ?>">

        <!-- Section 1: Basic Invoice Information -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-amber-600"></i> Invoice Basic Details
                </h3>
                <span class="text-[11px] font-mono text-slate-400">All fields marked with <span class="text-rose-500">*</span> are required</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Number <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="invoice_no" id="inv_number" required readonly
                               value="<?= sanitize($invoice['invoice_no'] ?? '') ?>"
                               class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-800 outline-none">
                        <?php if (!$isEdit): ?>
                        <button type="button" onclick="refreshInvoiceNo()" title="Regenerate invoice number" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-600 p-1">
                            <i class="fas fa-sync-alt text-xs"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Type <span class="text-rose-500">*</span></label>
                    <select name="invoice_type" id="inv_type" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                        <option value="tax_invoice" <?= ($invoice['invoice_type'] ?? '') === 'tax_invoice' ? 'selected' : '' ?>>Tax Invoice</option>
                        <option value="proforma_invoice" <?= ($invoice['invoice_type'] ?? '') === 'proforma_invoice' ? 'selected' : '' ?>>Proforma Invoice</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="invoice_date" id="inv_date" required
                           value="<?= $invoice['invoice_date'] ?? date('Y-m-d') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" id="inv_due_date"
                           value="<?= $invoice['due_date'] ?? date('Y-m-d', strtotime('+15 days')) ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Terms</label>
                    <select name="payment_terms" id="inv_terms" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                        <option value="Immediate" <?= ($invoice['payment_terms'] ?? '') === 'Immediate' ? 'selected' : '' ?>>Immediate</option>
                        <option value="Net 7" <?= ($invoice['payment_terms'] ?? '') === 'Net 7' ? 'selected' : '' ?>>Net 7 Days</option>
                        <option value="Net 15" <?= ($invoice['payment_terms'] ?? '') === 'Net 15' ? 'selected' : '' ?>>Net 15 Days</option>
                        <option value="Net 30" <?= ($invoice['payment_terms'] ?? '') === 'Net 30' ? 'selected' : '' ?>>Net 30 Days</option>
                        <option value="COD" <?= ($invoice['payment_terms'] ?? '') === 'COD' ? 'selected' : '' ?>>Cash on Delivery</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reference No. / PO Ref (Optional)</label>
                    <input type="text" name="reference_no" id="inv_ref" placeholder="e.g. PO-84920"
                           value="<?= sanitize($invoice['reference_no'] ?? '') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 2: Customer Details -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 mb-5 border-b border-slate-100 gap-2">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user text-amber-600"></i> Customer Details
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openNewCustomerModal()" class="text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-xl text-xs font-bold border border-amber-200 flex items-center gap-1.5 transition-all">
                        <i class="fas fa-user-plus"></i> Add New Customer
                    </button>
                </div>
            </div>

            <!-- Customer Search / Select -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="lg:col-span-2 relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Search & Select Customer <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="cust_search_input" placeholder="Type customer name or mobile number..." 
                               autocomplete="off"
                               class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none pr-8">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                    </div>
                    <!-- Autocomplete Dropdown List -->
                    <div id="cust_search_results" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Customer Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" id="cust_name" required
                           value="<?= sanitize($invoice['customer_name'] ?? '') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_mobile" id="cust_mobile" required
                           value="<?= sanitize($invoice['customer_mobile'] ?? '') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="customer_email" id="cust_email" placeholder="customer@example.com"
                           value="<?= sanitize($invoice['customer_email'] ?? '') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State <span class="text-rose-500">*</span></label>
                    <select name="customer_state" id="cust_state" onchange="onCustomerStateChange()" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                        <?php
                        $states = [
                            'Tamil Nadu' => '33', 'Kerala' => '32', 'Karnataka' => '29', 'Andhra Pradesh' => '37',
                            'Telangana' => '36', 'Maharashtra' => '27', 'Gujarat' => '24', 'Delhi' => '07',
                            'Uttar Pradesh' => '09', 'West Bengal' => '19', 'Rajasthan' => '08', 'Other State' => '99'
                        ];
                        $selectedState = $invoice['customer_state'] ?? 'Tamil Nadu';
                        foreach ($states as $st => $code):
                        ?>
                        <option value="<?= $st ?>" data-code="<?= $code ?>" <?= $selectedState === $st ? 'selected' : '' ?>><?= $st ?> (<?= $code ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State Code</label>
                    <input type="text" name="customer_state_code" id="cust_state_code" readonly
                           value="<?= $invoice['customer_state_code'] ?? '33' ?>"
                           class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN (Optional)</label>
                    <input type="text" name="customer_gst" id="cust_gst" placeholder="e.g. 33AAAAA0000A1Z5"
                           value="<?= sanitize($invoice['customer_gst'] ?? '') ?>"
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono outline-none uppercase">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Billing Address</label>
                    <textarea name="customer_billing_address" id="cust_billing" rows="2" placeholder="Full billing address..."
                              class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none"><?= sanitize($invoice['customer_address'] ?? '') ?></textarea>
                </div>

                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Shipping Address</label>
                        <label class="text-[11px] text-amber-700 font-semibold flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" id="same_as_billing" onchange="copyBillingToShipping()" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            Same as billing
                        </label>
                    </div>
                    <textarea name="customer_shipping_address" id="cust_shipping" rows="2" placeholder="Full shipping address..."
                              class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none"><?= sanitize($invoice['customer_shipping_address'] ?? $invoice['customer_address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Section 3: Product Selection & Items Table -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 mb-5 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-boxes text-amber-600"></i> Invoice Line Items
                    </h3>
                    <p class="text-xs text-slate-500">Search products by name, code or barcode from active inventory</p>
                </div>
                <div class="w-full sm:w-80 relative">
                    <input type="text" id="prod_search_bar" placeholder="Type product name, SKU or barcode to add..." 
                           autocomplete="off"
                           class="w-full bg-amber-50/50 border border-amber-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none pr-8 shadow-inner">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-amber-600">
                        <i class="fas fa-barcode text-sm"></i>
                    </span>
                    <div id="prod_search_results" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 max-h-64 overflow-y-auto divide-y divide-slate-100">
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs" id="invoice-items-table">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">#</th>
                            <th class="py-3 px-3 min-w-[200px]">Product Description</th>
                            <th class="py-3 px-3 w-28 text-center">Available Stock</th>
                            <th class="py-3 px-3 w-24 text-center">Qty</th>
                            <th class="py-3 px-3 w-28 text-right">Unit Price (₹)</th>
                            <th class="py-3 px-3 w-24 text-center">Disc %</th>
                            <th class="py-3 px-3 w-24 text-center">GST %</th>
                            <th class="py-3 px-3 w-32 text-right">Total (₹)</th>
                            <th class="py-3 px-3 w-12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="invoice-items-body" class="divide-y divide-slate-100">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Empty Items Notice -->
            <div id="items-empty-state" class="py-12 text-center text-slate-400">
                <i class="fas fa-shopping-basket text-3xl mb-2 text-slate-300 block"></i>
                <p class="font-bold text-slate-700 text-xs">No items added yet</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Use the search box above to add products to this invoice.</p>
            </div>
        </div>

        <!-- Section 4: Live Bill Calculations & Payment Details -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left: Payment Info & Notes (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Payment Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                        <i class="fas fa-credit-card text-amber-600"></i> Payment Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" id="pay_method" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                                <option value="cash" <?= ($invoice['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="upi" <?= ($invoice['payment_method'] ?? '') === 'upi' ? 'selected' : '' ?>>UPI / QR</option>
                                <option value="card" <?= ($invoice['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Debit / Credit Card</option>
                                <option value="bank_transfer" <?= ($invoice['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer / NEFT</option>
                                <option value="credit" <?= ($invoice['payment_method'] ?? '') === 'credit' ? 'selected' : '' ?>>Credit (Pay Later)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Paid Amount (₹)</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="pay_paid_amount"
                                   oninput="calculateTotals()"
                                   value="<?= (float)($invoice['paid_amount'] ?? 0) ?>"
                                   class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono font-bold outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Balance Due (₹)</label>
                            <input type="text" id="pay_balance_amount" readonly
                                   value="0.00"
                                   class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2 text-xs text-rose-600 font-mono font-black outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Payment Status</label>
                            <div id="pay_status_badge" class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200 text-center uppercase tracking-wide">
                                Paid
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deposit Account</label>
                            <select name="account_id" id="pay_account_id" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                                <?php foreach ($accounts as $acc): ?>
                                <option value="<?= $acc['id'] ?>"><?= sanitize($acc['name']) ?> (<?= ucfirst($acc['type']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Terms & Notes Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                        <i class="fas fa-sticky-note text-amber-600"></i> Notes & Terms
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Notes / Customer Remarks</label>
                        <textarea name="notes" id="inv_notes" rows="3" placeholder="Additional details, special handling, or warranty instructions..."
                                  class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none"><?= sanitize($invoice['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Live Financial Summary (5 Cols) -->
            <div class="lg:col-span-5">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm sticky top-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-calculator text-amber-600"></i> Financial Summary
                        </h3>
                        <span id="tax_type_badge" class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                            Intra-state (CGST + SGST)
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs text-slate-600 font-medium">
                        <div class="flex justify-between items-center">
                            <span>Subtotal (Gross):</span>
                            <span class="font-mono font-bold text-slate-900" id="disp_subtotal">₹0.00</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span>Item Discounts Total:</span>
                            <span class="font-mono font-bold text-emerald-600" id="disp_item_discount">-₹0.00</span>
                        </div>

                        <!-- Overall Invoice Discount -->
                        <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                            <div class="flex items-center gap-1.5">
                                <span>Overall Discount:</span>
                                <input type="text" inputmode="decimal" name="overall_discount_percent" id="overall_disc_pct"
                                       oninput="calculateTotals()"
                                       placeholder="0.0"
                                       value="<?= !empty($invoice['overall_discount_percent']) && (float)$invoice['overall_discount_percent'] > 0 ? (float)$invoice['overall_discount_percent'] : '' ?>"
                                       class="w-14 bg-slate-50 border border-slate-300 rounded px-1.5 py-0.5 text-xs font-mono text-center outline-none">
                                <span class="text-[11px]">%</span>
                            </div>
                            <span class="font-mono font-bold text-emerald-600" id="disp_overall_discount">-₹0.00</span>
                        </div>

                        <div class="flex justify-between items-center font-bold text-slate-800 pt-1 border-t border-slate-100">
                            <span>Taxable Value:</span>
                            <span class="font-mono" id="disp_taxable">₹0.00</span>
                        </div>

                        <!-- Tax Split Row -->
                        <div id="intra_tax_block" class="space-y-1.5 pt-1 border-t border-slate-100">
                            <div class="flex justify-between items-center text-[11px]">
                                <span>CGST Amount:</span>
                                <span class="font-mono font-bold text-slate-800" id="disp_cgst">₹0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span>SGST Amount:</span>
                                <span class="font-mono font-bold text-slate-800" id="disp_sgst">₹0.00</span>
                            </div>
                        </div>

                        <div id="inter_tax_block" class="hidden pt-1 border-t border-slate-100">
                            <div class="flex justify-between items-center text-[11px]">
                                <span>IGST Amount:</span>
                                <span class="font-mono font-bold text-slate-800" id="disp_igst">₹0.00</span>
                            </div>
                        </div>

                        <!-- Additional Charges -->
                        <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                            <span>Additional Charges (Freight/Packing):</span>
                            <div class="flex items-center gap-1">
                                <input type="number" step="0.01" min="0" name="additional_charges" id="additional_charges"
                                       oninput="calculateTotals()"
                                       value="<?= (float)($invoice['additional_charges'] ?? 0) ?>"
                                       class="w-20 bg-slate-50 border border-slate-300 rounded px-2 py-0.5 text-xs font-mono text-right outline-none">
                            </div>
                        </div>

                        <!-- Round Off -->
                        <div class="flex justify-between items-center">
                            <span>Round-off:</span>
                            <span class="font-mono font-bold text-slate-500" id="disp_round_off">₹0.00</span>
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-3 border-t-2 border-slate-900 flex justify-between items-center text-slate-950">
                            <div>
                                <span class="text-sm font-extrabold block">Grand Total</span>
                                <span class="text-[10px] text-slate-400">Inclusive of all taxes</span>
                            </div>
                            <span class="text-xl font-mono font-black text-amber-700" id="disp_grand_total">₹0.00</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <button type="button" onclick="saveInvoice('completed')" class="w-full gold-btn py-3 rounded-xl text-xs font-bold shadow-md hover:shadow-lg flex items-center justify-center gap-2 transform hover:-translate-y-0.5 transition-all">
                            <i class="fas fa-check-circle"></i> Save & Generate Final Invoice
                        </button>
                        <button type="button" onclick="saveInvoice('draft')" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                            <i class="fas fa-save text-slate-500"></i> Save as Draft (No stock deducted)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Add New Customer Inline -->
<div id="modal-inline-customer" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden p-6 relative">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-user-plus text-amber-600"></i> Add New Customer
            </h3>
            <button type="button" onclick="closeNewCustomerModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="inline-customer-form" onsubmit="submitInlineCustomer(event)" class="space-y-3.5">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Customer Full Name <span class="text-rose-500">*</span></label>
                <input type="text" id="m_cust_name" required placeholder="e.g. Ramesh Sthapathi" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                    <input type="tel" id="m_cust_mobile" required placeholder="10-digit mobile" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email (Optional)</label>
                    <input type="email" id="m_cust_email" placeholder="email@domain.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State <span class="text-rose-500">*</span></label>
                    <select id="m_cust_state" onchange="onModalStateChange()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                        <option value="Tamil Nadu" data-code="33">Tamil Nadu (33)</option>
                        <option value="Kerala" data-code="32">Kerala (32)</option>
                        <option value="Karnataka" data-code="29">Karnataka (29)</option>
                        <option value="Andhra Pradesh" data-code="37">Andhra Pradesh (37)</option>
                        <option value="Telangana" data-code="36">Telangana (36)</option>
                        <option value="Maharashtra" data-code="27">Maharashtra (27)</option>
                        <option value="Delhi" data-code="07">Delhi (07)</option>
                        <option value="Other State" data-code="99">Other State (99)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State Code</label>
                    <input type="text" id="m_cust_state_code" value="33" readonly class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN (Optional)</label>
                <input type="text" id="m_cust_gst" placeholder="33AAAAA0000A1Z5" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono outline-none focus:border-amber-500 uppercase">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Address</label>
                <textarea id="m_cust_address" rows="2" placeholder="Full postal address..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeNewCustomerModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save & Select Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
// State caches
window.COMPANY_STATE = "<?= getSetting('company_state', 'Tamil Nadu') ?>";
window.EXISTING_ITEMS = <?= json_encode($items ?? []) ?>;
let invoiceItems = [];

document.addEventListener('DOMContentLoaded', () => {
    // Initialize state code
    onCustomerStateChange();

    // Populate existing items if edit mode
    if (window.EXISTING_ITEMS && window.EXISTING_ITEMS.length > 0) {
        window.EXISTING_ITEMS.forEach(it => {
            addItemToTable({
                id: it.product_id,
                name: it.product_name,
                code: it.product_code,
                current_stock: parseInt(it.current_stock || 999),
                selling_price: parseFloat(it.unit_price),
                discount_percent: parseFloat(it.discount_percent || 0),
                gst_percent: parseFloat(it.tax_percent || 12),
                quantity: parseInt(it.quantity || 1)
            });
        });
    }

    // Customer search typing listener
    const custSearch = document.getElementById('cust_search_input');
    let custTimer = null;
    custSearch.addEventListener('input', (e) => {
        clearTimeout(custTimer);
        const q = e.target.value.trim();
        if (q.length < 2) {
            document.getElementById('cust_search_results').classList.add('hidden');
            return;
        }
        custTimer = setTimeout(() => searchCustomers(q), 250);
    });

    // Product search typing listener
    const prodSearch = document.getElementById('prod_search_bar');
    let prodTimer = null;
    prodSearch.addEventListener('input', (e) => {
        clearTimeout(prodTimer);
        const q = e.target.value.trim();
        if (q.length < 2) {
            document.getElementById('prod_search_results').classList.add('hidden');
            return;
        }
        prodTimer = setTimeout(() => searchProducts(q), 250);
    });

    // Close autocomplete on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#cust_search_input') && !e.target.closest('#cust_search_results')) {
            document.getElementById('cust_search_results').classList.add('hidden');
        }
        if (!e.target.closest('#prod_search_bar') && !e.target.closest('#prod_search_results')) {
            document.getElementById('prod_search_results').classList.add('hidden');
        }
    });

    calculateTotals();
});

function getCleanAppUrl(path) {
    const base = (window.APP_URL || '').replace(/\/+$/, '');
    const cleanPath = (path || '').replace(/^\/+/, '');
    return base + '/' + cleanPath;
}

// Regenerate invoice number
async function refreshInvoiceNo() {
    try {
        const res = await fetch(getCleanAppUrl('/billing/invoices/generate-number'));
        const data = await res.json();
        if (data && data.invoice_no) {
            document.getElementById('inv_number').value = data.invoice_no;
            showToast('info', 'Generated new invoice number: ' + data.invoice_no);
        }
    } catch (e) { console.error(e); }
}

// Customer Search & Auto-populate
async function searchCustomers(q) {
    try {
        const res = await fetch(getCleanAppUrl('/customers/search?q=' + encodeURIComponent(q)));
        const json = await res.json();
        const resultsEl = document.getElementById('cust_search_results');
        const list = json.data || [];

        if (list.length === 0) {
            resultsEl.innerHTML = `<div class="p-3 text-xs text-slate-400 text-center">No customers found. <button type="button" onclick="openNewCustomerModal('${q}')" class="text-amber-700 font-bold underline ml-1">Add new?</button></div>`;
            resultsEl.classList.remove('hidden');
            return;
        }

        resultsEl.innerHTML = list.map(c => `
            <div onclick="selectCustomer(${JSON.stringify(c).replace(/"/g, '&quot;')})" class="p-2.5 hover:bg-amber-50 cursor-pointer text-xs flex justify-between items-center transition-colors">
                <div>
                    <span class="font-bold text-slate-900">${c.name}</span>
                    <span class="text-slate-400 font-mono text-[11px] block">${c.mobile} ${c.city ? '• ' + c.city : ''}</span>
                </div>
                <span class="text-[10px] font-mono font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">${c.customer_code}</span>
            </div>
        `).join('');
        resultsEl.classList.remove('hidden');
    } catch(e) { console.error(e); }
}

function selectCustomer(cust) {
    document.getElementById('cust_id').value = cust.id;
    document.getElementById('cust_name').value = cust.name || '';
    document.getElementById('cust_mobile').value = cust.mobile || '';
    document.getElementById('cust_email').value = cust.email || '';
    document.getElementById('cust_gst').value = cust.gst_number || '';
    document.getElementById('cust_billing').value = cust.address || '';
    document.getElementById('cust_shipping').value = cust.shipping_address || cust.address || '';
    
    if (cust.state) {
        document.getElementById('cust_state').value = cust.state;
    }
    document.getElementById('cust_state_code').value = cust.state_code || '33';

    document.getElementById('cust_search_input').value = cust.name + ' (' + cust.mobile + ')';
    document.getElementById('cust_search_results').classList.add('hidden');
    onCustomerStateChange();
}

function onCustomerStateChange() {
    const stateEl = document.getElementById('cust_state');
    const selected = stateEl.options[stateEl.selectedIndex];
    const code = selected ? selected.getAttribute('data-code') : '33';
    document.getElementById('cust_state_code').value = code || '33';
    calculateTotals();
}

function copyBillingToShipping() {
    const isSame = document.getElementById('same_as_billing').checked;
    if (isSame) {
        document.getElementById('cust_shipping').value = document.getElementById('cust_billing').value;
    }
}

// Modal: Add New Customer Inline
function openNewCustomerModal(query = '') {
    document.getElementById('m_cust_name').value = isNaN(query) ? query : '';
    document.getElementById('m_cust_mobile').value = !isNaN(query) ? query : '';
    document.getElementById('modal-inline-customer').classList.remove('hidden');
}

function closeNewCustomerModal() {
    document.getElementById('modal-inline-customer').classList.add('hidden');
}

function onModalStateChange() {
    const el = document.getElementById('m_cust_state');
    const code = el.options[el.selectedIndex].getAttribute('data-code');
    document.getElementById('m_cust_state_code').value = code || '33';
}

async function submitInlineCustomer(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('_csrf_token', window.CSRF_TOKEN);
    formData.append('name', document.getElementById('m_cust_name').value);
    formData.append('mobile', document.getElementById('m_cust_mobile').value);
    formData.append('email', document.getElementById('m_cust_email').value);
    formData.append('state', document.getElementById('m_cust_state').value);
    formData.append('state_code', document.getElementById('m_cust_state_code').value);
    formData.append('gst_number', document.getElementById('m_cust_gst').value);
    formData.append('address', document.getElementById('m_cust_address').value);

    try {
        const res = await fetch(getCleanAppUrl('/customers/create'), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success && data.data && data.data.customer) {
            showToast('success', data.message || 'Customer saved.');
            selectCustomer(data.data.customer);
            closeNewCustomerModal();
        } else {
            showToast('error', data.message || 'Could not save customer.');
        }
    } catch(err) {
        console.error(err);
        showToast('error', 'Network error registering customer.');
    }
}

// Product Search & Add
async function searchProducts(q) {
    try {
        const res = await fetch(getCleanAppUrl('/billing/search-products?q=' + encodeURIComponent(q)));
        const json = await res.json();
        const resultsEl = document.getElementById('prod_search_results');
        const list = json.data || [];

        if (list.length === 0) {
            resultsEl.innerHTML = `<div class="p-3 text-xs text-slate-400 text-center">No matching products found.</div>`;
            resultsEl.classList.remove('hidden');
            return;
        }

        resultsEl.innerHTML = list.map(p => `
            <div onclick="addItemToTable(${JSON.stringify(p).replace(/"/g, '&quot;')})" class="p-2.5 hover:bg-amber-50 cursor-pointer text-xs flex justify-between items-center transition-colors">
                <div>
                    <span class="font-bold text-slate-900">${p.name}</span>
                    <span class="text-slate-400 font-mono text-[11px] block">${p.code || p.sku || p.barcode} • GST: ${p.gst_percent}%</span>
                </div>
                <div class="text-right">
                    <span class="font-bold font-mono text-slate-900 block">₹${parseFloat(p.selling_price).toFixed(2)}</span>
                    <span class="text-[10px] font-bold ${parseInt(p.current_stock) > 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-600 bg-rose-50'} px-1.5 py-0.5 rounded">
                        ${parseInt(p.current_stock)} in stock
                    </span>
                </div>
            </div>
        `).join('');
        resultsEl.classList.remove('hidden');
    } catch(e) { console.error(e); }
}

function addItemToTable(prod) {
    document.getElementById('prod_search_bar').value = '';
    document.getElementById('prod_search_results').classList.add('hidden');

    // Check if already in items
    const existing = invoiceItems.find(it => it.product_id === prod.id);
    if (existing) {
        existing.quantity += 1;
        renderItems();
        calculateTotals();
        return;
    }

    invoiceItems.push({
        product_id: prod.id,
        name: prod.name,
        code: prod.code || prod.sku || prod.barcode || '',
        category: prod.category_name || 'General',
        current_stock: parseInt(prod.current_stock || 0),
        quantity: parseInt(prod.quantity || 1),
        unit_price: parseFloat(prod.selling_price || 0),
        discount_percent: parseFloat(prod.discount_percent || 0),
        tax_percent: parseFloat(prod.gst_percent || 12)
    });

    renderItems();
    calculateTotals();
}

function removeItem(index) {
    invoiceItems.splice(index, 1);
    renderItems();
    calculateTotals();
}

function updateItemField(index, field, value) {
    if (!invoiceItems[index]) return;
    invoiceItems[index][field] = parseFloat(value) || 0;
    renderItems();
    calculateTotals();
}

function renderItems() {
    const tbody = document.getElementById('invoice-items-body');
    const emptyState = document.getElementById('items-empty-state');

    if (invoiceItems.length === 0) {
        tbody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    tbody.innerHTML = invoiceItems.map((item, idx) => {
        const gross = item.quantity * item.unit_price;
        const disc = (gross * item.discount_percent) / 100;
        const taxable = gross - disc;
        const tax = (taxable * item.tax_percent) / 100;
        const total = taxable + tax;
        const isStockExceeded = item.quantity > item.current_stock;

        return `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-2.5 px-3 text-center text-slate-400 font-mono">${idx + 1}</td>
                <td class="py-2.5 px-3">
                    <span class="font-bold text-slate-900 block">${item.name}</span>
                    <span class="text-[10px] text-slate-400 font-mono">${item.code} • ${item.category}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="px-2 py-0.5 rounded-full font-mono font-bold text-[10px] ${item.current_stock > 0 ? (isStockExceeded ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') : 'bg-rose-50 text-rose-600 border border-rose-200'}">
                        ${item.current_stock}
                    </span>
                    ${isStockExceeded ? '<span class="text-[9px] text-rose-600 block font-bold mt-0.5">Exceeds Stock!</span>' : ''}
                </td>
                <td class="py-2.5 px-3">
                    <input type="number" min="1" value="${item.quantity}" 
                           oninput="updateItemField(${idx}, 'quantity', this.value)"
                           class="w-16 bg-white border ${isStockExceeded ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 focus:border-amber-500'} rounded-lg px-2 py-1 text-center font-mono font-bold text-xs outline-none">
                </td>
                <td class="py-2.5 px-3 text-right">
                    <input type="number" step="0.01" min="0" value="${item.unit_price.toFixed(2)}"
                           oninput="updateItemField(${idx}, 'unit_price', this.value)"
                           class="w-24 bg-white border border-slate-300 focus:border-amber-500 rounded-lg px-2 py-1 text-right font-mono font-bold text-xs outline-none">
                </td>
                <td class="py-2.5 px-3 text-center">
                    <input type="number" step="0.1" min="0" max="100" value="${item.discount_percent}"
                           oninput="updateItemField(${idx}, 'discount_percent', this.value)"
                           class="w-16 bg-white border border-slate-300 focus:border-amber-500 rounded-lg px-2 py-1 text-center font-mono text-xs outline-none">
                </td>
                <td class="py-2.5 px-3 text-center">
                    <input type="number" step="0.1" min="0" max="100" value="${item.tax_percent}"
                           oninput="updateItemField(${idx}, 'tax_percent', this.value)"
                           class="w-16 bg-white border border-slate-300 focus:border-amber-500 rounded-lg px-2 py-1 text-center font-mono text-xs outline-none">
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                    ₹${total.toFixed(2)}
                </td>
                <td class="py-2.5 px-3 text-center">
                    <button type="button" onclick="removeItem(${idx})" class="text-slate-400 hover:text-rose-600 p-1 transition-colors">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function calculateTotals() {
    let subtotal = 0;
    let itemDiscountTotal = 0;
    let taxTotal = 0;

    invoiceItems.forEach(it => {
        const gross = it.quantity * it.unit_price;
        const disc = (gross * it.discount_percent) / 100;
        const taxable = gross - disc;
        const tax = (taxable * it.tax_percent) / 100;

        subtotal += gross;
        itemDiscountTotal += disc;
        taxTotal += tax;
    });

    const overallPct = parseFloat(document.getElementById('overall_disc_pct').value) || 0;
    const overallDiscountAmount = ((subtotal - itemDiscountTotal) * overallPct) / 100;
    const additionalCharges = parseFloat(document.getElementById('additional_charges').value) || 0;

    const taxableAmount = Math.max(0, subtotal - itemDiscountTotal - overallDiscountAmount);
    
    // Check Inter-state vs Intra-state
    const custState = (document.getElementById('cust_state').value || '').trim();
    const isInterstate = (custState && custState.toLowerCase() !== window.COMPANY_STATE.toLowerCase());

    const taxTypeBadge = document.getElementById('tax_type_badge');
    const intraBlock = document.getElementById('intra_tax_block');
    const interBlock = document.getElementById('inter_tax_block');

    let cgst = 0, sgst = 0, igst = 0;
    if (isInterstate) {
        taxTypeBadge.textContent = `Inter-state (${custState}) - IGST`;
        taxTypeBadge.className = 'px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-800 text-[10px] font-bold border border-indigo-200';
        intraBlock.classList.add('hidden');
        interBlock.classList.remove('hidden');
        igst = taxTotal;
        document.getElementById('disp_igst').textContent = '₹' + igst.toFixed(2);
    } else {
        taxTypeBadge.textContent = 'Intra-state (Tamil Nadu) - CGST + SGST';
        taxTypeBadge.className = 'px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200';
        intraBlock.classList.remove('hidden');
        interBlock.classList.add('hidden');
        cgst = taxTotal / 2;
        sgst = taxTotal / 2;
        document.getElementById('disp_cgst').textContent = '₹' + cgst.toFixed(2);
        document.getElementById('disp_sgst').textContent = '₹' + sgst.toFixed(2);
    }

    const rawTotal = taxableAmount + taxTotal + additionalCharges;
    const grandTotal = Math.round(rawTotal);
    const roundOff = (grandTotal - rawTotal);

    document.getElementById('disp_subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('disp_item_discount').textContent = '-₹' + itemDiscountTotal.toFixed(2);
    document.getElementById('disp_overall_discount').textContent = '-₹' + overallDiscountAmount.toFixed(2);
    document.getElementById('disp_taxable').textContent = '₹' + taxableAmount.toFixed(2);
    document.getElementById('disp_round_off').textContent = (roundOff >= 0 ? '+₹' : '-₹') + Math.abs(roundOff).toFixed(2);
    document.getElementById('disp_grand_total').textContent = '₹' + grandTotal.toFixed(2);

    // Paid amount & balance
    const paidInput = document.getElementById('pay_paid_amount');
    let paidVal = parseFloat(paidInput.value);
    if (isNaN(paidVal) || paidVal < 0) {
        paidVal = grandTotal;
        paidInput.value = grandTotal.toFixed(2);
    }

    const balance = Math.max(0, grandTotal - paidVal);
    document.getElementById('pay_balance_amount').value = '₹' + balance.toFixed(2);

    const badge = document.getElementById('pay_status_badge');
    if (balance <= 0.01) {
        badge.textContent = 'Paid';
        badge.className = 'px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200 text-center uppercase tracking-wide';
    } else if (paidVal > 0) {
        badge.textContent = 'Partial';
        badge.className = 'px-3 py-2 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200 text-center uppercase tracking-wide';
    } else {
        badge.textContent = 'Unpaid';
        badge.className = 'px-3 py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200 text-center uppercase tracking-wide';
    }
}

// Submission
async function saveInvoice(status) {
    if (invoiceItems.length === 0) {
        showToast('error', 'Please add at least one product before saving.');
        return;
    }

    // If final completed status, check stock
    if (status === 'completed') {
        const exceeded = invoiceItems.find(it => it.quantity > it.current_stock);
        if (exceeded) {
            showToast('error', `Insufficient stock for "${exceeded.name}". Available: ${exceeded.current_stock}, Requested: ${exceeded.quantity}`);
            return;
        }
    }

    const custName = document.getElementById('cust_name').value.trim();
    const custMobile = document.getElementById('cust_mobile').value.trim();
    if (!custName || !custMobile) {
        showToast('error', 'Customer name and mobile number are required.');
        return;
    }

    document.getElementById('invoice_status').value = status;
    const form = document.getElementById('invoice-form');
    const formData = new FormData(form);

    // Append items array
    invoiceItems.forEach((it, idx) => {
        formData.append(`items[${idx}][product_id]`, it.product_id);
        formData.append(`items[${idx}][quantity]`, it.quantity);
        formData.append(`items[${idx}][unit_price]`, it.unit_price);
        formData.append(`items[${idx}][discount_percent]`, it.discount_percent);
        formData.append(`items[${idx}][tax_percent]`, it.tax_percent);
    });

    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const saveUrl = isEdit 
        ? getCleanAppUrl('/billing/invoices/edit/<?= (int)($invoice['id'] ?? 0) ?>')
        : getCleanAppUrl('/billing/invoices/create');

    try {
        const res = await fetch(saveUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => {
                window.location.href = data.data.view_url || getCleanAppUrl('/billing/invoices');
            }, 700);
        } else {
            showToast('error', data.message || 'Error saving invoice.');
        }
    } catch(err) {
        console.error(err);
        showToast('error', 'Failed to communicate with server.');
    }
}
</script>
