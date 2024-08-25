<?php

namespace App\Repositories;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Http\Requests\Api\ProductUpdateRequest;
use App\Models\Product;
use App\Repositories\Contract\ProductsRepositoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductsRepository implements ProductsRepositoryContract
{
    public function __construct(
        protected ImageRepository $imageRepository
    ) {}

    public function create(CreateRequest $request): Product|false
    {
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);
            $product = Product::create($data['attributes']);
            $this->setProductRelationData($product, $data);

            DB::commit();

            return $product;
        } catch (Throwable $exception) {
            DB::rollBack();
            logs()->error($exception);

            return false;
        }
    }

    public function update(Product $product, EditRequest $request): bool
    {
        return $this->updateProduct($product, $this->formRequestData($request));
    }

    protected function setProductRelationData(Product $product, array $data): void
    {
        $product->categories()->sync($data['categories']);

        if (!empty($data['images'])) {
            $this->imageRepository->attach(
                $product,
                'images',
                $data['images'],
                $product->images_dir
            );
        }
    }

    protected function formRequestData(CreateRequest|EditRequest $request): array
    {
        return [
            'attributes' => collect($request->validated())
                ->except(['categories'])
                ->prepend(Str::slug($request->get('name')), 'slug')
                ->toArray(),
            'categories' => $request->get('categories', []),
            'images' => $request->file('images', [])
        ];
    }

    public function updateApi(Product $product, ProductUpdateRequest $request): bool
    {
        return $this->updateProduct($product, [
            'attributes' => collect($product->toArray())
                ->merge($request->validated())
                ->except(['categories'])
                ->prepend(Str::slug($request->get('name')), 'slug')
                ->toArray(),
            'categories' => $request->get('categories', []),
            'images' => $request->file('images', [])
        ]);
    }

    protected function updateProduct(Product $product, array $data): bool
    {
        try {
            DB::beginTransaction();
            $product->update($data['attributes']);
            $this->setProductRelationData($product, $data);

            DB::commit();

            return true;
        } catch (Throwable $exception) {
            DB::rollBack();
            logs()->error($exception);

            return false;
        }
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $products = Product::with(['categories', 'images'])
            ->when($request->get('exists'), function(Builder $query) {
                return $query->exists();
            })->when($request->get('out_of_stock'), function(Builder $query) {
                return $query->where('quantity', '<=', 0);
            })->orderByDesc('id');

        return $products->paginate(10);
    }
}
