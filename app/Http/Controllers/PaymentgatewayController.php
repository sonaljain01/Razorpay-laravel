<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
class PaymentgatewayController extends Controller
{
    public function createOrder(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        // Initialize Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create a Razorpay order
        $order = $api->order->create([
            'receipt' => 'order_rcptid_' . time(),
            'amount' => $request->amount * 100, // Amount in paise
            'currency' => 'INR',
        ]);

        // Save order to DB if needed

        return response()->json([
            'order_id' => $order->id,
            'name' => $request->name,
            'email' => $request->email,
            'amount' => $request->amount,
            'status' => 'pending',
            // 'data' => [
            //     "key" => Config("values.razorpayKey"),
            //     "amount" => $request->amount * 100,
            //     "order_id" => $order['id'],
            // ]
        ]);
    }

    public function verify(Request $request)
    {
        $success = true;
        $error = "Payment Failed!";

        if (empty($request->razorpay_payment_id) === false) {
            $api = new Api(Config("values.razorpayKey"), Config("values.razorpaySecret"));
            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }

        if ($success === true) {

            return redirect('/');
        } else {

            return redirect('/');
        }
    }

    public function cancelOrder(Request $request)
    {

        $orderId = $request->input('order_id');



        return response()->json(['status' => 'order_canceled']);
    }

    public function handleWebhook(Request $request)
    {

        $payload = $request->getContent();
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        $actualSignature = $request->header('X-Razorpay-Signature');

        if (!$this->verifyWebhookSignature($payload, $actualSignature, $webhookSecret)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON in payload'], 400);
        }

        $event = $data['event'] ?? null;

        // Handle different events
        if ($event === 'payment.captured') {
            // Payment was captured successfully
            $payment = $data['payload']['payment']['entity'] ?? null;

            $orderId = $payment['order_id'] ?? null;
            $amount = $payment['amount'] ?? null;


        } elseif ($event === 'payment.failed') {

            $payment = $data['payload']['payment']['entity'] ?? null;


            $orderId = $payment['order_id'] ?? null;

        } else {
            return response()->json(['error' => 'Unhandled event type'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function verifyWebhookSignature($payload, $actualSignature, $secret)
    {
            $expectedSignature = hash_hmac('sha256', $payload, $secret);

            return hash_equals($expectedSignature, $actualSignature);

        
    }


}
