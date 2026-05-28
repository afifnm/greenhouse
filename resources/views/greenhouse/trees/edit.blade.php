@extends('layouts.app')
@section('title', 'Edit Pohon')

@section('content')
<div class="max-w-md mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-800">Edit Pohon</h1>
            <p class="text-xs text-stone-400 font-mono">{{ $tree->tree_number }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('greenhouse.trees.update', [$greenhouse, $tree]) }}" class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Nomor Pohon</label>
            <input type="text" name="tree_number" value="{{ old('tree_number', $tree->tree_number) }}" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm font-mono uppercase focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
            @error('tree_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Varietas Melon</label>
            <select name="melon_variety_id" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all bg-white">
                <option value="">Pilih varietas...</option>
                @foreach($varieties as $v)
                    <option value="{{ $v->id }}" {{ old('melon_variety_id', $tree->melon_variety_id) == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-3">Status Pohon</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer">
                    <input type="radio" name="status" value="alive" {{ old('status', $tree->status) === 'alive' ? 'checked' : '' }} class="peer sr-only">
                    <div class="w-full p-4 rounded-xl border-2 border-stone-200 bg-white text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                        <div class="text-2xl mb-1">🌱</div>
                        <p class="text-sm font-semibold text-stone-700">Hidup</p>
                    </div>
                </label>
                <label class="relative flex cursor-pointer">
                    <input type="radio" name="status" value="dead" {{ old('status', $tree->status) === 'dead' ? 'checked' : '' }} class="peer sr-only">
                    <div class="w-full p-4 rounded-xl border-2 border-stone-200 bg-white text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                        <div class="text-2xl mb-1">✕</div>
                        <p class="text-sm font-semibold text-stone-700">Mati</p>
                    </div>
                </label>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="flex-1 text-center py-3 rounded-xl border border-stone-200 text-stone-500 font-semibold text-sm hover:bg-stone-50 transition-colors">Batal</a>
            <button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20">Simpan</button>
        </div>
    </form>
    <form method="POST" action="{{ route('greenhouse.trees.destroy', [$greenhouse, $tree]) }}" class="mt-3" onsubmit="return confirm('Yakin hapus pohon ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="w-full text-red-500 font-semibold py-3 rounded-xl text-sm hover:bg-red-50 transition-colors">Hapus Pohon</button>
    </form>
</div>
@endsection
