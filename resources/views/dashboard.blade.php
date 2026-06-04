@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-lg font-bold text-stone-800">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-stone-500 text-xs mt-1">Kelola greenhouse melon Anda</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $greenhouses->count() }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Greenhouse</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-stone-50 text-stone-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $greenhouses->sum('trees_count') }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Total Pohon</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $greenhouses->sum('alive_trees_count') }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Pohon Hidup</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-emerald-700 leading-tight">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Omset Hari Ini</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $gradeAToday }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Grade A Hari Ini</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-lg font-bold text-red-500 leading-tight">{{ $deadRate }}%</div>
                <div class="text-xs text-stone-500 mt-0.5">Dead Rate</div>
            </div>
        </div>
    </div>

    <!-- Greenhouse Cards -->
    <div class="space-y-3">
        @forelse($greenhouses as $gh)
        <a href="{{ route('greenhouse.show', $gh) }}" class="block bg-white rounded-2xl p-5 shadow-sm border border-stone-100 hover:shadow-md hover:border-emerald-200 transition-all">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-stone-800 truncate text-base">{{ $gh->name }}</h3>
                        @if($gh->is_active)
                            <span class="shrink-0 inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </div>
                    <p class="text-xs text-stone-400 font-mono mb-3">{{ $gh->code }}</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
                            <span class="text-xs font-semibold text-stone-700">{{ $gh->alive_trees_count ?? 0 }}</span>
                            <span class="text-xs text-stone-400">hidup</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-xs font-semibold text-stone-700">{{ $gh->trees_count - ($gh->alive_trees_count ?? 0) }}</span>
                            <span class="text-xs text-stone-400">mati</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-stone-700">{{ $gh->trees_count ?? 0 }}</span>
                            <span class="text-xs text-stone-400">total</span>
                        </div>
                    </div>
                </div>
                <svg class="w-5 h-5 text-stone-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-stone-100">
            <p class="text-stone-400 text-sm">Belum ada greenhouse yang dikelola.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
