<?php
// app/Mail/PlanRequestSubmittedMail.php
namespace App\Mail;

use App\Models\PlanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// (Optional) implements ShouldQueue for background sending.

class PlanRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PlanRequest $planRequest) {}

    public function build(): self
    {
        $plan   = $this->planRequest->plan;
        $user   = $this->planRequest->user;

        return $this->subject("New Plan Request: {$plan->title} by {$user->name}")
            ->markdown('emails.plan_request_submitted', [
                'plan'   => $plan,
                'user'   => $user,
                'data'   => $this->planRequest->submitted_form,
                'pr'     => $this->planRequest,
            ]);
    }
}
