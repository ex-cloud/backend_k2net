<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        //get menus
        // $menus = Menu::when(request()->q, function ($query) {
        //     $query->where('name', 'like', '%' . request()->q . '%');
        // })->latest()->paginate(5);

        // return new MenuCollection($menus);

        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Nested menus retrieved.',
            'data' => MenuResource::collection($menus)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreMenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->is_active ?? true,
            'parent_id' => $request->parent_id, // ✅ tambahkan ini
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu created successfully.',
            'data' => new MenuResource($menu)
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateMenuRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        // Update the menu
        $updated = $menu->update([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->is_active ?? true,
        ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Menu update failed.',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully.',
            'data'    => new MenuResource($menu),
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
        $menu = Menu::with('children')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu retrieved successfully.',
            'data' => new MenuResource($menu),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Menu $menu)
    {
        // Hapus menu
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully.',
            'data'    => null,
        ]);
    }
}
