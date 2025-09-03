<!doctype html>
<html><body style="font-family:Arial,Helvetica,sans-serif;background:#f6f8fa;margin:0;padding:24px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr><td align="center">
            <table role="presentation" width="100%" style="max-width:640px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
                <tr><td style="background:#0B66F0;color:#fff;padding:22px 24px;font-size:18px;font-weight:700;">
                        Plan Request Approved
                    </td></tr>
                <tr><td style="padding:18px 24px;font-size:14px;color:#111827;line-height:1.6;">
                        Hi {{ $user->name }},<br><br>
                        Your request for <strong>{{ $plan->title }}</strong> at <strong>{{ $company->name ?? 'our company' }}</strong> has been <strong>approved</strong>.
                    </td></tr>
                <tr><td align="center" style="padding:8px 24px 24px 24px;">
                        <a href="{{ $portalUrl }}" target="_blank" style="display:inline-block;background:#0B66F0;color:#fff;text-decoration:none;font-weight:600;padding:12px 18px;border-radius:8px;">Open Portal</a>
                    </td></tr>
            </table>
        </td></tr>
</table>
</body></html>
