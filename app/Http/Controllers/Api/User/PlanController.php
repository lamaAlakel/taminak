<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $plans = Plan::with(['categories', 'company'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($plans);
    }

    /**
     * GET /api/companies/{company}/plans
     * Paginated list of this company's plans.
     */
    public function plansByCompany(Request $request, Company $company)
    {
        $perPage = $request->query('per_page', 10);

        $plans = $company->plans()
            ->with('categories')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($plans);
    }

    /**
     * GET /api/categories/{category}/plans
     * Paginated list of plans in this category.
     */
    public function plansByCategory(Request $request, Category $category)
    {
        $perPage = $request->query('per_page', 10);

        $plans = $category->plans()
            ->with('company')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($plans);
    }
}
