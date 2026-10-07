
    // --- SECTION TOGGLE LOGIC ---
    function toggleSection(sectionId) {
        const sections = ['supplier-section', 'product-section', 'barcode-section', 'invoice-section'];
        let wasHidden = document.getElementById(sectionId).classList.contains('hidden');
        
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
            }
        });

        if (wasHidden) {
            const target = document.getElementById(sectionId);
            if (target) {
                target.classList.remove('hidden');
            }
        }
    }

    // --- SUPPLIER MANAGEMENT LOGIC ---
    let suppliers = [];
    let currentEditId = null;

    function openSupplierModal() {
        document.getElementById('supplier-form').reset();
        clearErrors();
        currentEditId = null;
        document.getElementById('supplier-products-container').innerHTML = '';
        addSupplierProductField(); // Add at least one empty product field
        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-truck-loading text-indigo-600"></i> Add Supplier';
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplier-modal').classList.add('hidden');
    }

    function clearErrors() {
        const errorSpans = document.querySelectorAll('span[id^="err-"]');
        errorSpans.forEach(span => span.classList.add('hidden'));
        const inputs = document.querySelectorAll('#supplier-form input, #supplier-form select, #supplier-form textarea');
        inputs.forEach(input => input.classList.remove('border-rose-500'));
    }

    function showError(fieldId, message = null) {
        const span = document.getElementById('err-' + fieldId);
        const input = document.getElementById(fieldId);
        if (span) {
            if (message) span.textContent = message;
            span.classList.remove('hidden');
        }
        if (input) {
            input.classList.add('border-rose-500');
        }
    }

    


    function addSupplierProductField(productData = null) {
        const container = document.getElementById('supplier-products-container');
        const div = document.createElement('div');
        div.className = 'supplier-product-block relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 border border-amber-200 rounded-lg bg-white shadow-sm';
        div.innerHTML = `
            <button type="button" onclick="removeSupplierProductField(this)" class="absolute -top-2 -right-2 bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors shadow-sm">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="sp_product_name[]" value="${productData ? productData.prod_name : ''}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                <select name="sp_product_category[]" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">Select</option>
                    <option value="Statues" ${productData && productData.prod_category === 'Statues' ? 'selected' : ''}>Statues</option>
                    <option value="Accessories" ${productData && productData.prod_category === 'Accessories' ? 'selected' : ''}>Accessories</option>
                    <option value="Metals" ${productData && productData.prod_category === 'Metals' ? 'selected' : ''}>Metals</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Pieces Count</label>
                <input type="number" min="0" name="sp_unit[]" value="${productData ? (productData.prod_unit || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 10" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">One Piece Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_piece_rate[]" value="${productData ? (productData.prod_piece_rate || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" oninput="calculateSupplierPurchase(this)">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Total Purchase Price (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0" name="sp_purchase_price[]" value="${productData ? productData.prod_purchase_price : ''}" required class="w-full bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-lg px-2 py-1.5 text-xs outline-none" readonly>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Margin Type</label>
                <select name="sp_margin_type[]" onchange="calculateSupplierSelling(this)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="%">Percentage (%)</option>
                    <option value="₹">Fixed Amount (₹)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Margin Value</label>
                <input type="number" step="0.01" min="0" name="sp_margin_value[]" oninput="calculateSupplierSelling(this)" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Selling Price (₹)</label>
                <input type="number" step="0.01" min="0" name="sp_selling_price[]" value="${productData ? (productData.prod_selling_price || '') : ''}" class="w-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-lg px-2 py-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">GST (%)</label>
                <input type="number" step="0.1" min="0" max="100" name="sp_gst[]" value="${productData ? (productData.prod_gst || '') : ''}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Barcode (Auto)</label>
                <input type="text" name="sp_barcode[]" value="${productData && productData.barcode ? productData.barcode : generateSupplierBarcode()}" class="w-full bg-slate-100 border border-slate-200 rounded-lg px-2 py-1.5 text-xs text-slate-500 font-mono outline-none" readonly>
            </div>
            <input type="hidden" name="sp_product_id[]" value="${productData ? productData.id : ''}">
        `;
        container.appendChild(div);
    }

    function generateSupplierBarcode() {
        return 'BAR-' + Math.floor(1000000000 + Math.random() * 9000000000);
    }

    function calculateSupplierSelling(element) {
        const parentBlock = element.closest('.supplier-product-block');
        const purchaseInput = parentBlock.querySelector('input[name="sp_purchase_price[]"]');
        const marginTypeSelect = parentBlock.querySelector('select[name="sp_margin_type[]"]');
        const marginValueInput = parentBlock.querySelector('input[name="sp_margin_value[]"]');
        const sellingInput = parentBlock.querySelector('input[name="sp_selling_price[]"]');

        const purchasePrice = parseFloat(purchaseInput.value) || 0;
        const marginType = marginTypeSelect.value;
        const marginValue = parseFloat(marginValueInput.value) || 0;
        let sellingPrice = purchasePrice;

        if (purchasePrice > 0 && marginValue >= 0) {
            if (marginType === '%') {
                sellingPrice = purchasePrice + (purchasePrice * marginValue / 100);
            } else {
                sellingPrice = purchasePrice + marginValue;
            }
        }
        sellingInput.value = sellingPrice.toFixed(2);
    }

    function calculateSupplierPurchase(element) {
        const parentBlock = element.closest('.supplier-product-block');
        const unitInput = parentBlock.querySelector('input[name="sp_unit[]"]');
        const pieceRateInput = parentBlock.querySelector('input[name="sp_piece_rate[]"]');
        const purchaseInput = parentBlock.querySelector('input[name="sp_purchase_price[]"]');

        const units = parseFloat(unitInput.value) || 0;
        const pieceRate = parseFloat(pieceRateInput.value) || 0;
        
        if (units > 0 && pieceRate >= 0) {
            purchaseInput.value = (units * pieceRate).toFixed(2);
        } else {
            purchaseInput.value = '';
        }
        
        calculateSupplierSelling(element);
        calculateTotalSupplierPurchase();
    }

    function calculateTotalSupplierPurchase() {
        const purchaseInputs = document.querySelectorAll('input[name="sp_purchase_price[]"]');
        let total = 0;
        purchaseInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalAmountInput = document.getElementById('total_amount_purchased');
        if (totalAmountInput) {
            totalAmountInput.value = total > 0 ? total.toFixed(2) : '';
        }
    }

    function removeSupplierProductField(btn) {
        btn.parentElement.remove();
        calculateTotalSupplierPurchase();
    }

    function togglePaymentAmount(checkbox, inputId) {
        const input = document.getElementById(inputId);
        if (checkbox.checked) {
            input.classList.remove('hidden');
        } else {
            input.classList.add('hidden');
            input.value = '';
        }
    }


    function handleSupplierSubmit(event) {
        event.preventDefault();
        clearErrors();
        let isValid = true;
        
        const formData = new FormData(event.target);
        
        // Build supplier object
        const data = {
            supplier_name: formData.get('supplier_name'),
            mobile_number: formData.get('mobile_number'),
            alt_mobile_number: formData.get('alt_mobile_number'),
            email: formData.get('email'),
            address: formData.get('address'),
            city: formData.get('city'),
            state: formData.get('state'),
            pincode: formData.get('pincode'),
            gst_number: formData.get('gst_number'),
            pan_number: formData.get('pan_number'),
            payment_method: formData.get('payment_method'),
            total_amount_purchased: formData.get('total_amount_purchased'),
            notes: formData.get('notes'),
            status: formData.get('status')
        };

        // 1. Validate Required Fields for Supplier
        const requiredFields = ['supplier_name', 'mobile_number', 'address'];
        requiredFields.forEach(field => {
            if (!data[field] || data[field].trim() === '') {
                showError(field, 'Required');
                isValid = false;
            }
        });

        // Add to array
        let supplierId;
        if (currentEditId) {
            supplierId = currentEditId;
            const index = suppliers.findIndex(s => s.id === currentEditId);
            if (index > -1) {
                data.barcode = suppliers[index].barcode || Math.floor(100000 + Math.random() * 900000).toString();
                suppliers[index] = { ...data, id: currentEditId };
            }
        } else {
            supplierId = Date.now();
            data.id = supplierId;
            data.barcode = Math.floor(100000 + Math.random() * 900000).toString();
            suppliers.push(data);
        }

        // Process products
        const sp_names = formData.getAll('sp_product_name[]');
        const sp_cats = formData.getAll('sp_product_category[]');
        const sp_units = formData.getAll('sp_unit[]');
        const sp_piece_rates = formData.getAll('sp_piece_rate[]');
        const sp_purchases = formData.getAll('sp_purchase_price[]');
        const sp_sells = formData.getAll('sp_selling_price[]');
        const sp_gsts = formData.getAll('sp_gst[]');

        const sp_barcodes = formData.getAll('sp_barcode[]');
        const sp_ids = formData.getAll('sp_product_id[]');
        
        // Remove old products linked to this supplier if editing (to replace with the new list)
        if (currentEditId) {
            products = products.filter(p => p.prod_supplier_id !== currentEditId);
        }

        // Add new product list
        for (let i = 0; i < sp_names.length; i++) {
            if (sp_names[i].trim() !== '') {
                let prodId = sp_ids[i] ? parseInt(sp_ids[i]) : Date.now() + i;
                products.push({
                    id: prodId,
                    prod_supplier_id: supplierId,
                    prod_name: sp_names[i],
                    prod_sku: 'SKU-' + Math.floor(Math.random() * 90000),
                    prod_barcode: sp_barcodes[i] || ('BAR-' + Math.floor(1000000000 + Math.random() * 9000000000)),
                    prod_category: sp_cats[i],
                    prod_unit: sp_units[i],
                    prod_piece_rate: sp_piece_rates[i],
                    prod_purchase_price: sp_purchases[i],
                    prod_selling_price: sp_sells[i],
                    prod_gst: sp_gsts[i],
                    prod_opening_stock: 0,
                    prod_current_stock: 0,
                    prod_status: 'Active'
                });
            }
        }
        
        alert(currentEditId ? 'Supplier and associated products updated.' : 'Supplier and products saved.');
        
        closeSupplierModal();
        renderSupplierTable();
        renderProductTable();
        renderBarcodeTable();
    }

    function renderBarcodeTable() {
        const tbody = document.getElementById('barcode-table-body');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr id="barcode-empty-state-row">
                    <td colspan="5" class="py-12 text-center text-emerald-400">
                        <i class="fas fa-barcode text-3xl mb-3 block text-emerald-300"></i>
                        <p class="font-bold text-emerald-600 text-sm">No barcodes found</p>
                        <p class="text-xs mt-1">Add products or suppliers to generate barcodes.</p>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = '';
        products.forEach(p => {
            const supplier = suppliers.find(s => s.id === p.prod_supplier_id);
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/50 transition-colors';
            
            tr.innerHTML = `
                <td class="py-3 px-4 font-mono text-[13px] font-bold text-emerald-700 bg-emerald-50/30 tracking-wider">
                    ${p.prod_barcode || 'N/A'}
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${p.prod_name}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-[10px] font-bold border border-slate-200">
                        ${p.prod_category || 'N/A'}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-indigo-700">${supplier ? supplier.supplier_name : 'N/A'}</div>
                </td>
                <td class="py-3 px-4 text-right">
                    <button class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm flex items-center gap-2 ml-auto">
                        <i class="fas fa-print"></i> Print
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderSupplierTable() {
        const tbody = document.getElementById('supplier-table-body');
        if (suppliers.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-slate-300"></i>
                        <p class="font-bold text-slate-600 text-sm">No suppliers found</p>
                        <p class="text-xs mt-1">Click "Add Supplier" to create your first entry.</p>
                    </td>
                </tr>`;
            return;
        }
        
        tbody.innerHTML = '';
        
        suppliers.forEach(s => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            
            // Fetch products for this supplier
            const supplierProducts = products.filter(p => p.prod_supplier_id === s.id);
            
            tr.innerHTML = `
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${s.supplier_name}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5"><i class="fas fa-envelope text-slate-400 mr-1"></i>${s.email || 'N/A'}</div>
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-700">${s.mobile_number}</div>
                </td>
                <td class="py-3 px-4 font-mono text-[12px] font-bold text-indigo-700 bg-indigo-50/50 rounded-lg text-center tracking-wider">
                    ${s.barcode || 'N/A'}
                </td>
                <td class="py-3 px-4">
                    <div class="font-bold text-indigo-700 text-xs">${supplierProducts.length} Products</div>
                </td>
                <td class="py-3 px-4 font-mono text-[11px] text-slate-600 font-semibold">${s.gst_number || 'N/A'}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${s.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${s.status || 'Active'}
                    </span>
                </td>
                <td class="py-3 px-4 text-right space-x-1">
                    <button onclick="viewSupplier(${s.id})" title="View" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-indigo-600 text-xs transition-colors"><i class="fas fa-eye"></i></button>
                    <button onclick="editSupplier(${s.id})" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-600 text-xs transition-colors"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteSupplier(${s.id})" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs transition-colors"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function deleteSupplier(id) {
        if (confirm('Are you sure you want to delete this supplier? All associated products will also be removed.')) {
            suppliers = suppliers.filter(s => s.id !== id);
            products = products.filter(p => p.prod_supplier_id !== id);
            renderSupplierTable();
            renderProductTable();
        }
    }

    function viewSupplier(id) {
        const s = suppliers.find(sup => sup.id === id);
        if (!s) return;
        
        const supplierProducts = products.filter(p => p.prod_supplier_id === id);
        let productsHtml = supplierProducts.map(p => `<li>- ${p.prod_name} (${p.prod_category}) : ₹${p.prod_purchase_price}</li>`).join('');
        if(!productsHtml) productsHtml = "<li>No products linked.</li>";
        
        alert('Viewing Supplier: ' + s.supplier_name + '\nMobile: ' + s.mobile_number + '\nAddress: ' + s.address + '\n\nProducts Supplied:\n' + productsHtml.replace(/<li>/g, '').replace(/<\/li>/g, '\n'));
    }

    function editSupplier(id) {
        const s = suppliers.find(sup => sup.id === id);
        if (!s) return;
        
        currentEditId = id;
        clearErrors();
        document.getElementById('supplier-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Supplier';
        
        const form = document.getElementById('supplier-form');
        
        // Load basic supplier fields
        Object.keys(s).forEach(key => {
            const input = form.elements[key];
            if (input && input.name && !input.name.includes('[]')) input.value = s[key];
        });
        
        // Load associated products
        document.getElementById('supplier-products-container').innerHTML = '';
        const supplierProducts = products.filter(p => p.prod_supplier_id === id);
        
        if (supplierProducts.length > 0) {
            supplierProducts.forEach(p => addSupplierProductField(p));
        } else {
            addSupplierProductField();
        }
        
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    // --- PRODUCT MANAGEMENT LOGIC ---
    let products = [];
    let currentProductEditId = null;

    function openProductModal() {
        document.getElementById('product-form').reset();
        clearProductErrors();
        currentProductEditId = null;
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-box-open text-amber-600"></i> Add Product';
        
        // Populate supplier dropdown
        const supplierSelect = document.getElementById('prod_supplier_id');
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.supplier_name;
            supplierSelect.appendChild(opt);
        });


        toggleProductOffer();
        document.getElementById('product-modal').classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
    }

    function closeProductViewModal() {
        document.getElementById('product-view-modal').classList.add('hidden');
    }

    function clearProductErrors() {
        const errorSpans = document.querySelectorAll('span[id^="err-prod_"]');
        errorSpans.forEach(span => span.classList.add('hidden'));
        const inputs = document.querySelectorAll('#product-form input, #product-form select, #product-form textarea');
        inputs.forEach(input => input.classList.remove('border-rose-500'));
    }

    function showProductError(fieldId, message = null) {
        const span = document.getElementById('err-' + fieldId);
        const input = document.getElementById(fieldId);
        if (span) {
            if (message) span.textContent = message;
            span.classList.remove('hidden');
        }
        if (input) {
            input.classList.add('border-rose-500');
        }
    }

    function calculateSellingPrice() {
        const purchasePrice = parseFloat(document.getElementById('prod_purchase_price').value) || 0;
        const marginType = document.getElementById('prod_margin_type').value;
        const marginValue = parseFloat(document.getElementById('prod_margin_value').value) || 0;
        let sellingPrice = purchasePrice;

        if (purchasePrice > 0 && marginValue >= 0) {
            if (marginType === '%') {
                sellingPrice = purchasePrice + (purchasePrice * marginValue / 100);
            } else {
                sellingPrice = purchasePrice + marginValue;
            }
        }
        document.getElementById('prod_selling_price').value = sellingPrice.toFixed(2);
        const mrpEl = document.getElementById('prod_mrp');
        if (mrpEl) mrpEl.value = sellingPrice.toFixed(2);
    }

    function toggleProductOffer() {
        const offerAvailable = document.getElementById('prod_offer_available').value;
        const offerFields = document.querySelectorAll('.prod-offer-field');
        if (offerAvailable === 'Yes') {
            offerFields.forEach(f => {
                f.classList.remove('opacity-50', 'pointer-events-none');
                const input = f.querySelector('input, select');
                if (input) input.disabled = false;
            });
        } else {
            offerFields.forEach(f => {
                f.classList.add('opacity-50', 'pointer-events-none');
                const input = f.querySelector('input, select');
                if (input) {
                    input.disabled = true;
                    if (input.tagName === 'INPUT') input.value = '';
                }
            });
        }
    }

    function handleProductSubmit(event) {
        event.preventDefault();
        clearProductErrors();
        let isValid = true;
        
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());

        const requiredFields = ['prod_name', 'prod_category', 'prod_supplier_id', 'prod_purchase_price', 'prod_selling_price', 'prod_gst'];
        requiredFields.forEach(field => {
            if (!data[field] || data[field].trim() === '') {
                showProductError(field);
                isValid = false;
            }
        });

        const numericFields = ['prod_purchase_price', 'prod_selling_price', 'prod_gst', 'prod_margin_value', 'prod_offer_amount'];
        numericFields.forEach(field => {
            if (data[field] && parseFloat(data[field]) < 0) {
                showProductError(field, 'Cannot be negative');
                isValid = false;
            }
        });

        if (data.prod_purchase_price && data.prod_selling_price) {
            if (parseFloat(data.prod_selling_price) < parseFloat(data.prod_purchase_price)) {
                showProductError('prod_selling_price', 'Selling price cannot be lower than purchase price');
                isValid = false;
            }
        }

        if (data.prod_offer_available === 'Yes' && data.prod_offer_amount) {
            if (data.prod_offer_type === '%' && parseFloat(data.prod_offer_amount) > 100) {
                showProductError('prod_offer_amount', 'Cannot exceed 100%');
                isValid = false;
            } else if (data.prod_offer_type === '₹' && parseFloat(data.prod_offer_amount) > parseFloat(data.prod_selling_price)) {
                showProductError('prod_offer_amount', 'Cannot exceed selling price');
                isValid = false;
            }
        }

        if (!isValid) return;

        data.prod_current_stock = 0;
        data.prod_opening_stock = 0;
        data.prod_sku = data.prod_sku || 'SKU-' + Math.floor(Math.random() * 90000);

        if (currentProductEditId) {
            const index = products.findIndex(p => p.id === currentProductEditId);
            if (index > -1) {
                data.prod_barcode = products[index].prod_barcode || data.prod_barcode || Math.floor(100000 + Math.random() * 900000).toString();
                products[index] = { ...data, id: currentProductEditId };
            }
            alert('Product updated successfully.');
        } else {
            data.id = Date.now();
            data.prod_barcode = data.prod_barcode || Math.floor(100000 + Math.random() * 900000).toString();
            products.push(data);
            alert('Product added successfully.');
        }
        
        closeProductModal();
        renderProductTable();
        renderSupplierTable(); // Update the supplier product counts
    }

    function renderProductTable() {
        const tbody = document.getElementById('product-table-body');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr id="product-empty-state-row">
                    <td colspan="10" class="py-12 text-center text-amber-400">
                        <i class="fas fa-box-open text-3xl mb-3 block text-amber-300"></i>
                        <p class="font-bold text-amber-600 text-sm">No products found</p>
                        <p class="text-xs mt-1">Click "Add Product" to create your first entry.</p>
                    </td>
                </tr>`;
            return;
        }
        
        tbody.innerHTML = '';
        
        products.forEach(p => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-amber-50/30 transition-colors';
            
            let offerDisplay = 'N/A';
            if (p.prod_offer_available === 'Yes' && p.prod_offer_amount) {
                offerDisplay = p.prod_offer_type === '%' ? `${p.prod_offer_amount}%` : `₹${parseFloat(p.prod_offer_amount).toFixed(2)}`;
            }
            
            let supplierName = 'Unknown';
            const sup = suppliers.find(s => s.id == p.prod_supplier_id);
            if(sup) supplierName = sup.supplier_name;

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <div class="font-bold text-slate-800">${p.prod_name}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5"><i class="fas fa-barcode text-slate-400 mr-1"></i>${p.prod_sku} / ${p.prod_barcode}</div>
                </td>
                <td class="py-3 px-4">
                    <div class="text-[10px] text-amber-700 bg-amber-50 inline-block px-2 py-0.5 mt-0.5 rounded-md border border-amber-100 font-medium">${p.prod_category}</div>
                </td>
                <td class="py-3 px-4 font-bold text-slate-700 text-xs">${supplierName}</td>
                <td class="py-3 px-4 font-mono">
                    <div class="text-slate-900 font-bold text-xs">P: ₹${parseFloat(p.prod_purchase_price).toFixed(2)}</div>
                    <div class="text-[10px] text-emerald-600 font-bold">S: ₹${parseFloat(p.prod_selling_price).toFixed(2)}</div>
                </td>
                <td class="py-3 px-4 font-mono text-[11px] text-slate-600 font-semibold">${p.prod_gst}%</td>
                <td class="py-3 px-4 font-bold ${p.prod_current_stock <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-700'} text-xs">${p.prod_current_stock} ${p.prod_unit}</td>
                <td class="py-3 px-4 font-bold text-fuchsia-600 text-xs">${offerDisplay}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${p.prod_status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${p.prod_status}
                    </span>
                </td>
                <td class="py-3 px-4 text-right space-x-1">
                    <button onclick="viewProduct(${p.id})" title="View" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-600 text-xs transition-colors"><i class="fas fa-eye"></i></button>
                    <button onclick="editProduct(${p.id})" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-indigo-600 text-xs transition-colors"><i class="fas fa-edit"></i></button>
                    <button onclick="deleteProduct(${p.id})" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs transition-colors"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            products = products.filter(p => p.id !== id);
            renderProductTable();
            renderSupplierTable(); // Update the supplier product counts
        }
    }

    function viewProduct(id) {
        const p = products.find(prod => prod.id === id);
        if (!p) return;
        
        let supplierName = 'Unknown';
        const sup = suppliers.find(s => s.id == p.prod_supplier_id);
        if(sup) supplierName = sup.supplier_name;

        let finalPrice = parseFloat(p.prod_selling_price);
        if (p.prod_offer_available === 'Yes' && p.prod_offer_amount) {
            if (p.prod_offer_type === '%') {
                finalPrice = finalPrice - (finalPrice * parseFloat(p.prod_offer_amount) / 100);
            } else {
                finalPrice = finalPrice - parseFloat(p.prod_offer_amount);
            }
        }

        const html = `
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Product Name</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_name}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">SKU / Barcode</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_sku} / ${p.prod_barcode || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Category / Brand</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_category} ${p.prod_brand ? '- ' + p.prod_brand : ''}</p>
                    </div>
                </div>
                
                <!-- Supplier Information -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 border-b border-slate-100 pb-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider">Supplier Name</p>
                        <p class="font-bold text-indigo-900 text-sm mt-0.5">${supplierName}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mobile Number</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup ? sup.mobile_number : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup && sup.email ? sup.email : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST Number</p>
                        <p class="font-mono text-slate-700 text-xs font-semibold mt-0.5">${sup && sup.gst_number ? sup.gst_number : 'N/A'}</p>
                    </div>
                    <div class="col-span-2 lg:col-span-4 mt-2 border-t border-slate-200/50 pt-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Address</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${sup && sup.address ? sup.address : 'N/A'}${sup && sup.city ? ', ' + sup.city : ''}${sup && sup.state ? ', ' + sup.state : ''}</p>
                    </div>
                </div>

                <!-- Pricing & Tax -->
                <div class="grid grid-cols-3 gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Purchase Price</p>
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${parseFloat(p.prod_purchase_price).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Selling Price</p>
                        <p class="font-bold text-emerald-600 text-sm mt-0.5">₹${parseFloat(p.prod_selling_price).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Final Price (Offer)</p>
                        <p class="font-bold text-indigo-600 text-sm mt-0.5">₹${finalPrice.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">GST</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_gst}% (${p.prod_tax_inclusive === 'Yes' ? 'Inclusive' : 'Exclusive'})</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">HSN/SAC Code</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_hsn || 'N/A'}</p>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Current Stock</p>
                        <p class="font-bold ${p.prod_current_stock <= (p.prod_min_stock || 0) ? 'text-rose-600' : 'text-slate-800'} text-lg mt-0.5">${p.prod_current_stock} ${p.prod_unit}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Min / Max Stock</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_min_stock || 0} / ${p.prod_max_stock || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Storage Location</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">${p.prod_location || 'N/A'}</p>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('product-view-content').innerHTML = html;
        document.getElementById('product-view-modal').classList.remove('hidden');
    }

    function editProduct(id) {
        const p = products.find(prod => prod.id === id);
        if (!p) return;
        
        currentProductEditId = id;
        clearProductErrors();
        document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-edit text-amber-500"></i> Edit Product';
        
        const supplierSelect = document.getElementById('prod_supplier_id');
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.supplier_name;
            supplierSelect.appendChild(opt);
        });

        const form = document.getElementById('product-form');
        Object.keys(p).forEach(key => {
            const input = form.elements[key];
            if (input) input.value = p[key];
        });
        
        toggleProductOffer();
        document.getElementById('product-modal').classList.remove('hidden');
    }
