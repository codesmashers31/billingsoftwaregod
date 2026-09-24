<div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xl relative overflow-hidden">
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xl mx-auto mb-3">
            <i class="fas fa-lock"></i>
        </div>
        <h2 class="text-lg font-bold text-slate-900">Reset Staff Password</h2>
        <p class="text-xs text-slate-500 mt-1">Enter your registered email address to receive password reset instructions</p>
    </div>

    <form action="<?= url('forgot-password') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Registered Staff Email</label>
            <input type="email" name="email" required placeholder="name@company.com" 
                   class="w-full bg-slate-50 border border-slate-300 focus:bg-white focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 outline-none">
        </div>
        <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-all">
            Send Reset Instructions
        </button>
        <div class="text-center pt-2">
            <a href="<?= url('login') ?>" class="text-xs text-amber-700 hover:underline font-semibold">
                <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
            </a>
        </div>
    </form>
</div>
