@extends('layouts.app')
@section('title', 'Masuk')

@push('styles')
<style>
    body { background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%); }
</style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">GH Melon</h1>
            <p class="text-emerald-200 text-sm mt-1">Sistem Manajemen Greenhouse</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-6">
            <h2 class="text-lg font-bold text-stone-800 mb-5">Masuk ke Akun</h2>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_webview" id="_webview_flag" value="0">
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1.5">Email / No. HP</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                        class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="admin@greenhouse.id">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/30">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-emerald-200 text-xs mt-6">
            Login: email atau nomor HP
        </p>
    </div>
</div>

@push('scripts')
<script>
    if (typeof Capacitor !== 'undefined' && Capacitor.isNativePlatform()) {
        document.getElementById('_webview_flag').value = '1';
    }
</script>
@endpush

@endsection