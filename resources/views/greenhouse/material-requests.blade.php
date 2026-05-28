@extends('layouts.app')
@section('title', $greenhouse->name . ' - Permintaan Material')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-6 pb-8">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-stone-800">Permintaan Material</h1>
                <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->name }}</p>
            </div>
            <a href="{{ route('greenhouse.show', $greenhouse) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-stone-100 text-stone-600 text-sm font-medium hover:bg-stone-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>

        <!-- Form Ajukan Request -->
        <form method="POST" action="{{ route('greenhouse.material-requests.store', $greenhouse) }}" class="bg-stone-50 rounded-xl p-4 border border-stone-100">
            @csrf
            <h3 class="text-sm font-bold text-stone-700 mb-3">Ajukan Permintaan Baru</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Nama Material</label>
                    <input type="text" name="material_name" placeholder="Contoh: Pupuk NPK" required
                        class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white @error('material_name') border-red-400 @enderror"
                        value="{{ old('material_name') }}">
                    @error('material_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Jumlah</label>
                    <input type="number" name="quantity" placeholder="10" min="1" required
                        class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white @error('quantity') border-red-400 @enderror"
                        value="{{ old('quantity') }}">
                    @error('quantity')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Satuan</label>
                    <input type="text" name="unit" placeholder="kg, pcs, liter" required
                        class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white @error('unit') border-red-400 @enderror"
                        value="{{ old('unit') }}">
                    @error('unit')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-stone-600 mb-1">Catatan (opsional)</label>
                <textarea name="notes" rows="2" placeholder="Keterangan tambahan..."
                    class="w-full text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none bg-white resize-none">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                Ajukan Permintaan
            </button>
        </form>
    </div>

    <!-- Daftar Request -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-stone-100">
            <h2 class="font-bold text-stone-700 text-sm">Riwayat Permintaan</h2>
        </div>

        @if($materialRequests->isEmpty())
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
                            <th class="text-left px-4 py-3 font-semibold text-stone-600">Diajukan</th>
                            <th class="text-center px-4 py-3 font-semibold text-stone-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materialRequests as $request)
                        <tr class="border-b border-stone-50 last:border-0 hover:bg-stone-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-stone-700">{{ $request->material_name }}</div>
                                @if($request->notes)
                                    <div class="text-xs text-stone-400 mt-0.5">{{ $request->notes }}</div>
                                @endif
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
                                <div class="text-stone-600 text-xs">{{ $request->user->name }}</div>
                                <div class="text-stone-400 text-xs">{{ $request->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('greenhouse.material-requests.destroy', [$greenhouse, $request]) }}" onsubmit="return confirm('Yakin hapus permintaan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-50 text-red-600 font-semibold px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($materialRequests->hasPages())
            <div class="px-5 py-3 border-t border-stone-100">
                {{ $materialRequests->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection