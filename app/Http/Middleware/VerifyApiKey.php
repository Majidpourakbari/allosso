<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
        }

        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        $validApiKey = config('app.api_key');

        if (!$validApiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        return $next($request);
    }
}
