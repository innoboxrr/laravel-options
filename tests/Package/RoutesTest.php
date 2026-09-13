<?php

namespace Innoboxrr\LaravelOptions\Tests\Package;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as FoundationRouteServiceProvider;
use Innoboxrr\LaravelOptions\Tests\TestCase;

final class RoutesTest extends TestCase
{
    public static int $applicationRoutesLoaded = 0;

    /**
     * Lo que hace ApplicationBuilder::withRouting() en toda aplicacion
     * Laravel 11 o posterior: guardar como se cargan las rutas de la app.
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        FoundationRouteServiceProvider::loadRoutesUsing(function () {
            self::$applicationRoutesLoaded++;
        });
    }

    protected function tearDown(): void
    {
        FoundationRouteServiceProvider::loadRoutesUsing(null);

        self::$applicationRoutesLoaded = 0;

        parent::tearDown();
    }

    public function test_no_vuelve_a_cargar_las_rutas_de_la_aplicacion(): void
    {
        $this->assertSame(0, self::$applicationRoutesLoaded, 'El paquete ejecuto el callback de rutas de la aplicacion.');
    }

    public function test_conserva_las_uris_los_nombres_y_los_metodos(): void
    {
        $expected = [
            'policies' => ['GET', 'policies'],
            'policy' => ['GET', 'policy'],
            'index' => ['GET', 'index'],
            'show' => ['GET', 'show'],
            'create' => ['POST', 'create'],
            'update' => ['PUT', 'update'],
            'delete' => ['DELETE', 'delete'],
            'restore' => ['POST', 'restore'],
            'force.delete' => ['DELETE', 'force-delete'],
            'export' => ['POST', 'export'],
        ];

        $routes = $this->app['router']->getRoutes();

        foreach ($expected as $name => [$method, $uri]) {
            $route = $routes->getByName("api.laravel-options.option.{$name}");

            $this->assertNotNull($route, "Falta la ruta api.laravel-options.option.{$name}.");
            $this->assertSame("api/laravel-options/option/{$uri}", $route->uri());
            $this->assertContains($method, $route->methods());
            $this->assertContains('api', $route->middleware());
        }
    }
}
