<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category_product')->delete();
        DB::table('categories')->delete();
        DB::table('products')->delete();
        Storage::deleteDirectory('faker');

        $createProduct = function (Category $category) {
            $products = Product::factory(rand(2, 5))->create()->pluck('id');
            $category->products()->attach($products);
        };

        Category::factory(5)->create()->each($createProduct);
        Category::factory(5)->withParent()->create()->each($createProduct);
    }
}
