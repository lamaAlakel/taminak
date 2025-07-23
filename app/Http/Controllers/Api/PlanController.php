<?php
// File: app/Http/Controllers/Company/PlanController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * List all plans belonging to the authenticated company,
     * including their categories.
     */
    public function index(Request $request)
    {
        $company = $request->user();
        $plans = $company->plans()->with('categories')->get();

        return response()->json($plans);
    }

    /**
     * Create a new plan for this company, attaching categories.
     */
    public function store(Request $request)
    {
        $company = $request->user();

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|integer|min:0',
            'form'        => 'required|array',
            'image'       => 'nullable|string',
            'categories'  => 'required|array',
            'categories.*'=> 'exists:categories,id',
        ]);

        // create plan under this company
        $plan = $company
            ->plans()
            ->create([
                'title'       => $data['title'],
                'description' => $data['description'],
                'price'       => $data['price'],
                'form'        => $data['form'],
                'image'       => $data['image'] ?? null,
            ]);

        // attach categories
        $plan->categories()->sync($data['categories']);

        // load relations and return
        return response()->json($plan->load('categories'), 201);
    }

    /**
     * Show a specific plan (only if it belongs to this company),
     * with its categories.
     */
    public function show(Request $request, Plan $plan)
    {
        $this->authorizePlan($request->user()->id, $plan);

        return response()->json($plan->load('categories'));
    }

    /**
     * Update a plan and its categories.
     */
    public function update(Request $request, Plan $plan)
    {
        $this->authorizePlan($request->user()->id, $plan);

        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price'       => 'sometimes|required|integer|min:0',
            'form'        => 'sometimes|required|array',
            'image'       => 'nullable|string',
            'categories'  => 'sometimes|required|array',
            'categories.*'=> 'exists:categories,id',
        ]);

        // update fields
        $plan->update(array_filter($data, fn($v, $k) => in_array($k, ['title','description','price','form','image']), true));

        // sync categories if provided
        if (isset($data['categories'])) {
            $plan->categories()->sync($data['categories']);
        }

        return response()->json($plan->load('categories'));
    }

    /**
     * Delete a plan.
     */
    public function destroy(Request $request, Plan $plan)
    {
        $this->authorizePlan($request->user()->id, $plan);

        $plan->delete();
        return response()->json(null, 204);
    }

    /**
     * Simple gate: ensure the plan belongs to the given company.
     */
    protected function authorizePlan(int $companyId, Plan $plan)
    {
        if ($plan->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }
    }
}
