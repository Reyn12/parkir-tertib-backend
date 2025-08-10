<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Fix: Manual parse JSON kalau request->all() kosong
        $data = $request->all();
        if (empty($data) && $request->header('Content-Type') === 'application/json') {
            $data = json_decode($request->getContent(), true) ?: [];
        }
        
        // Debug log lebih detail
        Log::info('Register debug:', [
            'request_all' => $request->all(),
            'raw_content' => $request->getContent(),
            'content_type' => $request->header('Content-Type'),
            'parsed_data' => $data,
            'json_decode_result' => json_decode($request->getContent(), true),
            'json_last_error' => json_last_error_msg(),
            'is_json' => $request->isJson(),
            'wants_json' => $request->wantsJson(),
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);
        
        // Map field names dari Flutter ke Laravel
        if (isset($data['name']) && !isset($data['username'])) {
            $data['username'] = $data['name'];
        }
        if (isset($data['phone']) && !isset($data['phone_number'])) {
            $data['phone_number'] = $data['phone'];
        }
        
        $validator = Validator::make($data, [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Register validation failed:', [
                'data_validated' => $data,
                'validation_errors' => $validator->errors()->toArray(),
                'rules' => $validator->getRules()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'profile_picture' => $data['profile_picture'] ?? null,
        ]);

        $token = Str::random(64);
        
        ApiToken::create([
            'user_id' => $user->user_id,
            'token' => $token,
            'name' => 'auth_token',
            'expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = Str::random(64);
        
        ApiToken::create([
            'user_id' => $user->user_id,
            'token' => $token,
            'name' => 'auth_token',
            'expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token) {
            ApiToken::where('token', $token)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                ]
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'sometimes|required|string|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
            'phone_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'username', 'email', 'phone_number', 'profile_picture'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                ]
            ]
        ]);
    }

    public function showUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'profile_picture' => $user->profile_picture,
                    'created_at' => $user->created_at,
                ]
            ]
        ]);
    }
}
