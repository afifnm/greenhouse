@extends('layouts.app')
@section('title', 'Nota Penjualan #' . $sale->id)

@section('content')
<div class="max-w-2xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Nota Penjualan</h1>
                <p class="text-xs text-stone-400 font-mono">#{{ $sale->id }}</p>
            </div>
            <a href="{{ route('sales.index') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Receipt -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
        <!-- Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-dashed border-stone-200">
            <div>
                <p class="text-xs text-stone-400 mb-0.5">Tanggal</p>
                <p class="text-sm font-semibold text-stone-700">{{ $sale->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-stone-400 mb-0.5">Kasir</p>
                <p class="text-sm font-semibold text-stone-700">{{ $sale->user->name }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-stone-400 mb-0.5">Nama Pembeli</p>
                <p class="text-lg font-bold text-stone-800">{{ $sale->buyer_name }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="mb-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-stone-200">
                        <th class="text-left py-2 text-xs font-semibold text-stone-500">Varietas</th>
                        <th class="text-right py-2 text-xs font-semibold text-stone-500">Berat</th>
                        <th class="text-right py-2 text-xs font-semibold text-stone-500">Harga/kg</th>
                        <th class="text-right py-2 text-xs font-semibold text-stone-500">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="py-3 text-stone-700 font-medium">{{ $item->melonVariety->name }}</td>
                        <td class="py-3 text-right text-stone-600">{{ number_format($item->weight_kg, 3, ',', '.') }} kg</td>
                        <td class="py-3 text-right text-stone-600">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                        <td class="py-3 text-right font-semibold text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Grand Total -->
        <div class="flex justify-end">
            <div class="bg-emerald-50 border-2 border-emerald-200 rounded-2xl px-6 py-4 text-right w-64">
                <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Total Bayar</div>
                <div class="text-3xl font-black text-emerald-800 mt-1">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Delete -->
        @if(auth()->user()->isAdmin())
        <div class="mt-6 pt-6 border-t border-dashed border-stone-200 flex justify-end">
            <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                onsubmit="return confirm('Yakin hapus penjualan ini?\nNota #{{ $sale->id }}\nPembeli: {{ $sale->buyer_name }}')">
                @csrf
                @method('delete')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-red-600 hover:bg-red-50 text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Penjualan
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
