<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\PostResource;

final class PostCollection extends ResourceCollection
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
            'data' => PostResource::collection($this->collection),
            'meta' => optional($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator ? $this->resource : null)?->toArray(),
        ];
    }
}
