<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $offers = Offer::with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($offers);
    }
}
