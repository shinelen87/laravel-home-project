<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'parent_id' => null, // або вказати інші значення за потреби
        ];
    }

    public function withParent(): static
    {
        return $this->state(fn ($attrs) => ['parent_id' => Category::all()->random()?->id]);
    }
}
