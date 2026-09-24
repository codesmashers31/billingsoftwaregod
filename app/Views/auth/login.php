<div class="bg-white border border-slate-200/90 rounded-2xl p-8 shadow-xl relative overflow-hidden">
    <!-- Top Emblem -->
    <div class="text-center mb-7">
        <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl mx-auto shadow-md mb-3.5">
            <i class="fas fa-om"></i>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= sanitize(getSetting('company_name', 'Divya Murti Heritage')) ?></h2>
        <p class="text-xs text-amber-700 font-semibold uppercase tracking-wider mt-0.5">Enterprise God Statue Management ERP</p>
    </div>

    <!-- Login Form -->
    <form action="<?= url('login') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Username or Staff Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fas fa-user-shield"></i>
                </div>
                <input type="text" name="username" value="superadmin" required autofocus 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-400 transition-all outline-none">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700">Password</label>
                <a href="<?= url('forgot-password') ?>" class="text-[11px] text-amber-700 hover:text-amber-800 font-semibold hover:underline">Forgot password?</a>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fas fa-key"></i>
                </div>
                <input type="password" name="password" value="admin123" required 
                       class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-400 transition-all outline-none">
            </div>
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center text-xs text-slate-600 font-medium cursor-pointer">
                <input type="checkbox" name="remember" value="1" checked class="rounded border-slate-300 text-amber-600 focus:ring-0 mr-2">
                <span>Keep me signed in</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 mt-2">
            <i class="fas fa-sign-in-alt"></i> Sign In to Portal
        </button>
    </form>

    <!-- Quick Demo Logins Helper -->
    <div class="mt-6 pt-5 border-t border-slate-200 text-center">
        <p class="text-[11px] font-bold text-slate-500 mb-2.5">Click a Role to Quick-Fill (Password: <code class="text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-mono">admin123</code>)</p>
        <div class="grid grid-cols-3 gap-2 text-[11px]">
            <button onclick="document.querySelector('[name=username]').value='superadmin';document.querySelector('[name=password]').value='admin123'" class="py-2 px-1 rounded-xl bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 border border-slate-200 text-slate-700 font-semibold transition-all">superadmin</button>
            <button onclick="document.querySelector('[name=username]').value='priya';document.querySelector('[name=password]').value='admin123'" class="py-2 px-1 rounded-xl bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 border border-slate-200 text-slate-700 font-semibold transition-all">priya (Billing)</button>
            <button onclick="document.querySelector('[name=username]').value='venkatesh';document.querySelector('[name=password]').value='admin123'" class="py-2 px-1 rounded-xl bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 border border-slate-200 text-slate-700 font-semibold transition-all">venkatesh (Acc)</button>
        </div>
    </div>
</div>
