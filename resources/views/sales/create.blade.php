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

        <!-- Items Table -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-bold text-stone-700">Item Penjualan</label>
                <button type="button" id="add-row-btn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition-colors border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="text-left py-2 pr-3 text-xs font-semibold text-stone-500 w-1/2">Varietas</th>
                            <th class="text-right py-2 pr-3 text-xs font-semibold text-stone-500 w-24">Berat (kg)</th>
                            <th class="text-right py-2 pr-3 text-xs font-semibold text-stone-500 w-28">Harga/kg (Rp)</th>
                            <th class="text-right py-2 text-xs font-semibold text-stone-500 w-32">Subtotal</th>
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody id="sale-items-body">
                        @php
                            $oldItems = old('sale_items', [[
                                'melon_variety_id' => '',
                                'weight_kg' => '',
                                'price_per_kg' => '',
                            ]]);
                        @endphp
                        @foreach($oldItems as $i => $oldItem)
                        <tr class="sale-row border-b border-stone-100">
                            <td class="py-2 pr-3">
                                <select name="sale_items[{{ $i }}][melon_variety_id]" required
                                    class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white @error('sale_items.' . $i . '.melon_variety_id') border-red-400 @enderror">
                                    <option value="">Pilih varietas</option>
                                    @foreach($varieties as $v)
                                        <option value="{{ $v->id }}" {{ (string) $oldItem['melon_variety_id'] === (string) $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                    @endforeach
                                </select>
                                @error('sale_items.' . $i . '.melon_variety_id')
                                    <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="py-2 pr-3">
                                <input type="number" name="sale_items[{{ $i }}][weight_kg]" data-weight
                                    placeholder="0.0" step="0.001" min="0" required
                                    class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none @error('sale_items.' . $i . '.weight_kg') border-red-400 @enderror"
                                    value="{{ $oldItem['weight_kg'] ?? '' }}">
                                @error('sale_items.' . $i . '.weight_kg')
                                    <p class="text-red-500 text-xs mt-0.5 text-right">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="py-2 pr-3">
                                <input type="number" name="sale_items[{{ $i }}][price_per_kg]" data-price
                                    placeholder="0" min="0" required
                                    class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none @error('sale_items.' . $i . '.price_per_kg') border-red-400 @enderror"
                                    value="{{ $oldItem['price_per_kg'] ?? '' }}">
                                @error('sale_items.' . $i . '.price_per_kg')
                                    <p class="text-red-500 text-xs mt-0.5 text-right">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="py-2 text-right">
                                <span class="subtotal-display text-emerald-700 font-semibold text-sm">Rp 0</span>
                            </td>
                            <td class="py-2 text-center">
                                <button type="button" onclick="removeRow(this)"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors text-lg leading-none">
                                    &times;
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grand Total -->
        <div class="flex justify-end mb-5">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 text-right">
                <span class="text-xs font-semibold text-emerald-600">Total</span>
                <div id="grand-total" class="text-2xl font-bold text-emerald-800">Rp 0</div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Penjualan
            </button>
        </div>
    </form>
</div>

<!-- Template for dynamically added rows -->
<template id="row-template">
    <tr class="sale-row border-b border-stone-100">
        <td class="py-2 pr-3">
            <select name="sale_items[N][melon_variety_id]" required
                class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white">
                <option value="">Pilih varietas</option>
                @foreach($varieties as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-2 pr-3">
            <input type="number" name="sale_items[N][weight_kg]" data-weight
                placeholder="0.0" step="0.001" min="0" required
                class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
        </td>
        <td class="py-2 pr-3">
            <input type="number" name="sale_items[N][price_per_kg]" data-price
                placeholder="0" min="0" required
                class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
        </td>
        <td class="py-2 text-right">
            <span class="subtotal-display text-emerald-700 font-semibold text-sm">Rp 0</span>
        </td>
        <td class="py-2 text-center">
            <button type="button" onclick="removeRow(this)"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors text-lg leading-none">
                &times;
            </button>
        </td>
    </tr>
</template>

@endsection