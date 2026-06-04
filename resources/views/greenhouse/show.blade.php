@extends('layouts.app')
@section('title', $greenhouse->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Greenhouse Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-lg font-bold text-stone-800">{{ $greenhouse->name }}</h1>
                    @if($greenhouse->is_active)
                        <span class="shrink-0 inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </div>
                <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->code }}</p>
            </div>
            @if(auth()->user()->greenhouses->count() > 1)
                <div class="relative">
                    <select onchange="window.location.href=this.value" class="text-xs bg-stone-50 border border-stone-200 rounded-lg px-2 py-1.5 text-stone-600 focus:ring-1 focus:ring-emerald-500 outline-none">
                        @foreach(auth()->user()->greenhouses as $gh)
                            <option value="{{ route('greenhouse.show', $gh) }}" {{ $gh->id === $greenhouse->id ? 'selected' : '' }}>{{ $gh->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="bg-emerald-50 rounded-xl p-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $greenhouse->trees_count ?? 0 }}</div>
                    <div class="text-xs text-emerald-600 mt-0.5">Total Pohon</div>
                </div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $greenhouse->alive_trees_count ?? 0 }}</div>
                    <div class="text-xs text-emerald-600 mt-0.5">Hidup</div>
                </div>
            </div>
            <div class="bg-red-50 rounded-xl p-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg font-bold text-red-500 leading-tight">{{ ($greenhouse->trees_count ?? 0) - ($greenhouse->alive_trees_count ?? 0) }}</div>
                    <div class="text-xs text-red-400 mt-0.5">Mati</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            <a href="{{ route('greenhouse.report', $greenhouse) }}" class="flex flex-col items-center gap-1.5 bg-stone-50 hover:bg-stone-100 rounded-xl py-3 px-2 transition-colors border border-stone-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m4-14v14m4-10v10M7 13v8M3 17v4"/></svg>
                <span class="text-xs font-semibold text-stone-600">Laporan</span>
            </a>
            <a href="{{ route('greenhouse.trees.create', $greenhouse) }}" class="flex flex-col items-center gap-1.5 bg-stone-50 hover:bg-stone-100 rounded-xl py-3 px-2 transition-colors border border-stone-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="text-xs font-semibold text-stone-600">Tambah Pohon</span>
            </a>
            <a href="{{ route('greenhouse.trees.bulk', $greenhouse) }}" class="flex flex-col items-center gap-1.5 bg-stone-50 hover:bg-stone-100 rounded-xl py-3 px-2 transition-colors border border-stone-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                <span class="text-xs font-semibold text-stone-600">Tambah Massal</span>
            </a>
            <a href="{{ route('greenhouse.material-requests.index', $greenhouse) }}" class="flex flex-col items-center gap-1.5 bg-stone-50 hover:bg-stone-100 rounded-xl py-3 px-2 transition-colors border border-stone-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span class="text-xs font-semibold text-stone-600">Permintaan Material</span>
            </a>
        </div>
    </div>

    <!-- Recent Trees (last 5) -->
    @if($greenhouse->trees_count > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-stone-100">
            <h2 class="font-bold text-stone-700 text-sm">Pohon Terakhir</h2>
        </div>
        @php
            $recentTrees = \App\Models\Tree::where('greenhouse_id', $greenhouse->id)->with('variety')->latest()->take(5)->get();
        @endphp
        @foreach($recentTrees as $tree)
        <a href="{{ route('greenhouse.trees.show', [$greenhouse, $tree]) }}" class="flex items-center gap-3 px-5 py-3 border-b border-stone-50 hover:bg-stone-50 transition-colors">
            <div class="w-10 h-10 rounded-xl {{ $tree->status === 'alive' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-stone-700">{{ $tree->tree_number }}</p>
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full {{ $tree->status === 'alive' ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $tree->status === 'alive' ? 'Hidup' : 'Mati' }}
                    </span>
                </div>
                <p class="text-xs text-stone-400 mt-0.5">{{ $tree->variety?->name ?? 'Tanpa varietas' }} • {{ $tree->fruits()->count() }} buah</p>
            </div>
            <svg class="w-4 h-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
        <a href="{{ route('greenhouse.trees.index', $greenhouse) }}" class="flex items-center justify-center gap-2 px-5 py-3 border-t border-stone-100 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 hover:text-emerald-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <span class="text-sm font-semibold">Lihat Semua Pohon</span>
        </a>
    </div>
    @else
    <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-stone-100">
        <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
        </div>
        <p class="text-stone-400 text-sm mb-4">Belum ada pohon di greenhouse ini.</p>
        <a href="{{ route('greenhouse.trees.create', $greenhouse) }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pohon Pertama
        </a>
    </div>
    @endif
</div>
@endsection
