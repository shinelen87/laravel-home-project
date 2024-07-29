<?php

namespace App\Repositories;

use App\Repositories\Contract\ImagesRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ImageRepository implements ImagesRepositoryContract
{

    /**
     * @throws Exception
     */
    public function attach(Model $model, string $relation, array $images = [], ?string $directory = null): void
    {
        {
            if (! method_exists($model, $relation)) {
                throw new Exception($model::class . "doesn't have '$relation' relation");
            }

            if (!empty($images)) {
                foreach($images as $image) {
                    call_user_func([$model, $relation])->create([
                        'path' => compact('image', 'directory')
                    ]);
                }
            }
        }
    }
}
