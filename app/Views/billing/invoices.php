<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Supplier Invoices & Payment Register</h1>
            <p class="text-xs text-slate-500">Manage supplier purchase amounts, track payments, and update settlement status.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="<?= url('suppliers') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all">
                <i class="fas fa-hammer text-amber-600"></i> Manage Suppliers
            </a>
            <a href="<?= url('products') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-boxes"></i> Product Entries
            </a>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('billing/invoices') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Search Supplier Invoices</label>
                <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" placeholder="Search by Supplier ID, Name, or Mobile..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Payment Statuses</option>
                    <option value="Paid" <?= ($filters['payment_status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Partially Paid" <?= ($filters['payment_status'] ?? '') === 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="Unpaid" <?= ($filters['payment_status'] ?? '') === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1.5 h-[35px]">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?= url('billing/invoices') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 px-3 py-2 rounded-xl text-xs h-[35px] flex items-center justify-center" title="Reset Filters">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Main Table: Strictly the 5 requested columns -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Supplier ID</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Supplier Name</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Amount</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700">Payment Status</th>
                        <th class="py-3.5 px-4 font-bold text-slate-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="supplier-invoices-tbody" class="divide-y divide-slate-100">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="5" class="py-14 text-center text-slate-400">
                                <i class="fas fa-truck-loading text-3xl mb-2 text-slate-300 block"></i>
                                <p class="font-bold text-slate-600 text-sm">No supplier invoices found</p>
                                <p class="text-xs text-slate-400 mt-0.5">Suppliers and their associated product purchases will appear here.</p>
                            </td>
                        </tr>
                    <?php else: foreach ($invoices as $inv): ?>
                        <?php 
                            $status = $inv['payment_status'] ?? 'Unpaid';
                            if ($status === 'Paid') {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif ($status === 'Partially Paid') {
                                $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                            } else {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                            }
                        ?>
                        <tr id="row-supplier-<?= $inv['id'] ?>" class="hover:bg-slate-50 transition-colors">
                            <!-- 1. Supplier ID -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <?= sanitize($inv['supplier_id']) ?>
                            </td>

                            <!-- 2. Supplier Name -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 text-sm"><?= sanitize($inv['supplier_name']) ?></span>
                                <?php if (!empty($inv['company_name'])): ?>
                                    <span class="text-[11px] text-slate-400 block"><?= sanitize($inv['company_name']) ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- 3. Amount -->
                            <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm">
                                <?= formatCurrency($inv['amount']) ?>
                            </td>

                            <!-- 4. Payment Status -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= $badgeClass ?>">
                                    <?= sanitize($status) ?>
                                </span>
                            </td>

                            <!-- 5. Actions: View / Edit -->
                            <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick="openViewModal(<?= (int)$inv['id'] ?>)" title="View Purchase Details" 
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                                    <i class="fas fa-eye text-xs"></i> View
                                </button>
                                <button type="button" onclick="openEditPaymentModal(<?= htmlspecialchars(json_encode($inv), ENT_QUOTES, 'UTF-8') ?>)" title="Edit / Update Payment" 
                                        class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold inline-flex items-center gap-1.5 border border-amber-200 transition-colors">
                                    <i class="fas fa-edit text-xs"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: View Supplier Purchase Details -->
<div id="modal-view-details" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-3xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-truck-loading text-amber-600"></i> Supplier Purchase & Payment Summary
                </h3>
                <p class="text-xs text-slate-500 mt-0.5" id="v_sub_heading">Supplier Overview</p>
            </div>
            <button type="button" onclick="closeViewModal()" class="text-slate-400 hover:text-slate-700 p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="space-y-5">
            <!-- Header Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Supplier ID</span>
                    <span id="v_supplier_id" class="font-mono font-bold text-slate-900 text-sm"></span>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Supplier Name</span>
                    <span id="v_supplier_name" class="font-bold text-slate-900 text-sm"></span>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Payment Status</span>
                    <span id="v_payment_status" class="inline-block mt-0.5"></span>
                </div>
            </div>

            <!-- Financial Metrics -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-3">
                    <span class="text-[10px] font-bold uppercase text-blue-700 block">Total Purchase Amount</span>
                    <span id="v_total_amount" class="font-mono font-black text-blue-900 text-base">₹0.00</span>
                </div>
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-3">
                    <span class="text-[10px] font-bold uppercase text-emerald-700 block">Paid Amount</span>
                    <span id="v_paid_amount" class="font-mono font-black text-emerald-900 text-base">₹0.00</span>
                </div>
                <div class="bg-rose-50/50 border border-rose-200 rounded-xl p-3">
                    <span class="text-[10px] font-bold uppercase text-rose-700 block">Balance Amount</span>
                    <span id="v_balance_amount" class="font-mono font-black text-rose-900 text-base">₹0.00</span>
                </div>
            </div>

            <!-- Associated Products List -->
            <div>
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Associated Products Purchased</span>
                    <span id="v_product_count_badge" class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold">0 products</span>
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
                        <tbody id="v_products_tbody" class="divide-y divide-slate-100 font-mono">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeViewModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Edit / Update Supplier Payment -->
<div id="modal-edit-payment" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-money-check-alt text-amber-600"></i> Update Supplier Payment
            </h3>
            <button type="button" onclick="closeEditPaymentModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="edit-payment-form" onsubmit="submitSupplierPayment(event)" class="space-y-4">
            <input type="hidden" id="e_supplier_id">
            <input type="hidden" id="e_total_purchase_amount" value="0">

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Supplier:</span>
                    <span id="e_supp_name" class="font-bold text-slate-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Supplier ID:</span>
                    <span id="e_supp_id_text" class="font-mono font-bold text-slate-700"></span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5">
                    <span class="text-slate-600 font-semibold">Total Purchase Amount:</span>
                    <span id="e_total_amount_display" class="font-mono font-black text-slate-900">₹0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Previously Paid:</span>
                    <span id="e_previously_paid_display" class="font-mono font-bold text-emerald-700">₹0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 font-semibold">Remaining Balance:</span>
                    <span id="e_balance_amount_display" class="font-mono font-black text-rose-600">₹0.00</span>
                </div>
            </div>

            <!-- Additional Payment Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Additional Payment (₹)</label>
                <input type="number" step="0.01" min="0" id="e_additional_amount" oninput="onAdditionalAmountInput()" placeholder="0.00" 
                       class="w-full bg-emerald-50/50 border border-emerald-300 rounded-xl px-3 py-2 text-sm text-emerald-900 font-mono font-bold outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                <p class="text-[11px] text-slate-400 mt-1">Enter payment received now (auto-increments total paid)</p>
            </div>

            <!-- Paid Amount Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Total Paid Amount (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0" id="e_paid_amount" oninput="onPaidAmountInput()" required 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono font-bold outline-none focus:border-amber-500">
                <p id="e_amount_helper" class="text-[11px] text-slate-400 mt-1">Formula: Balance = Total Amount - Paid Amount</p>
            </div>

            <!-- Payment Status Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Status <span class="text-rose-500">*</span></label>
                <select id="e_payment_status" onchange="onPaymentStatusChange()" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:border-amber-500">
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partially Paid">Partially Paid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                    <select id="e_payment_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI / QR</option>
                        <option value="Card">Card</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Date</label>
                    <input type="date" id="e_payment_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Transaction Ref (Optional)</label>
                <input type="text" id="e_notes" placeholder="e.g. UTR # / Cheque No / Cash voucher" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-amber-500">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeEditPaymentModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" id="btn-save-payment" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function getCleanAppUrl(path) {
    const base = (window.APP_URL || '').replace(/\/+$/, '');
    const cleanPath = (path || '').replace(/^\/+/, '');
    return base + '/' + cleanPath;
}

function formatInr(val) {
    const num = parseFloat(val) || 0;
    return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// VIEW ACTION
async function openViewModal(supplierId) {
    try {
        const res = await fetch(getCleanAppUrl('/billing/invoices/supplier/' + supplierId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (!json.success || !json.data) {
            alert('Failed to load supplier details.');
            return;
        }

        const data = json.data;
        document.getElementById('v_supplier_id').textContent = data.supplier_id || ('SUP-' + data.id);
        document.getElementById('v_supplier_name').textContent = data.supplier_name;
        document.getElementById('v_sub_heading').textContent = (data.company_name ? data.company_name + ' • ' : '') + (data.mobile || '');
        document.getElementById('v_total_amount').textContent = formatInr(data.total_amount);
        document.getElementById('v_paid_amount').textContent = formatInr(data.paid_amount);
        document.getElementById('v_balance_amount').textContent = formatInr(data.balance_amount);

        let badgeHtml = '';
        if (data.payment_status === 'Paid') {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">Paid</span>';
        } else if (data.payment_status === 'Partially Paid') {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-amber-50 text-amber-800 border-amber-200">Partially Paid</span>';
        } else {
            badgeHtml = '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-rose-50 text-rose-700 border-rose-200">Unpaid</span>';
        }
        document.getElementById('v_payment_status').innerHTML = badgeHtml;

        const tbody = document.getElementById('v_products_tbody');
        tbody.innerHTML = '';
        const prods = data.products || [];
        document.getElementById('v_product_count_badge').textContent = prods.length + ' products';

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
                    <td class="py-2.5 px-3 text-right text-slate-700">${formatInr(p.piece_rate)}</td>
                    <td class="py-2.5 px-3 text-right font-black text-slate-900">${formatInr(p.total_purchase_price)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        document.getElementById('modal-view-details').classList.remove('hidden');
    } catch(err) {
        console.error(err);
        alert('Error loading supplier details.');
    }
}

function closeViewModal() {
    document.getElementById('modal-view-details').classList.add('hidden');
}

// EDIT / UPDATE PAYMENT ACTION
let eCurrentPrevPaid = 0.00;

function openEditPaymentModal(inv) {
    document.getElementById('e_supplier_id').value = inv.id;
    document.getElementById('e_supp_name').textContent = inv.supplier_name;
    document.getElementById('e_supp_id_text').textContent = inv.supplier_id || ('SUP-' + inv.id);

    const totalAmt = parseFloat(inv.amount || inv.total_amount || 0);
    const paidAmt = parseFloat(inv.paid_amount || 0);
    eCurrentPrevPaid = paidAmt;
    const balAmt = Math.max(0, totalAmt - paidAmt);

    document.getElementById('e_total_purchase_amount').value = totalAmt;
    document.getElementById('e_total_amount_display').textContent = formatInr(totalAmt);
    document.getElementById('e_previously_paid_display').textContent = formatInr(paidAmt);
    document.getElementById('e_balance_amount_display').textContent = formatInr(balAmt);

    document.getElementById('e_additional_amount').value = '';
    document.getElementById('e_payment_status').value = inv.payment_status || 'Unpaid';
    document.getElementById('e_paid_amount').value = paidAmt.toFixed(2);
    document.getElementById('e_paid_amount').max = totalAmt > 0 ? totalAmt : '';
    document.getElementById('e_payment_method').value = inv.payment_method || 'Cash';
    document.getElementById('e_payment_date').value = inv.payment_date || new Date().toISOString().split('T')[0];
    document.getElementById('e_notes').value = inv.notes || '';

    onPaymentStatusChange();
    document.getElementById('modal-edit-payment').classList.remove('hidden');
}

function closeEditPaymentModal() {
    document.getElementById('modal-edit-payment').classList.add('hidden');
}

function onPaymentStatusChange() {
    const status = document.getElementById('e_payment_status').value;
    const total = parseFloat(document.getElementById('e_total_purchase_amount').value) || 0;
    const paidInput = document.getElementById('e_paid_amount');
    const balDisplay = document.getElementById('e_balance_amount_display');
    const addInput = document.getElementById('e_additional_amount');

    if (status === 'Paid') {
        paidInput.value = total.toFixed(2);
        if (addInput) addInput.value = Math.max(0, total - eCurrentPrevPaid).toFixed(2);
        balDisplay.textContent = formatInr(0);
    } else if (status === 'Unpaid') {
        paidInput.value = '0.00';
        if (addInput) addInput.value = '0.00';
        balDisplay.textContent = formatInr(total);
    } else if (status === 'Partially Paid') {
        let curr = parseFloat(paidInput.value) || 0;
        if (curr <= 0 && total > 0) {
            curr = total / 2;
            paidInput.value = curr.toFixed(2);
        }
        if (addInput) addInput.value = Math.max(0, curr - eCurrentPrevPaid).toFixed(2);
        const bal = Math.max(0, total - curr);
        balDisplay.textContent = formatInr(bal);
    }
}

function onAdditionalAmountInput() {
    const total = parseFloat(document.getElementById('e_total_purchase_amount').value) || 0;
    const addInput = document.getElementById('e_additional_amount');
    const paidInput = document.getElementById('e_paid_amount');

    let additional = parseFloat(addInput ? addInput.value : 0);
    if (isNaN(additional) || additional < 0) additional = 0;

    let newTotalPaid = eCurrentPrevPaid + additional;
    if (total > 0 && newTotalPaid > total) {
        newTotalPaid = total;
        additional = Math.max(0, total - eCurrentPrevPaid);
        if (addInput) addInput.value = additional.toFixed(2);
    }

    if (paidInput) {
        paidInput.value = newTotalPaid.toFixed(2);
    }

    const bal = Math.max(0, total - newTotalPaid);
    document.getElementById('e_balance_amount_display').textContent = formatInr(bal);

    const statusSelect = document.getElementById('e_payment_status');
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

function onPaidAmountInput() {
    const total = parseFloat(document.getElementById('e_total_purchase_amount').value) || 0;
    const paidInput = document.getElementById('e_paid_amount');
    const addInput = document.getElementById('e_additional_amount');
    let paid = parseFloat(paidInput.value) || 0;

    if (total > 0 && paid > total) {
        paid = total;
        paidInput.value = total.toFixed(2);
    }
    if (paid < 0) {
        paid = 0;
        paidInput.value = '0.00';
    }

    if (addInput) {
        addInput.value = Math.max(0, paid - eCurrentPrevPaid).toFixed(2);
    }

    const bal = Math.max(0, total - paid);
    document.getElementById('e_balance_amount_display').textContent = formatInr(bal);

    // Derive status dynamically
    const statusSelect = document.getElementById('e_payment_status');
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

async function submitSupplierPayment(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-payment');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const supplierId = document.getElementById('e_supplier_id').value;
    const paidAmount = document.getElementById('e_paid_amount').value;
    const additionalAmount = document.getElementById('e_additional_amount') ? document.getElementById('e_additional_amount').value : 0;
    const status = document.getElementById('e_payment_status').value;
    const method = document.getElementById('e_payment_method').value;
    const date = document.getElementById('e_payment_date').value;
    const notes = document.getElementById('e_notes').value;

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
        const res = await fetch(getCleanAppUrl('/billing/invoices/update-payment'), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            closeEditPaymentModal();
            if (typeof showToast === 'function') {
                showToast('success', data.message || 'Payment updated successfully.');
            }
            setTimeout(() => window.location.reload(), 500);
        } else {
            alert(data.message || 'Failed to update payment.');
        }
    } catch(err) {
        console.error(err);
        alert('Network error while updating payment.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
}
</script>
