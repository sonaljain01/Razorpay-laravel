<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
</head>

<body>
    <div style="text-align: center; margin-top: 50px;">
        <h2>Payment Failed!</h2>
        <p>Your payment could not be processed. Please try again.</p>
        <p>Error: {{ session('error_message', 'Unknown error occurred.') }}</p> <!-- Show error message if available -->
        <a href="{{ route('home') }}">Go to Home</a>
    </div>
</body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f0f4f8;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }


    div {
        background-color: #fff;
        padding: 40px 20px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #e74c3c;
        font-size: 28px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    p {
        font-size: 18px;
        margin: 10px 0;
        color: #555;
    }
    a {
        display: inline-block;
        text-decoration: none;
        background-color: #3498db;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    a:hover {
        background-color: #2980b9;
    }

</style>

</html>
