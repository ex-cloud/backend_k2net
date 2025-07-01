<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\DateResource;

final class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => $this->image,
            'full_image_url' => $this->image_url,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'is_published' => $this->is_published,
            'author' => auth()->user()->name,
            'category' => $this->category ? new CategoryResource($this->category) : null,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at'  => new DateResource($this->created_at),
            'created_at_formatted' => $this->created_at_formatted,
        ];
    }
}
