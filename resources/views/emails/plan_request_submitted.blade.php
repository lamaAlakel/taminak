{{-- resources/views/emails/plan_request_submitted_html.blade.php --}}
@php
    $brandBg    = '#0B66F0';   // primary accent (safe blue)
    $bg         = '#F3F4F6';   // page background
    $cardBg     = '#FFFFFF';   // card background
    $text       = '#111827';   // main text
    $muted      = '#6B7280';   // secondary text
    $border     = '#E5E7EB';   // borders
    $app        = $appName ?? config('app.name');
    $companyName= $company->name ?? '—';
    $submitted  = $submittedAt?->format('D, M j, Y g:i A');
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Plan Request</title>
</head>
<body style="margin:0;padding:0;background:{{ $bg }};font-family:Arial,Helvetica,sans-serif;color:{{ $text }};">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:{{ $bg }};padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;">
                <!-- Header -->
                <tr>
                    <td style="padding:12px 24px;" align="center">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;">
                            <tr>
                                <td align="left" style="font-size:18px;font-weight:700;color:{{ $text }};">
                                    {{ $app }}
                                </td>
                                <td align="right" style="font-size:12px;color:{{ $muted }};">
                                    {{ $submitted }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Card -->
                <tr>
                    <td style="padding:0 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:{{ $cardBg }};border:1px solid {{ $border }};border-radius:12px;overflow:hidden;">
                            <!-- Hero -->
                            <tr>
                                <td style="background:{{ $brandBg }};padding:24px;">
                                    <h1 style="margin:0;font-size:22px;line-height:1.2;color:#fff;">New Plan Request</h1>
                                    <p style="margin:8px 0 0 0;font-size:13px;line-height:1.5;color:#E5F0FF;">
                                        A new request was submitted for <strong style="color:#fff;">{{ $plan->title }}</strong>.
                                    </p>
                                </td>
                            </tr>

                            <!-- Summary -->
                            <tr>
                                <td style="padding:20px 24px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                        <tr>
                                            <td style="padding:0 0 12px 0;font-size:14px;color:{{ $muted }};">Plan</td>
                                            <td style="padding:0 0 12px 0;font-size:14px;text-align:right;color:{{ $text }};"><strong>{{ $plan->title }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:0 0 12px 0;font-size:14px;color:{{ $muted }};">Company</td>
                                            <td style="padding:0 0 12px 0;font-size:14px;text-align:right;color:{{ $text }};">{{ $companyName }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:0 0 12px 0;font-size:14px;color:{{ $muted }};">Submitted by</td>
                                            <td style="padding:0 0 12px 0;font-size:14px;text-align:right;color:{{ $text }};">
                                                {{ $user->name }}
                                                @if($user->email)
                                                    <span style="color:{{ $muted }};"> ({{ $user->email }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Divider -->
                            <tr>
                                <td style="height:1px;background:{{ $border }};"></td>
                            </tr>

                            <!-- Form Data -->
                            <tr>
                                <td style="padding:12px 24px 8px 24px;">
                                    <h2 style="margin:0 0 8px 0;font-size:16px;color:{{ $text }};">Form Data</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 24px 20px 24px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid {{ $border }};border-radius:8px;overflow:hidden;">
                                        <tr>
                                            <th align="left" style="padding:10px 12px;font-size:12px;text-transform:uppercase;letter-spacing:.4px;border-bottom:1px solid {{ $border }};background:#F9FAFB;color:#374151;">Field</th>
                                            <th align="left" style="padding:10px 12px;font-size:12px;text-transform:uppercase;letter-spacing:.4px;border-bottom:1px solid {{ $border }};background:#F9FAFB;color:#374151;">Value</th>
                                        </tr>
                                        @foreach($data as $key => $value)
                                            @php
                                                $label = ucwords(str_replace('_',' ', (string)$key));
                                                $display = is_scalar($value) || is_null($value)
                                                      ? (string)($value ?? '—')
                                                      : json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                $rowBg = $loop->even ? '#FFFFFF' : '#F8FAFC';
                                            @endphp
                                            <tr>
                                                <td style="padding:10px 12px;font-size:14px;border-bottom:1px solid {{ $border }};background:{{ $rowBg }};vertical-align:top;">{{ $label }}</td>
                                                <td style="padding:10px 12px;font-size:14px;border-bottom:1px solid {{ $border }};background:{{ $rowBg }};white-space:pre-wrap;word-break:break-word;vertical-align:top;">
                                                    {{ $display }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>

                            <!-- CTA -->
                            <tr>
                                <td align="center" style="padding:8px 24px 28px 24px;">
                                    <a href="{{ $manageUrl }}" target="_blank"
                                       style="display:inline-block;background:{{ $brandBg }};color:#fff;text-decoration:none;
                                           font-weight:600;font-size:14px;line-height:20px;padding:12px 18px;border-radius:8px;">
                                        Open in {{ $app }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:18px 24px;color:{{ $muted }};font-size:12px;">
                        © {{ date('Y') }} {{ $app }} · <a href="{{ $appUrl }}" style="color:{{ $muted }};text-decoration:underline;" target="_blank">{{ parse_url($appUrl, PHP_URL_HOST) }}</a>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
