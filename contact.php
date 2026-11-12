<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sent - QR Food Hub</title>
    <link rel="stylesheet" href="qr.css">
    <style>
        .thankyou-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
            padding: 20px;
        }
        .thankyou-box {
            background: #fff;
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 500px;
            width: 100%;
            border-top: 6px solid #FFD700;
        }
        .thankyou-box h1 {
            color: #281C02;
            margin-bottom: 15px;
            font-size: 28px;
        }
        .thankyou-box p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .thankyou-box .checkmark {
            font-size: 60px;
            color: #1a8f4a;
            margin-bottom: 20px;
        }
        .thankyou-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #281C02;
            color: #FFD700;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s ease;
        }
        .thankyou-btn:hover {
            background: #FFD700;
            color: #281C02;
        }
    </style>
</head>
<body>

<div class="thankyou-wrap">
    <div class="thankyou-box">
        <div class="checkmark">✓</div>
        <h1>Thank You!</h1>
        <p>
            Your message has been received.<br>
            We'll get back to you as soon as possible.
        </p>
        <a href="index.php" class="thankyou-btn">← Back to Home</a>
    </div>
</div>

</body>
</html>