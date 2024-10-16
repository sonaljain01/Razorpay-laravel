<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use App\Services\RazorpayService;
use App\Models\Order;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\OrderCancelRequest;
use Log;
use DB;
class PaymentgatewayController extends Controller
{
    public function createOrder(CreateOrderRequest $request)
    {
        // $validated = $request->validated();

        // Initialize Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create a Razorpay order
        try {
            $razorpayOrder = $api->order->create([
                'receipt' => 'order_rcptid_' . time(),
                'amount' => $request->amount * 100, // Amount in paise
                'currency' => 'INR',
            ]);

            // Save order to DB
            $order = Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'amount' => $request->amount,  // Amount in rupees
                'order_id' => $razorpayOrder['id'],
                'status' => 'pending',
            ]);
            // return view('order-confirm', $order);
            return response()->json([
                'order_id' => $razorpayOrder['id'],
                'name' => $request->name,
                'email' => $request->email,
                'amount' => $request->amount,
                'status' => 'pending',
                // Log::info('Expected Signature: ' . $expectedSignature),
                // 'payment_id' => $razorpayOrder['id'],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage()); // Log error message
            return response()->json(['error' => 'Failed to create order: ' . $e->getMessage()], 500); // Return error response
        }
    }

    private $api;

    public function __construct()
    {
        $this->api = new Api(config('values.razorpayKey'), config('values.razorpaySecret'));
    }
    public function verify(Request $request)
    {
        $input = $request->all();

        // Create an instance of the Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            // $attributes = $request->only('razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature');
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];
            $this->api->utility->verifyPaymentSignature($attributes);

            // return back()->with('success', 'Payment successful!');
            return response()->json(['success' => true]);
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return back()->with('error', 'Razorpay Error: ' . $e->getMessage());
        }
    }

    public function cancelOrder(OrderCancelRequest $request)
    {

        $orderId = $request->input('order_id');
        $order = Order::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        } elseif ($order->status === 'pending') {
            $order->update(['status' => 'canceled']);

            return response()->json(['status' => 'order_canceled', 'message' => 'Order canceled successfully.']);
        }

        return response()->json(['error' => 'Order already canceled.'], 400);
    }

    public function handleWebhook(Request $request)
    {

        $payload = $request->getContent();
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        $actualSignature = $request->header('X-Razorpay-Signature');

        if (!$this->verifyWebhookSignature($payload, $actualSignature, $webhookSecret)) {
            Log::error('Signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON in payload'], 400);
        }

        $event = $data['event'] ?? null;

        $payment = $data['payload']['payment']['entity'] ?? null;

        // Handle event types
        switch ($event) {
            case 'payment.captured':
                $this->processPayment($payment, 'captured');
                break;

            case 'payment.failed':
                $this->processPayment($payment, 'failed');
                break;

            default:
                Log::warning('Unhandled event type: ' . $event);
                return response()->json(['error' => 'Unhandled event type'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function processPayment(array $payment, string $status): void
    {
        $orderId = $payment['order_id'] ?? 'N/A';
        $amount = $payment['amount'] ?? 0;

        if ($status === 'captured') {
            $this->markOrderAsPaid($orderId, $amount);
            Log::info("Payment captured for Order ID: {$orderId}, Amount: {$amount}");
        } else {
            $this->markOrderAsFailed($orderId, $amount);
            Log::error("Payment failed for Order ID: {$orderId}, Amount: {$amount}");
        }
    }

    // // Dummy methods for marking order as paid/failed
    private function markOrderAsPaid(string $orderId, int $amount): void
    {
        DB::table('orders')->where('order_id', $orderId)->update([
            'status' => 'success',
            'amount' => $amount,
            'updated_at' => now()
        ]);
        // Logic to mark the order as paid
        Log::info("Order {$orderId} marked as paid with amount {$amount}.");
    }

    private function markOrderAsFailed(string $orderId, int $amount): void
    {
        DB::table('orders')->where('order_id', $orderId)->update([
            'status' => 'failed',
            'amount' => $amount,
            'updated_at' => now()
        ]);
        // Logic to mark the order as failed
        Log::error("Order {$orderId} marked as failed.");

    }

    private function verifyWebhookSignature($payload, $actualSignature, $secret)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        Log::info('Expected Signature: ' . $expectedSignature);
        return hash_equals($expectedSignature, $actualSignature);
    }

    public function initPayment(Request $request)
    {
        $orderId = $request->input('order_id'); // Get the order ID from the request

        $key = env('RAZORPAY_KEY');
        //actual amount from database
        $amount = $request->input('amount');

        return response()->json([
            'key' => $key,
            'razorpayAmount' => $amount,
        ]);
    }

    public function paymentFailed(Request $request)
    {
        $orderId = $request->input('order_id');

        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        $order->update(['status' => 'failed']);

        return response()->json(['message' => 'Order marked as failed.'], 200);
    }

}
