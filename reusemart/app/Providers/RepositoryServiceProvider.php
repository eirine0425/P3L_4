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

        $this->app->bind(
            \App\Repositories\Interfaces\PenitipRepositoryInterface::class,
            \App\Repositories\Eloquent\PenitipRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\RequestDonasiRepositoryInterface::class,
            \App\Repositories\Eloquent\RequestDonasiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\KeranjangBelanjaRepositoryInterface::class,
            \App\Repositories\Eloquent\KeranjangBelanjaRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\DetailTransaksiRepositoryInterface::class,
            \App\Repositories\Eloquent\DetailTransaksiRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\TransaksiRepositoryInterface::class,
            \App\Repositories\Eloquent\TransaksiRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface::class,
            \App\Repositories\Eloquent\TransaksiPenitipanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\KomisiRepositoryInterface::class,
            \App\Repositories\Eloquent\KomisiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PengirimanRepositoryInterface::class,
            \App\Repositories\Eloquent\PengirimanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\TransaksiMerchRepositoryInterface::class,
            \App\Repositories\Eloquent\TransaksiMerchRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\DetailTransaksiRepositoryInterface::class,
            \App\Repositories\Eloquent\DetailTransaksiRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\DiskusiProdukRepositoryInterface::class,
            \App\Repositories\Eloquent\DiskusiProdukRepository::class
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