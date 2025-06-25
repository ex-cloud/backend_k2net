<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TagResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'created_by'  => auth()->user() ? auth()->user()->name : $this->created_by,
            // 'updated_by'  => auth()->user() ? auth()->user()->name : $this->updated_by,
            'created_at'  => new DateResource($this->created_at),
        ];
    }
}
