{{-- resources/views/emails/plan_request_submitted.blade.php --}}
@component('mail::message')
    # New Plan Request

    **Plan:** {{ $plan->title }}
    **Company:** {{ $plan->company->name ?? '-' }}
    **Submitted by:** {{ $user->name }} ({{ $user->email }})

    @component('mail::panel')
        Submitted At: {{ $pr->created_at->toDayDateTimeString() }}
    @endcomponent

    ## Form Data
    @component('mail::table')
        | Field | Value |
        | :---- | :---- |
        @foreach($data as $key => $value)
            | {{ str_replace('_',' ', ucfirst($key)) }} | {{ is_scalar($value) ? $value : json_encode($value) }} |
        @endforeach
    @endcomponent

    @component('mail::button', ['url' => config('app.url')])
        Open {{ config('app.name') }}
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
