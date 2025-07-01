<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

final class TagController extends Controller
{
    /**
     * Get all tags
     */
    public function index(): JsonResponse
    {
        $tags = Tag::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'List Data Tags',
            'data' => TagResource::collection($tags),
        ]);
    }

    /**
     * Show tag detail with related posts by slug
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::with(['posts.tags', 'posts.category', 'posts.comments'])
            ->where('slug', $slug)
            ->first();

        if (!$tag) {
            return response()->json([
                'status' => false,
                'message' => 'Data Tag Tidak Ditemukan!',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'List Data Posts By Tag',
            'data' => new TagResource($tag),
        ]);
    }
}
