<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">ERP Store Settings & Configuration</h1>
            <p class="text-xs text-slate-500">Configure business identity, GST details, POS invoice prefixes, and thermal receipt notes</p>
        </div>
    </div>

    <form action="<?= url('settings') ?>" method="POST" data-ajax="true" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Business Identity -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-store text-amber-600"></i> Store Identity & Address
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Company / Showroom Name</label>
                    <input type="text" name="company_name" value="<?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">GSTIN Number</label>
                    <input type="text" name="company_gstin" value="<?= sanitize(getSetting('company_gstin', '33AABCG1234H1Z0')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                    <input type="text" name="company_phone" value="<?= sanitize(getSetting('company_phone', '+91 44 2464 1008')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Contact Email</label>
                    <input type="email" name="company_email" value="<?= sanitize(getSetting('company_email', 'contact@divyamurti.com')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Physical Address</label>
                    <textarea name="company_address" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"><?= sanitize(getSetting('company_address', '108 Sannidhi Square, Mylapore, Chennai - 600004')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Invoicing & Receipts Configuration -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fas fa-receipt text-amber-600"></i> Invoicing & Thermal Receipt Customization
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="<?= sanitize(getSetting('invoice_prefix', 'INV-')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Thermal Receipt Header Tagline</label>
                    <input type="text" name="thermal_header" value="<?= sanitize(getSetting('thermal_header', 'SACRED IDOLS & TEMPLE ARTIFACTS')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Footer Consecration Note</label>
                    <input type="text" name="invoice_footer_note" value="<?= sanitize(getSetting('invoice_footer_note', 'All idols are handcrafted and casted in accordance with Shilpa Shastra.')) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Thermal Receipt Footer Terms</label>
                    <textarea name="thermal_footer_terms" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:border-amber-500 outline-none"><?= sanitize(getSetting('thermal_footer_terms', 'Goods once sold can be exchanged within 7 days with bill in original consecrated packaging.')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Direct WhatsApp API Integration (Free Official Meta API & Local Bridge) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600 text-lg"></i> Direct WhatsApp Bill Delivery (Free Official API)
                </h3>
                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full border border-emerald-300 font-bold flex items-center gap-1.5">
                    <i class="fas fa-bolt text-emerald-600"></i> 100% Free Direct API
                </span>
            </div>

            <!-- Option 1: Official Free Meta WhatsApp Cloud API (Recommended) -->
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-950 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white inline-flex items-center justify-center text-[10px]">1</span>
                        Official Meta WhatsApp Cloud API (Free Tier from WhatsApp)
                    </span>
                    <span class="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded-md border border-emerald-300">
                        1,000 Free Messages / Month
                    </span>
                </div>
                <p class="text-[11px] text-emerald-900 leading-relaxed">
                    Provided directly by <strong>WhatsApp / Meta</strong> with zero monthly subscription. Send instant itemized bills without opening WhatsApp Web.
                    Get your free credentials from <a href="https://developers.facebook.com" target="_blank" class="font-bold underline text-emerald-700 hover:text-emerald-900">Meta Developers Portal &nearr;</a>
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            WhatsApp Phone Number ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="whatsapp_phone_number_id" value="<?= sanitize(getSetting('whatsapp_phone_number_id', '')) ?>" 
                               placeholder="e.g. 105943282498234" 
                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            Meta Permanent Access Token <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="whatsapp_meta_token" value="<?= sanitize(getSetting('whatsapp_meta_token', '')) ?>" 
                               placeholder="EAABwz..." 
                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- Option 2: Local Free WhatsApp Bridge (From Your Shop Mobile Phone) -->
            <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-950 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white inline-flex items-center justify-center text-[10px]">2</span>
                        Local WhatsApp Server Bridge (100% Free Forever from Shop Phone)
                    </span>
                    <span class="text-[10px] font-bold text-indigo-700 bg-white px-2 py-0.5 rounded-md border border-indigo-300">
                        Unlimited Free Bills
                    </span>
                </div>
                <p class="text-[11px] text-indigo-900 leading-relaxed">
                    Runs locally on your computer. Scans your shop WhatsApp once with QR code, then automatically dispatches bills silently from your phone number without any limit or Meta fees.
                </p>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Local Bridge Endpoint URL</label>
                    <input type="text" name="whatsapp_custom_webhook" value="<?= sanitize(getSetting('whatsapp_custom_webhook', 'http://localhost:3000/send')) ?>" 
                           placeholder="http://localhost:3000/send" 
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono focus:border-indigo-500 outline-none">
                </div>
            </div>

            <!-- Option 3: Optional UltraMsg Gateway -->
            <details class="text-xs text-slate-500 border border-slate-200 rounded-xl p-3 bg-slate-50">
                <summary class="font-bold text-slate-700 cursor-pointer hover:text-slate-900 flex items-center justify-between">
                    <span>Option 3: UltraMsg Gateway (Optional 3rd-party)</span>
                    <span class="text-[10px] text-slate-400">Click to expand</span>
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 pt-2 border-t border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">UltraMsg Instance ID</label>
                        <input type="text" name="whatsapp_instance_id" value="<?= sanitize(getSetting('whatsapp_instance_id', '')) ?>" placeholder="e.g. instance12345" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">UltraMsg Token</label>
                        <input type="password" name="whatsapp_token" value="<?= sanitize(getSetting('whatsapp_token', '')) ?>" placeholder="Token" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                    </div>
                </div>
            </details>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-save"></i> Save ERP Settings
            </button>
        </div>
    </form>
</div>
