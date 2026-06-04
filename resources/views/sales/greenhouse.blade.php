@extends('layouts.app')
@section('title', 'Kasir - ' . $greenhouse->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-20">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-stone-800">{{ $greenhouse->name }}</h1>
                <p class="text-xs text-stone-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sales.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <div class="text-xs text-emerald-600 font-semibold mb-0.5">Hari Ini</div>
                <div class="text-base font-bold text-emerald-800 leading-tight">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <div class="text-xs text-emerald-600 font-semibold mb-0.5">Bulan Ini</div>
                <div class="text-base font-bold text-emerald-800 leading-tight">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="bg-stone-50 rounded-xl p-3 text-center">
                <div class="text-xs text-stone-500 font-semibold mb-0.5">Total</div>
                <div class="text-base font-bold text-stone-700 leading-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    @if($recentSales->isEmpty())
    <!-- Empty State -->
    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-stone-100">
        <div class="w-14 h-14 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-stone-400 text-sm font-medium">Belum ada penjualan di greenhouse ini.</p>
    </div>
    @else
    <!-- Transaction Count + Report Link -->
    <div class="flex items-center justify-between mb-3">
        <p class="text-xs text-stone-400 font-medium">{{ $transactionCount }} transaksi</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('sales.report', $greenhouse) }}"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">
            Lihat Laporan
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-stone-100">
            <h2 class="text-sm font-bold text-stone-700">Data Penjualan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50">
                        <th class="text-center px-3 py-2 text-xs font-semibold text-stone-500">No</th>
                        <th class="text-left px-3 py-2 text-xs font-semibold text-stone-500">Tanggal</th>
                        <th class="text-left px-3 py-2 text-xs font-semibold text-stone-500">Pembeli</th>
                        <th class="text-right px-3 py-2 text-xs font-semibold text-stone-500">Total</th>
                        <th class="text-center px-3 py-2 text-xs font-semibold text-stone-500" title="Aksi (Detail, Cetak, Hapus)">Aksi</th>
                    </tr>
                </thead>
                <tbody id="sales-table-body">
                    @foreach($recentSales as $sale)
                    <tr class="border-b border-stone-50 last:border-0 hover:bg-stone-50 transition-colors sales-row">
                        <td class="px-3 py-2 text-center text-stone-500 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 text-stone-600 font-mono md:whitespace-nowrap">
                            {{ $sale->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="px-3 py-2 font-semibold text-stone-800">{{ $sale->buyer_name }}</td>
                        <td class="px-3 py-2 text-right font-bold text-emerald-700 md:whitespace-nowrap">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" title="Lihat Detail" class="detail-toggle inline-flex items-center justify-center w-7 h-7 rounded-lg text-stone-400 hover:text-blue-500 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4 detail-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <a href="{{ route('sales.print', $sale) }}" target="_blank" title="Cetak Struk" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-stone-400 hover:text-sky-500 hover:bg-sky-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </a>
                                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Yakin hapus?')" class="inline-flex">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" title="Hapus Transaksi" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <!-- Detail Row -->
                    <tr class="detail-row hidden bg-stone-50 border-b border-stone-100">
                        <td colspan="5" class="p-3">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-stone-200">
                                        <th class="text-left py-1 pr-4 text-stone-500 font-semibold">Varietas</th>
                                        <th class="text-right py-1 pr-4 text-stone-500 font-semibold">Berat</th>
                                        <th class="text-right py-1 pr-4 text-stone-500 font-semibold">Harga/kg</th>
                                        <th class="text-right py-1 text-stone-500 font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $item)
                                    <tr class="border-b border-stone-100 last:border-0">
                                        <td class="py-1 pr-4 text-stone-700">{{ $item->melonVariety->name }}</td>
                                        <td class="py-1 pr-4 text-right text-stone-600">{{ number_format($item->weight_kg, 1, ',', '.') }} kg</td>
                                        <td class="py-1 pr-4 text-right text-stone-600">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                                        <td class="py-1 text-right font-semibold text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($recentSales->hasPages())
    <div class="mt-4">
        {{ $recentSales->links() }}
    </div>
    @endif
    @endif
</div>

<!-- Floating Action Button: Transaksi Baru -->
@if(auth()->user()->isAdmin() || auth()->user()->isManager())
<a href="{{ route('sales.create', $greenhouse) }}"
    class="fixed bottom-20 right-4 z-40 inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-lg shadow-emerald-700/30 transition-all">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Transaksi Baru
</a>
@endif

@endsection