<?php declare(strict_types=1);

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
Route::post('/paystack-webhook', [PaymentController::class, 'ipn']);
Route::post('/payment/verify', [PaymentController::class, 'verify']);
