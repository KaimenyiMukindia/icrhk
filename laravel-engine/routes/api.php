<?php declare(strict_types=1);

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
Route::post('/pesapal-ipn', [PaymentController::class, 'ipn']);
Route::get('/payment/status/{trackingId}', [PaymentController::class, 'status']);
