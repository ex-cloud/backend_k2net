<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        //get tags
        $tags = Tag::when(request()->q, function ($tags) {
            $tags = $tags->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new TagCollection($tags, true, 'List Data Tags');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Tag Berhasil Disimpan!',
            'data' => new TagResource($tag)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Detail Data Tag Tidak Ditemukan!',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Tag!',
            'data' => new TagResource($tag),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $updated = $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tag Gagal Diupdate!',
                'data' => null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Tag Berhasil Diupdate!',
            'data' => new TagResource($tag),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(Tag $tag)
    {
        $deleted = $tag->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tag Gagal Dihapus!',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Tag Berhasil Dihapus!',
        ]);
    }
}
