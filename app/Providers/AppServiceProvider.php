<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Default is Tailwind CSS framework as of Laravel 8.x
        // https://laravel.com/docs/8.x/upgrade#pagination-defaults
        Paginator::useBootstrap();
    }
}
