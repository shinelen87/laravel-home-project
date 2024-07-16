<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
