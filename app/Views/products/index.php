<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">God Statues & Religious Catalog</h1>
            <p class="text-xs text-slate-500">Complete idol specifications: metal composition, height, weight, Shilpa finish, and live stock</p>
        </div>
        <div class="flex items-center gap-2.5">
            <?php if (hasPermission('products.export')): ?>
            <a href="<?= url('products/export') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                <i class="fas fa-file-excel text-emerald-600"></i> Export CSV
            </a>
            <?php endif; ?>
            <?php if (hasPermission('products.create')): ?>
            <a href="<?= url('products/create') ?>" class="gold-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> Add New Idol
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="<?= url('products') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= sanitize($filters['search'] ?? '') ?>" placeholder="Search by name, SKU, barcode, deity, material..." 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none">
            </div>
            <div>
                <select name="category_id" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="material" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                    <option value="">All Materials</option>
                    <?php foreach ($materials as $m): ?>
                        <option value="<?= $m ?>" <?= ($filters['material'] ?? '') == $m ? 'selected' : '' ?>><?= sanitize($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="<?= url('products') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 px-3 py-2 rounded-xl text-xs">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Products Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Statue / Product</th>
                        <th class="py-3.5 px-4">Category & Material</th>
                        <th class="py-3.5 px-4">Dimensions & Weight</th>
                        <th class="py-3.5 px-4">Retail Price</th>
                        <th class="py-3.5 px-4">Current Stock</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <i class="fas fa-dharmachakra text-4xl mb-3 block text-slate-300"></i>
                                <p class="font-bold text-slate-700">No statues found in catalog</p>
                                <p class="text-xs text-slate-400 mt-1">Add your first handcrafted deity idol</p>
                            </td>
                        </tr>
                    <?php else: foreach ($products as $p): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 overflow-hidden flex items-center justify-center shrink-0">
                                        <i class="fas fa-om text-amber-700 text-base"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 max-w-xs line-clamp-1"><?= sanitize($p['name']) ?></div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            <span class="text-amber-800 font-bold"><?= $p['code'] ?></span> • SKU: <?= $p['sku'] ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800"><?= sanitize($p['category_name']) ?></div>
                                <div class="text-[10px] text-amber-700 font-semibold mt-0.5">
                                    <?= sanitize($p['material']) ?> (<?= sanitize($p['finish_type'] ?? 'Natural') ?>)
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div><?= $p['height'] ? $p['height'] . '" Height' : 'N/A' ?></div>
                                <div class="text-[10px] text-slate-400"><?= $p['weight'] ? $p['weight'] . ' kg' : '' ?></div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <div class="text-slate-900 font-black text-sm"><?= formatCurrency($p['selling_price']) ?></div>
                                <div class="text-[10px] text-slate-400">GST <?= $p['gst_percent'] ?>% (HSN: <?= $p['hsn_code'] ?>)</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <?php if ($p['current_stock'] <= $p['min_stock']): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px] border border-rose-200">
                                        <?= $p['current_stock'] ?> units (Low)
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200">
                                        <?= $p['current_stock'] ?> units
                                    </span>
                                <?php endif; ?>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= sanitize($p['stock_location'] ?? 'Showroom') ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $p['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <button onclick="viewProductModal(<?= $p['id'] ?>)" title="View Specs & History" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if (hasPermission('products.edit')): ?>
                                <a href="<?= url('products/edit/' . $p['id']) ?>" title="Edit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-amber-700 text-xs inline-block">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('products.delete')): ?>
                                <button onclick="deleteProduct(<?= $p['id'] ?>)" title="Delete" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs">
                                    <i class="fas fa-trash-alt"></i>
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
        <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Showing Page <?= $pagination['page'] ?> of <?= $pagination['total_pages'] ?> (<?= $pagination['total'] ?> products)</span>
            <div class="flex items-center gap-1.5">
                <?php if ($pagination['page'] > 1): ?>
                    <a href="<?= url('products?page=' . ($pagination['page'] - 1)) ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">Prev</a>
                <?php endif; ?>
                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a href="<?= url('products?page=' . ($pagination['page'] + 1)) ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Product Detailed Specs View -->
<div id="modal-product-view" class="pos-modal hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900" id="modal-view-title">Product Details</h3>
            <button onclick="closeModal('modal-product-view')" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>

        <div id="modal-view-content" class="space-y-4 text-xs">
            <!-- Loaded dynamically via AJAX -->
        </div>
    </div>
</div>

<script>
async function viewProductModal(id) {
    try {
        const res = await fetch(window.APP_URL + '/products/view/' + id);
        const data = await res.json();
        if (data.success && data.product) {
            const p = data.product;
            document.getElementById('modal-view-title').textContent = p.name;
            let histHtml = '';
            (data.history || []).forEach(h => {
                histHtml += `
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] mb-1.5">
                        <div>
                            <span class="font-bold uppercase text-amber-800">${h.transaction_type}</span>
                            <span class="text-slate-500 font-mono ml-2">${h.reference_no || ''}</span>
                        </div>
                        <div class="font-mono font-bold ${h.quantity > 0 ? 'text-emerald-700' : 'text-rose-700'}">
                            ${h.quantity > 0 ? '+' : ''}${h.quantity} units (Bal: ${h.new_stock})
                        </div>
                    </div>
                `;
            });

            document.getElementById('modal-view-content').innerHTML = `
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Deity / God Name</span>
                        <span class="text-slate-900 font-bold text-sm">${p.god_name || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Metal / Material</span>
                        <span class="text-amber-800 font-bold text-sm">${p.material || 'N/A'} (${p.finish_type || 'Natural'})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Dimensions (H x W x L)</span>
                        <span class="text-slate-800 font-medium">${p.height || 0}" x ${p.width || 0}" x ${p.length || 0}"</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Net Weight</span>
                        <span class="text-slate-800 font-medium">${p.weight || 0} kg</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Pricing</span>
                        <span class="text-slate-900 font-black font-mono">Retail: ₹${parseFloat(p.selling_price).toFixed(2)} | Cost: ₹${parseFloat(p.purchase_price).toFixed(2)}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Barcode / SKU</span>
                        <span class="text-amber-800 font-mono font-bold">${p.barcode}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Recent Stock Movement History</h5>
                    <div class="max-h-48 overflow-y-auto">${histHtml || '<p class="text-slate-400">No inventory history yet.</p>'}</div>
                </div>
            `;
            openModal('modal-product-view');
        }
    } catch (e) { console.error(e); }
}

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this statue from the catalog?')) return;
    try {
        const formData = new FormData();
        formData.append('_csrf_token', window.CSRF_TOKEN);
        const res = await fetch(window.APP_URL + '/products/delete/' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 500);
        } else {
            showToast('error', data.message);
        }
    } catch (e) { console.error(e); }
}
</script>
