<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Image
 * @package App\Models
 * @property int id
 * @property string path
 * @property string imageable_type
 * @property int imageable_id
 * @property string created_at
 * @property string updated_at
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
}
