<?php

namespace App\Http\Controllers\Api\Greenhouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\TreeResource;
use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Tree;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TreeController extends Controller
{
    public function index(Request $request, Greenhouse $greenhouse): AnonymousResourceCollection
    {
        $request->validate([
            'status'   => 'nullable|in:alive,dead',
            'per_page' => 'nullable|in:25,50,100',
        ]);

        $perPage = (int) $request->input('per_page', 50);

        $trees = Tree::where('greenhouse_id', $greenhouse->id)
            ->with('variety')
            ->withCount('fruits')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('tree_number', 'like', "%{$s}%"))
            ->orderBy('tree_number')
            ->paginate($perPage);

        return TreeResource::collection($trees);
    }

    public function store(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $data = $request->validate([
            'tree_number'      => 'required|string|max:50|unique:trees,tree_number',
            'melon_variety_id' => 'nullable|exists:melon_varieties,id',
            'status'           => 'required|in:alive,dead',
        ]);

        $data['greenhouse_id'] = $greenhouse->id;
        $tree = Tree::create($data);
        $tree->load('variety');

        return response()->json(new TreeResource($tree), 201);
    }

    public function show(Greenhouse $greenhouse, Tree $tree): JsonResponse
    {
        $tree->load('variety', 'fruits');

        return response()->json(new TreeResource($tree));
    }

    public function update(Request $request, Greenhouse $greenhouse, Tree $tree): JsonResponse
    {
        $data = $request->validate([
            'tree_number'      => 'required|string|max:50|unique:trees,tree_number,' . $tree->id,
            'melon_variety_id' => 'nullable|exists:melon_varieties,id',
            'status'           => 'required|in:alive,dead',
        ]);

        $tree->update($data);
        $tree->load('variety');

        return response()->json(new TreeResource($tree));
    }

    public function destroy(Greenhouse $greenhouse, Tree $tree): JsonResponse
    {
        $tree->delete();

        return response()->json(['message' => 'Pohon berhasil dihapus.']);
    }

    public function bulkCreate(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $request->validate([
            'prefix'           => 'required|string|max:20',
            'count'            => 'required|integer|min:1|max:2000',
            'melon_variety_id' => 'nullable|exists:melon_varieties,id',
            'status'           => 'required|in:alive,dead',
        ]);

        $created = 0;
        for ($i = 1; $i <= $request->count; $i++) {
            $treeNumber = sprintf('%s-%04d', $request->prefix, $i);
            if (! Tree::where('tree_number', $treeNumber)->exists()) {
                Tree::create([
                    'greenhouse_id'    => $greenhouse->id,
                    'tree_number'      => $treeNumber,
                    'melon_variety_id' => $request->melon_variety_id,
                    'status'           => $request->status,
                ]);
                $created++;
            }
        }

        return response()->json([
            'message' => "{$created} pohon berhasil ditambahkan.",
            'created' => $created,
        ], 201);
    }
}
