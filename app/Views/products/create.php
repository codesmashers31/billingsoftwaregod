<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Add New God Statue / Religious Artifact</h1>
            <p class="text-xs text-slate-500">Register idol specifications, metal casting characteristics, dimensions, and initial stock</p>
        </div>
        <a href="<?= url('products') ?>" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
            <i class="fas fa-arrow-left"></i> Back to Catalog
        </a>
    </div>

    <!-- Create Form -->
    <form action="<?= url('products/create') ?>" method="POST" enctype="multipart/form-data" data-ajax="true" class="space-y-6">
        <?= csrf_field() ?>

        <!-- 1. Classification & Basic Info -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-info-circle text-amber-600"></i> 1. Basic Information & Identification
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Product Title / Statue Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Antique Brass Royal Ganesha 18 Inch" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">SKU (Stock Keeping Unit) <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" required placeholder="SKU-BRS-GAN-18" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Barcode / EAN (Auto-assigned if blank)</label>
                    <input type="text" name="barcode" placeholder="8901234500018" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="prod-category-select" required onchange="loadSubcategories(this.value)" 
                            class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subcategory / Deity Form</label>
                    <select name="subcategory_id" id="prod-subcategory-select" 
                            class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="">Select Category First</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Brand / Studio Maker</label>
                    <input type="text" name="brand" value="Swamimalai Bronzes" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description / Sacred Significance</label>
                    <textarea name="description" rows="1" placeholder="Sculpting details, Shilpa shastra style..." 
                              class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none"></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Sacred Statue Specific Specifications -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-om text-amber-600"></i> 2. Deity Characteristics & Metal Casting Specs
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">God / Deity Name</label>
                    <input type="text" name="god_name" placeholder="Lord Ganesha" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Material Composition</label>
                    <select name="material" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="Brass">Brass (Solid Yellow Brass)</option>
                        <option value="Panchaloha">Panchaloha (5 Sacred Metals)</option>
                        <option value="White Marble">White Makrana Marble</option>
                        <option value="Black Stone">Black Granite / Softstone</option>
                        <option value="Bronze">Lost-wax Cast Bronze</option>
                        <option value="Copper">Pure Copper</option>
                        <option value="Teak Wood">Handcrafted Teak Wood</option>
                        <option value="Resin">Polyresin Artifact</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Finish Type</label>
                    <select name="finish_type" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="Antique">Antique Patina</option>
                        <option value="Gold Plated">24K Gold Plated</option>
                        <option value="Glossy">Mirror Polish Glossy</option>
                        <option value="Matte">Matte Rustic</option>
                        <option value="Natural">Natural Raw Stone/Wood</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Size Classification</label>
                    <select name="size" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="Small">Small (Pooja Room: 3" - 8")</option>
                        <option value="Medium" selected>Medium (Altar: 9" - 18")</option>
                        <option value="Large">Large (Foyer / Hall: 19" - 36")</option>
                        <option value="Temple Size">Temple Consecration Size (3ft+)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Height (inches)</label>
                    <input type="number" step="0.1" name="height" placeholder="18.0" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Width (inches)</label>
                    <input type="number" step="0.1" name="width" placeholder="12.0" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Depth / Length (inches)</label>
                    <input type="number" step="0.1" name="length" placeholder="8.0" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Net Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" placeholder="14.50" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Pricing, GST & HSN -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-tags text-amber-600"></i> 3. Pricing, Wholesale & GST Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Retail Selling Price (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="selling_price" required placeholder="18900.00" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono font-bold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Artisan Purchase Cost (₹)</label>
                    <input type="number" step="0.01" name="purchase_price" placeholder="12500.00" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Wholesale Rate (₹)</label>
                    <input type="number" step="0.01" name="wholesale_price" placeholder="15500.00" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GST Tax Rate (%)</label>
                    <select name="gst_percent" class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                        <option value="0">0% (Nil / Exempted)</option>
                        <option value="5">5% (Handicrafts Lower)</option>
                        <option value="12" selected>12% (Standard Idols / Handicrafts)</option>
                        <option value="18">18% (Decor & Accessories)</option>
                        <option value="28">28% (Luxury Gold Foil Items)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">HSN / Tariff Code</label>
                    <input type="text" name="hsn_code" value="9701" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Default Discount (%)</label>
                    <input type="number" step="0.1" name="discount_percent" value="0.0" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>
            </div>
        </div>

        <!-- 4. Inventory, Locations & Opening Stock -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-boxes text-amber-600"></i> 4. Inventory Control & Physical Location
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Opening Physical Stock</label>
                    <input type="number" name="current_stock" value="5" min="0" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono font-bold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Low Stock Threshold</label>
                    <input type="number" name="min_stock" value="2" min="1" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Max Capacity</label>
                    <input type="number" name="max_stock" value="25" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Showroom / Rack Location</label>
                    <input type="text" name="stock_location" value="Main Showroom - Rack A1" 
                           class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-900 outline-none">
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-2 flex justify-end gap-3">
            <a href="<?= url('products') ?>" class="px-6 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Cancel
            </a>
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-save"></i> Save Product in Catalog
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
        let html = '<option value="">Select Subcategory</option>';
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
