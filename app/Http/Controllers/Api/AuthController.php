<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Create API Token via Laravel Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // Load specific profile depending on role
            $profile = null;
            if ($user->role === 'murid') {
                $profile = $user->profilMurid;
            } elseif ($user->role === 'guru') {
                $profile = $user->profilGuru;
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'profile' => $profile,
                    ],
                    'token' => $token,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.',
            'data' => null,
        ], 401);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request) {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
            'data' => null,
        ]);
    }
}
