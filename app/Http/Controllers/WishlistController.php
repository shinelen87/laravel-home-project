<?php

namespace App\Http\Controllers;

use App\Enums\WishType;
use App\Http\Requests\WishlistRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class WishlistController extends Controller
{
    public function add(Product $product, WishlistRequest $wishlistRequest): RedirectResponse
    {
        $type = $wishlistRequest->get('type');

        $wishType = WishType::tryFrom($type);

        if (!$wishType) {
            abort(400, 'Invalid wish type provided');
        }

        auth()->user()->addToWish($product, $wishType);

        notify()->success('Product added to wishlist');

        return redirect()->back();
    }

    public function remove(Product $product, WishlistRequest $wishlistRequest): RedirectResponse
    {
        $type = $wishlistRequest->get('type');

        $wishType = WishType::tryFrom($type);

        if (!$wishType) {
            abort(400, 'Invalid wish type provided');
        }

        auth()->user()->removeFromWish($product, $wishType);

        notify()->success('Product removed from wishlist');

        return redirect()->back();
    }
}
