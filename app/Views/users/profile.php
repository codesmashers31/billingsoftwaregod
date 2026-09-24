<?php ob_start(); ?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">My Profile</h2>
        </div>
        <div class="p-6">
            <form action="<?= url('profile') ?>" method="POST" class="space-y-6">
                
                <?php if (hasFlash('success')): ?>
                <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-semibold mb-4">
                    <?= getFlash('success') ?>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlash('error')): ?>
                <div class="p-4 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 text-sm font-semibold mb-4">
                    <?= getFlash('error') ?>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Username</label>
                        <input type="text" value="<?= sanitize($user['username']) ?>" disabled class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 font-semibold cursor-not-allowed">
                        <p class="text-[10px] text-slate-400 mt-1">Username cannot be changed.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Email Address</label>
                        <input type="text" value="<?= sanitize($user['email']) ?>" disabled class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 font-semibold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Full Name</label>
                        <input type="text" name="name" value="<?= sanitize($user['name']) ?>" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-900 font-semibold focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Mobile Number</label>
                        <input type="text" name="mobile" value="<?= sanitize($user['mobile']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-900 font-semibold focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all">
                    </div>
                </div>

                <hr class="border-slate-100 my-6">

                <div>
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Change Password</h3>
                    <p class="text-xs text-slate-500 mb-4">Leave blank if you do not wish to change your password.</p>
                    <div class="max-w-md">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">New Password</label>
                        <input type="password" name="password" placeholder="••••••••" minlength="6" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
