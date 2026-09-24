<!-- Top Header Navigation (Clean White Theme) -->
<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 z-20 shadow-sm">
    <!-- Left: Breadcrumb / Status -->
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <i class="fas fa-store text-amber-600"></i>
            <span class="text-slate-900 font-bold"><?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></span>
            <span>/</span>
            <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md font-mono text-[11px] border border-amber-200">
                GSTIN: <?= getSetting('company_gstin', '33AABCG1234H1Z0') ?>
            </span>
        </div>
    </div>

    <!-- Right: Fast Actions & Shortcuts -->
    <div class="flex items-center gap-3">
        <!-- Quick POS Trigger -->
        <?php if (hasPermission('billing.pos')): ?>
        <a href="<?= url('billing') ?>" class="gold-btn text-white px-3.5 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-bolt"></i>
            <span class="hidden sm:inline">POS Terminal</span>
            <kbd class="hidden md:inline-block bg-amber-800/30 text-white px-1.5 py-0.5 rounded text-[10px] font-mono">F2</kbd>
        </a>
        <?php endif; ?>

        <!-- Quick Stock Adjustment -->
        <?php if (hasPermission('inventory.adjust')): ?>
        <a href="<?= url('inventory') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all">
            <i class="fas fa-sliders-h text-amber-600"></i>
            <span class="hidden sm:inline">Stock Entry</span>
        </a>
        <?php endif; ?>

        <!-- User Role Tag -->
        <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-bold text-slate-800"><?= sanitize(auth('name', 'Staff')) ?></span>
            <span class="text-[10px] text-amber-800 font-semibold bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200">
                <?= sanitize(auth('role_name', 'Staff')) ?>
            </span>
        </div>
    </div>
</header>
