<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Ajax\Products\UploadThumbnailController;
use App\Http\Controllers\Ajax\RemoveThumbnailController;
use App\Http\Controllers\Pages\ThankYouController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function() {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', ProductsController::class)->except(['show']);
    Route::resource('categories', CategoriesController::class)->except(['show']);
});

Route::resource('products', \App\Http\Controllers\ProductsController::class)->only(['show', 'index']);
Route::get('checkout', \App\Http\Controllers\CheckoutController::class)->name('checkout');
Route::get('orders/{vendorOrderId}/thank-you', ThankYouController::class)->name('thankyou');

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::name('ajax.')->prefix('ajax')->group(function() {
    Route::group(['auth', 'role:admin|moderator'], function() {
        Route::post('products/{product}/images', \App\Http\Controllers\Ajax\Products\UploadImages::class)->name('product.images.upload');
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('image.remove');
        Route::post('products/{product}/thumbnail', UploadThumbnailController::class)->name('thumbnail.upload'); // додано цей рядок
    });

    Route::prefix('paypal')->name('paypal.')->group(function() {
        Route::post('order', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'create'])->name('order.create');
        Route::post('order/{vendorOrderId}/capture', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'capture'])->name('order.capture');
    });
});

Route::delete('products/{product}/thumbnail', [RemoveThumbnailController::class, '__invoke'])->name('ajax.thumbnail.remove');

Route::name('cart.')->prefix('cart')->group(function() {
    Route::get('/', [\App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::delete('/', [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::put('{product}/count', [\App\Http\Controllers\CartController::class, 'count'])->name('count');
});

Route::middleware(['auth'])->group(function() {
    Route::post('wishlist/{product}', [\App\Http\Controllers\WishListController::class, 'add'])->name('wishlist.add');
    Route::delete('wishlist/{product}', [\App\Http\Controllers\WishListController::class, 'remove'])->name('wishlist.remove');


    Route::name('account.')->prefix('account')->group(function() {
        Route::get('/', [App\Http\Controllers\Account\HomeController::class, 'index'])->name('home');
        Route::get('wishlist', App\Http\Controllers\Account\WishlistController::class)->name('wishlist');
    });

    Route::get('invoices/{order}', \App\Http\Controllers\InvoicesController::class)->name('invoice');
});

