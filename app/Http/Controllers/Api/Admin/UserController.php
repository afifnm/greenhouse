<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        if ($role = $request->role) {
            $query->where('role', $role);
        }

        if ($search = $request->search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        return UserResource::collection($query->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'phone'     => 'required|string|unique:users',
            'password'  => 'required|string|min:8',
            'role'      => ['required', Rule::in(['admin', 'manager'])],
            'is_active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(new UserResource($user), 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'password'  => 'nullable|string|min:8',
            'role'      => ['required', Rule::in(['admin', 'manager'])],
            'is_active' => 'boolean',
        ]);

        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(new UserResource($user));
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
