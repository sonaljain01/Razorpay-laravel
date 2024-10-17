<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>

<body>
    <div id="order-details" style="text-align: center; margin-top: 50px;">
        <h2>Order Confirmed!</h2>
        <p><strong>Name:</strong> {{ request('name') }}</p>
        <p><strong>Amount:</strong> ₹{{ request('amount') }}</p>

        <p><strong>Payment Status:</strong> 
            <span id="payment-status" class="status pending">
                {{ $payment_status ?? 'Pending' }}
            </span>
        </p>

        <!-- Cancel Order Button -->
        <button id="cancel-order" style="margin-top: 10px; display: none;">Cancel Order</button>
    </div>

    <!-- Warning Modal -->
    <div id="warning-modal" class="modal hidden">
        <div class="modal-content">
            <h3>Cancel Order</h3>
            <p>Do you wish to cancel the order?</p>
            <div class="modal-actions">
                <button id="confirm-cancel" class="confirm-btn">Yes, Cancel</button>
                <button id="close-modal" class="cancel-btn">No, Keep Order</button>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const orderId = new URLSearchParams(window.location.search).get('order_id');
        let paymentInProgress = true;
        let rzp;

        async function openRazorpay() {
            try {
                const response = await axios.post('{{ route('payment.init') }}', { order_id: orderId });
                const { key, razorpayAmount } = response.data;

                const options = {
                    key: key,
                    amount: razorpayAmount,
                    currency: "INR",
                    name: "BindassPAY",
                    description: "Test Transaction",
                    order_id: orderId,
                    handler: async function (response) {
                        paymentInProgress = false;
                        await axios.post('{{ route('payment.verify') }}', {
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature
                        });

                        updatePaymentStatus('Success', 'success');
                        window.location.href = "{{ route('payment.success') }}";
                    },
                    modal: {
                        backdropclose: false,
                        ondismiss: function () {
                            paymentInProgress = false;
                            document.getElementById('cancel-order').style.display = 'block';
                        }
                    }
                };

                rzp = new Razorpay(options);
                rzp.open();
            } catch (error) {
                console.error('Error initiating payment:', error);
                alert('Failed to initiate payment. Please try again.');
            }
        }

        openRazorpay();

        document.getElementById('cancel-order').addEventListener('click', function () {
            openWarningModal();
        });

        document.getElementById('confirm-cancel').addEventListener('click', async function () {
            closeWarningModal();
            try {
                const response = await axios.post('{{ route('payment.cancel') }}', {
                    order_id: orderId,
                    message: 'Order cancelled by user'
                });

                if (response.status === 200) {
                    updatePaymentStatus('Cancelled', 'danger');
                    window.location.href = "{{ route('payment.cancel.success') }}";
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('Failed to cancel the order. Please try again.');
            }
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            closeWarningModal();
            hideCancelOrderButton();
            rzp.open();
        });

        function updatePaymentStatus(status, statusClass) {
            const paymentStatus = document.getElementById('payment-status');
            paymentStatus.innerText = status;
            paymentStatus.className = `status ${statusClass}`; // Apply the color-coded class
        }

        function openWarningModal() {
            document.getElementById('warning-modal').classList.remove('hidden');
        }

        function closeWarningModal() {
            document.getElementById('warning-modal').classList.add('hidden');
        }

        function hideCancelOrderButton() {
            document.getElementById('cancel-order').style.display = 'none';
        }

        window.onbeforeunload = function (e) {
            if (paymentInProgress) {
                const message = "A payment is in progress. Do you really want to leave?";
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
        overflow: hidden;
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
        margin-top: 20px;
        transition: background-color 0.3s ease;
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

    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    .status.info {
        background-color: #2196F3;
        color: white;
    }

    .status.success {
        background-color: #4CAF50;
        color: white;
    }

    .status.pending {
        background-color: #FFC107;
        color: black;
    }

    .status.danger {
        background-color: #f44336;
        color: white;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal.hidden {
        display: none;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        width: 90%;
        max-width: 400px;
    }
</style>

</html>
