@extends('layouts.app')
@section('title', 'Kelola Greenhouse')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Kelola Greenhouse</h1>
            <p class="text-stone-500 text-sm mt-0.5">Semua data greenhouse melon</p>
        </div>
        <a href="{{ route('admin.greenhouses.create') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-lg shadow-emerald-700/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah GH
        </a>
    </div>

    @if($greenhouses->isEmpty())
    <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-stone-100">
        <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-stone-400 text-sm">Belum ada greenhouse.</p>
    </div>
    @else
    <div class="grid gap-3">
        @foreach($greenhouses as $gh)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <h3 class="font-bold text-stone-800 truncate">{{ $gh->name }}</h3>
                        <span class="shrink-0 inline-block w-2 h-2 rounded-full {{ $gh->is_active ? 'bg-emerald-400' : 'bg-stone-300' }}"></span>
                    </div>
                    <div class="flex items-center gap-4 flex-wrap">
                        <span class="text-xs text-stone-500"><span class="font-semibold text-stone-700">{{ $gh->trees_count ?? 0 }}</span> pohon</span>
                        <span class="text-xs text-stone-500"><span class="font-semibold text-stone-700">{{ $gh->users_count ?? 0 }}</span> PJ</span>
                    </div>
                    @if($gh->description)
                        <p class="text-xs text-stone-400 mt-2 line-clamp-1">{{ $gh->description }}</p>
                    @endif
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-stone-500 mb-1">Penanggung Jawab</p>
                        @if($gh->users->isEmpty())
                            <span class="inline-flex items-center rounded-lg bg-amber-50 border border-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1">Belum ada PJ</span>
                        @else
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($gh->users->take(3) as $user)
                                    <span class="inline-flex items-center rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1">{{ $user->name }}</span>
                                @endforeach
                                @if($gh->users->count() > 3)
                                    <span class="inline-flex items-center rounded-lg bg-stone-100 text-stone-500 text-xs font-semibold px-2.5 py-1">+{{ $gh->users->count() - 3 }} lagi</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-1 gap-2 md:w-40 shrink-0">
                    <a href="{{ route('admin.greenhouses.show', $gh) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-2.5 text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat Detail</span>
                    </a>
                    <a href="{{ route('admin.greenhouses.managers', $gh) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-700 px-3 py-2.5 text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                        <span>Atur PJ</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $greenhouses->links() }}</div>
    @endif
</div>
@endsection
