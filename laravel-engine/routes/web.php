<?php declare(strict_types=1);

use App\Http\Controllers\WordPress\WordPressBridgeController;
use Illuminate\Support\Facades\Route;

Route::get('/wordpress/registrations', [WordPressBridgeController::class, 'index']);
Route::post('/wordpress/registrations', [WordPressBridgeController::class, 'store']);
Route::put('/wordpress/registrations/{id}', [WordPressBridgeController::class, 'update']);
Route::delete('/wordpress/registrations/{id}', [WordPressBridgeController::class, 'destroy']);
