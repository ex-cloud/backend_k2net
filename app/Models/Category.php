<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Category extends Model
{
    protected $fillable = ['image', 'name', 'slug', 'description', 'created_by', 'updated_by', 'is_active', 'is_featured'];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/categories/' . $value) : null,
        );
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
