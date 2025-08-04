<?php
// File: app/Http/Controllers/Company/ServiceProviderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    /**
     * GET /api/company/service-providers
     * List all providers this company is contracted with.
     */
    public function index(Request $request)
    {
        $providers = $request->user()
            ->serviceProviders()
            ->get();

        return response()->json($providers);
    }

    /**
     * POST /api/company/service-providers
     * Create a new provider, then contract it.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'bio'            => 'nullable|string',
            'image'          => 'nullable|file',
            'contact_number' => 'required|string',
            'location'       => 'required|string',
        ]);

        if ($request->hasFile('image'))
        {
            $image = $this->storefile($request->file('image') , 'image/serviceProvider') ;
            $data['image'] = $image ;
        }

        // 1) Create provider record
        $provider = ServiceProvider::create($data);

        // 2) Attach via pivot
        $company = $request->user();

        $company
        ->serviceProviders()
        ->attach($provider->id);

        return response()->json($provider, 201);
    }

    /**
     * GET /api/company/service-providers/{id}
     * Show only if contracted.
     */
    public function show(Request $request, $id)
    {
        $provider = $request->user()
            ->serviceProviders()
            ->findOrFail($id);

        return response()->json($provider);
    }

    /**
     * PUT/PATCH /api/company/service-providers/{id}
     * Update provider’s details (only if contracted).
     */
    public function update(Request $request, $id)
    {
        $provider = $request->user()
            ->serviceProviders()
            ->findOrFail($id);

        $data = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'bio'            => 'nullable|string',
            'image'          => 'nullable|file',
            'contact_number' => 'sometimes|required|string',
            'location'       => 'sometimes|required|string',
        ]);

        if ($request->hasFile('image'))
        {
            $image = $this->storefile($request->file('image') , 'image/serviceProvider') ;
            $data['image'] = $image ;
        }

        $provider->update($data);

        return response()->json($provider);
    }

    /**
     * DELETE /api/company/service-providers/{id}
     * Just detaches the contract (does NOT delete the provider globally).
     */
    public function destroy(Request $request, $id)
    {
        // ensures it exists or throws 404
        $request->user()
            ->serviceProviders()
            ->findOrFail($id);

        // remove contract
        $request->user()
            ->serviceProviders()
            ->detach($id);

        return response()->json(null, 204);
    }
}
