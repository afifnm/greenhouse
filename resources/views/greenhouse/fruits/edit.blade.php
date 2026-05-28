@extends('layouts.app')
@section('title', 'Edit Buah')

@section('content')
<div class="max-w-md mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-800">Edit Data Buah</h1>
            <p class="text-xs text-stone-400 font-mono">{{ $tree->tree_number }} — Buah #{{ $fruit->id }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('greenhouse.fruits.update', [$greenhouse, $tree, $fruit]) }}" class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-3">Kondisi Buah</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer">
                    <input type="radio" name="condition" value="good" {{ old('condition', $fruit->condition) === 'good' ? 'checked' : '' }} class="peer sr-only">
                    <div class="w-full p-4 rounded-xl border-2 border-stone-200 bg-white text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                        <div class="text-2xl mb-1">✅</div>
                        <p class="text-sm font-semibold text-stone-700">Bagus</p>
                    </div>
                </label>
                <label class="relative flex cursor-pointer">
                    <input type="radio" name="condition" value="rotten" {{ old('condition', $fruit->condition) === 'rotten' ? 'checked' : '' }} class="peer sr-only">
                    <div class="w-full p-4 rounded-xl border-2 border-stone-200 bg-white text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                        <div class="text-2xl mb-1">❌</div>
                        <p class="text-sm font-semibold text-stone-700">Busuk</p>
                    </div>
                </label>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-3">Grade <span class="text-stone-400">(opsional)</span></label>
            <div class="grid grid-cols-4 gap-2">
                @foreach(['A', 'B', 'C', 'D'] as $grade)
                <label class="relative flex cursor-pointer">
                    <input type="radio" name="grade" value="{{ $grade }}" {{ old('grade', $fruit->grade) === $grade ? 'checked' : '' }} class="peer sr-only">
                    <div class="w-full py-3 rounded-xl border-2 border-stone-200 bg-white text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                        <span class="text-sm font-bold text-stone-600">{{ $grade }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Berat (kg) <span class="text-stone-400">(opsional)</span></label>
            <input type="number" name="weight" step="0.01" min="0.01" placeholder="Contoh: 1.50"
                class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                value="{{ old('weight', $fruit->weight) }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Catatan</label>
            <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all resize-none">{{ old('notes', $fruit->notes) }}</textarea>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="flex-1 text-center py-3 rounded-xl border border-stone-200 text-stone-500 font-semibold text-sm hover:bg-stone-50 transition-colors">Batal</a>
            <button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20">Simpan</button>
        </div>
    </form>
</div>
@endsection
