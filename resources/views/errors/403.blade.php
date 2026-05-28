<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — GH Melon</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-stone-50 px-4">
    <div class="text-center max-w-sm">
        <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-stone-800 mb-2">Akses Ditolak</h1>
        <p class="text-stone-500 text-sm mb-6">{{ $message ?? 'Anda tidak memiliki akses ke halaman ini.' }}</p>
        <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.greenhouses.index') : route('dashboard')) : route('login') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">
            Kembali
        </a>
    </div>
</body>
</html>