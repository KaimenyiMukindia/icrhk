<?php declare(strict_types=1);

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WordPress\WordPressBridgeController;
use Illuminate\Support\Facades\Route;

Route::get('/wordpress/registrations', [WordPressBridgeController::class, 'index']);
Route::post('/wordpress/registrations', [WordPressBridgeController::class, 'store']);
Route::put('/wordpress/registrations/{id}', [WordPressBridgeController::class, 'update']);
Route::delete('/wordpress/registrations/{id}', [WordPressBridgeController::class, 'destroy']);

Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
Route::post('/pesapal-ipn', [PaymentController::class, 'ipn']);
Route::get('/payment/status/{trackingId}', [PaymentController::class, 'status']);
