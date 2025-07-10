<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
            Paginator::useTailwind();
            ini_set('memory_limit', env('MEMORY_LIMIT', '1024M'));

            ini_set('max_execution_time', 0); // Sin límite de tiempo
            ini_set('memory_limit', '-1');    // Sin límite de memoria
    }

 
}
