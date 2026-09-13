<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Console;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionSeedCommandTest extends TestCase
{
    public function test_siembra_las_opciones(): void
    {
        $this->artisan('options:seed')->assertSuccessful();

        $this->assertTrue(Option::where('key', 'site_name')->exists());
    }

    /**
     * Se ejecuta al instalar y en cada despliegue, es decir, en produccion,
     * donde db:seed pide confirmacion y nadie la puede dar.
     */
    public function test_siembra_en_produccion_sin_pedir_confirmacion(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('options:seed')->assertSuccessful();

        $this->assertTrue(Option::where('key', 'site_name')->exists());
    }
}
