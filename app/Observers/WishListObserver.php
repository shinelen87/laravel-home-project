<?php

namespace App\Observers;

use App\Jobs\Wishlist\PriceUpdatedJob;
use App\Jobs\Wishlist\ProductExistsJob;
use App\Models\Product;

class WishListObserver
{
    public function updated(Product $product): void
    {
        if ($product->finalPrice < $product->getOriginal('finalPrice')) {
            $product->followers()->wherePivot('price', true)->get();
            PriceUpdatedJob::dispatch($product);
        }

        if ($product->exist && !$product->getOriginal('exist')) {
            ProductExistsJob::dispatch($product);
        }
    }
}
