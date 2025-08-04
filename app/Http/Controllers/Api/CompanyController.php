<?php
// File: app/Http/Controllers/Admin/CompanyController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // GET /api/admin/companies
    public function index()
    {
        return response()->json(Company::all());
    }

    // POST /api/admin/companies
    public function store(Request $request)
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
        return response()->json($company, 201);
    }

    // GET /api/admin/companies/{company}
    public function show(Company $company)
    {
        return response()->json($company);
    }

    // PUT/PATCH /api/admin/companies/{company}
    public function update(Request $request, Company $company)
    {
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

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        if ($request->hasFile('image'))
        {
            $image = $this->storefile($request->file('image') , 'image/company') ;
            $data['image'] = $image ;
        }

        $company->update($data);
        return response()->json($company, 200);
    }

    // DELETE /api/admin/companies/{company}
    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(null, 204);
    }
}
