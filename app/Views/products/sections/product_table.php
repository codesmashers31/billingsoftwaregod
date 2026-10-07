<!-- Product Section -->
        <div id="product-section" class="hidden animate-fade-in bg-amber-50/50 border border-amber-100 rounded-2xl p-6 shadow-inner">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-amber-900">Product Management</h4>
                    <p class="text-xs text-amber-600/80 mt-1 font-medium">Add new products to your catalog and manage inventory.</p>
                </div>
                <button onclick="openProductModal()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>

            <!-- Product Table -->
            <div class="overflow-x-auto rounded-xl border border-amber-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-amber-100/50 text-amber-700 uppercase text-[10px] font-bold border-b border-amber-200">
                        <tr>
                            <th class="py-3 px-4">Product & SKU</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Prices (Purchase/Sell)</th>
                            <th class="py-3 px-4">GST</th>
                            <th class="py-3 px-4">Stock</th>
                            <th class="py-3 px-4">Offer</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body" class="divide-y divide-amber-100 bg-white">
                        <tr id="product-empty-state-row">
                            <td colspan="9" class="py-12 text-center text-amber-400">
                                <i class="fas fa-box-open text-3xl mb-3 block text-amber-300"></i>
                                <p class="font-bold text-amber-600 text-sm">No products found</p>
                                <p class="text-xs mt-1">Click "Add Product" to create your first entry.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
