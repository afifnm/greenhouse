<nav class="bg-emerald-800 text-white shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.greenhouses.index') : route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <span class="font-bold text-lg tracking-tight">Green House App</span>
        </a>
        <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center gap-2 text-sm text-emerald-200">
                <span class="font-medium text-white">{{ auth()->user()->name }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full {{ auth()->user()->isAdmin() ? 'bg-amber-500 text-amber-900' : 'bg-emerald-500 text-white' }}">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'PJ' }}
                </span>
            </div>
            <button type="button" id="theme-toggle" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-emerald-700 transition-colors text-sm font-semibold" aria-label="Ubah tema">
                <svg data-theme-icon="light" class="w-5 h-5 hidden dark:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.7.7M4.04 19.96l-.7.7M21 12h-1M4 12H3m16.96 7.96l-.7-.7M4.04 4.04l-.7-.7M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                <svg data-theme-icon="dark" class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                <span class="hidden sm:inline" data-theme-label>Mode</span>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-emerald-700 transition-colors text-sm font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>
