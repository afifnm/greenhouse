@extends('layouts.app')
@section('title', 'Penjualan Baru - ' . $greenhouse->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Penjualan Baru</h1>
                <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->name }} · {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ route('sales.greenhouse', $greenhouse) }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('sales.store', $greenhouse) }}" class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
        @csrf

        <!-- Buyer Name -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-stone-700 mb-2">Nama Pembeli</label>
            <input type="text" name="buyer_name" placeholder="Masukkan nama pembeli" required
                class="w-full text-sm border border-stone-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none @error('buyer_name') border-red-400 @enderror"
                value="{{ old('buyer_name') }}">
            @error('buyer_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Items Card List -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-bold text-stone-700">Item Penjualan</label>
                <button type="button" id="add-row-btn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition-colors border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="space-y-2" id="sale-items-body">
                @php
                    $oldItems = old('sale_items', [[
                        'melon_variety_id' => '',
                        'weight_kg' => '',
                        'price_per_kg' => '',
                    ]]);
                @endphp
                @foreach($oldItems as $i => $oldItem)
                <div class="sale-row bg-white rounded-lg border border-stone-200 p-3 space-y-2">
                    <!-- Row 1: Varietas -->
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <select name="sale_items[{{ $i }}][melon_variety_id]" required
                                class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white @error('sale_items.' . $i . '.melon_variety_id') border-red-400 @enderror">
                                <option value="">Pilih varietas</option>
                                @foreach($varieties as $v)
                                    <option value="{{ $v->id }}" {{ (string) $oldItem['melon_variety_id'] === (string) $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                @endforeach
                            </select>
                            @error('sale_items.' . $i . '.melon_variety_id')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="button" onclick="removeRow(this)"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition-colors border border-red-200 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </div>

                    <!-- Row 2: Berat & Harga -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-stone-500 font-medium mb-0.5 block">Berat (kg)</label>
                            <input type="number" name="sale_items[{{ $i }}][weight_kg]" data-weight
                                placeholder="0" step="0.01" min="0" required
                                class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none @error('sale_items.' . $i . '.weight_kg') border-red-400 @enderror"
                                value="{{ $oldItem['weight_kg'] ?? '' }}">
                            @error('sale_items.' . $i . '.weight_kg')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs text-stone-500 font-medium mb-0.5 block">Harga/kg (Rp)</label>
                            <input type="number" name="sale_items[{{ $i }}][price_per_kg]" data-price
                                placeholder="0" min="0" required
                                class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none @error('sale_items.' . $i . '.price_per_kg') border-red-400 @enderror"
                                value="{{ $oldItem['price_per_kg'] ?? '' }}">
                            @error('sale_items.' . $i . '.price_per_kg')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="flex items-center justify-between pt-2 border-t border-stone-100">
                        <span class="text-xs text-stone-500 font-medium">Subtotal</span>
                        <span class="subtotal-display text-emerald-700 font-semibold text-sm">Rp 0</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Grand Total -->
        <div class="flex justify-end mb-5">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 text-right">
                <span class="text-xs font-semibold text-emerald-600">Total</span>
                <div id="grand-total" class="text-2xl font-bold text-emerald-800">Rp 0</div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3">
            <button type="submit" name="action" value="pay_later"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-stone-600 hover:bg-stone-700 text-white font-bold text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Bayar 
            </button>
            <button type="submit" name="action" value="pay_and_print"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2m-6 4a2 2 0 100-4 2 2 0 000 4z"/></svg>
                Bayar & Print
            </button>
        </div>
    </form>
</div>

<!-- Template for dynamically added rows -->
<template id="row-template">
    <div class="sale-row bg-white rounded-lg border border-stone-200 p-3 space-y-2">
        <!-- Row 1: Varietas -->
        <div class="flex items-start gap-2">
            <div class="flex-1">
                <select name="sale_items[N][melon_variety_id]" required
                    class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white">
                    <option value="">Pilih varietas</option>
                    @foreach($varieties as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" onclick="removeRow(this)"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition-colors border border-red-200 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
            </button>
        </div>

        <!-- Row 2: Berat & Harga -->
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs text-stone-500 font-medium mb-0.5 block">Berat (kg)</label>
                <input type="number" name="sale_items[N][weight_kg]" data-weight
                    placeholder="0" step="0.01" min="0" required
                    class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
            <div>
                <label class="text-xs text-stone-500 font-medium mb-0.5 block">Harga/kg (Rp)</label>
                <input type="number" name="sale_items[N][price_per_kg]" data-price
                    placeholder="0" min="0" required
                    class="w-full text-xs border border-stone-200 rounded-lg px-2 py-1.5 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
        </div>

        <!-- Subtotal -->
        <div class="flex items-center justify-between pt-2 border-t border-stone-100">
            <span class="text-xs text-stone-500 font-medium">Subtotal</span>
            <span class="subtotal-display text-emerald-700 font-semibold text-sm">Rp 0</span>
        </div>
    </div>
</template>

@endsection