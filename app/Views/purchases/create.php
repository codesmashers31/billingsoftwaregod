<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Create Sourcing Purchase Order</h1>
            <p class="text-xs text-slate-500">Record intake order from sthapathis. Completing will automatically increase live stock.</p>
        </div>
        <a href="<?= url('purchases') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-xl text-xs font-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <form action="<?= url('purchases/create') ?>" method="POST" data-ajax="true" class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Select Sthapathi / Foundry <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="">Choose Supplier...</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= sanitize($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Purchase Date</label>
                    <input type="date" name="purchase_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                        <option value="received">Received (Auto-Restock Stock Now)</option>
                        <option value="ordered">Ordered (Pending Delivery)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Idol Line Items</h3>
                <button type="button" onclick="addPurchaseRow()" class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold">
                    <i class="fas fa-plus mr-1"></i> Add Row
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">God Statue / Item</th>
                            <th class="py-2.5 px-3 text-center">Qty</th>
                            <th class="py-2.5 px-3 text-right">Unit Cost (₹)</th>
                            <th class="py-2.5 px-3 text-right">Total (₹)</th>
                            <th class="py-2.5 px-3 text-right">Remove</th>
                        </tr>
                    </thead>
                    <tbody id="purchase-items-body" class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 px-3">
                                <select name="items[0][product_id]" required onchange="onSelectProduct(this, 0)" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 outline-none">
                                    <option value="">Select Statue...</option>
                                    <?php foreach ($products as $pr): ?>
                                        <option value="<?= $pr['id'] ?>" data-cost="<?= $pr['purchase_price'] ?>"><?= sanitize($pr['name']) ?> (<?= $pr['sku'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <input type="number" name="items[0][quantity]" value="1" min="1" oninput="calcTotal(0)" class="w-20 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-center font-mono font-bold text-slate-900 outline-none">
                            </td>
                            <td class="py-2 px-3 text-right">
                                <input type="number" step="0.01" name="items[0][unit_cost]" id="cost-0" value="0.00" oninput="calcTotal(0)" class="w-28 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-right font-mono font-bold text-slate-900 outline-none">
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900" id="row-total-0">
                                ₹0.00
                            </td>
                            <td class="py-2 px-3 text-right">
                                <button type="button" onclick="this.closest('tr').remove(); calcGrandTotal();" class="text-rose-500 hover:text-rose-700 p-1"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grand Total -->
            <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-xs font-mono">
                <span class="text-slate-500 font-sans font-bold uppercase">Grand Total Amount:</span>
                <span id="purchase-grand-total" class="text-2xl font-black text-slate-900">₹0.00</span>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-check"></i> Save Sourcing Order
            </button>
        </div>
    </form>
</div>

<script>
let rowIndex = 1;
const productOptions = `<?php foreach ($products as $pr): ?><option value="<?= $pr['id'] ?>" data-cost="<?= $pr['purchase_price'] ?>"><?= sanitize($pr['name']) ?> (<?= $pr['sku'] ?>)</option><?php endforeach; ?>`;

function addPurchaseRow() {
    const idx = rowIndex++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="py-2 px-3">
            <select name="items[${idx}][product_id]" required onchange="onSelectProduct(this, ${idx})" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 outline-none">
                <option value="">Select Statue...</option>
                ${productOptions}
            </select>
        </td>
        <td class="py-2 px-3 text-center">
            <input type="number" name="items[${idx}][quantity]" value="1" min="1" oninput="calcTotal(${idx})" class="w-20 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-center font-mono font-bold text-slate-900 outline-none">
        </td>
        <td class="py-2 px-3 text-right">
            <input type="number" step="0.01" name="items[${idx}][unit_cost]" id="cost-${idx}" value="0.00" oninput="calcTotal(${idx})" class="w-28 bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 text-xs text-right font-mono font-bold text-slate-900 outline-none">
        </td>
        <td class="py-2 px-3 text-right font-mono font-bold text-slate-900" id="row-total-${idx}">
            ₹0.00
        </td>
        <td class="py-2 px-3 text-right">
            <button type="button" onclick="this.closest('tr').remove(); calcGrandTotal();" class="text-rose-500 hover:text-rose-700 p-1"><i class="fas fa-trash-alt"></i></button>
        </td>
    `;
    document.getElementById('purchase-items-body').appendChild(tr);
}

function onSelectProduct(select, idx) {
    const opt = select.selectedOptions[0];
    const cost = opt ? (opt.dataset.cost || 0) : 0;
    const costInput = document.getElementById('cost-' + idx);
    if (costInput) costInput.value = parseFloat(cost).toFixed(2);
    calcTotal(idx);
}

function calcTotal(idx) {
    const qtyInput = document.querySelector(`[name="items[${idx}][quantity]"]`);
    const costInput = document.getElementById('cost-' + idx);
    const totalEl = document.getElementById('row-total-' + idx);
    if (!qtyInput || !costInput || !totalEl) return;
    const total = (parseFloat(qtyInput.value) || 0) * (parseFloat(costInput.value) || 0);
    totalEl.textContent = '₹' + total.toFixed(2);
    calcGrandTotal();
}

function calcGrandTotal() {
    let grand = 0;
    document.querySelectorAll('[id^="row-total-"]').forEach(el => {
        const val = parseFloat(el.textContent.replace('₹', '')) || 0;
        grand += val;
    });
    document.getElementById('purchase-grand-total').textContent = '₹' + grand.toFixed(2);
}
</script>
