
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h1>Razorpay Payment</h1>

    <form id="paymentForm" method="POST" action="{{ url('pay/verify') }}">
        @csrf
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">

        <button type="button" id="payBtn">Pay Now</button>
    </form>

    <script>
        document.getElementById('payBtn').onclick = function(e) {
            e.preventDefault();

            // This AJAX request will send the order details and get the Razorpay order ID
            fetch("{{ url('api/user/payment/create-order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: 'John Doe', // Get actual user details dynamically
                    email: 'john@example.com',
                    amount: 1000 // Amount in rupees
                })
            })
            .then(response => response.json())
            .then(data => {
                // Initialize Razorpay with the order ID and other data
                var options = {
                    "key": "{{ env('RAZORPAY_KEY') }}", // Your Razorpay Key
                    "amount": data.amount * 100, // Amount in paise
                    "currency": "INR",
                    "name": data.name,
                    "description": "Test Transaction",
                    "order_id": data.order_id, // Order ID from Razorpay
                    "handler": function (response){
                        // Set the payment details in the form fields
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                        document.getElementById('razorpay_signature').value = response.razorpay_signature;
                        
                        // Submit the form to verify the payment
                        document.getElementById('paymentForm').submit();
                    },
                    "prefill": {
                        "name": data.name,
                        "email": data.email
                    },
                    "theme": {
                        "color": "#F37254"
                    }
                };

                var rzp1 = new Razorpay(options);
                rzp1.open();
            })
            .catch(error => console.error('Error:', error));
        };
    </script>
</body>
</html>
