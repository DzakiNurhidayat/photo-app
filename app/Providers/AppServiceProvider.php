<?php

namespace App\Providers;

use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\EloquentPhotoRepository;
use App\Repositories\EloquentTagRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PhotoRepositoryInterface::class, EloquentPhotoRepository::class);
        $this->app->bind(TagRepositoryInterface::class, EloquentTagRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
