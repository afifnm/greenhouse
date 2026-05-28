@extends('layouts.app')
@section('title', 'Daftar Pohon - ' . $greenhouse->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('greenhouse.show', $greenhouse) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="text-lg font-bold text-stone-800">Daftar Pohon</h1>
            <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->name }}</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <form method="GET" class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-4 space-y-3">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pohon..." class="w-full pl-9 pr-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>
            <select name="per_page" onchange="this.form.submit()" class="px-3 py-2.5 border border-stone-200 rounded-xl text-sm bg-white text-stone-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                @foreach([25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ (int) request('per_page', 50) === $size ? 'selected' : '' }}>{{ $size }} / halaman</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('greenhouse.trees.index', $greenhouse) }}?{{ http_build_query(request()->except(['page', 'status'])) }}" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold border transition-colors {{ !request('status') ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-white text-stone-500 border-stone-200 hover:bg-stone-50' }}">
                Semua
            </a>
            <a href="{{ route('greenhouse.trees.index', $greenhouse) }}?{{ http_build_query(array_merge(request()->except('page'), ['status' => 'alive'])) }}" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold border transition-colors {{ request('status') === 'alive' ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-white text-stone-500 border-stone-200 hover:bg-stone-50' }}">
                Hidup
            </a>
            <a href="{{ route('greenhouse.trees.index', $greenhouse) }}?{{ http_build_query(array_merge(request()->except('page'), ['status' => 'dead'])) }}" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold border transition-colors {{ request('status') === 'dead' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-stone-500 border-stone-200 hover:bg-stone-50' }}">
                Mati
            </a>
        </div>
    </form>

    <!-- Tree List -->
    @if($trees->isEmpty())
    <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-stone-100">
        <p class="text-stone-400 text-sm">Tidak ada pohon yang ditemukan.</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($trees as $tree)
        <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-stone-100 hover:shadow-md hover:border-emerald-200 transition-all">
            <div class="w-11 h-11 rounded-xl {{ $tree->status === 'alive' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-sm font-bold text-stone-700">{{ $tree->tree_number }}</span>
                    <span class="shrink-0 text-xs font-semibold px-1.5 py-0.5 rounded-full {{ $tree->status === 'alive' ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $tree->status === 'alive' ? 'Hidup' : 'Mati' }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-stone-400">
                    <span>{{ $tree->variety?->name ?? 'Tanpa varietas' }}</span>
                    @if($tree->status === 'alive')
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $tree->fruits()->count() }} buah
                        </span>
                        @php $good = $tree->fruits()->where('condition', 'good')->count(); @endphp
                        @if($good > 0)
                            <span class="flex items-center gap-1 text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $good }} bagus
                            </span>
                        @endif
                    @endif
                </div>
            </div>
            <svg class="w-4 h-4 text-stone-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $trees->links() }}</div>
    @endif

    <!-- Floating Add Button -->
    <a href="{{ route('greenhouse.trees.create', $greenhouse) }}" class="fixed bottom-20 md:bottom-6 right-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-full shadow-xl shadow-emerald-700/40 inline-flex items-center gap-2 px-4 py-3 text-sm font-semibold transition-colors z-40">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Tambah Pohon
    </a>
</div>
@endsection
