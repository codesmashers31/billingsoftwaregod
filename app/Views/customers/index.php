<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Customer CRM & Ledgers</h1>
            <p class="text-xs text-slate-500">Manage retail buyers, wholesale clients, temple trusts, credit limits, and outstanding balances</p>
        </div>
        <?php if (hasPermission('customers.create')): ?>
        <button onclick="openModal('modal-add-customer')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-user-plus"></i> Add New Customer
        </button>
        <?php endif; ?>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('customers') ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="<?= sanitize($search ?? '') ?>" placeholder="Search by name, phone, code, email..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <select name="type" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Types</option>
                    <option value="retail" <?= ($typeFilter ?? '') === 'retail' ? 'selected' : '' ?>>Retail Walk-in / Collector</option>
                    <option value="wholesale" <?= ($typeFilter ?? '') === 'wholesale' ? 'selected' : '' ?>>Wholesale Distributor</option>
                    <option value="temple_trust" <?= ($typeFilter ?? '') === 'temple_trust' ? 'selected' : '' ?>>Temple Trust / Devasthanam</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="<?= url('customers') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 px-3 py-2 rounded-xl text-xs">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Type & State</th>
                        <th class="py-3.5 px-4">Contact</th>
                        <th class="py-3.5 px-4">Outstanding Balance</th>
                        <th class="py-3.5 px-4">Credit Limit</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">No customers registered.</td></tr>
                    <?php else: foreach ($customers as $c): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900"><?= sanitize($c['name']) ?></div>
                                <div class="text-[10px] text-amber-800 font-mono font-semibold"><?= $c['customer_code'] ?> <?= $c['gst_number'] ? '• GST: ' . $c['gst_number'] : '' ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] uppercase font-bold border border-slate-200">
                                    <?= str_replace('_', ' ', $c['customer_type']) ?>
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= sanitize($c['city'] ?? '') ?>, <?= sanitize($c['state'] ?? 'Tamil Nadu') ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-900 font-mono font-bold"><?= sanitize($c['mobile']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= sanitize($c['email'] ?? 'N/A') ?></div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <?php if ($c['outstanding_balance'] > 0): ?>
                                    <span class="text-rose-600 font-black text-sm"><?= formatCurrency($c['outstanding_balance']) ?></span>
                                <?php else: ?>
                                    <span class="text-emerald-700 font-bold">₹0.00</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-500">
                                <?= formatCurrency($c['credit_limit']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <button onclick="viewCustomerProfile(<?= $c['id'] ?>)" title="View Ledger" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">
                                    <i class="fas fa-file-invoice"></i>
                                </button>
                                <?php if (hasPermission('customers.edit')): ?>
                                <button onclick="openEditCustomer(<?= htmlspecialchars(json_encode($c)) ?>)" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-800 text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Customer -->
<div id="modal-add-customer" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Register New Customer</h3>
            <button onclick="closeModal('modal-add-customer')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('customers/create') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Customer / Trust Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Sri Kapaleeshwarar Temple Trust" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="mobile" required placeholder="9840112233" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="contact@domain.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Customer Type</label>
                    <select name="customer_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="retail">Retail Walk-in / Collector</option>
                        <option value="wholesale">Wholesale Distributor</option>
                        <option value="temple_trust">Temple Trust / Devasthanam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Credit Limit (₹)</label>
                    <input type="number" name="credit_limit" value="0.00" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN Number (Optional)</label>
                <input type="text" name="gst_number" placeholder="33AAACT0123A1Z5" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                    <input type="text" name="city" value="Chennai" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State</label>
                    <input type="text" name="state" value="Tamil Nadu" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-customer')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Register Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Customer Ledger / Profile -->
<div id="modal-customer-ledger" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-3xl shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900" id="modal-cust-name">Customer Ledger</h3>
            <button onclick="closeModal('modal-customer-ledger')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-cust-body" class="space-y-4 text-xs"></div>
    </div>
</div>

<script>
async function viewCustomerProfile(id) {
    try {
        const res = await fetch(window.APP_URL + '/customers/profile/' + id);
        const data = await res.json();
        if (data.success && data.customer) {
            const c = data.customer;
            document.getElementById('modal-cust-name').textContent = c.name + ' - Statement & Ledger';
            let invHtml = '';
            (data.invoices || []).forEach(i => {
                invHtml += `
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3 font-mono font-bold text-slate-900">${i.invoice_no}</td>
                        <td class="py-2.5 px-3">${i.invoice_date}</td>
                        <td class="py-2.5 px-3 font-mono font-bold">₹${parseFloat(i.grand_total).toFixed(2)}</td>
                        <td class="py-2.5 px-3 font-mono text-emerald-700 font-bold">₹${parseFloat(i.paid_amount).toFixed(2)}</td>
                        <td class="py-2.5 px-3 uppercase text-[10px] font-bold">${i.payment_status}</td>
                    </tr>
                `;
            });

            document.getElementById('modal-cust-body').innerHTML = `
                <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono">
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Outstanding Due</span>
                        <span class="text-base font-black ${c.outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-700'}">₹${parseFloat(c.outstanding_balance).toFixed(2)}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Credit Limit</span>
                        <span class="text-base font-bold text-slate-800">₹${parseFloat(c.credit_limit).toFixed(2)}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Contact Mobile</span>
                        <span class="text-base font-bold text-slate-900">${c.mobile}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Purchase & Billing History</h5>
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold border-b border-slate-200">
                            <tr><th class="py-2 px-3">Invoice</th><th class="py-2 px-3">Date</th><th class="py-2 px-3">Total</th><th class="py-2 px-3">Paid</th><th class="py-2 px-3">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">${invHtml || '<tr><td colspan="5" class="py-4 text-center text-slate-400">No invoices on record.</td></tr>'}</tbody>
                    </table>
                </div>
            `;
            openModal('modal-customer-ledger');
        }
    } catch(e) { console.error(e); }
}
</script>
