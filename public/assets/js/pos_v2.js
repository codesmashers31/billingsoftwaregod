/**
 * Enterprise Sacred POS Billing Terminal JS Engine
 */

let posCart = [];
let selectedCustomer = { id: 1, name: 'Walk-in Retail Customer', mobile: '9999999999', state: 'Tamil Nadu' };
let heldBills = JSON.parse(localStorage.getItem('god_pos_held_bills') || '[]');
let activeCategoryId = null;

document.addEventListener('DOMContentLoaded', () => {
    initPosEventListeners();
    renderCart();
    renderHeldBillsCount();

    // Auto focus search on POS load
    const searchInput = document.getElementById('pos-search-input');
    if (searchInput) {
        searchInput.focus();
    }
});

function initPosEventListeners() {
    // 1. Instant Search with Debounce
    const searchInput = document.getElementById('pos-search-input');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchProducts(e.target.value);
            }, 180);
        });

        // Handle Enter key for direct barcode scan
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = searchInput.value.trim();
                if (code) {
                    scanBarcodeDirect(code);
                }
            }
        });
    }

    // 2. Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        // Don't trigger shortcuts if user is typing inside modal inputs
        const activeModal = document.querySelector('.pos-modal:not(.hidden)');
        
        if (e.key === 'F2') {
            e.preventDefault();
            const s = document.getElementById('pos-search-input');
            if (s) { s.focus(); s.select(); }
        } else if (e.key === 'F4') {
            e.preventDefault();
            openCustomerModal();
        } else if (e.key === 'F8') {
            e.preventDefault();
            if (posCart.length > 0) openPaymentModal();
        } else if (e.key === 'F9') {
            e.preventDefault();
            holdCurrentBill();
        } else if (e.key === 'Escape') {
            if (activeModal) {
                closeModal(activeModal.id);
            }
        }
    });

    // 3. Overall Discount Input
    const overallDiscInput = document.getElementById('pos-overall-discount');
    if (overallDiscInput) {
        overallDiscInput.addEventListener('input', () => {
            renderCart();
        });
    }
}

/**
 * Product Search & Catalog Filter
 */
async function searchProducts(keyword = '') {
    const grid = document.getElementById('pos-product-grid');
    if (!grid) return;

    let url = `${window.APP_URL}/billing/search-products?q=${encodeURIComponent(keyword)}`;
    if (activeCategoryId) {
        url += `&category_id=${activeCategoryId}`;
    }

    try {
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        renderProductGrid(data.data || []);
    } catch (err) {
        console.error('POS Search Error:', err);
    }
}

function filterCategory(categoryId, btnEl) {
    activeCategoryId = categoryId;
    document.querySelectorAll('.cat-pill-btn').forEach(b => {
        b.classList.remove('bg-amber-600', 'text-white', 'shadow-sm');
        b.classList.add('bg-slate-100', 'text-slate-600');
    });

    if (btnEl) {
        btnEl.classList.remove('bg-slate-100', 'text-slate-600');
        btnEl.classList.add('bg-amber-600', 'text-white', 'shadow-sm');
    }

    const searchInput = document.getElementById('pos-search-input');
    searchProducts(searchInput ? searchInput.value : '');
}

