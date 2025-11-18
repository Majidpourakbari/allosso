@php
    $minutes = $ttl->totalMinutes;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your AlloSSO Security Code</title>
</head>
<body style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #0f172a; color: #e2e8f0; margin: 0; padding: 32px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background: linear-gradient(140deg, #1f284b 0%, #445c88 60%, #1f284b 100%); border-radius: 24px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.08);">
        <tr>
            <td style="padding: 40px 48px; text-align: center;">
                <h1 style="margin: 0; font-size: 22px; letter-spacing: 0.12em; text-transform: uppercase; color: #f8fafc;">Allo<span style="color:#ff7a00;">SSO</span></h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 48px 36px;">
                <p style="margin: 0 0 16px; font-size: 16px; color: rgba(226, 232, 255, 0.85);">Hello {{ $user->name }},</p>
                <p style="margin: 0 0 24px; line-height: 1.6;">Use the following one-time code to continue signing in to AlloSSO. The code expires in {{ $minutes }} minutes.</p>
                <div style="display:inline-block; padding: 18px 26px; font-size: 28px; letter-spacing: 0.4em; font-weight: 700; color: #0f172a; background: linear-gradient(120deg,#ff7a00,#ff9b32); border-radius: 16px;">
                    {{ implode(' ', str_split($code)) }}
                </div>
                <p style="margin: 32px 0 0; line-height: 1.6; color: rgba(226, 232, 255, 0.7);">
                    If you did not request this code, you can safely ignore this email.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 28px 48px; background: rgba(15, 23, 42, 0.82); text-align: center; color: rgba(148, 163, 184, 0.9); font-size: 13px;">
                Secure access, simplified.<br>© {{ date('Y') }} AlloSSO Platform.
            </td>
        </tr>
    </table>
</body>
</html>
