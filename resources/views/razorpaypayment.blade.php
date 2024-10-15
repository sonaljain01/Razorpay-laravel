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
            <button type="submit">Pay Now</button>
        </form>

        <div id="status-message" style="margin-top: 20px; color: green;"></div>
    </div>

    <script>
        document.getElementById('payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const amount = document.getElementById('amount').value;

            try {
                // Create the Razorpay order
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

                // Launch Razorpay checkout
                const options = {
                    key: key,
                    amount: razorpayAmount,
                    currency: "INR",
                    name: "BindassPAY",
                    description: "Test Transaction",
                    order_id: order_id,
                    handler: async function(response) {
                        const verifyResponse = await axios.post('{{ route('payment.verify') }}', {
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature
                        });

                        document.getElementById('status-message').innerText =
                            "Payment Successful!";
                    },
                    prefill: {
                        name: name,
                        email: email
                    },
                    theme: {
                        color: "#3399cc"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();

                rzp.on('payment.failed', function(response) {
                    alert('Payment Failed: ' + response.error.description);
                });
            } catch (error) {
                console.error('Error creating order:', error);
                alert('Failed to create Razorpay order.');
            }
        });

        window.onbeforeunload = function(e) {
            if (paymentInProgress) {
                const message = "A payment is in progress. Do you really want to cancel it?";
                e.returnValue = message; // Some browsers require this for compatibility
                return message;
            }
        };

        window.addEventListener('popstate', function () {
            if (paymentInProgress && confirm("Cancel payment?")) {
                axios.post('{{ route('payment.cancel') }}', { message: 'Payment cancelled' })
                    .then(() => alert('Payment cancelled.'))
                    .catch(() => alert('Error cancelling payment.'));
                paymentInProgress = false;
            } else {
                history.pushState(null, null, window.location.href);
            }
        });

        // Initialize pushState to track the page
        history.pushState(null, null, window.location.href)
    </script>
</body>

</html>
