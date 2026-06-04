<?php

namespace App\Http\Controllers\Api\Greenhouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\FruitResource;
use App\Models\Fruit;
use App\Models\Greenhouse;
use App\Models\Tree;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FruitController extends Controller
{
    public function index(Greenhouse $greenhouse, Tree $tree): AnonymousResourceCollection
    {
        return FruitResource::collection($tree->fruits()->latest()->get());
    }

    public function store(Request $request, Greenhouse $greenhouse, Tree $tree): JsonResponse
    {
        if ($tree->status === 'dead') {
            return response()->json(['message' => 'Tidak dapat menambahkan buah ke pohon yang sudah mati.'], 422);
        }

        $data = $request->validate([
            'condition' => 'required|in:good,rotten',
            'grade'     => 'nullable|in:A,B,C,D',
            'weight'    => 'nullable|numeric|min:0.01',
            'notes'     => 'nullable|string',
        ]);

        if ($data['condition'] === 'rotten') {
            $data['grade']  = null;
            $data['weight'] = null;
        }

        $data['tree_id'] = $tree->id;
        $fruit = Fruit::create($data);

        return response()->json(new FruitResource($fruit), 201);
    }

    public function show(Greenhouse $greenhouse, Tree $tree, Fruit $fruit): JsonResponse
    {
        return response()->json(new FruitResource($fruit));
    }

    public function update(Request $request, Greenhouse $greenhouse, Tree $tree, Fruit $fruit): JsonResponse
    {
        $data = $request->validate([
            'condition' => 'required|in:good,rotten',
            'grade'     => 'nullable|in:A,B,C,D',
            'weight'    => 'nullable|numeric|min:0.01',
            'notes'     => 'nullable|string',
        ]);

        if ($data['condition'] === 'rotten') {
            $data['grade']  = null;
            $data['weight'] = null;
        }

        $fruit->update($data);

        return response()->json(new FruitResource($fruit));
    }

    public function destroy(Greenhouse $greenhouse, Tree $tree, Fruit $fruit): JsonResponse
    {
        $fruit->delete();

        return response()->json(['message' => 'Buah berhasil dihapus.']);
    }
}
