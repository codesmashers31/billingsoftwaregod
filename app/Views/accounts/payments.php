<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Payment Receipts & Supplier Disbursements</h1>
            <p class="text-xs text-slate-500">Collect customer ledger dues, pay sthapathi foundry invoices, and record direct vouchers</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openPaymentModal('received')" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-hand-holding-usd"></i> Receive Customer Payment
            </button>
            <button onclick="openPaymentModal('paid')" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-paper-plane"></i> Pay Supplier / Sthapathi
            </button>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Payment No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Party</th>
                        <th class="py-3.5 px-4">Account</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Mode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="7" class="py-12 text-center text-slate-400 font-sans">No payment records found.</td></tr>
                    <?php else: foreach ($payments as $pay): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900"><?= $pay['payment_no'] ?></td>
                            <td class="py-3.5 px-4 text-slate-600 font-sans"><?= formatDate($pay['payment_date']) ?></td>
                            <td class="py-3.5 px-4 font-sans">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $pay['payment_type'] === 'received' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                    <?= $pay['payment_type'] === 'received' ? 'Receipt (+)' : 'Payment (-)' ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-sans font-bold text-slate-900"><?= sanitize($pay['party_name'] ?? 'Direct') ?></td>
                            <td class="py-3.5 px-4 font-sans text-slate-700"><?= sanitize($pay['account_name']) ?></td>
                            <td class="py-3.5 px-4 font-black text-sm <?= $pay['payment_type'] === 'received' ? 'text-emerald-700' : 'text-rose-600' ?>">
                                <?= formatCurrency($pay['amount']) ?>
                            </td>
                            <td class="py-3.5 px-4 uppercase text-[10px] font-bold text-slate-500 font-sans"><?= $pay['payment_method'] ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Payment Voucher -->
<div id="modal-new-payment" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900" id="modal-pay-voucher-title">Record Payment Voucher</h3>
            <button onclick="closeModal('modal-new-payment')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= url('accounts/payments') ?>" method="POST" data-ajax="true" data-reload="true" class="space-y-3.5">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_type" id="form-pay-type" value="received">

            <div id="div-customer-select">
                <label class="block text-xs font-bold text-slate-700 mb-1">Select Customer</label>
                <select name="customer_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="">Choose Customer...</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?> (Due: <?= formatCurrency($c['outstanding_balance']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="div-supplier-select" class="hidden">
                <label class="block text-xs font-bold text-slate-700 mb-1">Select Sthapathi / Foundry</label>
                <select name="supplier_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <option value="">Choose Sthapathi...</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= sanitize($s['name']) ?> (Payable: <?= formatCurrency($s['outstanding_balance']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Account (Deposit / Source)</label>
                <select name="account_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                    <?php foreach ($accounts as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= sanitize($a['name']) ?> (<?= formatCurrency($a['current_balance']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono font-bold focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Mode</label>
                    <select name="payment_method" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / QR</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Remarks / Reference</label>
                <input type="text" name="notes" placeholder="e.g. Cleared advance bill..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-new-payment')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">Cancel</button>
                <button type="submit" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">Save Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(type) {
    document.getElementById('form-pay-type').value = type;
    if (type === 'received') {
        document.getElementById('modal-pay-voucher-title').textContent = 'Receive Customer Payment';
        document.getElementById('div-customer-select').classList.remove('hidden');
        document.getElementById('div-supplier-select').classList.add('hidden');
    } else {
        document.getElementById('modal-pay-voucher-title').textContent = 'Disburse Sthapathi / Supplier Payout';
        document.getElementById('div-customer-select').classList.add('hidden');
        document.getElementById('div-supplier-select').classList.remove('hidden');
    }
    openModal('modal-new-payment');
}
</script>
