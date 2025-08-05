<?php
// File: app/Http/Controllers/Api/AdminAuthController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /** Register new admin */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:admins,email',
            'password'     => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        $admin = Admin::create($data);
        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json(['admin' => $admin, 'token' => $token], 201);
    }

    /** Login admin and issue token */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;
        return response()->json(['admin' => $admin, 'token' => $token]);
    }

    /** Logout admin */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    /** Get admin profile */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /** Update admin profile */
    public function updateProfile(Request $request)
    {
        $admin = $request->user();
        $data = $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'email'        => "sometimes|required|email|unique:admins,email,{$admin->id}",
            'password'     => 'sometimes|required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        $admin->update($data);
        return response()->json($admin, 200);
    }
}
