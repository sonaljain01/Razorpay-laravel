<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>

<body>
    <div style="text-align: center; margin-top: 50px;">
        <h2>Order Confirmed!</h2>
        <p>Name: {{ request('name') }}</p>
        <p>Amount: ₹{{ request('amount') }}</p>
        <button id="pay-now">Pay Now</button>
        <button id="cancel-order" style="margin-top: 10px;">Cancel Order</button> <!-- Cancel Order Button -->
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let paymentInProgress = false;

        document.getElementById('pay-now').addEventListener('click', async function() {
            paymentInProgress = true;
            const orderId = new URLSearchParams(window.location.search).get('order_id');
            const name = "{{ request('name') }}";
            const amount = "{{ request('amount') }}";

            try {
                const response = await axios.post('{{ route('payment.init') }}', {
                    order_id: orderId
                });
                const {
                    key,
                    razorpayAmount
                } = response.data;

                const options = {
                    key: key,
                    amount: razorpayAmount,
                    currency: "INR",
                    name: "BindassPAY",
                    description: "Test Transaction",
                    order_id: orderId,
                    handler: async function(response) {
                        const verifyResponse = await axios.post('{{ route('payment.verify') }}', {
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature
                        });

                        window.location.href = "{{ route('payment.success') }}";
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();

                rzp.on('payment.failed', function(response) {
                    alert('Payment Failed: ' + response.error.description);
                    // Redirect to failure page
                    window.location.href = "{{ route('payment.failed') }}";
                });
            } catch (error) {
                console.error('Error initiating payment:', error);
                alert('Failed to initiate payment. Please try again.');
            }
        });

        document.getElementById('cancel-order').addEventListener('click', async function() {
            if (confirm("Are you sure you want to cancel the order?")) {
                try {
                    const orderId = new URLSearchParams(window.location.search).get('order_id');
                    const response = await axios.post('{{ route('payment.cancel') }}', {
                        order_id: orderId,
                        message: 'Order cancelled by user'
                    });

                    if (response.status === 200) {
                        
                        window.location.href = "{{ route('payment.cancel.success') }}"; // Redirect to home page
                    }
                } catch (error) {
                    console.error('Error cancelling order:', error);
                    alert('Failed to cancel the order. Please try again.');
                }
            }
        });

        window.onbeforeunload = function(e) {
            if (paymentInProgress) {
                const message = "A payment is in progress. Do you really want to cancel it?";
                e.returnValue = message; 
                return message;
            }
        };

    </script>
</body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f5f5f5;
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
        color: #333;
        font-size: 28px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    p {
        font-size: 18px;
        margin: 10px 0;
        color: #666;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 20px;
    }

    button:hover {
        background-color: #45a049;
    }

    #cancel-order {
        background-color: #f44336;

    }

    #cancel-order:hover {
        background-color: #d32f2f;

    }
</style>

</html>
