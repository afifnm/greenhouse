<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelonVariety;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MelonVarietyController extends Controller
{
    public function index()
    {
        $varieties = MelonVariety::orderBy('name')->paginate(15);
        return view('admin.varieties.index', compact('varieties'));
    }

    public function create()
    {
        return view('admin.varieties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = Str::slug($request->name);
        MelonVariety::create($validated);

        return redirect()->route('admin.varieties.index')->with('success', 'Varietas melon berhasil ditambahkan.');
    }

    public function edit(MelonVariety $variety)
    {
        return view('admin.varieties.edit', compact('variety'));
    }

    public function update(Request $request, MelonVariety $variety)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $variety->update($validated);

        return redirect()->route('admin.varieties.index')->with('success', 'Varietas melon berhasil diperbarui.');
    }

    public function destroy(MelonVariety $variety)
    {
        $variety->delete();
        return redirect()->route('admin.varieties.index')->with('success', 'Varietas melon berhasil dihapus.');
    }
}