<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Sales Invoices & Billing Register</h1>
            <p class="text-xs text-slate-500">Search customer bills, track payment & due amounts, print tax invoices, or record installments</p>
        </div>
        <div class="flex items-center gap-2.5">
            <?php if (hasPermission('billing.pos')): ?>
            <a href="<?= url('billing') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all">
                <i class="fas fa-cash-register text-amber-600"></i> Open POS Terminal
            </a>
            <?php endif; ?>
            <?php if (hasPermission('billing.create')): ?>
            <a href="<?= url('billing/invoices/create') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i> Create New Invoice
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('billing/invoices') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Search Invoices</label>
                <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" placeholder="Invoice #, customer name, mobile, reference..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">From Date</label>
                <input type="date" name="date_from" value="<?= sanitize($filters['date_from'] ?? '') ?>" 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">To Date</label>
                <input type="date" name="date_to" value="<?= sanitize($filters['date_to'] ?? '') ?>" 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Payment Statuses</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid (Settled)</option>
                    <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="unpaid" <?= ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="overdue" <?= ($filters['payment_status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
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

    <!-- Invoices Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Invoice No & Type</th>
                        <th class="py-3.5 px-4">Date & Due Date</th>
                        <th class="py-3.5 px-4">Customer Details</th>
                        <th class="py-3.5 px-4">Total Amount</th>
                        <th class="py-3.5 px-4">Paid / Due</th>
                        <th class="py-3.5 px-4">Payment Status</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="8" class="py-14 text-center text-slate-400">
                            <i class="fas fa-file-invoice text-3xl mb-2 text-slate-300 block"></i>
                            <p class="font-bold text-slate-600 text-sm">No invoices recorded</p>
                            <p class="text-xs text-slate-400 mt-0.5">Click "Create New Invoice" to issue your first invoice.</p>
                        </td></tr>
                    <?php else: foreach ($invoices as $inv): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <!-- Invoice No & Type -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-black text-slate-900 text-sm block"><?= $inv['invoice_no'] ?></span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded <?= ($inv['invoice_type'] ?? 'tax_invoice') === 'proforma_invoice' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= ($inv['invoice_type'] ?? 'tax_invoice') === 'proforma_invoice' ? 'Proforma' : 'Tax Invoice' ?>
                                    </span>
                                    <?php if (!empty($inv['reference_no'])): ?>
                                        <span class="text-[10px] text-slate-400 font-mono">Ref: <?= sanitize($inv['reference_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Date & Due Date -->
                            <td class="py-3.5 px-4 text-slate-600">
                                <div class="font-semibold text-slate-800"><?= formatDate($inv['invoice_date']) ?></div>
                                <?php if (!empty($inv['due_date'])): ?>
                                    <div class="text-[10px] text-slate-400 font-mono">Due: <?= formatDate($inv['due_date']) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900"><?= sanitize($inv['customer_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= sanitize($inv['customer_mobile']) ?> <?= !empty($inv['customer_state']) ? '• ' . sanitize($inv['customer_state']) : '' ?></div>
                            </td>

                            <!-- Grand Total -->
                            <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm">
                                <?= formatCurrency($inv['grand_total']) ?>
                            </td>

                            <!-- Paid / Due -->
                            <td class="py-3.5 px-4 font-mono">
                                <div class="text-emerald-700 font-bold text-xs">Paid: <?= formatCurrency($inv['paid_amount']) ?></div>
                                <?php if ((float)$inv['due_amount'] > 0): ?>
                                    <div class="text-rose-600 font-bold text-xs">Due: <?= formatCurrency($inv['due_amount']) ?></div>
                                <?php else: ?>
                                    <div class="text-slate-400 text-[10px]">Settled</div>
                                <?php endif; ?>
                            </td>

                            <!-- Payment Status -->
                            <td class="py-3.5 px-4">
                                <?php if (!empty($inv['is_overdue'])): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px] border border-rose-300 animate-pulse">Overdue</span>
                                <?php elseif ($inv['payment_status'] === 'paid'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Paid</span>
                                <?php elseif ($inv['payment_status'] === 'partial'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200">Partial</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">Unpaid</span>
                                <?php endif; ?>
                            </td>

                            <!-- Invoice Status -->
                            <td class="py-3.5 px-4">
                                <?php if ($inv['status'] === 'cancelled'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">Cancelled</span>
                                <?php elseif ($inv['status'] === 'draft'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] border border-slate-300">Draft</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">Completed</span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                <a href="<?= url('billing/invoice/' . $inv['id']) ?>" title="View Invoice" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs inline-block">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($inv['status'] !== 'cancelled' && hasPermission('billing.edit')): ?>
                                <a href="<?= url('billing/invoices/edit/' . $inv['id']) ?>" title="Edit Invoice" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-amber-800 text-xs inline-block">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <a href="<?= url('billing/invoice/' . $inv['id'] . '/pdf') ?>" target="_blank" title="Download PDF" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-100 text-indigo-700 text-xs inline-block">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="<?= url('billing/invoice/' . $inv['id'] . '/print?type=a4') ?>" target="_blank" title="Print A4" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs inline-block">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if ((float)$inv['due_amount'] > 0 && $inv['status'] !== 'cancelled' && hasPermission('billing.create')): ?>
                                <button onclick="openPaymentModal(<?= htmlspecialchars(json_encode($inv)) ?>)" title="Record Payment" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs inline-block">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($inv['status'] !== 'cancelled' && hasPermission('billing.cancel')): ?>
                                <button onclick="cancelInvoice(<?= $inv['id'] ?>, '<?= $inv['invoice_no'] ?>')" title="Cancel Invoice" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs inline-block">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div>
                Showing page <span class="font-bold text-slate-800"><?= $pagination['page'] ?></span> of <span class="font-bold text-slate-800"><?= $pagination['total_pages'] ?></span> (<?= $pagination['total'] ?> total invoices)
            </div>
            <div class="flex items-center gap-1">
                <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                    <a href="?page=<?= $p ?>&search=<?= urlencode($filters['search'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>&payment_status=<?= urlencode($filters['payment_status'] ?? '') ?>&date_from=<?= urlencode($filters['date_from'] ?? '') ?>&date_to=<?= urlencode($filters['date_to'] ?? '') ?>" 
                       class="px-3 py-1.5 rounded-lg <?= $p === (int)$pagination['page'] ? 'bg-amber-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Record Payment -->
<div id="modal-record-payment" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-emerald-600"></i> Record Payment
            </h3>
            <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="record-payment-form" onsubmit="submitPayment(event)" class="space-y-4">
            <input type="hidden" id="pay_invoice_id">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1">
                <div class="flex justify-between"><span class="text-slate-500">Invoice Number:</span><span id="m_pay_inv_no" class="font-mono font-bold text-slate-900"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Customer:</span><span id="m_pay_cust_name" class="font-bold text-slate-900"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Total Invoice Amount:</span><span id="m_pay_grand_total" class="font-mono font-bold text-slate-900"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Outstanding Due:</span><span id="m_pay_due_amount" class="font-mono font-black text-rose-600"></span></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Amount (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0.01" id="m_pay_amount" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-900 font-mono font-bold outline-none focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                    <select id="m_pay_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-emerald-500">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / QR</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Date</label>
                    <input type="date" id="m_pay_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Transaction Reference (Optional)</label>
                <input type="text" id="m_pay_ref" placeholder="UPI Ref / Cheque No / Auth Code" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 outline-none focus:border-emerald-500">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-md transition-all">Record Payment</button>
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

function openPaymentModal(inv) {
    document.getElementById('pay_invoice_id').value = inv.id;
    document.getElementById('m_pay_inv_no').textContent = inv.invoice_no;
    document.getElementById('m_pay_cust_name').textContent = inv.customer_name;
    document.getElementById('m_pay_grand_total').textContent = '₹' + parseFloat(inv.grand_total).toFixed(2);
    document.getElementById('m_pay_due_amount').textContent = '₹' + parseFloat(inv.due_amount).toFixed(2);
    document.getElementById('m_pay_amount').value = parseFloat(inv.due_amount).toFixed(2);
    document.getElementById('m_pay_amount').max = parseFloat(inv.due_amount).toFixed(2);
    document.getElementById('modal-record-payment').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('modal-record-payment').classList.add('hidden');
}

async function submitPayment(e) {
    e.preventDefault();
    const id = document.getElementById('pay_invoice_id').value;
    const amount = document.getElementById('m_pay_amount').value;
    const method = document.getElementById('m_pay_method').value;
    const date = document.getElementById('m_pay_date').value;
    const ref = document.getElementById('m_pay_ref').value;

    const formData = new FormData();
    formData.append('_csrf_token', window.CSRF_TOKEN);
    formData.append('amount', amount);
    formData.append('payment_method', method);
    formData.append('payment_date', date);
    formData.append('transaction_reference', ref);

    try {
        const res = await fetch(getCleanAppUrl('/billing/invoice/' + id + '/payment'), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message || 'Payment recorded.');
            closePaymentModal();
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast('error', data.message || 'Failed to record payment.');
        }
    } catch(err) {
        console.error(err);
        showToast('error', 'Network error.');
    }
}

async function cancelInvoice(id, invNo) {
    if (!confirm(`Are you sure you want to cancel invoice ${invNo}? All sold products will be automatically restocked back to inventory.`)) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(getCleanAppUrl('/billing/invoice/' + id + '/cancel'), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast('error', data.message);
        }
    } catch(e) { console.error(e); }
}
</script>
