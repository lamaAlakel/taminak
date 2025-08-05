<?php
// app/Http/Controllers/Api/RateController.php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RateController extends Controller
{
    /**
     * GET /api/rates
     * List ALL ratings by the authenticated user.
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->user()
                ->rates()
                ->with('user')
                ->get()
        );
    }
}
