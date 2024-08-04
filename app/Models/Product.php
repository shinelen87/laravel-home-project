<?php

namespace App\Models;

use App\Observers\ProductObserver;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

/**
 * Class Product
 *
 * @package App\Models
 * @property int id
 * @property string name
 * @property string slug
 * @property string SKU
 * @property string description
 * @property int price
 * @property int discount
 * @property int quantity
 * @property string thumbnail
 * @property int category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperProduct
 */
#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    use HasFactory, Sortable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'slug', 'SKU', 'description', 'price',
        'discount', 'quantity', 'thumbnail', 'category_id'
    ];

    public $sortable = [
        'id',
        'name',
        'SKU',
        'price',
        'quantity',
        'discount',
        'created_at',
        'updated_at'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function setThumbnailAttribute($image): void
    {
        $fileService = app(FileServiceContract::class);

        if (!empty($this->attributes['thumbnail'])) {
             $fileService->remove($this->attributes['thumbnail']);
        }

        if ($image) {
            $this->attributes['thumbnail'] = $fileService->upload(
                $image,
                $this->images_dir
            );
        } else {
            $this->attributes['thumbnail'] = null;
        }
    }

    public function imagesDir(): Attribute
    {
        return Attribute::get(fn () => 'products/' . $this->attributes['slug']);
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['thumbnail'] ? Storage::url($this->attributes['thumbnail']) : '');
    }

    public function finalPrice(): Attribute
    {
        return Attribute::get(
            fn() => round($this->attributes['price'] - ($this->attributes['price'] * ($this->attributes['discount'] / 100)), 2)
        );
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'wishlist',
            'product_id',
            'user_id'
        );
    }
}
