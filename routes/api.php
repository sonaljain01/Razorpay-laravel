<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentgatewayController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::group(['prefix' => 'user'], function () {
// Route::group(['middleware' => 'auth:api'], function () {
Route::post('payment/create-order', [PaymentgatewayController::class, 'createOrder']);
Route::post('payment/cancel', [PaymentgatewayController::class, 'cancelOrder']);
Route::post('webhook/razorpay', [PaymentgatewayController::class, 'handleWebhook']);
Route::post('payment/verify', [PaymentgatewayController::class, 'verify']);
// });
// });
Route::get('webhook/razorpay/', function () {
    return response()->json([
        'message' => 'No Reaponse',
    ]);
});