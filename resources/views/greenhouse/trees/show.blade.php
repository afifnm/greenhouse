@extends('layouts.app')
@section('title', $tree->tree_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('greenhouse.trees.index', $greenhouse) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold text-stone-800 truncate">{{ $tree->tree_number }}</h1>
            <p class="text-xs text-stone-400">{{ $greenhouse->name }}</p>
        </div>
        <a href="{{ route('greenhouse.trees.edit', [$greenhouse, $tree]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </div>

    <!-- Tree Status Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 mb-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl {{ $tree->status === 'alive' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }} flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-stone-800">Status: {{ $tree->status === 'alive' ? 'Hidup 🌱' : 'Mati ✕' }}</p>
                    <p class="text-xs text-stone-400">Varietas: {{ $tree->variety?->name ?? 'Belum diatur' }}</p>
                    @if($tree->status === 'alive')
                    @php
                        $total = $tree->fruits()->count();
                        $good = $tree->fruits()->where('condition', 'good')->count();
                        $rotten = $tree->fruits()->where('condition', 'rotten')->count();
                        $gradeA = $tree->fruits()->where('grade', 'A')->count();
                    @endphp
                    <div class="flex gap-4 mt-2 text-xs">
                        <div class="flex items-center gap-1">
                            <span class="text-stone-400">Total:</span>
                            <span class="font-bold text-stone-700">{{ $total }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-stone-400">Bagus:</span>
                            <span class="font-bold text-emerald-700">{{ $good }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-stone-400">Busuk:</span>
                            <span class="font-bold text-red-600">{{ $rotten }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-stone-400">Grade A:</span>
                            <span class="font-bold text-amber-700">{{ $gradeA }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($tree->status === 'dead')
                <span class="shrink-0 text-xs font-semibold px-2 py-1 rounded-lg bg-red-50 text-red-600 border border-red-200">Pohon Mati</span>
            @endif
        </div>

        @if($tree->status === 'alive')
        <a href="{{ route('greenhouse.fruits.create', [$greenhouse, $tree]) }}" class="w-full flex items-center justify-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20 mb-5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Data Buah
        </a>
        @else
        <div class="bg-stone-50 rounded-xl p-4 text-center">
            <p class="text-sm text-stone-500">Pohon mati tidak bisa menambahkan data buah.</p>
        </div>
        @endif
    </div>

    <!-- Fruit List -->
    @if($tree->fruits->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-100">
            <h2 class="font-bold text-stone-700 text-sm">Data Buah ({{ $tree->fruits->count() }})</h2>
        </div>
        @foreach($tree->fruits as $fruit)
        <div class="flex items-center gap-3 px-5 py-4 border-b border-stone-50 last:border-0">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $fruit->condition === 'good' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-stone-700">Buah #{{ $fruit->id }}</span>
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full {{ $fruit->condition === 'good' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                        {{ $fruit->condition === 'good' ? 'Bagus' : 'Busuk' }}
                    </span>
                    @if($fruit->grade)
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700">Grade {{ $fruit->grade }}</span>
                    @endif
                    @if($fruit->weight)
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-stone-100 text-stone-600">{{ $fruit->weight }} kg</span>
                    @endif
                </div>
                @if($fruit->notes)
                    <p class="text-xs text-stone-400 mt-0.5">{{ $fruit->notes }}</p>
                @endif
                <p class="text-xs text-stone-300 mt-0.5">{{ $fruit->created_at->format('d M Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                <a href="{{ route('greenhouse.fruits.edit', [$greenhouse, $tree, $fruit]) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-stone-50 hover:bg-stone-100 text-stone-600 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('greenhouse.fruits.destroy', [$greenhouse, $tree, $fruit]) }}" onsubmit="return confirm('Hapus data buah ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h3m10-4V4a1 1 0 00-1-1h-3M5 6h14"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
