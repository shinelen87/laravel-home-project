<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\JsonResponse;

class RemoveImageController extends Controller
{
    public function __invoke(Image $image): JsonResponse
    {
        try {
            $image->deleteOrFail();

            return response()->json(['message' => 'Image removed successfully']);
        } catch (\Throwable $exception) {
            logs()->error($exception);

            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }
}
