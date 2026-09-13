<?php

namespace Innoboxrr\LaravelOptions\Providers;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Las rutas de la API del paquete.
 *
 * Extiende el ServiceProvider base y no el RouteServiceProvider de Laravel.
 * Ese guarda en una propiedad estatica, compartida por todas sus subclases, el
 * callback con el que la aplicacion carga sus rutas (withRouting), y cada
 * subclase lo vuelve a ejecutar: las rutas de la aplicacion se registraban
 * otra vez por cada paquete asi.
 */
class RouteServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        // Con las rutas cacheadas ya estan dentro de la cache de la aplicacion.
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $this->mapApiRoutes();

    }

    protected function mapApiRoutes(): void
    {

        foreach (glob(__DIR__ . '/../../routes/api/models/*.php') ?: [] as $file) {

            $name = basename($file, '.php');

            Route::middleware('api')
                ->prefix('api/laravel-options/' . $name)
                ->as('api.laravel-options.' . $name . '.')
                ->namespace('Innoboxrr\LaravelOptions\Http\Controllers')
                ->group($file);

        }

    }

}
