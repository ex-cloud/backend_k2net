<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DateResource
 *
 * @property CarbonInterface $resource
 */
final class DateResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [

            'human' => $this->resource->diffForHumans(),
            'string' => $this->resource->toDateTimeString(),
            'local' => $this->resource->toDateTimeLocalString(),
            'timestamp' => $this->resource->timestamp,
            // 'date' => $this->resource->format('Y-m-d'),
            // 'time' => $this->resource->format('H:i:s'),
            // 'timestamp' => $this->resource->timestamp,
            // 'timezone' => $this->resource->timezone,
        ];
    }
}
