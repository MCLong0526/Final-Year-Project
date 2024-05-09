<!DOCTYPE html>
<html>
<head>
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
            margin-bottom: 20px;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verification Code</h1>
        <p>Your verification code is: <strong>{{ $verificationCode }}</strong></p>
        <p>Please use this verification code to verify your email address.</p>
        <p>If you did not request this verification code, please ignore this email.</p>
        <p>Thank you!</p>
    </div>
</body>
</html>
