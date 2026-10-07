<!-- Supplier Section with Table -->
        <div id="supplier-section" class="hidden animate-fade-in bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left mb-6">
                <div>
                    <h4 class="text-xl font-extrabold text-slate-900">Supplier Management</h4>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Add new suppliers and manage product associations.</p>
                </div>
                <button onclick="openSupplierModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>

            <!-- Supplier Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Mobile</th>
                            <th class="py-3 px-4">Barcode No.</th>
                            <th class="py-3 px-4">Products</th>
                            <th class="py-3 px-4">GST Number</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplier-table-body" class="divide-y divide-slate-100">
                        <tr id="empty-state-row">
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-3xl mb-3 block text-slate-300"></i>
                                <p class="font-bold text-slate-600 text-sm">No suppliers found</p>
                                <p class="text-xs mt-1">Click "Add Supplier" to create your first entry.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
