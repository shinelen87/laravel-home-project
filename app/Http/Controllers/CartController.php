<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::instance('cart');

        ds($cart->content());

        return view('cart/index', compact('cart'));
    }

    public function add(Product $product): RedirectResponse
    {
        Cart::instance('cart')->add($product->id, $product->name, 1, $product->finalPrice)
            ->associate(Product::class);

        notify()->success("Product $product->name added to cart successfully!", 'Product Added');

        return redirect()->back();

    }

    public function remove(Request $request): RedirectResponse
    {

        $data = $request->validate([
            'rowId' => ['required', 'string']
        ]);

        Cart::instance('cart')->remove($data['rowId']);

        notify()->success("Product was removed from the cart");

        return redirect()->back();
    }

    public function count(Request $request, Product $product)
    {
        $data = $request->validate([
            'rowId' => ['required', 'string'],
            'quantity' => ['required', 'integer']
        ]);

        Cart::instance('cart')->update($data['rowId'], $data['quantity']);

        notify()->success("Product quantity updated successfully!");

        return redirect()->back();
    }
}
