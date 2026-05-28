@extends('layouts.app')
@section('title', 'Tambah Varietas')

@section('content')
<div class="max-w-md mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.varieties.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-xl font-bold text-stone-800">Tambah Varietas Melon</h1>
    </div>

    <form method="POST" action="{{ route('admin.varieties.store') }}" class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Nama Varietas <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all" placeholder="Sugar Honey">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Deskripsi <span class="text-stone-400">(opsional)</span></label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all resize-none" placeholder="Deskripsi varietas melon...">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20">
            Simpan Varietas
        </button>
    </form>
</div>
@endsection
