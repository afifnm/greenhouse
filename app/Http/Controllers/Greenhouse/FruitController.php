<?php

namespace App\Http\Controllers\Greenhouse;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\Greenhouse;
use App\Models\Tree;
use Illuminate\Http\Request;

class FruitController extends Controller
{
    public function create(Greenhouse $greenhouse, Tree $tree)
    {
        if ($tree->status === 'dead') {
            return redirect()->route('greenhouse.trees.show', [$greenhouse, $tree])
                ->with('error', 'Pohon mati tidak bisa ditambahkan buah.');
        }
        return view('greenhouse.fruits.create', compact('greenhouse', 'tree'));
    }

    public function store(Request $request, Greenhouse $greenhouse, Tree $tree)
    {
        if ($tree->status === 'dead') {
            return back()->with('error', 'Pohon mati tidak bisa ditambahkan buah.');
        }

        $validated = $request->validate([
            'condition' => 'required|in:good,rotten',
            'grade' => 'nullable|required_if:condition,good|in:A,B,C,D',
            'weight' => 'nullable|numeric|min:0.01|max:9999.99',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['condition'] === 'rotten') {
            $validated['grade'] = null;
            $validated['weight'] = null;
        }

        $validated['tree_id'] = $tree->id;
        Fruit::create($validated);

        return redirect()->route('greenhouse.trees.show', [$greenhouse, $tree])
            ->with('success', 'Data buah berhasil ditambahkan.');
    }

    public function edit(Greenhouse $greenhouse, Tree $tree, Fruit $fruit)
    {
        return view('greenhouse.fruits.edit', compact('greenhouse', 'tree', 'fruit'));
    }

    public function update(Request $request, Greenhouse $greenhouse, Tree $tree, Fruit $fruit)
    {
        $validated = $request->validate([
            'condition' => 'required|in:good,rotten',
            'grade' => 'nullable|required_if:condition,good|in:A,B,C,D',
            'weight' => 'nullable|numeric|min:0.01|max:9999.99',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['condition'] === 'rotten') {
            $validated['grade'] = null;
            $validated['weight'] = null;
        }

        $fruit->update($validated);

        return redirect()->route('greenhouse.trees.show', [$greenhouse, $tree])
            ->with('success', 'Data buah berhasil diperbarui.');
    }

    public function destroy(Greenhouse $greenhouse, Tree $tree, Fruit $fruit)
    {
        $fruit->delete();
        return redirect()->route('greenhouse.trees.show', [$greenhouse, $tree])
            ->with('success', 'Data buah berhasil dihapus.');
    }
}
