<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle ?? 'Login - Divya Murti ERP') ?></title>
    
    <!-- Tailwind CSS CDN & Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
    <script>
        window.APP_URL = "<?= url() ?>";
        window.CSRF_TOKEN = "<?= csrf_token() ?>";
    </script>
</head>
<body class="h-full bg-slate-100 text-slate-900 flex items-center justify-center p-4 relative font-sans">
    <div class="w-full max-w-md relative z-10">
        <!-- Flash messages -->
        <?php if ($flashSuccess = flash('success')): ?>
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span><?= sanitize($flashSuccess) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($flashSuccessHtml = flash('success_html')): ?>
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-sm">
                <?= $flashSuccessHtml ?>
            </div>
        <?php endif; ?>
        <?php if ($flashError = flash('error')): ?>
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2 shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600"></i>
                <span><?= sanitize($flashError) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
