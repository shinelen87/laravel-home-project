<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

class ProductFactory extends Factory
{
    protected $model = Product::class;

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
            'SKU' => strtoupper($this->faker->unique()->bothify('??######')),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'discount' => $this->faker->numberBetween(0, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
            'thumbnail' => '',
        ];
    }

    public function withThumbnail(): static
    {
        return $this->state(fn (array $attrs) => ['thumbnail' => $this->generateImage($attrs['slug'])]);
    }

    protected function generateImage(string $slug): string
    {
        $dirName = "faker/products/$slug";
        $faker = \Faker\Factory::create();
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        if (!Storage::exists($dirName)) {
            Storage::createDirectory($dirName);
        }

        /**
         * @var \Smknstd\FakerPicsumImages\FakerPicsumImagesProvider $faker
         */
        return $dirName . '/' . $faker->image(
                dir: Storage::path($dirName),
                isFullPath: false
            );
    }
}
