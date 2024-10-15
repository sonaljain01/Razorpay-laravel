<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentgatewayController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::group(['prefix' => 'user'], function () {
    // Route::group(['middleware' => 'auth:api'], function () {
        Route::post('sonal/Razorpay-laravel/public/payment/create-order', [PaymentgatewayController::class, 'createOrder']);
        Route::post('sonal/Razorpay-laravel/public/payment/cancel', [PaymentgatewayController::class, 'cancelOrder']);
        Route::post('sonal/Razorpay-laravel/public/webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook']);
        Route::post('sonal/Razorpay-laravel/public/payment/verify', [PaymentgatewayController::class, 'verify']);
    });
// });
