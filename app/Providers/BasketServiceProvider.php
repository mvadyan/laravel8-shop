<?php

namespace App\Providers;

use App\Services\BasketService;
use Illuminate\Support\ServiceProvider;

class BasketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('basketService', BasketService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
