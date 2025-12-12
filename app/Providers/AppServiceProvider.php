<?php

namespace App\Providers;

use App\Modules\Auth\AuthService;
use App\Modules\Posts\Contracts\PostsRepositoryInterface;
use App\Modules\Posts\Policies\PostPolicy;
use App\Modules\Posts\PostsRepository;
use App\Modules\Posts\PostsService;
use App\Modules\Users\UsersRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->singleton(UsersRepository::class);
        $this->app->singleton(PostsRepositoryInterface::class, PostsRepository::class);

        // Services
        $this->app->singleton(AuthService::class);
        $this->app->singleton(PostsService::class);
        
        // Policies
        $this->app->singleton(PostPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

