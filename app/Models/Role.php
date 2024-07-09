<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Role
 *
 * @package App\Models
 * @property int id
 * @property string name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin IdeHelperRole
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
