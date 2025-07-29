<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use App\Models\User;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        $apiToken = ApiToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah expired'
            ], 401);
        }

        $user = User::find($apiToken->user_id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 401);
        }

        // Set user ke request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
} 