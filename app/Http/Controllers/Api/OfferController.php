<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Plan;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * GET /api/company/offers
     * List all offers for this company.
     */
    public function index(Request $request)
    {
        $offers = $request->user()->offers()->with('plan')->get();
        return response()->json($offers);
    }

    /**
     * POST /api/company/offers
     * Create a new offer on one of this company's plans.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id'     => 'required|integer|exists:plans,id|unique:offers,plan_id',
            'percent'     => 'required|integer|min:0|max:100',
            'image'       => 'nullable|string',
            'description' => 'required|string',
        ]);

        // Ensure plan belongs to this company
        $plan = Plan::findOrFail($data['plan_id']);
        if ($plan->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized plan'], 403);
        }

        $offer = Offer::create($data);
        return response()->json($offer->load('plan'), 201);
    }

    /**
     * GET /api/company/offers/{offer}
     */
    public function show(Request $request, Offer $offer)
    {
        $this->authorizeOffer($request->user()->id, $offer);
        return response()->json($offer->load('plan'));
    }

    /**
     * PUT/PATCH /api/company/offers/{offer}
     */
    public function update(Request $request, Offer $offer)
    {
        $this->authorizeOffer($request->user()->id, $offer);

        $data = $request->validate([
            'percent'     => 'sometimes|required|integer|min:0|max:100',
            'image'       => 'nullable|string',
            'description' => 'sometimes|required|string',
        ]);

        $offer->update($data);
        return response()->json($offer->load('plan'));
    }

    /**
     * DELETE /api/company/offers/{offer}
     */
    public function destroy(Request $request, Offer $offer)
    {
        $this->authorizeOffer($request->user()->id, $offer);
        $offer->delete();
        return response()->json(null, 204);
    }

    /**
     * Ensure the given offer belongs to a plan of this company.
     */
    protected function authorizeOffer(int $companyId, Offer $offer)
    {
        if ($offer->plan->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }
    }
}

