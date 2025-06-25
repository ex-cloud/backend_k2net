<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;

class CategoryController extends Controller
{
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */

    public function store(StoreCategoryRequest $request)
    {
        $image = $request->file('image');
        $image->storeAs('categories', $image->hashName(), 'public');
        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar kategori!',
            ], 422);
        }
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'image' => $image->hashName(),
        ]);
        $category->image = $image->hashName();

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Category Berhasil Disimpan!',
            'data' => new CategoryResource($category)
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
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Detail Data Category Tidak Ditemukan!',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Category!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('public')->delete('categories/' . basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('categories', $image->hashName(), 'public');

            //update category with new image
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);
        }

        //update category without image
        $updated = $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'description' => $request->description,
        ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Data Category Gagal Diupdate!',
                'data' => null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Category Berhasil Diupdate!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(Category $category)
    {
        //remove image
        Storage::disk('public')->delete('categories/' . basename($category->image));
        
        $deleted = $category->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data Category Gagal Dihapus!',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Category Berhasil Dihapus!',
        ]);
    }
}
