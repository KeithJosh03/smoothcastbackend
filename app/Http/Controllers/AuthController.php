<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserProvider;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate login data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $customerRole = Role::where('role_name', 'customer')->first();
            $roleId = $customerRole ? $customerRole->id : 1;

            $user = null;

            if ($request->filled('provider_id') && $request->filled('provider_name')) {
                // SOCIAL REGISTRATION FLOW
                $provider = UserProvider::where('provider_name', $request->provider_name)
                    ->where('provider_id', $request->provider_id)
                    ->first();

                if ($provider) {
                    $user = $provider->user;
                } else {
                    $user = User::where('email', $request->email)->first();

                    if (!$user) {
                        $user = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => null,
                            'role_id' => $roleId,
                            'avatar_url' => $request->avatar_url,
                        ]);
                    }

                    UserProvider::create([
                        'user_id' => $user->id,
                        'provider_name' => $request->provider_name,
                        'provider_id' => $request->provider_id,
                    ]);
                }
            } else {
                // MANUAL REGISTRATION FLOW
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => $roleId,
                    'avatar_url' => $request->avatar_url,
                ]);
            }

            DB::commit();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
