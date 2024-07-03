<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;

Route::get('/create-category', function () {
    $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
    return response()->json($category);
});

Route::get('/create-product', function () {
    $category = Category::first(); // або знайдіть потрібну категорію іншим способом
    $product = Product::create([
        'name' => 'Smartphone',
        'slug' => 'smartphone',
        'SKU' => 'SP12345',
        'description' => 'This is a sample smartphone.',
        'price' => 499.99,
        'discount' => 10,
        'quantity' => 50,
        'thumbnail' => 'path/to/thumbnail.jpg',
        'category_id' => $category->id
    ]);
    return response()->json($product);
});

Route::get('/categories', function () {
    return response()->json(Category::all());
});

Route::get('/products', function () {
    return response()->json(Product::all());
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
