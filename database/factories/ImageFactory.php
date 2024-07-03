<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Product;

class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => $this->faker->imageUrl,
            'imageable_type' => $this->faker->randomElement([Product::class, Category::class]),
            'imageable_id' => function (array $attributes) {
                if ($attributes['imageable_type'] == Product::class) {
                    return Product::inRandomOrder()->first()->id;
                } else {
                    return Category::inRandomOrder()->first()->id;
                }
            },
        ];
    }
}
