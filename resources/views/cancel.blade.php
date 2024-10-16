<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancelled</title>
</head>

<body>
    <div style="text-align: center; margin-top: 50px;">
        <h2>Order Cancelled</h2>
        <p>Your order has been successfully cancelled.</p>
        <a href="{{ route('home') }}">Back to Home</a>
    </div>
</body>

<style>
    body {
        background-color: #f5f5f5;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    div {
        background-color: #fff;
        padding: 50px 80px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #333;
        font-size: 28px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    p {
        font-size: 18px;
        margin: 10px 0;
        color: #666;
        margin-bottom: 30px;
    }

    a {
        padding: 12px 20px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 18px;
        transition: background-color 0.3s ease;
        margin-top: 200px;
    }

    a:hover {
        background-color: #45a049;
    }
</style>

</html>