function renderProductGrid(products) {
    const grid = document.getElementById('pos-product-grid');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full py-16 text-center text-slate-400">
                <i class="fas fa-box-open text-4xl text-slate-600 mb-3 block"></i>
                <p class="font-medium">No matching god statues or items found</p>
                <p class="text-xs text-slate-500 mt-1">Try another search keyword or clear category filter</p>
            </div>
        `;
        return;
    }

    let html = '';
    products.forEach(p => {
        const isOutOfStock = parseInt(p.current_stock) <= 0;
        const stockBadge = isOutOfStock 
            ? '<span class="bg-rose-50 text-rose-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-rose-200 whitespace-nowrap shrink-0">Out of Stock</span>'
            : `<span class="bg-[#f0fdf4] text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-[#bbf7d0] whitespace-nowrap shrink-0">${p.current_stock} in stock</span>`;

        const statueSpecs = p.material ? `${p.material} ${p.height ? '• ' + p.height + '"' : ''}` : '';

        html += `
            <div onclick="addToCartById(${p.id}, '${escapeHtml(p.name)}', ${p.selling_price}, ${p.discount_percent || 0}, ${p.gst_percent || 12}, ${p.current_stock})" 
                 class="pos-product-card h-[144px] bg-white border border-slate-100 shadow-sm rounded-2xl p-3.5 flex flex-col cursor-pointer group hover:border-amber-300 hover:shadow-md transition-all ${isOutOfStock ? 'opacity-50 pointer-events-none' : ''}">
                <div>
                      <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold text-amber-700 bg-[#fff8ef] px-2 py-1 rounded-md border border-amber-200 whitespace-nowrap truncate">${p.code || p.sku}</span>
                        ${stockBadge}
                    </div>
                    <h4 class="text-[13px] font-extrabold text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 h-[36px] leading-snug mt-2">
                        ${p.name}
                    </h4>
                </div>
                <div class="mt-auto pt-3 flex items-center justify-between">
                    <div class="flex items-baseline">
                        <span class="text-[10px] text-slate-400 font-medium">Rate:</span>
                        <span class="text-[14px] font-black text-slate-900 font-mono ml-1">₹${parseFloat(p.selling_price).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                    </div>
                    <button class="w-7 h-7 rounded-lg bg-[#fef3c7] text-amber-700 group-hover:bg-amber-500 group-hover:text-white flex items-center justify-center transition-all shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>
        `;
    });

    grid.innerHTML = html;
}

/**
 * Barcode Scanner Event
 */
async function scanBarcodeDirect(code) {
    try {
        const formData = new FormData();
        formData.append('code', code);
        formData.append('_csrf_token', window.CSRF_TOKEN);

        const res = await fetch(`${window.APP_URL}/billing/scan`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await res.json();

        if (result.success && result.product) {
            const p = result.product;
            addToCartById(p.id, p.name, p.selling_price, p.discount_percent || 0, p.gst_percent || 12, p.current_stock);
            showToast('success', `Scanned: ${p.name}`);
            const searchInput = document.getElementById('pos-search-input');
            if (searchInput) { searchInput.value = ''; searchInput.focus(); }
        } else {
            showToast('error', result.message || 'Product not found.');
        }
    } catch (err) {
        console.error('Barcode scan error:', err);
    }
}

/**
 * Cart Operations
 */
let activeBillGstMode = 'item'; // 'item', '0', '5', '12', '18', '20', '28', 'custom'
let activeCustomBillGst = null;

function changeBillGstPreset(val) {
    activeBillGstMode = val;
    const customInput = document.getElementById('pos-custom-tax-rate');
    if (val === 'custom') {
        if (customInput) {
            customInput.classList.remove('hidden');
            customInput.focus();
            if (activeCustomBillGst !== null) {
                customInput.value = activeCustomBillGst;
            }
        }
    } else {
        if (customInput) customInput.classList.add('hidden');
        if (val !== 'item') {
            const numVal = parseFloat(val) || 0;
            posCart.forEach(item => {
                item.gst_percent = numVal;
            });
        }
    }
    renderCart();
}

function onCustomGstInput(val) {
    const rate = Math.max(0, Math.min(100, parseFloat(val) || 0));
    activeCustomBillGst = rate;
    posCart.forEach(item => {
        item.gst_percent = rate;
    });
    renderCart();
}

function updateItemGst(index, val) {
    if (!posCart[index]) return;
    const rate = Math.max(0, Math.min(100, parseFloat(val) || 0));
    posCart[index].gst_percent = rate;
    
    // Check if all items now match a preset or are heterogeneous
    const distinctRates = Array.from(new Set(posCart.map(i => parseFloat(i.gst_percent) || 0)));
    const presetSelect = document.getElementById('pos-tax-preset');
    if (presetSelect) {
        if (distinctRates.length === 1) {
            const single = String(distinctRates[0]);
            if (['0', '5', '12', '18', '20', '28'].includes(single)) {
                presetSelect.value = single;
                activeBillGstMode = single;
            } else {
                presetSelect.value = 'custom';
                activeBillGstMode = 'custom';
                activeCustomBillGst = distinctRates[0];
                const custInp = document.getElementById('pos-custom-tax-rate');
                if (custInp) { custInp.classList.remove('hidden'); custInp.value = distinctRates[0]; }
            }
        } else {
            presetSelect.value = 'item';
            activeBillGstMode = 'item';
            const custInp = document.getElementById('pos-custom-tax-rate');
            if (custInp) custInp.classList.add('hidden');
        }
    }
    renderCart();
}

function addToCartById(id, name, unitPrice, discountPercent, gstPercent, maxStock) {
    const existingIndex = posCart.findIndex(item => item.product_id === id);

    let initialGst = parseFloat(gstPercent) || 12;
    if (activeBillGstMode === 'custom' && activeCustomBillGst !== null) {
        initialGst = activeCustomBillGst;
    } else if (activeBillGstMode !== 'item') {
        initialGst = parseFloat(activeBillGstMode) || 0;
    }

    if (existingIndex > -1) {
        if (posCart[existingIndex].quantity + 1 > maxStock) {
            showToast('error', `Cannot add more. Only ${maxStock} units available.`);
            return;
        }
        posCart[existingIndex].quantity += 1;
    } else {
        if (maxStock <= 0) {
            showToast('error', 'Item is out of stock.');
            return;
        }
        posCart.push({
            product_id: id,
            name: name,
            unit_price: parseFloat(unitPrice),
            quantity: 1,
            discount_percent: parseFloat(discountPercent) || 0,
            gst_percent: initialGst,
            max_stock: maxStock
        });
    }

    renderCart();
}

function updateItemQty(index, delta) {
    if (!posCart[index]) return;
    const newQty = posCart[index].quantity + delta;

    if (newQty <= 0) {
        posCart.splice(index, 1);
    } else if (newQty > posCart[index].max_stock) {
        showToast('error', `Max stock available: ${posCart[index].max_stock}`);
    } else {
        posCart[index].quantity = newQty;
    }

    renderCart();
}

function updateItemDiscount(index, disc) {
    if (!posCart[index]) return;
    posCart[index].discount_percent = Math.min(100, Math.max(0, parseFloat(disc) || 0));
    renderCart();
}

function removeFromCart(index) {
    posCart.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (posCart.length === 0) return;
    if (confirm('Are you sure you want to clear the billing cart?')) {
        posCart = [];
        renderCart();
        showToast('info', 'Billing cart cleared.');
    }
}

/**
 * Cart Calculation & DOM Rendering
 */
function renderCart() {
    const cartContainer = document.getElementById('pos-cart-items');
    const badgeEl = document.getElementById('cart-items-count');
    if (!cartContainer) return;

    if (badgeEl) badgeEl.textContent = posCart.reduce((sum, i) => sum + i.quantity, 0);

    if (posCart.length === 0) {
        cartContainer.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center text-slate-400 py-12">
                <i class="fas fa-shopping-cart text-4xl text-slate-300 mb-3"></i>
                <p class="font-medium text-slate-600">Cart is empty</p>
                <p class="text-xs text-slate-400 mt-1">Scan barcode or click items to add</p>
            </div>
        `;
        const taxLabelEl = document.getElementById('summary-tax-label');
        if (taxLabelEl) taxLabelEl.textContent = 'GST Tax:';
        updateTotalsUI(0, 0, 0, 0, 0, 0);
        return;
    }

    let subtotal = 0;
    let itemDiscountTotal = 0;
    let taxTotal = 0;
    let html = '';

    posCart.forEach((item, idx) => {
        const gross = item.quantity * item.unit_price;
        const discAmt = (gross * item.discount_percent) / 100;
        const net = gross - discAmt;
        const taxAmt = (net * item.gst_percent) / 100;
        const lineTotal = net + taxAmt;

        subtotal += gross;
        itemDiscountTotal += discAmt;
        taxTotal += taxAmt;

        html += `
            <div class="bg-white border border-slate-200 hover:border-amber-300 shadow-xs rounded-xl p-3 mb-2 flex items-center justify-between transition-all">
                <div class="flex-1 pr-3">
                    <h5 class="text-xs font-bold text-slate-900 line-clamp-1">${item.name}</h5>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-slate-700">₹${item.unit_price.toFixed(2)}</span>
                        <div class="inline-flex items-center gap-1 bg-amber-50/80 border border-amber-200 rounded px-1.5 py-0.5" title="Dynamic GST Tax Rate">
                            <span class="text-[10px] font-bold text-amber-800">GST:</span>
                            <input type="number" min="0" max="100" step="0.5" value="${item.gst_percent}" 
                                   onchange="updateItemGst(${idx}, this.value)" 
                                   title="Edit GST percentage for this item"
                                   class="w-10 bg-white border border-amber-300 rounded px-1 py-0 text-center font-mono font-bold text-[10px] text-slate-800 outline-none focus:border-amber-500">
                            <span class="text-[10px] text-amber-800 font-bold">%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center bg-slate-100 rounded-lg border border-slate-200 p-0.5">
                        <button onclick="updateItemQty(${idx}, -1)" class="w-6 h-6 rounded bg-white text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs font-bold shadow-xs">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="w-7 text-center text-xs font-bold text-slate-900 font-mono">${item.quantity}</span>
                        <button onclick="updateItemQty(${idx}, 1)" class="w-6 h-6 rounded bg-white text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs font-bold shadow-xs">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="text-right min-w-[70px]">
                        <span class="text-xs font-black text-slate-900 font-mono block">₹${lineTotal.toFixed(2)}</span>
                        <span class="text-[9px] text-slate-400 font-mono">tax: ₹${taxAmt.toFixed(2)}</span>
                    </div>

                    <button onclick="removeFromCart(${idx})" class="text-slate-400 hover:text-rose-600 p-1 transition-colors" title="Remove item">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        `;
    });

    cartContainer.innerHTML = html;

    // Distinct tax rate label calculation
    const distinctRates = Array.from(new Set(posCart.map(i => parseFloat(i.gst_percent) || 0)));
    let taxLabel = 'GST Tax';
    if (distinctRates.length === 1) {
        taxLabel += ` (${distinctRates[0]}%):`;
    } else if (distinctRates.length > 1) {
        taxLabel += ` (${distinctRates.join('%, ')}%):`;
    } else {
        taxLabel += ':';
    }
    const taxLabelEl = document.getElementById('summary-tax-label');
    if (taxLabelEl) taxLabelEl.textContent = taxLabel;

    // Overall discount calculation in Rupees (₹)
    const overallDiscInput = document.getElementById('pos-overall-discount');
    let overallDiscAmt = overallDiscInput ? (parseFloat(overallDiscInput.value) || 0) : 0;
    if (isNaN(overallDiscAmt) || overallDiscAmt < 0) overallDiscAmt = 0;

    const netSubtotal = Math.max(0, subtotal - itemDiscountTotal);
    if (overallDiscAmt > netSubtotal) {
        overallDiscAmt = netSubtotal;
    }

    const totalBeforeRound = (netSubtotal - overallDiscAmt) + taxTotal;
    const grandTotal = Math.max(0, Math.round(totalBeforeRound));
    const roundOff = (grandTotal - totalBeforeRound);

    updateTotalsUI(subtotal, itemDiscountTotal + overallDiscAmt, taxTotal, roundOff, grandTotal);
}

function updateTotalsUI(subtotal, totalDisc, totalTax, roundOff, grandTotal) {
    const elSubtotal = document.getElementById('summary-subtotal');
    const elDiscount = document.getElementById('summary-discount');
    const elTax = document.getElementById('summary-tax');
    const elRound = document.getElementById('summary-roundoff');
    const elGrand = document.getElementById('summary-grandtotal');
    const elPayBtn = document.getElementById('btn-pos-pay');

    if (elSubtotal) elSubtotal.textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (elDiscount) elDiscount.textContent = '-₹' + totalDisc.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (elTax) elTax.textContent = '₹' + totalTax.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (elRound) elRound.textContent = (roundOff >= 0 ? '+' : '') + '₹' + roundOff.toFixed(2);
    if (elGrand) elGrand.textContent = '₹' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (elPayBtn) {
        elPayBtn.disabled = posCart.length === 0;
        elPayBtn.innerHTML = `<i class="fas fa-check-circle mr-2"></i>Pay ₹${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})} [F8]`;
    }
}

/**
 * Customer Selection
 */
function openCustomerModal() {
    openModal('pos-customer-modal');
    const searchInput = document.getElementById('cust-search-input');
    if (searchInput) { searchInput.value = ''; searchInput.focus(); }
}

async function searchCustomerPos(term = '') {
    try {
        const res = await fetch(`${window.APP_URL}/customers/search?q=${encodeURIComponent(term)}`);
        const data = await res.json();
        const listContainer = document.getElementById('pos-customer-list');
        if (!listContainer) return;

        let html = '';
        (data.data || []).forEach(c => {
            html += `
                <div onclick="selectCustomer(${c.id}, '${escapeHtml(c.name)}', '${c.mobile}', '${escapeHtml(c.state || 'Tamil Nadu')}')" 
                     class="p-3 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg cursor-pointer flex items-center justify-between mb-2">
                    <div>
                        <div class="font-semibold text-white text-sm">${c.name}</div>
                        <div class="text-xs text-slate-400 font-mono">${c.mobile} • ${c.customer_code}</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded bg-amber-500/20 text-amber-300 font-medium">Select</span>
                </div>
            `;
        });
        listContainer.innerHTML = html;
    } catch (err) {
        console.error(err);
    }
}

function selectCustomer(id, name, mobile, state) {
    selectedCustomer = { id, name, mobile, state };
    const nameEl = document.getElementById('selected-customer-name');
    const mobileEl = document.getElementById('selected-customer-mobile');
    if (nameEl) nameEl.textContent = name;
    if (mobileEl) mobileEl.textContent = mobile;
    closeModal('pos-customer-modal');
    showToast('info', `Customer set to: ${name}`);
}

/**
 * Hold & Resume Bills
 */
function holdCurrentBill() {
    if (posCart.length === 0) {
        showToast('error', 'Cart is empty. Nothing to hold.');
        return;
    }

    const billObj = {
        id: 'HOLD-' + Date.now(),
        timestamp: new Date().toLocaleTimeString(),
        customer: selectedCustomer,
        cart: [...posCart],
        overallDiscount: document.getElementById('pos-overall-discount')?.value || 0
    };

    heldBills.push(billObj);
    localStorage.setItem('god_pos_held_bills', JSON.stringify(heldBills));

    posCart = [];
    renderCart();
    renderHeldBillsCount();
    showToast('success', 'Bill placed on hold! You may resume it anytime.');
}

function renderHeldBillsCount() {
    const badge = document.getElementById('held-bills-badge');
    if (badge) badge.textContent = heldBills.length;
}

function openHeldBillsModal() {
    const list = document.getElementById('held-bills-list');
    if (!list) return;

    if (heldBills.length === 0) {
        list.innerHTML = `<div class="p-8 text-center text-slate-400">No suspended/held bills at present.</div>`;
    } else {
        let html = '';
        heldBills.forEach((b, idx) => {
            const count = b.cart.reduce((s, i) => s + i.quantity, 0);
            html += `
                <div class="p-3 bg-slate-800 border border-slate-700 rounded-lg flex items-center justify-between mb-2">
                    <div>
                        <span class="text-xs font-mono text-amber-400 font-bold">${b.id} (${b.timestamp})</span>
                        <div class="text-sm font-semibold text-white">${b.customer.name}</div>
                        <div class="text-xs text-slate-400">${count} item(s)</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="resumeBill(${idx})" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded text-xs font-bold">
                            Resume
                        </button>
                        <button onclick="deleteHeldBill(${idx})" class="p-1.5 text-rose-400 hover:text-rose-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        list.innerHTML = html;
    }

    openModal('pos-held-modal');
}

function resumeBill(index) {
    const bill = heldBills[index];
    if (!bill) return;

    posCart = [...bill.cart];
    selectedCustomer = bill.customer;
    selectCustomer(bill.customer.id, bill.customer.name, bill.customer.mobile, bill.customer.state);

    const overallDiscInput = document.getElementById('pos-overall-discount');
    if (overallDiscInput) overallDiscInput.value = bill.overallDiscount || 0;

    heldBills.splice(index, 1);
    localStorage.setItem('god_pos_held_bills', JSON.stringify(heldBills));

    renderCart();
    renderHeldBillsCount();
    closeModal('pos-held-modal');
    showToast('success', 'Suspended bill resumed into active cart.');
}

function deleteHeldBill(index) {
    heldBills.splice(index, 1);
    localStorage.setItem('god_pos_held_bills', JSON.stringify(heldBills));
    renderHeldBillsCount();
    openHeldBillsModal();
}

function calculatePosBalance() {
    const elGrand = document.getElementById('summary-grandtotal');
    const grandVal = parseFloat(elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : 0) || 0;
    const paidInput = document.getElementById('modal-pay-amount');
    const balanceInput = document.getElementById('modal-balance-amount');

    let paidVal = parseFloat(paidInput?.value || 0);
    if (isNaN(paidVal) || paidVal < 0) paidVal = 0;

    // If exactly one payment method is checked, sync this paid value into that method's input
    const checkedBoxes = Array.from(document.querySelectorAll('input[name="payment_methods[]"]:checked')).map(cb => cb.value);
    if (checkedBoxes.length === 1) {
        const methodMap = {
            'cash': 'pos_payment_amount_cash',
            'upi': 'pos_payment_amount_upi',
            'bank_transfer': 'pos_payment_amount_bank',
            'credit': 'pos_payment_amount_credit'
        };
        const activeInputId = methodMap[checkedBoxes[0]];
        const activeInput = document.getElementById(activeInputId);
        if (activeInput && document.activeElement !== activeInput) {
            if (checkedBoxes[0] === 'credit') {
                activeInput.value = grandVal > 0 ? grandVal.toFixed(2) : '0.00';
            } else {
                activeInput.value = paidVal > 0 ? paidVal.toFixed(2) : '';
            }
        }
    }

    const balance = Math.max(0, grandVal - paidVal);

    if (balanceInput) {
        balanceInput.value = balance.toFixed(2);
    }

    // If credit checkbox is checked and there are other methods checked, sync remaining balance to credit input
    const creditChk = document.getElementById('pos_chk_credit');
    const creditInp = document.getElementById('pos_payment_amount_credit');
    if (creditChk && creditChk.checked && creditInp && document.activeElement !== creditInp) {
        if (checkedBoxes.length > 1) {
            creditInp.value = balance > 0 ? balance.toFixed(2) : '0.00';
        }
    }
}
window.calculatePosBalance = calculatePosBalance;

function togglePosPaymentAmount(checkbox, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const elGrand = document.getElementById('summary-grandtotal');
    const grandVal = parseFloat(elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : 0) || 0;

    if (checkbox.checked) {
        input.classList.remove('hidden');
        // Auto-fill remaining balance if input is empty
        if (input.value === '' || parseFloat(input.value) === 0) {
            const otherPaid = getPosOtherMethodsPaid(inputId);
            const remaining = Math.max(0, grandVal - otherPaid);
            if (checkbox.value === 'credit' && document.querySelectorAll('input[name="payment_methods[]"]:checked').length === 1) {
                input.value = remaining > 0 ? remaining.toFixed(2) : '0.00';
            } else if (remaining > 0) {
                input.value = remaining.toFixed(2);
            }
        }
        input.focus();
    } else {
        input.classList.add('hidden');
        input.value = '';
    }

    calculatePosTotalPaidFromMethods();
}
window.togglePosPaymentAmount = togglePosPaymentAmount;

function getPosOtherMethodsPaid(excludeInputId) {
    let sum = 0;
    const methodIds = ['pos_payment_amount_cash', 'pos_payment_amount_upi', 'pos_payment_amount_bank'];
    methodIds.forEach(id => {
        if (id !== excludeInputId) {
            const el = document.getElementById(id);
            if (el && !el.classList.contains('hidden')) {
                sum += (parseFloat(el.value) || 0);
            }
        }
    });
    return sum;
}

function calculatePosTotalPaidFromMethods() {
    const cashInput = document.getElementById('pos_payment_amount_cash');
    const upiInput = document.getElementById('pos_payment_amount_upi');
    const bankInput = document.getElementById('pos_payment_amount_bank');
    const creditInput = document.getElementById('pos_payment_amount_credit');

    const cash = (cashInput && !cashInput.classList.contains('hidden')) ? (parseFloat(cashInput.value) || 0) : 0;
    const upi = (upiInput && !upiInput.classList.contains('hidden')) ? (parseFloat(upiInput.value) || 0) : 0;
    const bank = (bankInput && !bankInput.classList.contains('hidden')) ? (parseFloat(bankInput.value) || 0) : 0;
    const credit = (creditInput && !creditInput.classList.contains('hidden')) ? (parseFloat(creditInput.value) || 0) : 0;

    // Total actual immediate payment
    const totalPaid = cash + upi + bank;
    const paidInput = document.getElementById('modal-pay-amount');
    const checkedCount = document.querySelectorAll('input[name="payment_methods[]"]:checked').length;

    if (paidInput) {
        if (checkedCount > 0 || (cash + upi + bank + credit) > 0) {
            paidInput.value = totalPaid > 0 ? totalPaid.toFixed(2) : '0.00';
        } else {
            paidInput.value = '';
        }
    }

    // Toggle and update UPI QR if UPI is active
    const upiContainer = document.getElementById('dynamic-upi-container');
    const showQrBtn = document.getElementById('btn-show-qr');
    const upiChecked = document.getElementById('pos_chk_upi')?.checked;

    if (upiChecked) {
        const elGrand = document.getElementById('summary-grandtotal');
        const grandVal = parseFloat(elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : 0) || 0;
        const upiVal = upi > 0 ? upi : grandVal;
        generateUpiQrCode(upiVal);
        if (showQrBtn) {
            showQrBtn.classList.remove('hidden');
            showQrBtn.classList.add('flex');
        }
    } else {
        if (upiContainer) {
            upiContainer.classList.add('hidden');
            upiContainer.classList.remove('flex');
        }
        if (showQrBtn) {
            showQrBtn.classList.add('hidden');
            showQrBtn.classList.remove('flex');
        }
    }

    calculatePosBalance();
}
window.calculatePosTotalPaidFromMethods = calculatePosTotalPaidFromMethods;
window.onPosPaymentMethodChange = calculatePosTotalPaidFromMethods;

/**
 * Payment Modal & Checkout Completion
 */
function openPaymentModal() {
    if (posCart.length === 0) return;

    const elGrand = document.getElementById('summary-grandtotal');
    const grandVal = parseFloat(elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : '0') || 0;

    const payModalGrand = document.getElementById('modal-pay-grandtotal');
    const payModalAmount = document.getElementById('modal-pay-amount');

    if (payModalGrand) payModalGrand.textContent = '₹' + grandVal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    if (payModalAmount) payModalAmount.value = grandVal.toFixed(2);
    
    // Reset payment methods checkboxes: Cash checked by default with full amount prefilled
    const methodConfigs = [
        { chk: 'pos_chk_cash', input: 'pos_payment_amount_cash', defaultChecked: true },
        { chk: 'pos_chk_upi', input: 'pos_payment_amount_upi', defaultChecked: false },
        { chk: 'pos_chk_bank', input: 'pos_payment_amount_bank', defaultChecked: false },
        { chk: 'pos_chk_credit', input: 'pos_payment_amount_credit', defaultChecked: false }
    ];

    methodConfigs.forEach(m => {
        const chkEl = document.getElementById(m.chk);
        const inpEl = document.getElementById(m.input);
        if (chkEl) chkEl.checked = m.defaultChecked;
        if (inpEl) {
            if (m.defaultChecked) {
                inpEl.classList.remove('hidden');
                inpEl.value = grandVal > 0 ? grandVal.toFixed(2) : '0.00';
            } else {
                inpEl.classList.add('hidden');
                inpEl.value = '';
            }
        }
    });

    const upiContainer = document.getElementById('dynamic-upi-container');
    if (upiContainer) {
        upiContainer.classList.add('hidden');
        upiContainer.classList.remove('flex');
    }
    const showQrBtn = document.getElementById('btn-show-qr');
    if (showQrBtn) {
        showQrBtn.classList.add('hidden');
        showQrBtn.classList.remove('flex');
    }
    const doneQrBtn = document.getElementById('btn-done-qr');
    if (doneQrBtn) {
        doneQrBtn.classList.add('hidden');
        doneQrBtn.classList.remove('flex');
    }
    const confirmBtn = document.getElementById('btn-confirm-checkout');
    if (confirmBtn) {
        confirmBtn.classList.remove('hidden');
        confirmBtn.classList.add('flex');
    }

    // Show QR Button logic
    if (showQrBtn && !showQrBtn.dataset.hasListener) {
        showQrBtn.addEventListener('click', () => {
            const upiBox = document.getElementById('dynamic-upi-container');
            const doneBtn = document.getElementById('btn-done-qr');
            if (upiBox && doneBtn) {
                upiBox.classList.remove('hidden');
                upiBox.classList.add('flex');
                showQrBtn.classList.add('hidden');
                showQrBtn.classList.remove('flex');
                doneBtn.classList.remove('hidden');
                doneBtn.classList.add('flex');
            }
        });
        showQrBtn.dataset.hasListener = 'true';
    }

    calculatePosBalance();
    openModal('pos-payment-modal');
}

function generateUpiQrCode(amount) {
    if (typeof SYSTEM_UPI_ID === 'undefined' || !SYSTEM_UPI_ID) return;
    if (isNaN(amount) || amount <= 0) amount = 0;
    const upiString = `upi://pay?pa=${SYSTEM_UPI_ID}&pn=${encodeURIComponent(typeof SYSTEM_COMPANY_NAME !== 'undefined' ? SYSTEM_COMPANY_NAME : 'Store')}&am=${amount}&cu=INR`;
    const qrUrl = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodeURIComponent(upiString)}&choe=UTF-8`;
    
    const qrImg = document.getElementById('dynamic-upi-qr');
    const amtTxt = document.getElementById('upi-exact-amount');
    if (qrImg) qrImg.src = qrUrl;
    if (amtTxt) amtTxt.textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2});
}

async function processCheckout() {
    const payBtn = document.getElementById('btn-confirm-checkout');
    if (payBtn) {
        payBtn.disabled = true;
        payBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing Invoice...';
    }

    const checkedBoxes = Array.from(document.querySelectorAll('input[name="payment_methods[]"]:checked')).map(cb => cb.value);
    const selectedMethods = checkedBoxes.length > 0 ? checkedBoxes : ['cash'];
    const method = selectedMethods.length > 1 ? 'split' : selectedMethods[0];

    const accountId = document.getElementById('payment-account-select')?.value || 1;
    const paidAmt = document.getElementById('modal-pay-amount')?.value || 0;
    const notes = document.getElementById('modal-pay-notes')?.value || '';
    const overallDiscAmt = parseFloat(document.getElementById('pos-overall-discount')?.value || 0) || 0;
    const netSubtotal = Math.max(0, posCart.reduce((s, i) => s + (i.quantity * i.unit_price) - ((i.quantity * i.unit_price * (i.discount_percent || 0)) / 100), 0));
    const overallDiscPercent = netSubtotal > 0 ? Number(((overallDiscAmt / netSubtotal) * 100).toFixed(2)) : 0;

    const paymentAmounts = {
        cash: (!document.getElementById('pos_payment_amount_cash')?.classList.contains('hidden')) ? (parseFloat(document.getElementById('pos_payment_amount_cash')?.value || 0) || 0) : 0,
        upi: (!document.getElementById('pos_payment_amount_upi')?.classList.contains('hidden')) ? (parseFloat(document.getElementById('pos_payment_amount_upi')?.value || 0) || 0) : 0,
        bank_transfer: (!document.getElementById('pos_payment_amount_bank')?.classList.contains('hidden')) ? (parseFloat(document.getElementById('pos_payment_amount_bank')?.value || 0) || 0) : 0,
        credit: (!document.getElementById('pos_payment_amount_credit')?.classList.contains('hidden')) ? (parseFloat(document.getElementById('pos_payment_amount_credit')?.value || 0) || 0) : 0
    };

    let billGstPercent = null;
    if (activeBillGstMode === 'custom' && activeCustomBillGst !== null) {
        billGstPercent = activeCustomBillGst;
    } else if (activeBillGstMode !== 'item') {
        billGstPercent = parseFloat(activeBillGstMode);
    }

    const payload = {
        _csrf_token: window.CSRF_TOKEN,
        customer_id: selectedCustomer.id,
        payment_method: method,
        payment_methods: selectedMethods,
        payment_amounts: paymentAmounts,
        account_id: accountId,
        overall_discount_amount: overallDiscAmt,
        overall_discount_percent: overallDiscPercent,
        bill_gst_percent: billGstPercent,
        paid_amount: paidAmt,
        notes: notes,
        items: posCart
    };

    try {
        const res = await fetch(`${window.APP_URL}/billing/checkout`, {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            }
        });

        const result = await res.json();

        if (result.success) {
            closeModal('pos-payment-modal');
            posCart = [];
            renderCart();
            showToast('success', `Invoice ${result.data.invoice_no} generated successfully!`);

            // Open Print Prompt Modal
            document.getElementById('completed-invoice-no').textContent = result.data.invoice_no;
            document.getElementById('btn-print-a4').onclick = () => window.open(result.data.print_a4_url, '_blank');
            document.getElementById('btn-print-thermal').onclick = () => window.open(result.data.print_thermal_url, '_blank', 'width=400,height=600');
            document.getElementById('btn-view-invoice').onclick = () => window.location.href = result.data.view_url;
            openModal('pos-complete-modal');
        } else {
            showToast('error', result.message || 'Checkout failed.');
        }
    } catch (err) {
        console.error(err);
        showToast('error', 'Checkout error. Please check server logs.');
    } finally {
        if (payBtn) {
            payBtn.disabled = false;
            payBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Complete Transaction';
        }
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}








