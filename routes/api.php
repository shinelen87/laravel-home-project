<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('auth', AuthController::class)
    ->name('auth');

Route::prefix('v1')->name('v1.')->middleware('auth:sanctum')->group(function () {
    require_once __DIR__ . '/versions/v1.php';
});

Route::prefix('v2')->name('v2.')->middleware('auth:sanctum')->group(function () {
    require_once __DIR__ . '/versions/v2.php';
});
