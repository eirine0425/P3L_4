<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class,
            
        );

        $this->app->bind(
            \App\Repositories\Interfaces\AlamatRepositoryInterface::class,
            \App\Repositories\Eloquent\AlamatRepository::class,
            
        );

        $this->app->bind(
            \App\Repositories\Interfaces\OrganisasiRepositoryInterface::class,
            \App\Repositories\Eloquent\OrganisasiRepository::class,
            
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PembeliRepositoryInterface::class,
            \App\Repositories\Eloquent\PembeliRepository::class,
            
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PenitipRepositoryInterface::class,
            \App\Repositories\Eloquent\PenitipRepository::class,
            
        );

        $this->app->bind(
            \App\Repositories\Interfaces\RoleRepositoryInterface::class,
            \App\Repositories\Eloquent\RoleRepository::class,
            
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}