<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanRequest;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data'   => [
                'user_count'          => User::count(),
                'plan_request_count'  => PlanRequest::count(),
            ],
        ]);
    }
}
