<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
</head>
<body class="h-full bg-slate-100 text-slate-800 flex items-center justify-center p-6 text-center">
    <div class="max-w-md bg-white border border-slate-200 p-8 rounded-2xl shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl mx-auto mb-4 border border-rose-200">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-900 mb-2">500 - Application Error</h1>
        <p class="text-xs text-slate-500 mb-6"><?= sanitize($message ?? 'An unexpected system error occurred. Please contact the technical administrator.') ?></p>
        <a href="<?= url('dashboard') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition-all">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>
