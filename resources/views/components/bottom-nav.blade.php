<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-stone-200 z-50 shadow-2xl">
    <div class="flex justify-around items-center h-16">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.greenhouses.index') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('admin.greenhouses.*') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="text-xs font-medium">Greenhouse</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('admin.users.*') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                <span class="text-xs font-medium">Users</span>
            </a>
            <a href="{{ route('admin.varieties.index') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('admin.varieties.*') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="text-xs font-medium">Varieties</span>
            </a>
            <a href="{{ route('sales.index') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('sales.*') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-xs font-medium">Kasir</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-xs font-medium">Dashboard</span>
            </a>
            @php
                $userGh = auth()->user()->greenhouses->first();
            @endphp
            @if($userGh)
                <a href="{{ route('greenhouse.show', $userGh) }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('greenhouse.show') ? 'text-emerald-700' : 'text-stone-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm0 8a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zm12 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    <span class="text-xs font-medium">Greenhouse</span>
                </a>
            @endif
            <a href="{{ route('sales.index') }}" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 {{ request()->routeIs('sales.*') ? 'text-emerald-700' : 'text-stone-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-xs font-medium">Penjualan</span>
            </a>
        @endif
        <a href="#" onclick="event.preventDefault(); document.querySelector('form[action=\'{{ route('logout') }}\']').submit()" class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 text-stone-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span class="text-xs font-medium">Logout</span>
        </a>
    </div>
</nav>
