<?php

namespace Innoboxrr\LaravelOptions\Tests\Package;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\ExportEvent;
use Innoboxrr\LaravelOptions\Http\Events\Option\Listeners\ExportEvent\SendExportNotification;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

use function Orchestra\Testbench\default_migration_path;

/**
 * El paquete dentro de una aplicacion Laravel 13 recien creada.
 *
 * Una aplicacion nueva trae CACHE_STORE=database y todavia no tiene la tabla
 * cache: la crea la primera migracion. Si un proveedor lee la cache al
 * arrancar, php artisan migrate falla antes de llegar a crearla.
 */
final class FreshApplicationBootTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('cache.default', 'database');
    }

    /**
     * Sin migrar de antemano: migrar es justo lo que se prueba.
     */
    protected function defineDatabaseMigrations(): void
    {
        //
    }

    public function test_arranca_y_migra_con_la_cache_en_base_de_datos_y_sin_tabla_cache(): void
    {
        $this->assertSame('database', config('cache.default'));
        $this->assertFalse(Schema::hasTable('cache'));

        $this->app->make('migrator')->path(default_migration_path());

        $this->artisan('migrate')->assertSuccessful();

        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('options'));
    }

    public function test_registra_los_listeners_y_el_observer_de_option(): void
    {
        $this->assertContains(SendExportNotification::class, Event::getRawListeners()[ExportEvent::class] ?? []);

        $this->assertTrue(Event::hasListeners('eloquent.created: ' . Option::class));
    }

    /**
     * La aplicacion ya registra este listener una vez. Si el paquete lo
     * registra otra, el usuario recibe el correo de verificacion repetido.
     */
    public function test_no_registra_el_listener_de_verificacion_de_correo(): void
    {
        $this->assertNotContains(SendEmailVerificationNotification::class, Event::getRawListeners()[Registered::class] ?? []);
    }
}
