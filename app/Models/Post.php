<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'image', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'status', 'published_at', 'author_id', 'category_id', 'created_by', 'updated_by', 'is_published'];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url('/storage/posts/' . $value),
        );
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at->locale('id')->translatedFormat('l, d F Y');
    }

    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->updated_at->locale('id')->translatedFormat('l, d F Y');
    }
}
