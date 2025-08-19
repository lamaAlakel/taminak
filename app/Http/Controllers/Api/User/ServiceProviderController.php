<?php
// File: app/Http/Controllers/Company/ServiceProviderController.php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ServiceProvider;
use App\Traits\FileStorageTrait;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    use FileStorageTrait;
    /**
     * GET /api/company/service-providers
     * List all providers this company is contracted with.
     */
    public function index($company_id)
    {
        $providers = Company::with('serviceProviders')
            ->find($company_id);

        return response()->json($providers);
    }


}
