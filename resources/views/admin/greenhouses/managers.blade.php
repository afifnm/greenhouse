@extends('layouts.app')
@section('title', 'Atur PJ - ' . $greenhouse->name)

@section('content')
<div class="max-w-md mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.greenhouses.show', $greenhouse) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-800">Atur Penanggung Jawab</h1>
            <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.greenhouses.managers.assign', $greenhouse) }}" class="space-y-4">
        @csrf @method('PUT')

        @if($managers->isEmpty())
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 text-center">
                <p class="text-stone-400 text-sm">Belum ada user dengan role Manager.</p>
                <a href="{{ route('admin.users.create') }}" class="text-emerald-700 text-sm font-semibold hover:underline mt-2 inline-block">+ Buat User Manager</a>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
                <div class="p-4 border-b border-stone-100">
                    <p class="text-xs text-stone-500">Pilih penanggung jawab untuk <strong>{{ $greenhouse->name }}</strong>. Satu GH boleh punya lebih dari 1 PJ.</p>
                </div>
                <div class="divide-y divide-stone-100">
                    @foreach($managers as $manager)
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-stone-50 transition-colors">
                        <input type="checkbox" name="managers[]" value="{{ $manager->id }}"
                            {{ in_array($manager->id, $assigned) ? 'checked' : '' }}
                            class="w-4.5 h-4.5 text-emerald-600 rounded border-stone-300 focus:ring-emerald-500">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-stone-700 truncate">{{ $manager->name }}</p>
                            <p class="text-xs text-stone-400">{{ $manager->email }}</p>
                        </div>
                        @if(in_array($manager->id, $assigned))
                            <span class="shrink-0 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Terpilih</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20">
                Simpan Perubahan
            </button>
        @endif
    </form>
</div>
@endsection
