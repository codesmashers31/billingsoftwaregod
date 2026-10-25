<div class="space-y-6 max-w-6xl mx-auto pb-12">
    <!-- Top Breadcrumbs & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="<?= url() ?>" class="hover:text-amber-600 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="<?= url('products') ?>" class="hover:text-amber-600 transition-colors">Products</a>
                <span>/</span>
                <span class="text-slate-600 font-semibold">Barcode Customization</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center shadow-md shadow-amber-500/20 text-lg">
                    <i class="fas fa-barcode"></i>
                </span>
                <span>Barcode Customization & Generator</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Configure your product barcode prefix (e.g. <strong class="text-amber-700">SS</strong>), 4-digit random number length, and POS scanner compatibility.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="<?= url('products') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-xs">
                <i class="fas fa-boxes text-slate-400"></i>
                <span>View Products</span>
            </a>
            <a href="<?= url('billing') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                <i class="fas fa-cash-register text-amber-400"></i>
                <span>Open POS Billing</span>
            </a>
        </div>
    </div>

    <!-- Status Alert if flashed -->
    <?php if ($msg = \App\Core\Session::flash('success')): ?>
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span><?= sanitize($msg) ?></span>
    </div>
    <?php endif; ?>

    <!-- Main Configuration Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Settings Form (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Barcode Generation Rules</h2>
                        <p class="text-[11px] text-slate-400">Customize starting word, digit count, and formatting</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Active Format: <strong id="badge-active-format"><?= sanitize($prefix . $separator . str_repeat('X', $numberLength)) ?></strong></span>
                </span>
            </div>

            <form id="barcode-settings-form" action="<?= url('barcode-settings') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <!-- 1. Barcode Prefix / Starting Word -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="barcode_prefix" class="block text-xs font-bold text-slate-800">
                            Barcode Prefix / Starting Word <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">e.g., <strong class="text-slate-600">SS</strong>, <strong class="text-slate-600">DIVYA</strong>, <strong class="text-slate-600">BAR</strong></span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                            <i class="fas fa-font"></i>
                        </div>
                        <input type="text" id="barcode_prefix" name="barcode_prefix" 
                               value="<?= sanitize($prefix) ?>" 
                               maxlength="10"
                               placeholder="e.g. SS"
                               required
                               autocomplete="off"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-24 py-2.5 text-sm font-bold text-slate-900 uppercase tracking-wider focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all">
                        <div class="absolute inset-y-0 right-2 flex items-center gap-1">
                            <button type="button" onclick="setQuickPrefix('SS')" class="px-2 py-1 text-[10px] font-extrabold rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-800 transition-colors">SS</button>
                            <button type="button" onclick="setQuickPrefix('DIVYA')" class="px-2 py-1 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">DIVYA</button>
                            <button type="button" onclick="setQuickPrefix('BAR')" class="px-2 py-1 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">BAR</button>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-amber-500"></i>
                        <span>This prefix appears at the beginning of every newly generated product barcode.</span>
                    </p>
                </div>

                <!-- 2. Separator Character -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Separator Character</label>
                    <div class="grid grid-cols-4 gap-2.5">
                        <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition-all <?= $separator === '-' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <input type="radio" name="barcode_separator" value="-" <?= $separator === '-' ? 'checked' : '' ?> class="accent-amber-600" onchange="updateLivePreview()">
                            <span class="text-xs">Hyphen <strong class="font-mono">(-)</strong></span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition-all <?= $separator === '_' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <input type="radio" name="barcode_separator" value="_" <?= $separator === '_' ? 'checked' : '' ?> class="accent-amber-600" onchange="updateLivePreview()">
                            <span class="text-xs">Under <strong class="font-mono">(_)</strong></span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition-all <?= $separator === '/' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <input type="radio" name="barcode_separator" value="/" <?= $separator === '/' ? 'checked' : '' ?> class="accent-amber-600" onchange="updateLivePreview()">
                            <span class="text-xs">Slash <strong class="font-mono">(/)</strong></span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition-all <?= $separator === '' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <input type="radio" name="barcode_separator" value="" <?= $separator === '' ? 'checked' : '' ?> class="accent-amber-600" onchange="updateLivePreview()">
                            <span class="text-xs">None <strong class="font-mono">(empty)</strong></span>
                        </label>
                    </div>
                </div>

                <!-- 3. Random Number Length (Digits) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="barcode_number_length" class="block text-xs font-bold text-slate-800">
                            Random Number Digits Count <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                            Current: <span id="label-digit-count"><?= (int)$numberLength ?> Digits</span> (<?= pow(10, $numberLength - 1) ?> - <?= pow(10, $numberLength) - 1 ?>)
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative w-32 shrink-0">
                            <input type="number" id="barcode_number_length" name="barcode_number_length" 
                                   value="<?= (int)$numberLength ?>" 
                                   min="3" max="8" 
                                   required
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 text-center focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="setDigitLength(4)" class="px-3 py-2 text-xs font-extrabold rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-800 transition-colors border border-amber-300">
                                4 Digits (Standard)
                            </button>
                            <button type="button" onclick="setDigitLength(5)" class="px-3 py-2 text-xs font-medium rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors border border-slate-200">
                                5 Digits
                            </button>
                            <button type="button" onclick="setDigitLength(6)" class="px-3 py-2 text-xs font-medium rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors border border-slate-200">
                                6 Digits
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">User request default is <strong>4 numbers</strong> (e.g., <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">SS-4829</code>), providing 9,000 unique sequential/random IDs per prefix.</p>
                </div>

                <!-- 4. Auto-Assign on Product Add Toggle -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="pr-4">
                        <div class="text-xs font-bold text-slate-800">Auto-Generate for New Products</div>
                        <div class="text-[11px] text-slate-500">Automatically fill the Barcode input with this format when adding products in Product Entries.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="barcode_auto_assign" value="1" <?= $autoAssign === '1' ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="resetToDefault()" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all">
                        <i class="fas fa-undo mr-1"></i> Reset to SS-XXXX Default
                    </button>
                    
                    <button type="submit" id="btn-save-settings" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shadow-amber-600/20 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save"></i>
                        <span>Save Barcode Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Live Interactive Preview & Barcode Tag Sticker (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Authentic Barcode Sticker Preview Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-800 rounded-2xl p-6 shadow-xl text-white">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 flex items-center gap-1.5">
                        <i class="fas fa-eye"></i> Live Barcode Label Preview
                    </span>
                    <button type="button" onclick="regenerateSampleRandom()" class="text-[11px] text-slate-300 hover:text-white flex items-center gap-1 bg-white/10 hover:bg-white/20 px-2.5 py-1 rounded-lg transition-all">
                        <i class="fas fa-dice"></i> Roll New Sample
                    </button>
                </div>

                <!-- Realistic White Thermal / Print Sticker -->
                <div class="bg-white text-slate-900 rounded-xl p-5 shadow-2xl border border-slate-100 flex flex-col items-center text-center relative overflow-hidden">
                    <div class="w-full text-center border-b border-dashed border-slate-300 pb-2 mb-3">
                        <div class="text-[11px] font-black uppercase tracking-wider text-amber-800"><?= sanitize(getSetting('company_name', 'DIVYA MURTI EMPORIUM')) ?></div>
                        <div class="text-[9px] text-slate-500 font-medium">Sacred Shilpa Shastra Idols & Temple Artifacts</div>
                    </div>

                    <div class="text-xs font-bold text-slate-800 mb-1 line-clamp-1">Lord Murugan Vel Bronze 15in</div>
                    
                    <!-- Dynamic SVG Barcode Graphic -->
                    <div id="barcode-svg-container" class="my-2 p-2 bg-white flex justify-center items-center w-full">
                        <svg id="live-barcode-svg" class="w-full max-w-[280px] h-16"></svg>
                    </div>

                    <!-- Monospace Barcode String -->
                    <div id="preview-barcode-text" class="font-mono text-lg font-black tracking-widest text-slate-900 my-0.5">
                        SS-4829
                    </div>

                    <div class="w-full border-t border-dashed border-slate-300 pt-2 mt-2 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500 font-semibold">SKU: SKU-67080</span>
                        <span class="font-black text-emerald-700 text-xs">MRP: ₹ 3,450.00</span>
                    </div>

                    <div class="text-[8px] text-slate-400 mt-1 uppercase tracking-wider">Scannable in POS & Mobile Scanner</div>
                </div>

                <!-- Quick Copy and POS Test -->
                <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Format: <strong id="preview-format-rule" class="text-amber-300">SS-{4 digits}</strong></span>
                    <button type="button" onclick="copyCurrentBarcode()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[11px] transition-all">
                        <i class="fas fa-copy"></i> Copy Barcode
                    </button>
                </div>
            </div>

            <!-- Quick Generated Samples Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-amber-600"></i> Sample Barcodes (Generated with your rules)
                    </h3>
                </div>
                <div id="sample-barcodes-list" class="grid grid-cols-2 gap-2">
                    <?php foreach ($samples as $s): ?>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-400 transition-colors group">
                        <span class="font-mono text-xs font-bold text-slate-800 tracking-wider"><?= sanitize($s) ?></span>
                        <button type="button" onclick="copyText('<?= sanitize($s) ?>')" class="text-slate-400 group-hover:text-amber-600 text-xs p-1" title="Copy">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-[10px] text-slate-400 italic">Every time a new product is saved, the system guarantees a non-colliding unique barcode matching this pattern.</p>
            </div>

            <!-- Handheld Scanner Compatibility Note -->
            <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 text-xs space-y-2 text-amber-950">
                <div class="font-bold flex items-center gap-2 text-amber-900">
                    <i class="fas fa-qrcode text-amber-600"></i>
                    <span>Scanner & POS Compatibility Guaranteed</span>
                </div>
                <p class="text-[11px] leading-relaxed text-amber-900/80">
                    Standard USB, Bluetooth, and Wireless handheld barcode scanners (Honeywell, Zebra, TVS, Pegasus) read Code 128 / Code 39 format. When scanned at <strong class="text-amber-950 font-bold">POS Billing (/billing)</strong>, the system instantly matches this barcode to the product.
                </p>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Recently Created Products & Their Barcodes -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-tags text-amber-600"></i>
                    <span>Recent Products in Database</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Showing last 5 products and their active barcode codes</p>
            </div>
            <a href="<?= url('products') ?>" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                <span>View All Products</span>
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Product Name</th>
                        <th class="py-3 px-4">SKU Code</th>
                        <th class="py-3 px-4">Barcode Tag</th>
                        <th class="py-3 px-4 text-right">Selling Price</th>
                        <th class="py-3 px-4 text-center">Stock</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <?php if (empty($recentProducts)): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400 text-xs">No products found in the catalog.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentProducts as $rp): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-4 font-bold text-slate-900"><?= sanitize($rp['name']) ?></td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500"><?= sanitize($rp['sku'] ?: 'N/A') ?></td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono font-bold text-xs bg-amber-50 text-amber-900 border border-amber-200">
                                <i class="fas fa-barcode text-amber-600"></i>
                                <span><?= sanitize($rp['barcode'] ?: 'No Barcode') ?></span>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900"><?= formatCurrency((float)($rp['selling_price'] ?? 0)) ?></td>
                        <td class="py-3 px-4 text-center font-bold <?= (int)$rp['current_stock'] > 0 ? 'text-emerald-700' : 'text-rose-600' ?>">
                            <?= (int)$rp['current_stock'] ?>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="<?= url('products') ?>?search=<?= urlencode($rp['barcode']) ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-600 hover:text-amber-700 hover:bg-amber-50 transition-colors">
                                <i class="fas fa-search"></i> Find
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Simple SVG Barcode Generator & Interactive JS Script -->
<script>
    let currentRandomNumber = 4829;

    function getSelectedSeparator() {
        const selected = document.querySelector('input[name="barcode_separator"]:checked');
        return selected ? selected.value : '-';
    }

    function setQuickPrefix(pfx) {
        const input = document.getElementById('barcode_prefix');
        if (input) {
            input.value = pfx;
            updateLivePreview();
        }
    }

    function setDigitLength(len) {
        const input = document.getElementById('barcode_number_length');
        if (input) {
            input.value = len;
            updateLivePreview();
        }
    }

    function resetToDefault() {
        document.getElementById('barcode_prefix').value = 'SS';
        document.getElementById('barcode_number_length').value = '4';
        const sepRadio = document.querySelector('input[name="barcode_separator"][value="-"]');
        if (sepRadio) sepRadio.checked = true;
        updateLivePreview();
    }

    function generateRandomForLength(len) {
        const min = Math.pow(10, Math.max(2, len - 1));
        const max = Math.pow(10, Math.max(2, len)) - 1;
        return Math.floor(min + Math.random() * (max - min + 1));
    }

    function regenerateSampleRandom() {
        const len = parseInt(document.getElementById('barcode_number_length').value) || 4;
        currentRandomNumber = generateRandomForLength(len);
        updateLivePreview();
        refreshSampleList();
    }

    function updateLivePreview() {
        const prefixInput = document.getElementById('barcode_prefix');
        let prefix = (prefixInput ? prefixInput.value.trim().toUpperCase() : 'SS');
        // Filter prefix
        prefix = prefix.replace(/[^A-Z0-9_\-]/g, '') || 'SS';
        if (prefixInput && prefixInput.value !== prefix) {
            prefixInput.value = prefix;
        }

        const lenInput = document.getElementById('barcode_number_length');
        let len = parseInt(lenInput ? lenInput.value : 4) || 4;
        if (len < 3) len = 3;
        if (len > 8) len = 8;

        const sep = getSelectedSeparator();

        // Update digit label
        const digitLabel = document.getElementById('label-digit-count');
        if (digitLabel) {
            digitLabel.textContent = len + ' Digits';
        }

        // Adjust random number if digit length changed
        const strNum = String(currentRandomNumber);
        if (strNum.length !== len) {
            currentRandomNumber = generateRandomForLength(len);
        }

        const barcodeStr = prefix + sep + currentRandomNumber;
        const formatRuleStr = prefix + sep + 'X'.repeat(len);

        // Update UI displays
        const previewText = document.getElementById('preview-barcode-text');
        if (previewText) previewText.textContent = barcodeStr;

        const formatBadge = document.getElementById('badge-active-format');
        if (formatBadge) formatBadge.textContent = formatRuleStr;

        const ruleSpan = document.getElementById('preview-format-rule');
        if (ruleSpan) ruleSpan.textContent = prefix + sep + '{' + len + ' digits}';

        // Draw SVG Barcode
        drawBarcodeSVG(barcodeStr);
    }

    // High quality procedural SVG barcode generator (pseudo Code 128 / Code 39)
    function drawBarcodeSVG(code) {
        const svg = document.getElementById('live-barcode-svg');
        if (!svg) return;

        svg.innerHTML = '';
        const width = 280;
        const height = 60;
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

        // Generate pseudo-deterministic bar pattern from string
        let bars = [];
        // Quiet zone
        bars.push(0, 0, 0, 0);
        // Start guard
        bars.push(1, 1, 0, 1, 0, 0, 1, 1, 1, 0, 0);

        for (let i = 0; i < code.length; i++) {
            const charCode = code.charCodeAt(i);
            // Create 9 elements (bars and spaces)
            const seed = (charCode * (i + 1) * 31) % 1000;
            const b1 = (seed % 3) + 1;
            const b2 = ((seed >> 2) % 3) + 1;
            const b3 = ((seed >> 4) % 3) + 1;
            
            for (let b = 0; b < b1; b++) bars.push(1);
            for (let s = 0; s < b2; s++) bars.push(0);
            for (let b = 0; b < b3; b++) bars.push(1);
            bars.push(0);
        }

        // Stop guard
        bars.push(1, 1, 0, 0, 1, 1, 1, 0, 1, 0, 1, 1);
        bars.push(0, 0, 0, 0);

        const totalModules = bars.length;
        const moduleWidth = width / totalModules;

        let svgContent = '';
        for (let idx = 0; idx < totalModules; idx++) {
            if (bars[idx] === 1) {
                const x = (idx * moduleWidth).toFixed(2);
                const w = (moduleWidth + 0.1).toFixed(2);
                svgContent += `<rect x="${x}" y="0" width="${w}" height="${height}" fill="#0f172a" />`;
            }
        }
        svg.innerHTML = svgContent;
    }

    function refreshSampleList() {
        const container = document.getElementById('sample-barcodes-list');
        if (!container) return;

        const prefix = (document.getElementById('barcode_prefix').value.trim().toUpperCase() || 'SS').replace(/[^A-Z0-9_\-]/g, '') || 'SS';
        const len = parseInt(document.getElementById('barcode_number_length').value) || 4;
        const sep = getSelectedSeparator();

        container.innerHTML = '';
        for (let i = 0; i < 4; i++) {
            const num = generateRandomForLength(len);
            const bCode = prefix + sep + num;
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-400 transition-colors group';
            div.innerHTML = `
                <span class="font-mono text-xs font-bold text-slate-800 tracking-wider">${bCode}</span>
                <button type="button" onclick="copyText('${bCode}')" class="text-slate-400 group-hover:text-amber-600 text-xs p-1" title="Copy">
                    <i class="fas fa-copy"></i>
                </button>
            `;
            container.appendChild(div);
        }
    }

    function copyCurrentBarcode() {
        const txt = document.getElementById('preview-barcode-text');
        if (txt) {
            copyText(txt.textContent.trim());
        }
    }

    function copyText(str) {
        if (!str) return;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(str).then(() => {
                showToast('success', `Copied "${str}" to clipboard!`);
            });
        } else {
            const input = document.createElement('input');
            input.value = str;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            showToast('success', `Copied "${str}" to clipboard!`);
        }
    }

    function showToast(type, msg) {
        const container = document.getElementById('toast-container');
        if (!container) {
            alert(msg);
            return;
        }
        const toast = document.createElement('div');
        toast.className = `p-3 rounded-xl shadow-lg text-xs font-bold flex items-center gap-2 mb-2 transition-all transform duration-300 ${
            type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'
        }`;
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> <span>${msg}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Bind event listeners
    document.addEventListener('DOMContentLoaded', () => {
        const prefixInput = document.getElementById('barcode_prefix');
        if (prefixInput) {
            prefixInput.addEventListener('input', () => {
                updateLivePreview();
                refreshSampleList();
            });
        }

        const lenInput = document.getElementById('barcode_number_length');
        if (lenInput) {
            lenInput.addEventListener('input', () => {
                updateLivePreview();
                refreshSampleList();
            });
        }

        // Handle AJAX form submission
        const form = document.getElementById('barcode-settings-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const saveBtn = document.getElementById('btn-save-settings');
                const originalHtml = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

                try {
                    const formData = new FormData(form);
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        showToast('success', json.message || 'Settings saved successfully!');
                        updateLivePreview();
                        refreshSampleList();
                    } else {
                        showToast('error', json.error || 'Failed to save settings.');
                    }
                } catch (err) {
                    console.error('Save error:', err);
                    form.submit(); // fallback to normal submit
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalHtml;
                }
            });
        }

        updateLivePreview();
    });
</script>
