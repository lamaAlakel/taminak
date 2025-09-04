<?php
// File: app/Http/Controllers/Api/User/UserAuthController.php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    /**
     * Register a new user and send verification email (no token until verified)
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            // email must be unique among VERIFIED users; allows reuse if an unverified record exists
            'email'        => ['required','email', Rule::unique('users', 'email')->whereNotNull('email_verified_at')],
            'password'     => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        // If an unverified account with this email exists, update its info instead of failing
        $existing = User::where('email', $data['email'])->first();

        if ($existing && ! $existing->hasVerifiedEmail()) {
            $existing->fill([
                'name'         => $data['name'],
                'password'     => $data['password'],
                'phone_number' => $data['phone_number'] ?? $existing->phone_number,
            ])->save();

            $existing->notify(new VerifyEmailNotification());

            return response()->json([
                'message' => 'Account info updated. Verification email resent.',
            ], 200);
        }

        // Otherwise create a fresh account
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'],
            'phone_number' => $data['phone_number'] ?? null,
        ]);

        $user->notify(new VerifyEmailNotification());

        return response()->json([
            'message' => 'Registered successfully. Verification email sent.',
        ], 201);
    }


    /**
     * Verify email from signed URL
     * Route: GET /auth/verify/{id}/{hash}
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 422);
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification signature.'], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }

    /**
     * Resend verification email (by email or for the authenticated user)
     * Unauthenticated: provide { email }
     * Authenticated: email is optional; current user will be used
     */
    public function resendVerification(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
        ]);

        $user = $request->user();

        if (! $user && isset($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        }

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.'], 422);
        }

        $user->notify(new VerifyEmailNotification());

        return response()->json(['message' => 'Verification email resent.'], 200);
    }

    /**
     * Login user and issue token (requires verified email)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->blocked) {
            return response()->json([
                'message' => 'Your account has been blocked. Please contact support.'
            ], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email not verified. Please verify your email.',
            ], 403);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user (delete current token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    /**
     * Get authenticated user profile (requires verified email)
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update user profile (requires verified email)
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $data = $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'email'        => "sometimes|required|email|unique:users,email,{$user->id}",
            'password'     => 'sometimes|required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user, 200);
    }
}
