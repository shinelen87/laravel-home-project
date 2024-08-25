<?php


use App\Http\Controllers\Api\V2\ProductsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductsController::class);
