<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Temporary Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 25px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .temp-password {
            font-size: 18px;
            background-color: #f0f0f0;
            padding: 10px;
            display: inline-block;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $name }},</h2>
        <p>We received a request to reset your password.</p>
        <p>Use the following temporary password to log in:</p>

        <div class="temp-password">{{ $tempPassword }}</div>

        <p>Once logged in, please change your password immediately for security purposes.</p>

        <div class="footer">
            <p>If you didn’t request this, you can safely ignore this email.</p>
            <p>— Support Team</p>
        </div>
    </div>
</body>
</html>
