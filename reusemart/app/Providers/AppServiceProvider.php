<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\UseCases\Auth\AuthUseCase;
use App\Repositories\Interfaces\UserRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(AuthUseCase::class, function ($app) {
            $userRepo = $app->make(UserRepositoryInterface::class);
            return new AuthUseCase($userRepo);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
