<?php

namespace Innoboxrr\LaravelOptions\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        
        // $this->mergeConfigFrom(__DIR__ . '/../../config/innoboxrrlaraveloptions.php', 'innoboxrrlaraveloptions');

    }

    public function boot()
    {
        
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // $this->loadViewsFrom(__DIR__.'/../../resources/views', 'innoboxrrlaraveloptions');

        if ($this->app->runningInConsole()) {
            
            // $this->publishes([__DIR__.'/../../resources/views' => resource_path('views/vendor/innoboxrrlaraveloptions'),], 'views');

            // $this->publishes([__DIR__.'/../../config/innoboxrrlaraveloptions.php' => config_path('innoboxrrlaraveloptions.php')], 'config');

        }

    }
    
}