<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Mail\PlanRequestApprovedMail;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PlanRequestController extends Controller
{
    protected function companyId(): int
    {
        // Adapt if your auth user resolves company differently
        return (int) Auth::id();
    }

    // 1) List all plan requests for this company (optional status filter)
    public function index(Request $request): JsonResponse
    {
        $status  = $request->query('status'); // pending|approved|rejected (optional)

        $q = PlanRequest::with(['user','plan'])
            ->whereHas('plan', fn($p) => $p->where('company_id', $this->companyId()))
            ->latest();

        if ($status) {
            $q->where('status', $status);
        }

        return response()->json($q->get());
    }

    // 2) List requests for a specific plan (must belong to this company)
    public function byPlan(Request $request, Plan $plan): JsonResponse
    {
        abort_if($plan->company_id !== $this->companyId(), 403);

        $status  = $request->query('status');

        $q = PlanRequest::with(['user','plan'])
            ->where('plan_id', $plan->id)
            ->latest();

        if ($status) {
            $q->where('status', $status);
        }

        return response()->json($q->get());
    }

    // Accept -> set status=approved and email the user
    public function accept(Request $request, PlanRequest $planRequest): JsonResponse
    {
        abort_if($planRequest->plan->company_id !== $this->companyId(), 403);

        if ($planRequest->status === PlanRequest::STATUS_APPROVED) {
            return response()->json(['status' => 'ok', 'message' => 'Already approved.']);
        }

        DB::transaction(function () use ($planRequest) {
            $planRequest->update(['status' => PlanRequest::STATUS_APPROVED]);
        });

        if ($planRequest->user?->email) {
            Mail::to($planRequest->user->email)
                ->send(new PlanRequestApprovedMail($planRequest));
        }
        $companyName = $planRequest->plan->company->name ?? 'the company';
        $title = 'Your plan request was approved';
        $body  = "Your request for \"{$planRequest->plan->title}\" was approved by {$companyName}.";

        FirebaseNotificationService::sendNotification($title , $body, $planRequest->user->fcm_token) ;
        return response()->json(['status' => 'ok', 'message' => 'Request approved.']);
    }

    // Reject -> set status=rejected (no extra columns, email optional)
    public function reject(Request $request, PlanRequest $planRequest): JsonResponse
    {
        abort_if($planRequest->plan->company_id !== $this->companyId(), 403);

        if ($planRequest->status === PlanRequest::STATUS_REJECTED) {
            return response()->json(['status' => 'ok', 'message' => 'Already rejected.']);
        }

        DB::transaction(function () use ($planRequest) {
            $planRequest->update(['status' => PlanRequest::STATUS_REJECTED]);
        });

        return response()->json(['status' => 'ok', 'message' => 'Request rejected.']);
    }
}
