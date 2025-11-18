<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
