<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Razorpay\Api\Api;
class CheckPaymentStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    // Retrieve the order from the database and check status
    $order = $api->order->fetch('order_id');

    if ($order->status == 'pending') {
        // Check order status via Razorpay API
        $payment = $api->order->fetch($order->id)->payments();

        if (count($payment['items']) && $payment['items'][0]['status'] == 'captured') {
            // Payment successful
        } else {
            // Payment failed
            $order->update(['status' => 'failed']);
        }
    }
    }
}
