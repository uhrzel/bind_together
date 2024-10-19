<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
</head>
<body>
    <h1>Verify Your Email Address</h1>
    <p>Hello {{ $name }},</p>
    <p>Please click the button below to verify your email address:</p>
    <a href="{{ $verificationUrl }}" style="padding: 10px 15px; background-color: #3498db; color: white; text-decoration: none;">
        Verify Email Address
    </a>
    <p>If you did not create an account, no further action is required.</p>
</body>
</html>
