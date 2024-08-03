<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Ajax\Products\UploadThumbnailController;
use App\Http\Controllers\Ajax\RemoveThumbnailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function() {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', ProductsController::class)->except(['show']);
    Route::resource('categories', CategoriesController::class)->except(['show']);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::name('ajax.')->prefix('ajax')->group(function() {
    Route::group(['auth', 'role:admin|moderator'], function() {
        Route::post('products/{product}/images', \App\Http\Controllers\Ajax\Products\UploadImages::class)->name('product.images.upload');
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('image.remove');
        Route::post('products/{product}/thumbnail', UploadThumbnailController::class)->name('thumbnail.upload'); // додано цей рядок
    });
});

Route::delete('products/{product}/thumbnail', [RemoveThumbnailController::class, '__invoke'])->name('ajax.thumbnail.remove');

