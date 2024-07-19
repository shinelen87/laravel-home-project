<?php

namespace App\Models;

use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class Image
 *
 * @package App\Models
 * @property int id
 * @property string path
 * @property string imageable_type
 * @property int imageable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperImage
 */
class Image extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['path', 'imageable_type', 'imageable_id'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setPathAttribute($path): void
    {
        $this->attributes['path'] = app(FileServiceContract::class)->upload(
            $path['image'],
            $path['directory'] ?? null
        );
    }
}
