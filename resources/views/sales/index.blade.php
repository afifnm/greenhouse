@extends('layouts.app')
@section('title', 'Kasir - Pilih Greenhouse')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Kasir</h1>
                <p class="text-xs text-stone-400 font-mono mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    @if($greenhouses->isEmpty())
    <!-- Empty State -->
    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-stone-100">
        <div class="w-14 h-14 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-stone-400 text-sm font-medium">Tidak ada greenhouse yang bisa diakses.</p>
    </div>
    @else
    <!-- Greenhouse Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($greenhouses as $gh)
        @php
            $summary = $salesSummary->get($gh->id);
            $todayTotal = $summary->today_total ?? 0;
            $monthTotal = $summary->month_total ?? 0;
        @endphp
        <a href="{{ route('sales.greenhouse', $gh) }}"
            class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h2 class="text-base font-bold text-stone-800 group-hover:text-emerald-700 transition-colors">{{ $gh->name }}</h2>
                    <p class="text-xs text-stone-400 font-mono">{{ $gh->code }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="bg-stone-50 rounded-xl p-2.5 text-center">
                    <div class="text-xs text-stone-500 mb-0.5">Hari Ini</div>
                    <div class="text-sm font-bold text-stone-800">Rp {{ number_format($todayTotal, 0, ',', '.') }}</div>
                </div>
                <div class="bg-stone-50 rounded-xl p-2.5 text-center">
                    <div class="text-xs text-stone-500 mb-0.5">Bulan Ini</div>
                    <div class="text-sm font-bold text-stone-800">Rp {{ number_format($monthTotal, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-400">{{ $gh->sales_count ?? 0 }} transaksi</span>
                <span class="text-xs font-semibold text-emerald-600 group-hover:text-emerald-700">Buka Kasir →</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection