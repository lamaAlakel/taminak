<?php
// app/Http/Controllers/Api/RateController.php

namespace App\Http\Controllers\Api;

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
                ->with('company')
                ->get()
        );
    }

    /**
     * POST /api/rates
     * Create a new rating (1 per user/company).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                // ensure unique per user:
                Rule::unique('rates')->where(fn($q) => $q->where('user_id', $request->user()->id))
            ],
            'rate'       => 'required|integer|min:1|max:5',
        ]);

        $rate = Rate::create([
            'user_id'    => $request->user()->id,
            'company_id' => $data['company_id'],
            'rate'       => $data['rate'],
        ]);

        return response()->json($rate->load('company'), 201);
    }

    /**
     * PUT /api/rates/{rate}
     * Update your own rating.
     */
    public function update(Request $request, Rate $rate)
    {
        // only the owner can update
        if ($rate->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'rate' => 'required|integer|min:1|max:5',
        ]);

        $rate->update(['rate' => $data['rate']]);

        return response()->json($rate->load('company'));
    }

    /**
     * DELETE /api/rates/{rate}
     * Delete your own rating.
     */
    public function destroy(Request $request, Rate $rate)
    {
        if ($rate->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $rate->delete();
        return response()->json(null, 204);
    }
}
