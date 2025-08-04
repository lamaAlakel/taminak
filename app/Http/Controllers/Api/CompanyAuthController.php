<?php
// File: app/Http/Controllers/Api/CompanyAuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\FileStorageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyAuthController extends Controller
{
    use FileStorageTrait ;
    /** Register new company */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:companies,email',
            'password'       => 'required|string|min:8|confirmed',
            'phone_number'   => 'required|string',
            'address'        => 'required|string',
            'license_number' => 'required|string',
            'image'          => 'nullable|file',
            'bio'            => 'nullable|string',
        ]);

        if ($request->hasFile('image'))
        {
            $image = $this->storefile($request->file('image') , 'image/company') ;
            $data['image'] = $image ;
        }

        $company = Company::create($data);
        $token = $company->createToken('company_token')->plainTextToken;

        return response()->json(['company' => $company, 'token' => $token], 201);
    }

    /** Login company and issue token */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $company = Company::where('email', $request->email)->first();

        if (! $company || ! Hash::check($request->password, $company->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $company->createToken('company_token')->plainTextToken;
        return response()->json(['company' => $company, 'token' => $token]);
    }

    /** Logout company */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    /** Get company profile */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /** Update company profile */
    public function updateProfile(Request $request)
    {
        $company = $request->user();
        $data = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'email'          => "sometimes|required|email|unique:companies,email,{$company->id}",
            'password'       => 'sometimes|required|string|min:8|confirmed',
            'phone_number'   => 'sometimes|required|string',
            'address'        => 'sometimes|required|string',
            'license_number' => 'sometimes|required|string',
            'image'          => 'nullable|file',
            'bio'            => 'nullable|string',
        ]);
        if ($request->hasFile('image'))
        {
            $image = $this->storefile($request->file('image') , 'image/company') ;
            $data['image'] = $image ;
        }

        $company->update($data);
        return response()->json($company, 200);
    }
}
