<div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-amber-600/20 text-amber-400 flex items-center justify-center text-2xl mx-auto mb-3 border border-amber-500/30">
            <i class="fas fa-unlock-alt"></i>
        </div>
        <h2 class="text-xl font-bold font-spiritual text-white">Create New Password</h2>
        <p class="text-xs text-slate-400 mt-1">For account: <span class="text-amber-400 font-semibold"><?= sanitize($email ?? '') ?></span></p>
    </div>

    <form action="<?= url('reset-password') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= sanitize($token ?? '') ?>">

        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">New Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fas fa-lock"></i>
                </div>
                <input type="password" name="password" required minlength="6" autofocus
                       class="w-full bg-slate-950/80 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-600 transition-colors">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Confirm New Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fas fa-check-double"></i>
                </div>
                <input type="password" name="password_confirmation" required minlength="6"
                       class="w-full bg-slate-950/80 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-600 transition-colors">
            </div>
        </div>

        <button type="submit" class="w-full gold-btn text-slate-950 font-bold py-2.5 rounded-xl text-sm shadow-xl flex items-center justify-center gap-2">
            <i class="fas fa-save"></i> Save New Password
        </button>
    </form>
</div>
