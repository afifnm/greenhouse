@extends('layouts.app')
@section('title', 'Pengaturan Nota')

@section('content')
<div class="max-w-xl mx-auto px-4 pt-4 sm:pt-6 pb-20">

    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-stone-100">

        <h1 class="text-lg font-bold text-stone-800 mb-4 border-b border-stone-100 pb-4">
            Pengaturan Nota
        </h1>

        @if (session('success'))
            <div class="mb-4 bg-emerald-50 text-emerald-700 text-sm font-medium p-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="namacv" class="text-sm font-medium text-stone-600 block mb-1.5">Nama CV/Toko</label>
                    <input type="text" id="namacv" name="namacv" value="{{ old('namacv', $settings['namacv'] ?? '') }}"
                        class="w-full text-sm rounded-lg border-stone-200 placeholder:text-stone-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="alamat" class="text-sm font-medium text-stone-600 block mb-1.5">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                        class="w-full text-sm rounded-lg border-stone-200 placeholder:text-stone-300 focus:ring-emerald-500 focus:border-emerald-500">{{ old('alamat', $settings['alamat'] ?? '') }}</textarea>
                </div>

                <div>
                    <label for="telp" class="text-sm font-medium text-stone-600 block mb-1.5">No. Telepon</label>
                    <input type="text" id="telp" name="telp" value="{{ old('telp', $settings['telp'] ?? '') }}"
                        class="w-full text-sm rounded-lg border-stone-200 placeholder:text-stone-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="mt-6 border-t border-stone-100 pt-4">
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-medium hover:bg-emerald-800 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
