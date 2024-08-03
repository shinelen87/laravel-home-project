<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use Tests\Feature\Traits\SetupTrait;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use SetupTrait;

    public function test_it_creates_product_with_valid_data()
    {
        $file = UploadedFile::fake()->image('test_image.jpg');

        $data = Product::factory()->make(['thumbnail' => $file])->toArray();
        $slug = $data['slug'];
        $imagePath = "$slug/uploaded_image.jpg";

        $this->mock(
            FileService::class,
            function (MockInterface $mock) use ($imagePath) {
            $mock->shouldReceive('upload')
                ->andReturn($imagePath);
            }
        );

        $this->actingAs($this->user())
            ->post(route('admin.products.store'), array_merge($data, ['thumbnail' => $file]))
            ->assertStatus(302)
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas(Product::class, [
            'slug' => $slug,
            'thumbnail' => $imagePath,
        ]);
    }
}
