@extends('layouts.app')
@section('title', 'Varietas Melon')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Varietas Melon</h1>
            <p class="text-stone-500 text-sm mt-0.5">Kelola jenis melon</p>
        </div>
        <a href="{{ route('admin.varieties.create') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-lg shadow-emerald-700/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Varietas
        </a>
    </div>

    @if($varieties->isEmpty())
    <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-stone-100">
        <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
        </div>
        <p class="text-stone-400 text-sm">Belum ada varietas melon.</p>
    </div>
    @else
    <div class="grid gap-3">
        @foreach($varieties as $variety)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <h3 class="font-bold text-stone-800 truncate">{{ $variety->name }}</h3>
                        <span class="shrink-0 text-xs font-mono bg-stone-100 text-stone-500 px-2 py-0.5 rounded-full">{{ $variety->trees->count() }} pohon</span>
                    </div>
                    <p class="text-xs text-stone-400 font-mono mb-2">{{ $variety->slug }}</p>
                    @if($variety->description)
                        <p class="text-sm text-stone-500">{{ $variety->description }}</p>
                    @else
                        <p class="text-sm text-stone-300 italic">Tanpa deskripsi</p>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                    <a href="{{ route('admin.varieties.edit', $variety) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-stone-50 hover:bg-stone-100 text-stone-600 text-xs font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.varieties.destroy', $variety) }}" onsubmit="return confirm('Yakin hapus varietas {{ $variety->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h3m10-4V4a1 1 0 00-1-1h-3M5 6h14"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $varieties->links() }}</div>
    @endif
</div>
@endsection
