<?php

namespace nattaponra\chatkun_pusher;

use Illuminate\Support\Facades\Route;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class ChatKunServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */


    public function boot()
    {

        //Public 
        $this->publishes([
            __DIR__.'/public' => base_path('public'),
        ]);
        //Create View
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views'),
        ]);
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        //Create Model
        $this->publishes([
            __DIR__.'/models' => base_path('app'),
        ]);
        //Create Controller
        $this->publishes([
            __DIR__.'/controllers' => base_path('app/Http/Controllers'),
        ]);
      

        //Create Migrations
        $this->publishes([
            __DIR__.'/migrations' => base_path('database/migrations'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {


    }
}
