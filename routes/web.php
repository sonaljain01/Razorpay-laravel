<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentgatewayController;



Route::get('/', function () {
    return view('razorpaypayment');
});
Route::post('sonal/Razorpay-laravel/public/payment/create', [PaymentgatewayController::class, 'createOrder'])->name('payment.create');
Route::post('sonal/Razorpay-laravel/public/payment/verify', [PaymentgatewayController::class, 'verify'])->name('payment.verify');
Route::post('sonal/Razorpay-laravel/public/payment/cancel', [PaymentgatewayController::class, 'cancelOrder'])->name('payment.cancel');
Route::post('sonal/Razorpay-laravel/public/webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook'])->name('payment.webhook');