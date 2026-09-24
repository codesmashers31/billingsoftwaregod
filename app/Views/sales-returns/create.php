<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Process Sales Return</h1>
            <p class="text-xs text-slate-500">Lookup original invoice, select items to return, and issue customer refund</p>
        </div>
        <a href="<?= url('sales-returns') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-xl text-xs font-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <!-- Step 1: Invoice Lookup -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fas fa-search text-amber-600"></i> Step 1: Lookup Original Sales Invoice
        </h3>

        <div class="flex gap-3">
            <input type="text" id="return-invoice-lookup" placeholder="Enter invoice number (e.g. INV-202608-0001)..." 
                   class="flex-1 bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
            <button type="button" onclick="lookupInvoiceForReturn()" class="gold-btn px-5 py-2 rounded-xl text-xs font-bold shadow-sm">
                Lookup Invoice
            </button>
        </div>
    </div>

    <!-- Step 2: Return Items Form (Hidden until lookup) -->
    <form id="form-sales-return" action="<?= url('sales-returns/create') ?>" method="POST" data-ajax="true" class="hidden space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="invoice_id" id="return-invoice-id">

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-boxes text-amber-600"></i> Step 2: Select Items to Return
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">God Statue Item</th>
                            <th class="py-2.5 px-3 text-center">Billed Qty</th>
                            <th class="py-2.5 px-3 text-center">Return Qty</th>
                            <th class="py-2.5 px-3 text-right">Unit Rate (₹)</th>
                        </tr>
                    </thead>
                    <tbody id="return-items-body" class="divide-y divide-slate-100 font-mono"></tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-money-bill-wave text-amber-600"></i> Step 3: Refund Settlement
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Refund Settlement Mode</label>
                    <select name="refund_mode" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="cash">Cash Refund</option>
                        <option value="credit">Store Credit / Customer Ledger</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reason for Return</label>
                    <input type="text" name="reason" placeholder="e.g. Size exchange / Customer requested Panchaloha upgrade..." 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-check"></i> Process Return & Restock
            </button>
        </div>
    </form>
</div>

<script>
async function lookupInvoiceForReturn() {
    const invNo = document.getElementById('return-invoice-lookup').value.trim();
    if (!invNo) return alert('Please enter an invoice number');
    try {
        const res = await fetch(window.APP_URL + '/sales-returns/lookup?invoice_no=' + encodeURIComponent(invNo));
        const data = await res.json();
        if (data.success && data.invoice) {
            document.getElementById('return-invoice-id').value = data.invoice.id;
            let rows = '';
            (data.items || []).forEach((item, idx) => {
                rows += `
                    <tr>
                        <td class="py-2.5 px-3 font-sans font-bold text-slate-900">${item.product_name}
                            <input type="hidden" name="items[${idx}][invoice_item_id]" value="${item.id}">
                            <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${idx}][unit_price]" value="${item.unit_price}">
                        </td>
                        <td class="py-2.5 px-3 text-center font-bold">${item.quantity}</td>
                        <td class="py-2.5 px-3 text-center">
                            <input type="number" name="items[${idx}][quantity]" value="0" min="0" max="${item.quantity}" class="w-16 bg-slate-50 border border-slate-300 rounded-lg text-center font-bold text-slate-900 py-1 text-xs">
                        </td>
                        <td class="py-2.5 px-3 text-right">₹${parseFloat(item.unit_price).toFixed(2)}</td>
                    </tr>
                `;
            });
            document.getElementById('return-items-body').innerHTML = rows;
            document.getElementById('form-sales-return').classList.remove('hidden');
        } else {
            alert(data.message || 'Invoice not found.');
        }
    } catch(e) { console.error(e); }
}
</script>
