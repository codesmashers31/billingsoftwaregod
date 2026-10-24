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

        <!-- Direct WhatsApp Gateway API Configuration (Silent Direct Sending without WhatsApp Web) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600 text-sm"></i> Direct WhatsApp Gateway API (Silent Background Sending)
                </h3>
                <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full border border-emerald-200 font-bold">No WhatsApp Web Required</span>
            </div>

            <p class="text-xs text-slate-500">
                To send bills directly to the customer's phone in the background without opening WhatsApp Web, enter your WhatsApp Gateway API details (e.g., from <a href="https://ultramsg.com" target="_blank" class="text-emerald-600 font-bold underline">UltraMsg.com</a> or Meta WhatsApp Cloud API):
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp Gateway Instance ID (UltraMsg)</label>
                    <input type="text" name="whatsapp_instance_id" value="<?= sanitize(getSetting('whatsapp_instance_id', '')) ?>" placeholder="e.g. instance12345" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp API Token / Key</label>
                    <input type="password" name="whatsapp_token" value="<?= sanitize(getSetting('whatsapp_token', '')) ?>" placeholder="Enter API Token" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 font-mono focus:border-emerald-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[11px] text-slate-400">
                        <i class="fas fa-info-circle text-amber-500 mr-1"></i>
                        When configured, bills are dispatched silently from the server to the customer's WhatsApp instantly. If not configured, bills close instantly on screen without popping open WhatsApp Web.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="gold-btn px-8 py-2.5 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                <i class="fas fa-save"></i> Save ERP Settings
            </button>
        </div>
    </form>
</div>
