<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle ?? 'Divya Murti ERP') ?> - <?= sanitize(getSetting('company_name', 'God Statue ERP')) ?></title>
    
    <!-- Inter Clean Font & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome Pro / Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Clean Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">

    <!-- Global App State -->
    <script>
        window.APP_URL = "<?= url() ?>";
        window.CSRF_TOKEN = "<?= csrf_token() ?>";
    </script>
</head>
<body class="h-full bg-slate-50 text-slate-800 flex overflow-hidden font-sans antialiased">
    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <!-- Sidebar Partial -->
    <?php require __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-slate-50">
        <!-- Header Partial -->
        <?php require __DIR__ . '/header.php'; ?>

        <!-- Main Scrollable Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-7 relative bg-slate-50">
            <!-- Flash Message Banner -->
            <?php if ($flashSuccess = flash('success')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                        <span><?= sanitize($flashSuccess) ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <?php if ($flashError = flash('error')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                        <span><?= sanitize($flashError) ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <!-- View Dynamic Content -->
            <?= $content ?? '' ?>
        </main>
    </div>

    <!-- Base Scripts -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
