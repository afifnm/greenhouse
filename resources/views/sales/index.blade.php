@extends('layouts.app')
@section('title', 'Riwayat Penjualan')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Riwayat Penjualan</h1>
                <p class="text-xs text-stone-400 font-mono mt-0.5">{{ $sales->total() }} transaksi</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('sales.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-bold transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Penjualan Baru
                </a>
            @endif
        </div>
    </div>

    @if($sales->isEmpty())
    <!-- Empty State -->
    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-stone-100">
        <div class="w-14 h-14 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-stone-400 text-sm font-medium">Belum ada penjualan.</p>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 mt-4 bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Penjualan Pertama
            </a>
        @endif
    </div>
    @else
    <!-- Sales Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500">Tanggal</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500">Pembeli</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500">Kasir</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-stone-500">Total</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-stone-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr class="border-b border-stone-50 last:border-0 hover:bg-stone-50 transition-colors">
                    <td class="px-5 py-3.5 text-stone-600 text-xs font-mono whitespace-nowrap">
                        {{ $sale->created_at->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-stone-800">{{ $sale->buyer_name }}</td>
                    <td class="px-5 py-3.5 text-stone-500 text-xs">{{ $sale->user->name }}</td>
                    <td class="px-5 py-3.5 text-right font-bold text-emerald-700 whitespace-nowrap">
                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" class="detail-toggle inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-stone-50 hover:bg-stone-100 text-stone-500 text-xs font-semibold border border-stone-200 transition-colors">
                                Detail
                                <svg class="w-3 h-3 detail-arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                                    onsubmit="return confirm('Yakin hapus penjualan ini?\nPembeli: {{ $sale->buyer_name }}\nTotal: Rp {{ number_format($sale->total, 0, ',', '.') }}')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                <!-- Detail Row -->
                <tr class="detail-row hidden bg-stone-50 border-b border-stone-100">
                    <td colspan="5" class="px-5 py-3">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-stone-200">
                                    <th class="text-left py-1.5 pr-8 text-stone-500 font-semibold">Varietas</th>
                                    <th class="text-right py-1.5 pr-8 text-stone-500 font-semibold">Berat</th>
                                    <th class="text-right py-1.5 pr-8 text-stone-500 font-semibold">Harga/kg</th>
                                    <th class="text-right py-1.5 text-stone-500 font-semibold">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr class="border-b border-stone-100 last:border-0">
                                    <td class="py-1.5 pr-8 text-stone-700">{{ $item->melonVariety->name }}</td>
                                    <td class="py-1.5 pr-8 text-right text-stone-600">{{ number_format($item->weight_kg, 3, ',', '.') }} kg</td>
                                    <td class="py-1.5 pr-8 text-right text-stone-600">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                                    <td class="py-1.5 text-right font-semibold text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
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

    <!-- Pagination -->
    <div class="mt-4">
        {{ $sales->links() }}
    </div>
    @endif
</div>

@endsection
