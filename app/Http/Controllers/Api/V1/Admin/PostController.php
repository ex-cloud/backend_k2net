<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\HasSlugAndImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use HasSlugAndImage;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     *
     */
    public function index()
    {
        $posts = Post::with('user', 'category', 'comments', 'tags')->when(request()->q, function ($posts) {
            $posts = $posts->where('title', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new PostCollection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        $slug = $this->generateSlug($request->slug, $request->title);
        $imageName = $this->storeImage($request->file('image'), 'posts');


        $post = Post::create([
            'title'       => $request->title,
            'slug'        => $slug,
            'content'     => $request->content,
            'image'       => $imageName,
            'description' => $request->description,
            'meta_title'  => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'      => $request->status,
            'published_at' => $request->published_at,
            'is_published' => $request->is_published,
            'category_id' => $request->category_id,
            'author_id'   => auth()->id(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($request->filled('tags')) {
            $post->tags()->attach($request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully!',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = Post::with(['user', 'category', 'comments', 'tags'])->find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Post retrieved successfully.',
            'data' => new PostResource($post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdatePostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $slug = $this->generateSlug($request->slug, $request->title);
        $imageName = $this->storeImage($request->file('image'), 'posts');
        // ka menggunakan UUID untuk file name, bisa ganti hashName() dengan Str::uuid() . '.' . $image->getClientOriginalExtension()
        // Data awal
        $data = [
            'title'            => $request->title,
            'slug'             => $slug,
            'image'            => $imageName ?? $post->image, // Gunakan gambar lama jika tidak ada gambar baru
            'author_id'        => auth()->id(), // Update author_id jika perlu
            'content'          => $request->content,
            'description'      => $request->description,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'           => $request->status,
            'published_at'     => $request->published_at,
            'is_published'     => $request->is_published,
            'category_id'      => $request->category_id,
        ];

        // Handle upload image baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($post->image && \Storage::disk('public')->exists('posts/' . $post->image)) {
                \Storage::disk('public')->delete('posts/' . $post->image);
            }

            $image = $request->file('image');
            $image->storeAs('posts', $image->hashName(), 'public');
            $data['image'] = $image->hashName();
        }

        // Update post
        $post->update($data);

        // Sinkronisasi tag
        $post->tags()->sync($request->tags ?? []);

        return response()->json([
            'status'  => true,
            'message' => 'Post berhasil diperbarui!',
            'data'    => new PostResource($post),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        // Hapus gambar jika ada dan bukan default
        if ($post->image && $post->image !== 'default.png' && \Storage::disk('public')->exists('posts/' . $post->image)) {
            \Storage::disk('public')->delete('posts/' . $post->image);
        }

        // Hapus relasi jika ada (opsional, contoh: detach tags)
        $post->tags()->detach();

        // Hapus post
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully.',
        ]);
    }

}
