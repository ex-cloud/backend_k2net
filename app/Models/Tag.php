<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

final class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'created_by', 'updated_by'];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
