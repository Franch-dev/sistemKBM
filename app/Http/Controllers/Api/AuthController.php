<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * POST /api/login  — body: email, password
     * Mengembalikan Bearer token + data user + alamat dashboard sesuai role.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('role', 'kelas.jurusan')->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
                'errors'  => ['email' => ['Email atau password salah.']],
            ], 422);
        }

        if (! $user->role) {
            return response()->json(['message' => 'Akun belum memiliki role. Hubungi Admin.'], 403);
        }

        $token = $user->createToken('kbm-web')->plainTextToken;

        return response()->json([
            'message'    => 'Login berhasil.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $user->toApiArray(),
            'redirect'   => $user->dashboardPath(),
        ]);
    }

    /** POST /api/logout — mencabut token yang sedang dipakai. */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /** GET /api/me — data user yang sedang login. */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()->toApiArray()]);
    }
}
