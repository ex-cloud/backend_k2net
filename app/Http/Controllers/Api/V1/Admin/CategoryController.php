<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\HasSlugAndImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use HasSlugAndImage;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        //get categories
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(StoreCategoryRequest $request)
    {
        $slug = $this->generateSlug($request->slug, $request->name);
        $imageName = $this->storeImage($request->file('image'), 'categories');
        $category = Category::create(
            [
                'name'        => $request->name,
                'slug'        => $slug,
                'image'       => $imageName,
                'description' => $request->description,
                'is_active'   => $request->is_active ?? true,
                'is_featured' => $request->is_featured ?? false,
                'created_by'  => auth()->id(),
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Data Category Berhasil Disimpan!',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Detail Data Category Tidak Ditemukan!',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail Data Category!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCategoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $slug = $this->generateSlug($request->slug, $request->name);
        $data = [
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'is_active'   => $request->is_active ?? $category->is_active,
            'is_featured' => $request->is_featured ?? $category->is_featured,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($category->image && Storage::disk('public')->exists('categories/' . $category->image)) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }

            // Upload gambar baru
            $image = $request->file('image');
            $image->storeAs('categories', $image->hashName(), 'public');

            $data['image'] = $image->hashName();
        }

        $updated = $category->update($data);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => 'Data Category Gagal Diupdate!',
                'data' => null,
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Category Berhasil Diupdate!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->image && Storage::disk('public')->exists('categories/' . $category->image)) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }

            $deleted = $category->delete();

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Category Gagal Dihapus!',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data Category Berhasil Dihapus!',
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Gagal menghapus kategori: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus kategori.',
            ], 500);
        }
    }
}
