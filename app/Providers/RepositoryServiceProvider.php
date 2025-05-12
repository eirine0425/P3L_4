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
            \App\Repositories\Interfaces\BarangRepositoryInterface::class,
            \App\Repositories\Eloquent\BarangRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\DonasiRepositoryInterface::class,
            \App\Repositories\Eloquent\DonasiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\GaransiRepositoryInterface::class,
            \App\Repositories\Eloquent\GaransiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\KategoriBarangRepositoryInterface::class,
            \App\Repositories\Eloquent\KategoriBarangRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\MerchRepositoryInterface::class,
            \App\Repositories\Eloquent\MerchRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PegawaiRepositoryInterface::class,
            \App\Repositories\Eloquent\PegawaiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\OrganisasiRepositoryInterface::class,
            \App\Repositories\Eloquent\OrganisasiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\AlamatRepositoryInterface::class,
            \App\Repositories\Eloquent\AlamatRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PembeliRepositoryInterface::class,
            \App\Repositories\Eloquent\PembeliRepository::class
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