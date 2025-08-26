{{-- resources/views/emails/plan_request_submitted_text.blade.php --}}
New Plan Request

Plan: {{ $plan->title }}
Company: {{ $company->name ?? '-' }}
Submitted by: {{ $user->name }} @if($user->email) ({{ $user->email }}) @endif
Submitted at: {{ $submittedAt?->toDayDateTimeString() }}

Form Data:
@foreach($data as $key => $value)
    - {{ ucwords(str_replace('_',' ', (string)$key)) }}: {{ is_scalar($value) || is_null($value) ? ($value ?? '—') : json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}
@endforeach

Open: {{ $manageUrl }}
