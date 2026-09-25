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
            ? '<span class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-200 whitespace-nowrap shrink-0">Out of Stock</span>'
            : `<span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200 whitespace-nowrap shrink-0">${p.current_stock} in stock</span>`;

        const statueSpecs = p.material ? `${p.material} ${p.height ? '• ' + p.height + '"' : ''}` : '';

        html += `
            <div onclick="addToCartById(${p.id}, '${escapeHtml(p.name)}', ${p.selling_price}, ${p.discount_percent || 0}, ${p.gst_percent || 12}, ${p.current_stock})" 
                 class="pos-product-card h-32 bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer group hover:bg-white hover:border-amber-400 ${isOutOfStock ? 'opacity-50 pointer-events-none' : ''}">
                <div>
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[11px] font-mono text-amber-800 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 whitespace-nowrap truncate max-w-[55%]">${p.code || p.sku}</span>
                        ${stockBadge}
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 leading-snug mt-2">
                        ${p.name}
                    </h4>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400">Rate:</span>
                        <span class="text-sm font-black text-slate-900 font-mono ml-1">₹${parseFloat(p.selling_price).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                    </div>
                    <button class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center transition-all font-bold">
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
function addToCartById(id, name, unitPrice, discountPercent, gstPercent, maxStock) {
    const existingIndex = posCart.findIndex(item => item.product_id === id);

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
            gst_percent: parseFloat(gstPercent) || 12,
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
                <i class="fas fa-shopping-cart text-4xl text-slate-600 mb-3"></i>
                <p class="font-medium text-slate-300">Cart is empty</p>
                <p class="text-xs text-slate-500 mt-1">Scan barcode or click items to add</p>
            </div>
        `;
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
            <div class="bg-slate-800/90 border border-slate-700/70 rounded-xl p-3 mb-2 flex items-center justify-between">
                <div class="flex-1 pr-3">
                    <h5 class="text-sm font-semibold text-slate-100 line-clamp-1">${item.name}</h5>
                    <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                        <span>₹${item.unit_price.toFixed(2)}</span>
                        <span>• GST ${item.gst_percent}%</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center bg-slate-900 rounded-lg border border-slate-700 p-0.5">
                        <button onclick="updateItemQty(${idx}, -1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center text-xs">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="w-8 text-center text-xs font-bold text-amber-400">${item.quantity}</span>
                        <button onclick="updateItemQty(${idx}, 1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center text-xs">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="text-right min-w-[70px]">
                        <span class="text-sm font-bold text-amber-400 block">₹${lineTotal.toFixed(2)}</span>
                    </div>

                    <button onclick="removeFromCart(${idx})" class="text-slate-500 hover:text-rose-400 p-1">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        `;
    });

    cartContainer.innerHTML = html;

    // Overall discount calculation
    const overallDiscInput = document.getElementById('pos-overall-discount');
    const overallDiscPercent = overallDiscInput ? (parseFloat(overallDiscInput.value) || 0) : 0;
    const overallDiscAmt = ((subtotal - itemDiscountTotal) * overallDiscPercent) / 100;

    const totalBeforeRound = (subtotal - itemDiscountTotal - overallDiscAmt) + taxTotal;
    const grandTotal = Math.round(totalBeforeRound);
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

/**
 * Payment Modal & Checkout Completion
 */
function openPaymentModal() {
    if (posCart.length === 0) return;

    const elGrand = document.getElementById('summary-grandtotal');
    const grandVal = elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : '0';

    const payModalGrand = document.getElementById('modal-pay-grandtotal');
    const payModalAmount = document.getElementById('modal-pay-amount');

    if (payModalGrand) payModalGrand.textContent = '₹' + parseFloat(grandVal).toLocaleString('en-IN', {minimumFractionDigits: 2});
    if (payModalAmount) payModalAmount.value = grandVal;
    
    // Setup UPI QR dynamically
    generateUpiQrCode(grandVal);
    
    // Listen to payment method changes
    const radios = document.querySelectorAll('input[name="payment_method_radio"]');
    radios.forEach(r => {
        if (!r.dataset.hasListener) {
            r.addEventListener('change', (e) => {
                const upiContainer = document.getElementById('dynamic-upi-container');
                const showQrBtn = document.getElementById('btn-show-qr');
                if(upiContainer && showQrBtn) {
                    if (e.target.value === 'upi') {
                        showQrBtn.classList.remove('hidden');
                        showQrBtn.classList.add('flex');
                        upiContainer.classList.add('hidden');
                        upiContainer.classList.remove('flex');
                    } else {
                        showQrBtn.classList.add('hidden');
                        showQrBtn.classList.remove('flex');
                        upiContainer.classList.add('hidden');
                        upiContainer.classList.remove('flex');
                    }
                }
            });
            r.dataset.hasListener = 'true';
        }
    });
    
    // Show QR Button logic
    const showQrBtn = document.getElementById('btn-show-qr');
    if (showQrBtn && !showQrBtn.dataset.hasListener) {
        showQrBtn.addEventListener('click', () => {
            const upiContainer = document.getElementById('dynamic-upi-container');
            if (upiContainer) {
                upiContainer.classList.remove('hidden');
                upiContainer.classList.add('flex');
                showQrBtn.classList.add('hidden');
                showQrBtn.classList.remove('flex');
            }
        });
        showQrBtn.dataset.hasListener = 'true';
    }

    // Reset to cash and hide UPI container
    const defaultRadio = document.querySelector('input[name="payment_method_radio"][value="cash"]');
    if (defaultRadio) {
        defaultRadio.checked = true;
        defaultRadio.dispatchEvent(new Event('change'));
    }

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

    const method = document.querySelector('input[name="payment_method_radio"]:checked')?.value || 'cash';
    const accountId = document.getElementById('payment-account-select')?.value || 1;
    const paidAmt = document.getElementById('modal-pay-amount')?.value || 0;
    const notes = document.getElementById('modal-pay-notes')?.value || '';
    const overallDisc = document.getElementById('pos-overall-discount')?.value || 0;

    const payload = {
        _csrf_token: window.CSRF_TOKEN,
        customer_id: selectedCustomer.id,
        payment_method: method,
        account_id: accountId,
        overall_discount_percent: overallDisc,
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





