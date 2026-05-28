<?php

namespace App\Http\Controllers\Greenhouse;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Tree;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    public function index(Request $request, Greenhouse $greenhouse)
    {
        $request->validate([
            'status' => 'nullable|in:alive,dead',
            'per_page' => 'nullable|in:25,50,100',
        ]);

        $perPage = (int) $request->input('per_page', 50);

        $trees = Tree::where('greenhouse_id', $greenhouse->id)
            ->with('variety', 'fruits')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('tree_number', 'like', "%{$s}%"))
            ->orderBy('tree_number')
            ->paginate($perPage)
            ->withQueryString();

        return view('greenhouse.trees.index', compact('greenhouse', 'trees'));
    }

    public function create(Request $request, Greenhouse $greenhouse)
    {
        $varieties = MelonVariety::all();
        return view('greenhouse.trees.create', compact('greenhouse', 'varieties'));
    }

    public function store(Request $request, Greenhouse $greenhouse)
    {
        $validated = $request->validate([
            'tree_number' => 'required|string|max:50|unique:trees,tree_number',
            'melon_variety_id' => 'nullable|exists:melon_varieties,id',
            'status' => 'required|in:alive,dead',
        ]);

        $validated['greenhouse_id'] = $greenhouse->id;
        Tree::create($validated);

        return redirect()->route('greenhouse.trees.index', $greenhouse)->with('success', 'Pohon berhasil ditambahkan.');
    }

    public function show(Greenhouse $greenhouse, Tree $tree)
    {
        $tree->load('variety', 'fruits');
        return view('greenhouse.trees.show', compact('greenhouse', 'tree'));
    }

    public function edit(Greenhouse $greenhouse, Tree $tree)
    {
        $varieties = MelonVariety::all();
        return view('greenhouse.trees.edit', compact('greenhouse', 'tree', 'varieties'));
    }

    public function update(Request $request, Greenhouse $greenhouse, Tree $tree)
    {
        $validated = $request->validate([
            'tree_number' => 'required|string|max:50|unique:trees,tree_number,' . $tree->id,
            'melon_variety_id' => 'nullable|exists:melon_varieties,id',
            'status' => 'required|in:alive,dead',
        ]);

        $tree->update($validated);

        return redirect()->route('greenhouse.trees.index', $greenhouse)->with('success', 'Pohon berhasil diperbarui.');
    }

    public function destroy(Greenhouse $greenhouse, Tree $tree)
    {
        $tree->delete();
        return redirect()->route('greenhouse.trees.index', $greenhouse)->with('success', 'Pohon berhasil dihapus.');
    }

    public function bulkCreate(Request $request, Greenhouse $greenhouse)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'prefix' => 'required|string|max:20',
                'count' => 'required|integer|min:1|max:2000',
                'melon_variety_id' => 'nullable|exists:melon_varieties,id',
                'status' => 'required|in:alive,dead',
            ]);

            $created = 0;
            for ($i = 1; $i <= $request->count; $i++) {
                $treeNumber = sprintf('%s-%04d', $request->prefix, $i);
                if (!Tree::where('tree_number', $treeNumber)->exists()) {
                    Tree::create([
                        'greenhouse_id' => $greenhouse->id,
                        'tree_number' => $treeNumber,
                        'melon_variety_id' => $request->melon_variety_id,
                        'status' => $request->status,
                    ]);
                    $created++;
                }
            }

            return redirect()->route('greenhouse.trees.index', $greenhouse)
                ->with('success', "{$created} pohon berhasil ditambahkan.");
        }

        $varieties = MelonVariety::all();
        return view('greenhouse.trees.bulk-create', compact('greenhouse', 'varieties'));
    }
}
