<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public $status;
    public $message;

    public function __construct($resource, $status = true, $message = 'Success')
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    public function toArray($request): array
    {
        return [
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->collection->transform(function ($categories) {
                return [
                    'id' => $categories->id,
                    'name' => $categories->name,
                    'slug' => $categories->slug,
                    'image' => $categories->image,
                    'is_active' => $categories->is_active,
                    'is_featured' => $categories->is_featured,
                    'description' => $categories->description,
                    'created' => new DateResource($categories->created_at),
                ];
            }),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
