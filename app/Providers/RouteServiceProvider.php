<?php
namespace App\Providers;

use App\Models\Tag;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::model('tag', Tag::class); // pastikan ini ada
        Route::bind('tag', function ($value) {
            return Tag::where('slug', $value)->firstOrFail();
        });
    }
}
