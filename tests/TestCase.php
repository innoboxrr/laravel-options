<?php

namespace Innoboxrr\LaravelOptions\Tests;

use Innoboxrr\LaravelOptions\Tests\Fixtures\AdminUser;
use Innoboxrr\LaravelOptions\Tests\Fixtures\User;
use Orchestra\Testbench\TestCase as Testbench;

use function Orchestra\Testbench\default_migration_path;

/**
 * Una aplicacion Laravel con los proveedores que el paquete declara en su
 * composer.json.
 *
 * Se leen del propio composer.json y no de una lista escrita aqui: lo que se
 * prueba tiene que ser exactamente lo que recibe quien instala el paquete, y
 * una segunda lista acabaria divergiendo de la primera. Delante van los de sus
 * dependencias, que la aplicacion descubre sola y Testbench no.
 */
abstract class TestCase extends Testbench
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return array_values(array_filter([
            'Laravel\Sanctum\SanctumServiceProvider',
            'Maatwebsite\Excel\ExcelServiceProvider',
            'Innoboxrr\SearchSurge\Providers\SearchSurgeServiceProvider',
            'Innoboxrr\Traits\Providers\AppServiceProvider',
            ...self::declaredProviders(),
        ], 'class_exists'));
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * SQLite en memoria, vacia en cada test: basta migrar. users y cache las
     * trae Testbench, como las traeria la aplicacion; options la registra el
     * AppServiceProvider del paquete.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->app->make('migrator')->path(default_migration_path());

        $this->artisan('migrate')->run();
    }

    /**
     * @return array<int, class-string>
     */
    public static function declaredProviders(): array
    {
        return self::composer()['extra']['laravel']['providers'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function composer(): array
    {
        return json_decode((string) file_get_contents(dirname(__DIR__) . '/composer.json'), true);
    }

    protected function signIn(bool $admin = false): User
    {
        $class = $admin ? AdminUser::class : User::class;

        $user = $class::forceCreate([
            'name' => $admin ? 'Admin' : 'User',
            'email' => uniqid('user', true) . '@example.test',
            'password' => 'secret',
        ]);

        $this->actingAs($user, 'sanctum');

        return $user;
    }
}
