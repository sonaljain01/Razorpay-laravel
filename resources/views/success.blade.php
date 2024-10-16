<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>

<body>
    <h2>Payment Successful!</h2>
    <p>Your payment has been received, and your order is confirmed.</p>
    <a href="{{ route('home') }}">Back to Home</a>
</body>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f0f4f8;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }


    h2 {
        color: #4CAF50;
        font-size: 28px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    p {
        font-size: 18px;
        margin: 10px 0 20px;
        color: #555;
    }

    a {
        text-decoration: none;
        background-color: #4CAF50;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
</style>

</html>
