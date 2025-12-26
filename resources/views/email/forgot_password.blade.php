Hi {{ $user->full_name }},<br>

Your password reset code is: <b>{{ $passwordResetCode->code }}</b><br>

This code will expire at: <b>{{ $passwordResetCode->expires_at }}</b><br>

If you didn't request this, you can simply ignore this email.<br>

Regards,<br>
<b>{{ config('app.name') }}</b>
