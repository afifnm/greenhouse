@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="max-w-md mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-xl font-bold text-stone-800">Edit User</h1>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">No. HP <span class="text-stone-400">(kosongkan jika tidak digunakan)</span></label>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all" placeholder="081234567890">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Password Baru <span class="text-stone-400">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all" placeholder="Min. 6 karakter">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1.5">Role</label>
            <select name="role" required class="w-full px-4 py-1 py-3 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all bg-white">
                <option value="admin" {{ $user->isAdmin() ? 'selected' : '' }}>Admin</option>
                <option value="manager" {{ $user->isManager() ? 'selected' : '' }}>Manager (PJ GH)</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-stone-300 focus:ring-emerald-500">
            <label for="is_active" class="text-sm font-medium text-stone-600">Akun aktif</label>
        </div>
        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-emerald-700/20">
            Update User
        </button>
    </form>
</div>
@endsection
