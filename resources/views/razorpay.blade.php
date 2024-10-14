<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<form name='razorpayform' action="/pay/verify" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="<?php echo request()->order_id ?>">
</form>
<script>
    var options = {
        "key": "<?php echo request()->key ?>",
        "amount": "<?php echo request()->amount ?>",
        "currency": "INR",
        "name": "YOUR COMPANY NAME",
        "description": "YOUR COMPANY DESCRIPTION",
        "image": "YOUR COMPANY IMAGE",
        "order_id": "<?php echo request()->order_id ?>",
        "handler": function(response) {
            alert(response.razorpay_payment_id);
            alert(response.razorpay_order_id);
            alert(response.razorpay_signature)
        },
        "theme": {
            "color": "#F37254"
        }
    };
    options.handler = function(response) {
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.razorpayform.submit();
    };
    options.theme.image_padding = false;
    options.modal = {
        ondismiss: function() {
            window.location.href = "/pay?payment=false"
        },
        escape: true,
        backdropclose: false
    };
    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function(response) {
        alert(response.error.code);
        alert(response.error.description);
        alert(response.error.source);
        alert(response.error.step);
        alert(response.error.reason);
        alert(response.error.metadata.order_id);
        alert(response.error.metadata.payment_id);
    });
    rzp1.open();
</script>