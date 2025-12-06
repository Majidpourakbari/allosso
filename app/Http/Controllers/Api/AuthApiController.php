<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * Verify allohash and return user information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAllohash(Request $request): JsonResponse
    {
        $request->validate([
            'allohash' => ['required', 'string'],
        ]);

        $allohash = $request->input('allohash');

        // Find user by allohash
        $user = User::where('allohash', $allohash)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid allohash',
                'data' => null,
            ], 404);
        }

        // Return user information (excluding sensitive data)
        return response()->json([
            'success' => true,
            'message' => 'User verified successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'allohash' => $user->allohash,
                'access_erp' => (bool) $user->access_erp,
                'access_admin_portal' => (bool) $user->access_admin_portal,
                'access_ai_developer' => (bool) $user->access_ai_developer,
                'created_at' => $user->created_at?->toISOString(),
            ],
        ], 200);
    }

    /**
     * Check if allohash is valid (lightweight check)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAllohash(Request $request): JsonResponse
    {
        $request->validate([
            'allohash' => ['required', 'string'],
        ]);

        $allohash = $request->input('allohash');

        $exists = User::where('allohash', $allohash)->exists();

        return response()->json([
            'success' => true,
            'valid' => $exists,
            'message' => $exists ? 'Allohash is valid' : 'Allohash is invalid',
        ], 200);
    }

    public function externalAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $email = strtolower($validated['email']);
        $password = $validated['password'];
        $name = $validated['name'] ?? null;

        $user = User::where('email', $email)->first();

        if ($user) {
            if (!Hash::check($password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password',
                ], 401)->header('Access-Control-Allow-Origin', '*')
                         ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                         ->header('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
            }

            if (!$user->allohash) {
                do {
                    $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
                    $allohash = Hash::make($uniqueCode);
                } while (User::where('allohash', $allohash)->exists());

                $user->allohash = $allohash;
                $user->save();
            }
        } else {
            do {
                $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
                $allohash = Hash::make($uniqueCode);
            } while (User::where('allohash', $allohash)->exists());

            $user = User::create([
                'name' => $name ?? explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($password),
                'allohash' => $allohash,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'allohash' => $user->allohash,
                'created_at' => $user->created_at?->toISOString(),
            ],
        ], 200)->header('Access-Control-Allow-Origin', '*')
               ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
               ->header('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
    }
}
