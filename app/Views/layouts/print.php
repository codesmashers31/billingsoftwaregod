<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-6">
    <div class="no-print fixed top-4 right-4 flex items-center gap-3 z-50 bg-slate-900/90 text-white p-3 rounded-xl shadow-xl backdrop-blur">
        <button onclick="window.print()" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs flex items-center gap-2">
            <i class="fas fa-print"></i> Print Document
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg text-xs">
            Close
        </button>
    </div>

    <?= $content ?? '' ?>
</body>
</html>
