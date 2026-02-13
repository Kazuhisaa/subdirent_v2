
<!DOCTYPE html>
<html>
<head>
    <style>
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #3b82f6;
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            margin: 20px 0;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', Helvetica, Arial, sans-serif; background-color: #f4f7f6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 16px; margin-top: 50px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 40px; text-align: center;">
                <div style="margin-bottom: 20px;">
                    <img src="{{ $message->embed(public_path('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png')) }}" alt="Logo" width="50" style="vertical-align: middle;">
                    <img src="{{ $message->embed(public_path('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png')) }}" alt="SubdiRent" width="120" style="vertical-align: middle;">
                </div>

                <h2 style="color: #0A2540; margin: 0;">Verify it's you</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 10px;">
                    We've received a request to reset your password. <br> Use the 6-digit code below to proceed.
                </p>

                <div class="otp-code">
                    {{ $otp }}
                </div>

                <p style="color: #94a3b8; font-size: 12px; margin-top: 30px;">
                    This code will expire shortly. If you did not request this, please ignore this email.
                </p>
                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                <p style="color: #94a3b8; font-size: 11px;">&copy; {{ date('Y') }} SubdiRent Rental Management</p>
            </td>
        </tr>
    </table>
</body>
</html>