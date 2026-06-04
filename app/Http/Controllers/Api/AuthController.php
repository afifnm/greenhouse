<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            throw ValidationException::withMessages([
                'login' => ['Email/nomor HP atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
