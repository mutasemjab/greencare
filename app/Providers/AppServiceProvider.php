<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FirestoreRoomService;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirestoreRoomService::class, function ($app) {
            return new FirestoreRoomService();
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