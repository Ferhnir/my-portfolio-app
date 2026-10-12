<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password</title>
</head>

<body style="margin:0; padding:0; background:#f4f4f5; font-family:Arial,Helvetica,sans-serif; color:#18181b;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f4f5; padding:40px 20px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="padding:24px; border-bottom:1px solid #e4e4e7;">
                        <strong style="font-size:20px;">
                            {{ config('app.name') }}
                        </strong>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px;">

                        <h1 style="margin:0 0 16px; font-size:24px; color:#18181b;">
                            Reset your password
                        </h1>

                        <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">
                            Hello {{ $user->name ?? 'there' }},
                        </p>

                        <p style="margin:0 0 24px; font-size:16px; line-height:1.6; color:#52525b;">
                            We received a request to reset the password for your account.
                            Click the button below to choose a new password.
                        </p>

                        <!-- Button -->
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="border-radius:6px; background:#18181b;">
                                    <a href="{{ $resetUrl }}"
                                       style="display:inline-block; padding:12px 20px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold;">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:16px 0 0; font-size:14px; line-height:1.6; color:#71717a;">
                            If you didn't request a password reset, you can safely ignore
                            this email. Your password will remain unchanged.
                        </p>

                        <!-- Fallback URL -->
                        <p style="margin:24px 0 0; font-size:13px; line-height:1.6; color:#a1a1aa;">
                            If the button doesn't work, copy and paste this URL into your
                            browser:
                        </p>

                        <p style="margin:8px 0 0; font-size:13px; line-height:1.6; word-break:break-all;">
                            <a href="{{ $resetUrl }}" style="color:#18181b;">
                                {{ $resetUrl }}
                            </a>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:20px 24px; background:#fafafa; text-align:center;">
                        <p style="margin:0; font-size:13px; color:#71717a;">
                            © {{ date('Y') }} {{ config('app.name') }}.
                            All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>