@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">

    <h1 class="text-xl font-bold text-stone-800 dark:text-stone-100 mb-6">Profil Saya</h1>

    {{-- Info Card --}}
    <div class="bg-white dark:bg-stone-800 rounded-2xl p-6 shadow-sm border border-stone-100 dark:border-stone-700 mb-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-full bg-emerald-600 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-lg font-semibold text-stone-800 dark:text-stone-100">{{ auth()->user()->name }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ auth()->user()->isAdmin() ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' }}">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'PJ Greenhouse' }}
                </span>
            </div>
        </div>

        <div class="space-y-2 text-sm text-stone-500 dark:text-stone-400 border-t border-stone-100 dark:border-stone-700 pt-4">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ auth()->user()->email }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span>{{ auth()->user()->phone ?? 'Belum diisi' }}</span>
            </div>
        </div>
    </div>

    {{-- Edit Form Card --}}
    <div class="bg-white dark:bg-stone-800 rounded-2xl p-6 shadow-sm border border-stone-100 dark:border-stone-700 mb-4">
        <h2 class="text-base font-semibold text-stone-700 dark:text-stone-200 mb-4">Edit Profil</h2>

        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm mb-4">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1.5">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                    placeholder="Opsional">
            </div>

            <div class="border-t border-stone-100 dark:border-stone-700 pt-5 mb-5">
                <h3 class="text-sm font-semibold text-stone-600 dark:text-stone-300 mb-3">
                    Ubah Password
                    <span class="font-normal text-stone-400">(kosongkan jika tidak diubah)</span>
                </h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1.5">Password Baru</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-900 text-stone-800 dark:text-stone-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-2.5 px-4 rounded-xl transition-colors text-sm">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Logout Card --}}
    <div class="bg-white dark:bg-stone-800 rounded-2xl p-4 shadow-sm border border-stone-100 dark:border-stone-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 font-semibold py-2.5 px-4 rounded-xl transition-colors text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar dari Akun
            </button>
        </form>
    </div>

</div>
@endsection
