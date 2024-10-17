<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentgatewayController;



Route::get('/', function () {
    return view('payment');
})->name('home');
Route::post('payment/create', [PaymentgatewayController::class, 'createOrder'])->name('payment.create');
Route::post('payment/verify', [PaymentgatewayController::class, 'verify'])->name('payment.verify');

Route::post('webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook'])->name('payment.webhook');
Route::get('payment/success', function () {
    return view('success');
})->name('payment.success');
Route::get('payment/failed', function () {
    return view('failed');
})->name('payment.failed');
Route::get('/payment/confirm', function() {
    return view('confirm');
})->name('payment.confirm');
Route::post('/payment/init', [PaymentgatewayController::class, 'initPayment'])->name('payment.init');

Route::post('/payment/failed', [PaymentgatewayController::class, 'paymentFailed'])->name('payment.failed');
Route::post('/payment/cancel', [PaymentgatewayController::class, 'cancelOrder'])->name('payment.cancel');
Route::get('/payment/cancel/success', function () {
    return view('cancel');
})->name('payment.cancel.success');
// In web.php (routes)
Route::get('order/confirmation/{orderId}', [PaymentgatewayController::class, 'showOrderConfirmation']);
