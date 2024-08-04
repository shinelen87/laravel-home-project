<?php

namespace App\Models;

use App\Enums\WishType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App\Models
 * @property int id
 * @property string name
 * @property string lastname
 * @property string phone
 * @property string birthdate
 * @property string email
 * @property string email_verified_at
 * @property string password
 * @property string remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'birthdate',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wishes(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'wishlist',
            'user_id',
            'product_id'
        )->withPivot(['price', 'exist']);
    }

    public function addToWish(Product $product, WishType $type = WishType::PRICE): void
    {
        $wished = $this->wishes()->find($product);

        if ($wished) {
            $this->wishes()->updateExistingPivot($wished, [$type->value => true]);
        } else {
            $this->wishes()->attach($product, [$type->value => true]);
        }
    }

    public function removeFromWish(Product $product, WishType $type = WishType::PRICE): void
    {
        $this->wishes()->updateExistingPivot($product, [$type->value => false]);
        $product = $this->wishes()->find($product);

        if (!$product->pivot->exist && !$product->pivot->price) {
            $this->wishes()->detach($product);
        }
    }

    public function isWishedProduct(Product $product, string $type = null): bool
    {
        $typeEnum = WishType::tryFrom($type) ?? WishType::PRICE;

        return $this->wishes()
            ->where('product_id', $product->id)
            ->wherePivot($typeEnum->value, true)
            ->exists();
    }
}
