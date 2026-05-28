@extends('layouts.app')
@section('title', $greenhouse->name . ' - Detail')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.greenhouses.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-800">{{ $greenhouse->name }}</h1>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-stone-50 text-stone-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-emerald-700 leading-tight">{{ $greenhouse->trees_count ?? 0 }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Total Pohon</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-emerald-700 leading-tight">{{ $greenhouse->alive_trees_count ?? 0 }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Pohon Hidup</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-red-500 leading-tight">{{ ($greenhouse->trees_count ?? 0) - ($greenhouse->alive_trees_count ?? 0) }}</div>
                <div class="text-xs text-stone-500 mt-0.5">Pohon Mati</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-1 mb-4 bg-stone-100 p-1 rounded-xl w-fit">
        <button onclick="switchTab('detail')" id="tab-detail" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors bg-white text-emerald-700 shadow-sm">Detail</button>
        <button onclick="switchTab('material')" id="tab-material" class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors text-stone-500 hover:text-stone-700">Material</button>
    </div>

    <!-- Tab: Detail -->
    <div id="content-detail" class="tab-content">
        <!-- Managers -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-stone-800">Penanggung Jawab</h2>
                <a href="{{ route('admin.greenhouses.managers', $greenhouse) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">+ Kelola PJ</a>
            </div>
            @if($greenhouse->users->isEmpty())
                <p class="text-sm text-stone-400 text-center py-4">Belum ada penanggung jawab.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($greenhouse->users as $user)
                        <span class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium px-3 py-1.5 rounded-full">
                            <div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            {{ $user->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('greenhouse.report', $greenhouse) }}" class="bg-emerald-700 text-white font-semibold py-3 rounded-xl text-sm text-center hover:bg-emerald-800 transition-colors">Lihat Laporan</a>
            <a href="{{ route('admin.greenhouses.edit', $greenhouse) }}" class="flex-1 bg-white border border-emerald-200 text-emerald-700 font-semibold py-3 rounded-xl text-sm text-center hover:bg-emerald-50 transition-colors">Edit</a>
            <form method="POST" action="{{ route('admin.greenhouses.destroy', $greenhouse) }}" onsubmit="return confirm('Yakin hapus greenhouse ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full bg-red-50 border border-red-200 text-red-600 font-semibold py-3 rounded-xl text-sm text-center hover:bg-red-100 transition-colors">Hapus</button>
            </form>
        </div>
    </div>

    <!-- Tab: Material -->
    <div id="content-material" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-stone-100 flex items-center justify-between">
                <h2 class="font-bold text-stone-700 text-sm">Permintaan Material</h2>
                <span class="text-xs text-stone-400">{{ $greenhouse->materialRequests->count() }} permintaan</span>
            </div>

            @if($greenhouse->materialRequests->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-stone-400 text-sm">Belum ada permintaan material.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-100">
                                <th class="text-left px-5 py-3 font-semibold text-stone-600">Material</th>
                                <th class="text-center px-4 py-3 font-semibold text-stone-600">Jumlah</th>
                                <th class="text-center px-4 py-3 font-semibold text-stone-600">Status</th>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">Penajukan</th>
                                <th class="text-center px-4 py-3 font-semibold text-stone-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($greenhouse->materialRequests as $request)
                            <tr class="border-b border-stone-50 last:border-0 hover:bg-stone-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-stone-700">{{ $request->material_name }}</div>
                                    @if($request->notes)
                                        <div class="text-xs text-stone-400 mt-0.5">{{ $request->notes }}</div>
                                    @endif
                                    <div class="text-xs text-stone-400 mt-0.5">{{ $request->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-stone-700">{{ $request->quantity }} {{ $request->unit }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $request->status === 'fulfilled' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $request->status === 'fulfilled' ? 'Terpenuhi' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-stone-600 text-xs">{{ $request->user->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('greenhouse.material-requests.update', [$greenhouse, $request]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $request->status === 'pending' ? 'fulfilled' : 'pending' }}">
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors {{ $request->status === 'pending' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-amber-100 text-amber-700 hover:bg-amber-200' }}">
                                            {{ $request->status === 'pending' ? '✓ Terpenuhi' : '↩ Belum' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
        el.classList.add('text-stone-500');
    });

    document.getElementById('content-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
    activeBtn.classList.remove('text-stone-500');
}
</script>
@endpush
@endsection