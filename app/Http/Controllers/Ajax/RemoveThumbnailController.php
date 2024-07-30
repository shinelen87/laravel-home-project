<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class RemoveThumbnailController extends Controller
{
    public function __invoke(Product $product): JsonResponse
    {
        try {
            $product->thumbnail = null;
            $product->save();

            return response()->json(['message' => 'Thumbnail removed successfully']);
        } catch (\Throwable $exception) {
            logs()->error($exception);

            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }
}
