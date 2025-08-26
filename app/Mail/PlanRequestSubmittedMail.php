<?php
// app/Mail/PlanRequestSubmittedMail.php
namespace App\Mail;

use App\Models\PlanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue; // (optional) queue it

class PlanRequestSubmittedMail extends Mailable /* implements ShouldQueue */
{
    use Queueable, SerializesModels;

    public function __construct(
        public PlanRequest $planRequest,
        public ?string $manageUrl = null
    ) {}

    public function build(): self
    {
        $plan    = $this->planRequest->plan;
        $user    = $this->planRequest->user;
        $company = $plan->company;

        return $this->subject('New Plan Request · ' . $plan->title)
            ->view('emails.plan_request_submitted_html')
            ->text('emails.plan_request_submitted_text')
            ->with([
                'plan'       => $plan,
                'user'       => $user,
                'company'    => $company,
                'data'       => $this->planRequest->submitted_form,
                'submittedAt'=> $this->planRequest->created_at,
                'manageUrl'  => $this->manageUrl
                    ?? url("/admin/plans/{$plan->id}/requests/{$this->planRequest->id}"),
                'appName'    => config('app.name'),
                'appUrl'     => config('app.url'),
            ]);
    }
}
