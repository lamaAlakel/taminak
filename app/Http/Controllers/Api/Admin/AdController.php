<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index()
    {
        return AdResource::collection(Ad::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|string',
            'description' => 'required|string',
        ]);

        $ad = Ad::create($data);

        return new AdResource($ad);
    }

    public function show(Ad $ad)
    {
        return new AdResource($ad);
    }

    public function update(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'image' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
        ]);

        $ad->update($data);

        return new AdResource($ad);
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();

        return response()->noContent();
    }
}
