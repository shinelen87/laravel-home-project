<?php

namespace App\Http\Controllers\Ajax\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadThumbnailController extends Controller
{
    public function __invoke(Request  $request, Product $product): JsonResponse
    {
        try {
            $request->validate([
                'thumbnail' => 'required|image'
            ]);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('thumbnails', 'public');
                $product->update(['thumbnail' => $path]);
                return response()->json(['message' => 'Thumbnail uploaded successfully', 'path' => $path]);
            }

            return response()->json(['message' => 'Thumbnail uploaded successfully', 'thumbnail' => $thumbnailPath]);
        } catch (\Throwable $exception) {
            logs()->error($exception);

            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
