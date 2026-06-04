@extends('layouts.app')
@section('title', 'Laporan Penjualan - ' . $greenhouse->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Laporan Penjualan</h1>
                <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->name }} · {{ now()->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('sales.greenhouse', $greenhouse) }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>

        <!-- Filter Tanggal -->
        <form method="GET" class="flex flex-wrap items-end gap-2 mb-4">
            <div>
                <label class="block text-xs font-semibold text-stone-500 mb-1">Dari</label>
                <input type="date" name="from" value="{{ $from ?? '' }}"
                    class="text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-500 mb-1">Sampai</label>
                <input type="date" name="to" value="{{ $to ?? '' }}"
                    class="text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold transition-colors">
                Filter
            </button>
            @if($from || $to)
                <a href="{{ request()->url() }}"
                    class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-600 text-sm font-semibold transition-colors">
                    Reset
                </a>
            @endif
        </form>

        <!-- Summary -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50 rounded-xl p-3.5 text-center">
                <div class="text-xs text-emerald-600 font-semibold mb-1">Total Berat Terjual</div>
                <div class="text-xl font-bold text-emerald-800">{{ number_format($grandWeight, 1, ',', '.') }} kg</div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3.5 text-center">
                <div class="text-xs text-emerald-600 font-semibold mb-1">Total Omset</div>
                <div class="text-xl font-bold text-emerald-800">Rp {{ number_format($grandOmset, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    @if($report->isEmpty())
    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-stone-100">
        <div class="w-14 h-14 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-stone-400 text-sm font-medium">Belum ada penjualan di greenhouse ini.</p>
    </div>
    @else
    <!-- Report Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <table class="w-full text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50">
                    <th class="text-left px-2 sm:px-5 py-2 sm:py-3 text-xs font-semibold text-stone-500">#</th>
                    <th class="text-left px-2 sm:px-5 py-2 sm:py-3 text-xs font-semibold text-stone-500">Varietas</th>
                    <th class="text-center px-2 sm:px-5 py-2 sm:py-3 text-xs font-semibold text-stone-500">Transaksi</th>
                    <th class="text-right px-2 sm:px-5 py-2 sm:py-3 text-xs font-semibold text-stone-500">Berat (kg)</th>
                    <th class="text-right px-2 sm:px-5 py-2 sm:py-3 text-xs font-semibold text-stone-500">Omset</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $i => $row)
                <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50 transition-colors">
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-stone-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 font-semibold text-stone-800 text-xs sm:text-sm">{{ $row->variety_name }}</td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-center">
                        <span class="inline-flex items-center justify-center min-w-[1.5rem] px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">
                            {{ $row->total_transaksi }}
                        </span>
                    </td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-right text-stone-700 text-xs sm:text-sm">{{ number_format($row->total_berat, 1, ',', '.') }}</td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-right font-bold text-emerald-700 text-xs sm:text-sm">Rp {{ number_format($row->total_omset, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                    <td colspan="2" class="px-2 sm:px-5 py-2 sm:py-3 text-xs sm:text-sm font-black text-emerald-800">TOTAL</td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-center">
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-lg bg-emerald-200 text-emerald-800 text-xs font-bold">
                            {{ $report->sum('total_transaksi') }}
                        </span>
                    </td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-right text-xs sm:text-sm font-black text-emerald-800">{{ number_format($grandWeight, 1, ',', '.') }}</td>
                    <td class="px-2 sm:px-5 py-2 sm:py-3 text-right text-xs sm:text-sm font-black text-emerald-800">Rp {{ number_format($grandOmset, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
@endsection