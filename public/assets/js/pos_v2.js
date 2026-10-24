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
            openPaymentModal();
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

    // 4. Initialize Customer Modal and Search Logic
    initCustomerModalListeners();
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

function handleProductCardClick(cardEl) {
    if (!cardEl) return;
    const name = cardEl.dataset.name || 'Product';
    const maxStock = parseInt(cardEl.dataset.maxStock) || 0;
    const id = parseInt(cardEl.dataset.productId);
    const unitPrice = parseFloat(cardEl.dataset.price);
    const discountPercent = parseFloat(cardEl.dataset.discount) || 0;
    const gstPercent = parseFloat(cardEl.dataset.gst) || 12;
    
    addToCartById(id, name, unitPrice, discountPercent, gstPercent, maxStock);
}
window.handleProductCardClick = handleProductCardClick;

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
        const maxStock = parseInt(p.current_stock) || 0;
        const cartItem = posCart.find(i => i.product_id === parseInt(p.id));
        const inCartQty = cartItem ? cartItem.quantity : 0;
        const remainingStock = Math.max(0, maxStock - inCartQty);
        const isOutOfStock = (remainingStock <= 0 || maxStock <= 0);

        const safeName = escapeHtml(p.name);
        const code = escapeHtml(p.code || p.sku || '');

        let stockBadge = '';
        if (isOutOfStock) {
            const label = maxStock <= 0 ? 'Out of Stock' : '0 in stock (Max Reached)';
            stockBadge = `<span class="pos-stock-badge bg-red-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full border border-red-700 shadow-xs whitespace-nowrap shrink-0 flex items-center gap-1"><i class="fas fa-ban text-[9px]"></i> ${label}</span>`;
        } else {
            const inCartLabel = inCartQty > 0 ? ` (${inCartQty} in cart)` : '';
            stockBadge = `<span class="pos-stock-badge bg-[#f0fdf4] text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-[#bbf7d0] whitespace-nowrap shrink-0">${remainingStock} in stock${inCartLabel}</span>`;
        }

        html += `
            <div data-product-id="${p.id}"
                 data-max-stock="${maxStock}"
                 data-name="${safeName}"
                 data-price="${p.selling_price}"
                 data-discount="${p.discount_percent || 0}"
                 data-gst="${p.gst_percent || 12}"
                 data-code="${code}"
                 data-disabled="false"
                 onclick="handleProductCardClick(this)" 
                 class="pos-product-card h-[144px] ${isOutOfStock ? 'pos-card-out-of-stock bg-red-50/90 border-2 border-red-500 cursor-pointer shadow-xs' : 'bg-white border border-slate-100 hover:border-amber-300 hover:shadow-md cursor-pointer'} shadow-sm rounded-2xl p-3.5 flex flex-col group transition-all select-none">
                <div>
                    <div class="flex items-center justify-between gap-1">
                        <span class="pos-code-badge text-[10px] font-bold ${isOutOfStock ? 'text-red-800 bg-red-100 border-red-300' : 'text-amber-700 bg-[#fff8ef] border-amber-200'} px-2 py-1 rounded-md border whitespace-nowrap truncate">${code}</span>
                        ${stockBadge}
                    </div>
                    <h4 class="pos-prod-title text-[13px] font-extrabold ${isOutOfStock ? 'text-red-950' : 'text-slate-900 group-hover:text-amber-700'} transition-colors line-clamp-2 h-[36px] leading-snug mt-2">
                        ${safeName}
                    </h4>
                </div>
                <div class="mt-auto pt-3 flex items-center justify-between">
                    <div class="flex items-baseline">
                        <span class="text-[10px] text-slate-400 font-medium">Rate:</span>
                        <span class="pos-price-val text-[14px] font-black ${isOutOfStock ? 'text-red-900' : 'text-slate-900'} font-mono ml-1">₹${parseFloat(p.selling_price).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                    </div>
                    <button type="button" class="pos-add-btn w-7 h-7 rounded-lg ${isOutOfStock ? 'bg-red-600 text-white hover:bg-red-700 cursor-pointer shadow-sm' : 'bg-[#fef3c7] text-amber-700 group-hover:bg-amber-500 group-hover:text-white cursor-pointer'} flex items-center justify-center transition-all shadow-sm">
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
            const stock = parseInt(p.current_stock) || 0;
            const inCart = posCart.find(i => i.product_id === parseInt(p.id))?.quantity || 0;
            if (stock <= 0) {
                showToast('warning', `Scanned "${p.name}" is OUT OF STOCK — added to cart in Red Billing mode.`);
            } else if (inCart + 1 > stock) {
                showToast('warning', `Quantity exceeds available stock (${stock}) for "${p.name}" — billing in Red.`);
            }
            addToCartById(p.id, p.name, p.selling_price, p.discount_percent || 0, p.gst_percent || 12, stock);
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
    id = parseInt(id);
    maxStock = parseInt(maxStock) || 0;
    const existingIndex = posCart.findIndex(item => item.product_id === id);
    const inCartQty = existingIndex > -1 ? posCart[existingIndex].quantity : 0;

    if (maxStock <= 0) {
        showToast('warning', `Item "${name}" is OUT OF STOCK — added to cart in Red Billing mode.`);
    } else if (inCartQty + 1 > maxStock) {
        showToast('warning', `Quantity exceeds stock (${maxStock}) for "${name}" — billing in Red.`);
    }

    let initialGst = parseFloat(gstPercent) || 12;
    if (activeBillGstMode === 'custom' && activeCustomBillGst !== null) {
        initialGst = activeCustomBillGst;
    } else if (activeBillGstMode !== 'item') {
        initialGst = parseFloat(activeBillGstMode) || 0;
    }

    if (existingIndex > -1) {
        posCart[existingIndex].quantity += 1;
    } else {
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
window.addToCartById = addToCartById;

function updateItemQty(index, delta) {
    if (!posCart[index]) return;
    const newQty = posCart[index].quantity + delta;

    if (newQty <= 0) {
        posCart.splice(index, 1);
    } else {
        const maxStock = parseInt(posCart[index].max_stock) || 0;
        if (maxStock <= 0 || newQty > maxStock) {
            showToast('warning', `Quantity exceeds stock (${maxStock}) — billing in Red.`);
        }
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

        const maxStock = parseInt(item.max_stock) || 0;
        const qty = parseInt(item.quantity) || 1;
        const remainingStock = maxStock - qty;
        const consumptionRatio = maxStock > 0 ? (qty / maxStock) : 1;

        const isOutOfStock = (maxStock <= 0 || remainingStock <= 0);
        // Yellow blinking warning when: original stock > 2 AND (added >= 80% or remaining <= 2) AND not out of stock
        const isYellowWarning = (!isOutOfStock && maxStock > 2 && (consumptionRatio >= 0.8 || remainingStock <= 2));

        let rowStyleClasses = 'bg-white border-slate-200 hover:border-amber-300 shadow-xs';
        let stockStatusBadge = '';

        if (isOutOfStock) {
            rowStyleClasses = 'bg-red-50/95 border-2 border-red-500 red-out-of-stock-row text-red-950 shadow-md';
            stockStatusBadge = `
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-600 text-white font-black text-[9px] uppercase tracking-wider shadow-xs">
                    <i class="fas fa-ban text-[8px]"></i> Out of Stock Sale (${maxStock} in stock)
                </span>
            `;
        } else if (isYellowWarning) {
            rowStyleClasses = 'bg-amber-50/95 border-2 border-amber-400 yellow-blink-warning shadow-md';
            const pct = Math.round(consumptionRatio * 100);
            stockStatusBadge = `
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500 text-slate-900 font-black text-[9px] uppercase tracking-wider shadow-xs">
                    <i class="fas fa-exclamation-triangle text-[8px]"></i> Warning: ${pct}% Added (${remainingStock} left)
                </span>
            `;
        } else {
            stockStatusBadge = maxStock > 0 ? `<span class="text-[10px] text-slate-400 font-medium">(${remainingStock} of ${maxStock} in stock)</span>` : '';
        }

        html += `
            <div class="${rowStyleClasses} rounded-xl p-3 mb-2 flex items-center justify-between transition-all">
                <div class="flex-1 pr-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h5 class="text-xs font-extrabold ${isOutOfStock ? 'text-red-950' : 'text-slate-900'} line-clamp-1">${item.name}</h5>
                        ${stockStatusBadge}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2.5 flex-wrap">
                        <span class="font-mono ${isOutOfStock ? 'text-red-900 font-bold' : 'text-slate-700'}">₹${item.unit_price.toFixed(2)}</span>
                        
                        <!-- Bigger GST text box without spinner arrows -->
                        <div class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-300 rounded-lg px-2 py-1 shadow-xs" title="Dynamic GST Tax Rate">
                            <span class="text-xs font-black text-amber-900">GST:</span>
                            <input type="text" inputmode="decimal" value="${item.gst_percent}" 
                                   oninput="updateItemGst(${idx}, this.value)" 
                                   title="Edit GST percentage for this item"
                                   class="w-14 h-7 bg-white border border-amber-400 rounded-md px-1 py-0 text-center font-mono font-black text-xs text-slate-900 outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-500 shadow-inner">
                            <span class="text-xs text-amber-900 font-bold">%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center bg-slate-100 rounded-lg border border-slate-200 p-0.5">
                        <button onclick="updateItemQty(${idx}, -1)" class="w-6 h-6 rounded bg-white text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs font-bold shadow-xs">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="w-7 text-center text-xs font-bold ${isOutOfStock ? 'text-red-900' : 'text-slate-900'} font-mono">${item.quantity}</span>
                        <button onclick="updateItemQty(${idx}, 1)" class="w-6 h-6 rounded bg-white text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs font-bold shadow-xs">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="text-right min-w-[70px]">
                        <span class="text-xs font-black ${isOutOfStock ? 'text-red-950' : 'text-slate-900'} font-mono block">₹${lineTotal.toFixed(2)}</span>
                        <span class="text-[9px] ${isOutOfStock ? 'text-red-700' : 'text-slate-400'} font-mono">tax: ₹${taxAmt.toFixed(2)}</span>
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
    syncProductCardsStockState();
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
        elPayBtn.disabled = false;
        if (posCart.length === 0) {
            elPayBtn.classList.add('opacity-75');
            elPayBtn.innerHTML = `<i class="fas fa-shopping-cart mr-2"></i>Complete Bill [F8]`;
        } else {
            elPayBtn.classList.remove('opacity-75');
            elPayBtn.innerHTML = `<i class="fas fa-check-circle mr-2"></i>Pay ₹${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})} [F8]`;
        }
    }
}

/**
 * Dynamically Sync Product Cards with Remaining Stock in Real Time
 * When stock reaches 0 (either originally or when depleted in cart),
 * the card displays as RED and is disabled from selection!
 */
function syncProductCardsStockState() {
    const cards = document.querySelectorAll('.pos-product-card');
    cards.forEach(card => {
        const id = parseInt(card.dataset.productId);
        const maxStock = parseInt(card.dataset.maxStock) || 0;
        const name = card.dataset.name || 'Product';
        
        // Count how many of this product are in the current cart
        const cartItem = posCart.find(i => i.product_id === id);
        const inCartQty = cartItem ? cartItem.quantity : 0;
        const remainingStock = Math.max(0, maxStock - inCartQty);
        
        const badgeEl = card.querySelector('.pos-stock-badge');
        const codeEl = card.querySelector('.pos-code-badge');
        const btnEl = card.querySelector('.pos-add-btn');
        const nameEl = card.querySelector('.pos-prod-title');
        const priceEl = card.querySelector('.pos-price-val');
        
        if (remainingStock <= 0 || maxStock <= 0) {
            // OUT OF STOCK -> Display RED warning, but ALLOW billing!
            card.dataset.disabled = 'false';
            card.classList.remove('bg-white', 'border-slate-100', 'hover:border-amber-300', 'cursor-not-allowed');
            card.classList.add('pos-card-out-of-stock', 'bg-red-50', 'border-2', 'border-red-500', 'cursor-pointer', 'shadow-xs');
            
            if (badgeEl) {
                const label = maxStock <= 0 ? 'Out of Stock (Red Bill)' : '0 in stock (Red Bill)';
                badgeEl.className = 'pos-stock-badge bg-red-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full border border-red-700 shadow-xs whitespace-nowrap shrink-0 flex items-center gap-1';
                badgeEl.innerHTML = `<i class="fas fa-ban text-[9px]"></i> ${label}`;
            }
            if (codeEl) {
                codeEl.className = 'pos-code-badge text-[10px] font-bold text-red-800 bg-red-100 px-2 py-1 rounded-md border border-red-300 whitespace-nowrap truncate';
            }
            if (nameEl) {
                nameEl.className = 'pos-prod-title text-[13px] font-extrabold text-red-950 line-clamp-2 h-[36px] leading-snug mt-2';
            }
            if (priceEl) {
                priceEl.className = 'pos-price-val text-[14px] font-black text-red-900 font-mono ml-1';
            }
            if (btnEl) {
                btnEl.className = 'pos-add-btn w-7 h-7 rounded-lg bg-red-600 text-white hover:bg-red-700 flex items-center justify-center transition-all shadow-xs cursor-pointer';
                btnEl.innerHTML = '<i class="fas fa-plus text-xs"></i>';
                btnEl.removeAttribute('disabled');
            }
        } else {
            // IN STOCK -> Active White / Amber design
            card.dataset.disabled = 'false';
            card.classList.remove('pos-card-out-of-stock', 'bg-red-50', 'border-2', 'border-red-500', 'cursor-not-allowed', 'shadow-xs');
            card.classList.add('bg-white', 'border', 'border-slate-100', 'hover:border-amber-300', 'hover:shadow-md', 'cursor-pointer');
            
            if (badgeEl) {
                const inCartLabel = inCartQty > 0 ? ` (${inCartQty} in cart)` : '';
                badgeEl.className = 'pos-stock-badge bg-[#f0fdf4] text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-[#bbf7d0] whitespace-nowrap shrink-0';
                badgeEl.innerHTML = `${remainingStock} in stock${inCartLabel}`;
            }
            if (codeEl) {
                codeEl.className = 'pos-code-badge text-[10px] font-bold text-amber-700 bg-[#fff8ef] px-2 py-1 rounded-md border border-amber-200 whitespace-nowrap truncate';
            }
            if (nameEl) {
                nameEl.className = 'pos-prod-title text-[13px] font-extrabold text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 h-[36px] leading-snug mt-2';
            }
            if (priceEl) {
                priceEl.className = 'pos-price-val text-[14px] font-black text-slate-900 font-mono ml-1';
            }
            if (btnEl) {
                btnEl.className = 'pos-add-btn w-7 h-7 rounded-lg bg-[#fef3c7] text-amber-700 group-hover:bg-amber-500 group-hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer';
                btnEl.innerHTML = '<i class="fas fa-plus text-xs"></i>';
                btnEl.removeAttribute('disabled');
            }
        }
    });
}
window.syncProductCardsStockState = syncProductCardsStockState;

