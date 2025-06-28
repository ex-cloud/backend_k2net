<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Slider extends Model
{
    protected $fillable = [
        'title',
        'image',
        'link',
        'status',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url('/storage/sliders/' . $value),
        );
    }
}
