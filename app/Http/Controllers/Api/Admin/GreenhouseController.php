<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GreenhouseResource;
use App\Http\Resources\UserResource;
use App\Models\Greenhouse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GreenhouseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Greenhouse::withCount(['trees', 'users']);

        if ($search = $request->search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
            );
        }

        return GreenhouseResource::collection($query->paginate(12));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:greenhouses',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $greenhouse = Greenhouse::create($data);

        return response()->json(new GreenhouseResource($greenhouse), 201);
    }

    public function show(Greenhouse $greenhouse): JsonResponse
    {
        $greenhouse->loadCount(['trees', 'users']);
        $greenhouse->load('users');

        return response()->json(new GreenhouseResource($greenhouse));
    }

    public function update(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:greenhouses,code,' . $greenhouse->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $greenhouse->update($data);

        return response()->json(new GreenhouseResource($greenhouse));
    }

    public function destroy(Greenhouse $greenhouse): JsonResponse
    {
        $greenhouse->delete();

        return response()->json(['message' => 'Greenhouse berhasil dihapus.']);
    }

    public function managers(Greenhouse $greenhouse): AnonymousResourceCollection
    {
        $managers = User::where('role', 'manager')->get();
        $assigned = $greenhouse->users()->pluck('users.id')->toArray();

        return UserResource::collection($managers)->additional([
            'assigned_ids' => $assigned,
        ]);
    }

    public function assignManagers(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $request->validate([
            'user_ids'   => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $greenhouse->users()->sync($request->user_ids ?? []);

        return response()->json(['message' => 'Manager berhasil disinkronkan.']);
    }
}
