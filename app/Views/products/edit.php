<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Edit Statue: <?= sanitize($product['name']) ?></h1>
            <p class="text-xs text-slate-500">Modify deity specs, dimensions, retail pricing, and showroom location</p>
        </div>
        <a href="<?= url('products') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
            <i class="fas fa-arrow-left"></i> Back to Catalog
        </a>
    </div>

    <!-- Edit Form -->
    <form action="<?= url('products/edit/' . $product['id']) ?>" method="POST" enctype="multipart/form-data" data-ajax="true" class="space-y-6">
        <?= csrf_field() ?>

        <!-- 1. Classification & Basic Info -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-info-circle text-amber-600"></i> 1. Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Product Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="<?= sanitize($product['name']) ?>" required 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="prod-category-select" required onchange="loadSubcategories(this.value)" 
                            class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory / Deity Form</label>
                    <select name="subcategory_id" id="prod-subcategory-select" 
                            class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="">None / General</option>
                        <?php foreach ($subcategories as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $product['subcategory_id'] == $s['id'] ? 'selected' : '' ?>><?= sanitize($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Brand / Studio Maker</label>
                    <input type="text" name="brand" value="<?= sanitize($product['brand'] ?? 'Divine Heritage') ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="1" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none"><?= sanitize($product['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Deity Characteristics -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-om text-amber-600"></i> 2. Deity Characteristics & Metal Casting Specs
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">God / Deity Name</label>
                    <input type="text" name="god_name" value="<?= sanitize($product['god_name'] ?? '') ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Material Composition</label>
                    <select name="material" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <?php foreach (['Brass', 'Panchaloha', 'White Marble', 'Black Stone', 'Bronze', 'Copper', 'Teak Wood', 'Resin'] as $m): ?>
                            <option value="<?= $m ?>" <?= $product['material'] == $m ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Finish Type</label>
                    <select name="finish_type" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <?php foreach (['Antique', 'Gold Plated', 'Glossy', 'Matte', 'Natural'] as $f): ?>
                            <option value="<?= $f ?>" <?= $product['finish_type'] == $f ? 'selected' : '' ?>><?= $f ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Size Classification</label>
                    <select name="size" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <?php foreach (['Small', 'Medium', 'Large', 'Temple Size'] as $sz): ?>
                            <option value="<?= $sz ?>" <?= $product['size'] == $sz ? 'selected' : '' ?>><?= $sz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Height (inches)</label>
                    <input type="number" step="0.1" name="height" value="<?= $product['height'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Width (inches)</label>
                    <input type="number" step="0.1" name="width" value="<?= $product['width'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Depth / Length (inches)</label>
                    <input type="number" step="0.1" name="length" value="<?= $product['length'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Net Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" value="<?= $product['weight'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Pricing & Taxes -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-tags text-amber-600"></i> 3. Pricing, Wholesale & GST Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Retail Selling Price (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="selling_price" value="<?= $product['selling_price'] ?>" required 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs font-black text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Artisan Purchase Cost (₹)</label>
                    <input type="number" step="0.01" name="purchase_price" value="<?= $product['purchase_price'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Wholesale Rate (₹)</label>
                    <input type="number" step="0.01" name="wholesale_price" value="<?= $product['wholesale_price'] ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GST Tax Rate (%)</label>
                    <select name="gst_percent" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <?php foreach ([0, 5, 12, 18, 28] as $t): ?>
                            <option value="<?= $t ?>" <?= $product['gst_percent'] == $t ? 'selected' : '' ?>><?= $t ?>%</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. Locations & Thresholds -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-boxes text-amber-600"></i> 4. Inventory Parameters & Thresholds
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Low Stock Alert Threshold</label>
                    <input type="number" name="min_stock" value="<?= $product['min_stock'] ?>" min="1" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Showroom / Rack Location</label>
                    <input type="text" name="stock_location" value="<?= sanitize($product['stock_location'] ?? '') ?>" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catalog Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="active" <?= $product['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $product['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="out_of_stock" <?= $product['status'] == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-2 flex justify-end gap-3">
            <a href="<?= url('products') ?>" class="px-6 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Cancel
            </a>
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-save"></i> Save Product Changes
            </button>
        </div>
    </form>
</div>

<script>
async function loadSubcategories(categoryId) {
    const select = document.getElementById('prod-subcategory-select');
    select.innerHTML = '<option value="">Loading...</option>';

    if (!categoryId) {
        select.innerHTML = '<option value="">Select Category First</option>';
        return;
    }

    try {
        const res = await fetch(window.APP_URL + '/subcategories/by-category/' + categoryId);
        const data = await res.json();
        let html = '<option value="">None / General</option>';
        (data.data || []).forEach(s => {
            html += `<option value="${s.id}">${s.name}</option>`;
        });
        select.innerHTML = html;
    } catch(err) {
        console.error(err);
        select.innerHTML = '<option value="">None available</option>';
    }
}
</script>
