<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterBorrowerRequest;
use App\Http\Resources\UserResource;
use App\Models\LoginSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        if (($user->isOfficer() || $user->isOwner()) && ! $user->is_active) {
            throw ValidationException::withMessages(['email' => 'This account is not active.']);
        }

        if ($user->isOfficer() || $user->isOwner()) {
            $user->tokens()->delete();
        }

        $token = $user->createToken(
            $request->device_name ?? 'default',
            ['role:'.$user->role],
        )->plainTextToken;

        LoginSession::create([
            'user_id' => $user->id,
            'token' => hash('sha256', explode('|', $token, 2)[1] ?? $token),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function register(RegisterBorrowerRequest $request)
    {
        $borrower = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'borrower',
        ]);

        $token = $borrower->createToken($request->device_name ?? 'default')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($borrower),
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
