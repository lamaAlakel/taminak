<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\PlanRequestSubmittedMail;
use App\Models\Plan;
use App\Models\PlanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PlanRequestController extends Controller
{
    public function store(Request $request, Plan $plan): JsonResponse
    {
        $user = Auth::user();
        abort_if(!$user, 401, 'Unauthenticated.');

        $schema = $plan->form ?? []; // e.g. ['name'=>'string','email'=>'email','age'=>'numeric', ...]
        if (!is_array($schema) || empty($schema)) {
            abort(422, 'Plan form schema is not defined.');
        }

        // Expect a JSON object of fields in submitted_form
        $submitted = $request->input('submitted_form');
        if (!is_array($submitted)) {
            abort(422, 'submitted_form must be an object/associative array.');
        }

        // Build dynamic rules from schema
        $rules = [];
        foreach ($schema as $field => $type) {
            $type = strtolower((string)$type);

            $fieldRules = ['required'];
            $fieldRules[] = match ($type) {
                'email'   => 'email',
                'numeric' => 'numeric',
                'integer' => 'integer',
                'date'    => 'date',
                default   => 'string', // 'string' by default
            };

            // Example size limits (optional)
            if (in_array('string', $fieldRules, true)) {
                $fieldRules[] = 'max:255';
            }

            $rules["submitted_form.$field"] = $fieldRules;
        }

        // Validate and keep only allowed keys
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        $filtered = Arr::only($submitted, array_keys($schema));

        // Persist + email atomically (email after commit)
        $planRequest = DB::transaction(function () use ($user, $plan, $filtered) {
            return PlanRequest::create([
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'submitted_form' => $filtered,
                'status'         => 'pending'
            ]);
        });

        // Send email to company (if email exists)
        $companyEmail = $plan->company?->email;
        if ($companyEmail) {
            Mail::to($companyEmail)->send(new PlanRequestSubmittedMail($planRequest));
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Plan request submitted successfully.',
            'data' => [
                'plan_request_id' => $planRequest->id,
            ],
        ], 201);
    }
    public function myRequests(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId  = Auth::id();
        $status  = $request->query('status'); // optional: pending|approved|rejected

        $q = PlanRequest::with(['plan.company'])
            ->where('user_id', $userId)
            ->latest();

        if ($status) {
            $q->where('status', $status);
        }

        return response()->json($q->get());
    }
}
