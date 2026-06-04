<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MelonVarietyResource;
use App\Models\MelonVariety;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class MelonVarietyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return MelonVarietyResource::collection(
            MelonVariety::orderBy('name')->paginate(15)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:melon_varieties',
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $variety = MelonVariety::create($data);

        return response()->json(new MelonVarietyResource($variety), 201);
    }

    public function show(MelonVariety $variety): JsonResponse
    {
        return response()->json(new MelonVarietyResource($variety));
    }

    public function update(Request $request, MelonVariety $variety): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:melon_varieties,name,' . $variety->id,
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $variety->update($data);

        return response()->json(new MelonVarietyResource($variety));
    }

    public function destroy(MelonVariety $variety): JsonResponse
    {
        $variety->delete();

        return response()->json(['message' => 'Varietas melon berhasil dihapus.']);
    }

    public function all(): AnonymousResourceCollection
    {
        return MelonVarietyResource::collection(MelonVariety::orderBy('name')->get());
    }
}
