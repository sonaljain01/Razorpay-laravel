<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BindassPAY</title>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div style="width: 100%; max-width: 400px; margin: 50px auto;">
        <h2>BindassPAY</h2>
        <form id="payment-form">
            <div>
                <label>Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label>Amount (INR):</label>
                <input type="number" name="amount" id="amount" required min="1">
            </div>
            <button type="submit">Create Order</button>
        </form>

        <div id="status-message" style="margin-top: 20px; color: green;"></div>
    </div>

    <script>
        let paymentInProgress = false; 

        document.getElementById('payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const amount = document.getElementById('amount').value;

            try {

                const response = await axios.post('{{ route('payment.create') }}', {
                    name: name,
                    email: email,
                    amount: amount,
                });

                const {
                    order_id,
                    key,
                    amount: razorpayAmount
                } = response.data;


                window.location.href = "{{ route('payment.confirm') }}?name=" + name + "&amount=" + amount +
                    "&order_id=" + order_id;

            } catch (error) {
                console.error('Error creating order:', error);
                alert('Failed to create Razorpay order.');
            }
        });

        window.onbeforeunload = function(e) {
            if (paymentInProgress) {
                const message = "A payment is in progress. Do you really want to cancel it?";
                e.returnValue = message; 
                return message;
            }
        };

        window.addEventListener('popstate', function() {
            if (paymentInProgress && confirm("Cancel payment?")) {
                axios.post('{{ route('payment.cancel') }}', {
                        message: 'Payment cancelled'
                    })
                    .then(() => {
                        alert('Payment cancelled.');
                        window.location.href = "{{ route('home') }}"; // Navigate to home page
                    })
                    .catch(() => alert('Error cancelling payment.'));
                paymentInProgress = false;
            } else {
                history.pushState(null, null, window.location.href);
            }
        });
    </script>
</body>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        background: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    div {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    form div {
        margin-bottom: 15px;
        text-align: left;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input:focus {
        border-color: #4CAF50;
        outline: none;
    }


    button {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        font-size: 18px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    button:hover {
        background-color: #45a049;
    }

</style>

</html>
