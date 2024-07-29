<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Http\Requests\Admin\Products\CreateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contract\ProductsRepositoryContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Enums\Permissions\Product as Permission;
use Throwable;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::with(['categories'])
            ->sortable()
            ->paginate(10);

        return view('admin/products/index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin/products/create', ['categories' => Category::select(['id', 'name'])->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, ProductsRepositoryContract $repository): RedirectResponse
    {
        if ($product = $repository->create($request)) {
            notify()->success("Product $product->name created successfully!", 'Product Created');
            return redirect()->route('admin.products.index');
        }

        notify()->error('Product creation failed!', 'Product Creation Failed');
        return redirect()->back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['images', 'categories']);

        $categories = Category::all();
        $productCategories = $product->categories->pluck('id')->toArray();

        return view('admin/products/edit', compact('categories', 'productCategories', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditRequest $request, Product $product, ProductsRepositoryContract $repository): RedirectResponse
    {
        if ($product = $repository->update($product, $request)) {
            notify()->success("Product $product->name was updated!");
            return redirect()->route('admin.products.index');
        }
        notify()->error("Oops, smth went wrong");
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     * @throws Throwable
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->middleware('permission:' . Permission::DELETE->value);

        $product->categories()->detach();
        $product->deleteOrFail();

        notify()->success("Product '$product->name' was removed!");

        return redirect()->route('admin.products.index');
    }
}
