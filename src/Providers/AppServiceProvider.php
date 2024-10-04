<?php

namespace Innoboxrr\LaravelOptions\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-options.php', 'laravel-options');

    }

    public function boot()
    {
        
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-options');

        if ($this->app->runningInConsole()) {
            
            $this->publishes([__DIR__.'/../../resources/views' => resource_path('views/vendor/laravel-options'),], 'views');

            $this->publishes([__DIR__.'/../../config/laravel-options.php' => config_path('laravel-options.php')], 'config');

            $this->publishes([__DIR__.'/../../resources/vue/laravel-options' => resource_path('vue/vendor/laravel-options')], 'vue');

        }

    }
    
}