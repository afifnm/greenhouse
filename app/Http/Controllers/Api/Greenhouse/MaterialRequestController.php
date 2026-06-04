<?php

namespace App\Http\Controllers\Api\Greenhouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialRequestResource;
use App\Models\Greenhouse;
use App\Models\MaterialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MaterialRequestController extends Controller
{
    public function index(Greenhouse $greenhouse): AnonymousResourceCollection
    {
        $requests = MaterialRequest::with('user')
            ->where('greenhouse_id', $greenhouse->id)
            ->latest()
            ->paginate(20);

        return MaterialRequestResource::collection($requests);
    }

    public function store(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $data = $request->validate([
            'material_name' => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'unit'          => 'required|string|max:50',
            'notes'         => 'nullable|string',
        ]);

        $data['greenhouse_id'] = $greenhouse->id;
        $data['user_id']       = $request->user()->id;
        $data['status']        = 'pending';

        $materialRequest = MaterialRequest::create($data);
        $materialRequest->load('user');

        return response()->json(new MaterialRequestResource($materialRequest), 201);
    }

    public function update(Request $request, Greenhouse $greenhouse, MaterialRequest $materialRequest): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,fulfilled',
        ]);

        $materialRequest->update($data);

        return response()->json(new MaterialRequestResource($materialRequest));
    }

    public function destroy(Greenhouse $greenhouse, MaterialRequest $materialRequest): JsonResponse
    {
        $materialRequest->delete();

        return response()->json(['message' => 'Permintaan bahan berhasil dihapus.']);
    }
}