/**
 * Enterprise POS Customer Selection & Quick Registration
 */
function openCustomerModal() {
    openModal('pos-customer-modal');
    const searchInput = document.getElementById('cust-search-input');
    const clearBtn = document.getElementById('btn-clear-cust-search');
    if (searchInput) {
        searchInput.value = '';
        setTimeout(() => searchInput.focus(), 80);
    }
    if (clearBtn) clearBtn.classList.add('hidden');
    // Immediately load customers on modal open
    searchCustomerPos('');
}
window.openCustomerModal = openCustomerModal;

function clearCustomerSearch() {
    const searchInput = document.getElementById('cust-search-input');
    const clearBtn = document.getElementById('btn-clear-cust-search');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
    }
    if (clearBtn) clearBtn.classList.add('hidden');
    searchCustomerPos('');
}
window.clearCustomerSearch = clearCustomerSearch;

async function searchCustomerPos(term = '') {
    const listContainer = document.getElementById('pos-customer-list');
    const statusLabel = document.getElementById('cust-list-status');
    if (!listContainer) return;

    const trimmedTerm = term.trim();
    if (statusLabel) {
        statusLabel.textContent = trimmedTerm ? `Filtered (${trimmedTerm})` : 'All Active';
    }

    try {
        listContainer.innerHTML = `
            <div class="py-6 text-center text-slate-400 text-xs">
                <i class="fas fa-spinner fa-spin mr-1.5 text-amber-500"></i> Finding customers...
            </div>
        `;

        const res = await fetch(`${window.APP_URL}/customers/search?q=${encodeURIComponent(trimmedTerm)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        const customers = data.data || [];

        let html = '';

        // 1. Pinned Top Option: Default Walk-in Retail Customer
        const isWalkInActive = (selectedCustomer.id === 1);
        const termMatchesWalkIn = (!trimmedTerm || 'walk-in retail customer 9999999999 cust-0001 counter regular'.toLowerCase().includes(trimmedTerm.toLowerCase()));

        if (termMatchesWalkIn) {
            html += `
                <div onclick="selectCustomer(1, 'Walk-in Retail Customer', '9999999999', 'Tamil Nadu')" 
                     class="p-2.5 rounded-xl border cursor-pointer flex items-center justify-between mb-1.5 transition-all select-none ${isWalkInActive ? 'bg-amber-500/15 border-amber-500/60 shadow-sm' : 'bg-slate-800 hover:bg-slate-700 border-slate-700'}">
                    <div class="flex items-center gap-2.5 overflow-hidden pr-2">
                        <div class="w-8 h-8 rounded-lg ${isWalkInActive ? 'bg-amber-500 text-slate-900 font-bold' : 'bg-slate-700 text-amber-300'} flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold ${isWalkInActive ? 'text-amber-300' : 'text-white'} text-xs truncate">Walk-in Retail Customer</div>
                            <div class="text-[10px] ${isWalkInActive ? 'text-amber-200/80' : 'text-slate-400'} font-mono truncate">9999999999 • CUST-0001 (Counter Sale)</div>
                        </div>
                    </div>
                    ${isWalkInActive ? 
                        '<span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-500 text-slate-900 font-extrabold uppercase tracking-wider shrink-0">Active</span>' : 
                        '<span class="text-xs px-2.5 py-1 rounded-md bg-amber-500/20 text-amber-300 font-bold hover:bg-amber-500 hover:text-slate-900 transition-colors shrink-0">Select</span>'
                    }
                </div>
            `;
        }

        // 2. Render database customer list
        let renderedDbCount = 0;
        customers.forEach(c => {
            if (parseInt(c.id) === 1) return; // already rendered as walk-in above
            renderedDbCount++;

            const isSelected = (selectedCustomer.id === parseInt(c.id));
            const safeName = escapeHtml(c.name || 'Unnamed');
            const safeMobile = escapeHtml(c.mobile || '');
            const safeCode = escapeHtml(c.customer_code || '');
            const safeCity = escapeHtml(c.city || '');
            const safeState = escapeHtml(c.state || 'Tamil Nadu');

            html += `
                <div class="pos-customer-row p-2.5 rounded-xl border cursor-pointer flex items-center justify-between mb-1.5 transition-all select-none ${isSelected ? 'bg-amber-500/15 border-amber-500/60 shadow-sm' : 'bg-slate-800 hover:bg-slate-700 border-slate-700'}"
                     data-id="${c.id}"
                     data-name="${safeName}"
                     data-mobile="${safeMobile}"
                     data-state="${safeState}"
                     onclick="handleCustomerCardClick(this)">
                    <div class="overflow-hidden pr-2">
                        <div class="font-bold ${isSelected ? 'text-amber-300' : 'text-white'} text-xs truncate flex items-center gap-1.5">
                            <span class="truncate">${safeName}</span>
                            ${c.customer_type === 'wholesale' ? '<span class="text-[9px] bg-indigo-500/30 text-indigo-200 px-1.5 py-0.2 rounded font-mono font-normal uppercase shrink-0">Wholesale</span>' : ''}
                        </div>
                        <div class="text-[10px] ${isSelected ? 'text-amber-200/80' : 'text-slate-400'} font-mono truncate mt-0.5">
                            ${safeMobile || 'No Mobile'} • ${safeCode}${safeCity ? ' • ' + safeCity : ''}
                        </div>
                    </div>
                    ${isSelected ? 
                        '<span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-500 text-slate-900 font-extrabold uppercase tracking-wider shrink-0">Selected ✓</span>' : 
                        '<button type="button" class="text-xs px-2.5 py-1 rounded-md bg-amber-500/20 text-amber-300 font-bold hover:bg-amber-500 hover:text-slate-900 transition-colors shrink-0">Select</button>'
                    }
                </div>
            `;
        });

        // 3. Fallback if no matching results
        if (!termMatchesWalkIn && renderedDbCount === 0) {
            const safeTerm = escapeHtml(trimmedTerm);
            html = `
                <div class="p-4 text-center bg-slate-50 border border-dashed border-slate-200 rounded-xl my-2">
                    <i class="fas fa-user-slash text-2xl text-slate-400 mb-1"></i>
                    <div class="font-bold text-xs text-slate-700">No customer found for "${safeTerm}"</div>
                    <div class="text-[11px] text-slate-400 mt-0.5 mb-2.5">You can register them instantly using the quick form below.</div>
                    <button type="button" onclick="prefillQuickAdd('${safeTerm}')" 
                            class="text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-arrow-down"></i> Use "${safeTerm}" in Quick Register
                    </button>
                </div>
            `;
        }

        listContainer.innerHTML = html;
    } catch (err) {
        console.error('Customer Search Error:', err);
        listContainer.innerHTML = `<div class="p-3 text-center text-rose-500 text-xs">Error searching customers. Please try again.</div>`;
    }
}
window.searchCustomerPos = searchCustomerPos;

function handleCustomerCardClick(el) {
    if (!el) return;
    const id = el.dataset.id;
    const name = el.dataset.name;
    const mobile = el.dataset.mobile;
    const state = el.dataset.state;
    selectCustomer(id, name, mobile, state);
}
window.handleCustomerCardClick = handleCustomerCardClick;

function prefillQuickAdd(val) {
    val = (val || '').trim();
    if (!val) return;
    const isDigits = /^\d+$/.test(val);
    const nameInp = document.getElementById('pos-quick-cust-name');
    const mobileInp = document.getElementById('pos-quick-cust-mobile');

    if (isDigits) {
        if (mobileInp) mobileInp.value = val;
        if (nameInp) nameInp.focus();
    } else {
        if (nameInp) nameInp.value = val;
        if (mobileInp) mobileInp.focus();
    }
    showToast('info', `Prefilled "${val}" into quick registration.`);
}
window.prefillQuickAdd = prefillQuickAdd;

function onCustomerInputsChanged() {
    const mobileInp = document.getElementById('pos-customer-mobile');
    const nameInp = document.getElementById('pos-customer-name');
    
    const mobile = (mobileInp?.value || '').trim();
    const name = (nameInp?.value || '').trim();
    
    selectedCustomer.mobile = mobile || '9999999999';
    selectedCustomer.name = name || 'Walk-in Retail Customer';
    
    // Update Payment Modal Customer Info
    const payNameEl = document.getElementById('modal-pay-customer-name');
    const payMobileEl = document.getElementById('modal-pay-customer-mobile');
    if (payNameEl) payNameEl.textContent = selectedCustomer.name;
    if (payMobileEl) payMobileEl.textContent = selectedCustomer.mobile;
}
window.onCustomerInputsChanged = onCustomerInputsChanged;

function selectCustomer(id, name, mobile, state) {
    selectedCustomer = { 
        id: parseInt(id) || 1, 
        name: String(name || 'Walk-in Retail Customer'), 
        mobile: String(mobile || '9999999999'), 
        state: String(state || 'Tamil Nadu') 
    };

    // Update Live Input fields in POS Header
    const mobileInp = document.getElementById('pos-customer-mobile');
    const nameInp = document.getElementById('pos-customer-name');
    if (mobileInp) mobileInp.value = selectedCustomer.mobile;
    if (nameInp) nameInp.value = selectedCustomer.name;

    // Backward compatibility for static banner elements if any
    const nameEl = document.getElementById('selected-customer-name');
    const mobileEl = document.getElementById('selected-customer-mobile');
    if (nameEl) nameEl.textContent = selectedCustomer.name;
    if (mobileEl) {
        if (selectedCustomer.id === 1) {
            mobileEl.textContent = '9999999999 • Regular';
        } else {
            mobileEl.textContent = `${selectedCustomer.mobile || 'No Mobile'} • Active Customer`;
        }
    }

    // Update Payment Modal Customer Info
    const payNameEl = document.getElementById('modal-pay-customer-name');
    const payMobileEl = document.getElementById('modal-pay-customer-mobile');
    if (payNameEl) payNameEl.textContent = selectedCustomer.name;
    if (payMobileEl) payMobileEl.textContent = selectedCustomer.mobile;

    closeModal('pos-customer-modal');
    showToast('success', `Customer set to: ${selectedCustomer.name}`);
}
window.selectCustomer = selectCustomer;

function initCustomerModalListeners() {
    // Search input debouncer
    const searchInput = document.getElementById('cust-search-input');
    const clearBtn = document.getElementById('btn-clear-cust-search');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (clearBtn) {
                if (val.trim().length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchCustomerPos(val);
            }, 180);
        });
    }

    // Quick Register Customer Form dedicated AJAX handler
    const quickForm = document.getElementById('pos-quick-customer-form');
    if (quickForm) {
        quickForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const nameInput = document.getElementById('pos-quick-cust-name');
            const mobileInput = document.getElementById('pos-quick-cust-mobile');
            const submitBtn = document.getElementById('btn-save-quick-cust');

            const name = nameInput ? nameInput.value.trim() : '';
            const mobile = mobileInput ? mobileInput.value.trim() : '';

            if (!name) {
                showToast('error', 'Please enter customer full name.');
                if (nameInput) nameInput.focus();
                return;
            }

            if (!mobile || mobile.length < 7) {
                showToast('error', 'Please enter a valid mobile number.');
                if (mobileInput) mobileInput.focus();
                return;
            }

            const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Registering & Linking...';
            }

            try {
                const formData = new FormData(quickForm);
                const res = await fetch(`${window.APP_URL}/customers/create`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await res.json();

                if (result.success) {
                    const c = (result.data && result.data.customer) ? result.data.customer : null;
                    if (c && c.id) {
                        selectCustomer(c.id, c.name, c.mobile, c.state);
                    } else {
                        selectCustomer(result.data?.id || 1, name, mobile, 'Tamil Nadu');
                    }
                    quickForm.reset();
                    showToast('success', result.message || `Customer ${name} selected for billing!`);
                } else {
                    let errMsg = result.message || 'Could not register customer.';
                    if (result.errors) {
                        const list = Object.values(result.errors).flat().join(' ');
                        errMsg += ` ${list}`;
                    }
                    showToast('error', errMsg);
                }
            } catch (err) {
                console.error('Customer Register Error:', err);
                showToast('error', 'Network error while registering customer.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origBtnHtml;
                }
            }
        });
    }
}
window.initCustomerModalListeners = initCustomerModalListeners;

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
    if (!posCart || posCart.length === 0) {
        showToast('error', 'Cannot complete bill: Cart is empty! Please add at least 1 product.');
        const s = document.getElementById('pos-search-input');
        if (s) {
            s.focus();
            s.select();
            s.classList.add('ring-2', 'ring-rose-500');
            setTimeout(() => s.classList.remove('ring-2', 'ring-rose-500'), 1500);
        }
        return;
    }

    const elGrand = document.getElementById('summary-grandtotal');
    const grandVal = parseFloat(elGrand ? elGrand.textContent.replace(/[^\d.-]/g, '') : '0') || 0;

    const payModalGrand = document.getElementById('modal-pay-grandtotal');
    const payModalAmount = document.getElementById('modal-pay-amount');

    if (payModalGrand) payModalGrand.textContent = '₹' + grandVal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    if (payModalAmount) payModalAmount.value = grandVal.toFixed(2);
    
    // Sync Selected Customer into Payment Modal
    const payModalCustName = document.getElementById('modal-pay-customer-name');
    const payModalCustMobile = document.getElementById('modal-pay-customer-mobile');
    if (payModalCustName) payModalCustName.textContent = selectedCustomer.name || 'Walk-in Retail Customer';
    if (payModalCustMobile) payModalCustMobile.textContent = selectedCustomer.mobile || '9999999999';
    
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
        customer_name: (document.getElementById('pos-customer-name')?.value || selectedCustomer.name || 'Walk-in Retail Customer').trim(),
        customer_mobile: (document.getElementById('pos-customer-mobile')?.value || selectedCustomer.mobile || '9999999999').trim(),
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
            const cNameEl = document.getElementById('completed-customer-name');
            const cMobEl = document.getElementById('completed-customer-mobile');
            if (cNameEl) cNameEl.textContent = result.data.customer_name || selectedCustomer.name || 'Walk-in';
            if (cMobEl) cMobEl.textContent = result.data.customer_mobile || selectedCustomer.mobile || 'N/A';
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
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}








