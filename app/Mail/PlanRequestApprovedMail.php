<?php

namespace App\Mail;

use App\Models\PlanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PlanRequest $planRequest, public ?string $portalUrl = null) {}

    public function build(): self
    {
        $plan = $this->planRequest->plan;
        $user = $this->planRequest->user;

        return $this->subject('Approved · ' . $plan->title)
            ->view('emails.plan_request_approved_html')
            ->text('emails.plan_request_approved_text')
            ->with([
                'plan'      => $plan,
                'company'   => $plan->company,
                'user'      => $user,
                'appName'   => config('app.name'),
                'portalUrl' => $this->portalUrl ?? url('/'),
            ]);
    }
}
