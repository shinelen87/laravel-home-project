<?php

namespace App\Http\Resources\Products\V2;

use App\Http\Resources\Categories\CategoriesCollection;
use App\Http\Resources\Images\ImagesCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->formattedPrice(),
            'description' => $this->description ?? 'No description available',
            'thumbnail' => $this->thumbnailUrl,
            'stock_status' => $this->inStock(),
            'availability' => $this->getAvailabilityMessage(),
            'quantity' => $this->quantity ?? 0,
            'prices' => [
                'price' => $this->price,
                'discount' => $this->discount ?? 0,
                'final' => $this->calculateFinalPrice(),
            ],
            'categories' => new CategoriesCollection($this->categories),
            'images' => new ImagesCollection($this->images)
        ];
    }
}
