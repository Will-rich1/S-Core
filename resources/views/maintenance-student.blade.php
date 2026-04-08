<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - S-Core ITBSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-white border border-slate-200 rounded-2xl shadow-sm p-8 text-center">
        <div class="mx-auto w-14 h-14 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">S-Core Sedang Maintenance</h1>
        <p class="text-slate-600 mb-6">Sistem untuk mahasiswa sementara tidak dapat diakses karena sedang ada perbaikan. Silakan coba lagi beberapa saat.</p>

        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium">
            Kembali ke Login
        </a>
    </div>
</body>
</html>
