<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentgatewayController;
// Route::get('/', function () {
//     return view('welcome');
// });


// Route::view('/pay/razorpay', 'razorpay');
// Route::post('/pay/verify', [PaymentgatewayController::class, 'verify']);

// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '.*');

// Route::post('webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook']);


Route::get('/', function () {
    return view('razorpaypayment');
});
Route::post('/payment/create', [PaymentgatewayController::class, 'createOrder'])->name('payment.create');
Route::post('/payment/verify', [PaymentgatewayController::class, 'verify'])->name('payment.verify');
Route::post('/payment/cancel', [PaymentgatewayController::class, 'cancelOrder'])->name('payment.cancel');
Route::post('/webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook'])->name('payment.webhook');