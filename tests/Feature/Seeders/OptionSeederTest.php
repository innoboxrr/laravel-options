<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Seeders;

use Innoboxrr\LaravelOptions\Database\Seeders\OptionSeeder;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionSeederTest extends TestCase
{
    public function test_crea_las_opciones_por_defecto(): void
    {
        $this->seed(OptionSeeder::class);

        $this->assertSame('Mi Sitio', Option::where('key', 'site_name')->value('value'));
        $this->assertSame('Descripción de mi sitio', Option::where('key', 'site_description')->value('value'));
        $this->assertSame('Home', json_decode(Option::where('key', 'theme')->value('value'), true)['home']['title']);
    }

    /**
     * Se siembra en cada despliegue: lo que se cambio desde el panel se queda.
     */
    public function test_no_sobrescribe_las_opciones_que_ya_existen(): void
    {
        Option::forceCreate(['key' => 'site_name', 'name' => 'Nombre propio', 'value' => 'Mi Empresa']);

        $this->seed(OptionSeeder::class);

        $option = Option::where('key', 'site_name')->firstOrFail();

        $this->assertSame('Mi Empresa', $option->value);
        $this->assertSame('Nombre propio', $option->name);
        $this->assertTrue(Option::where('key', 'theme')->exists());
    }

    public function test_sembrar_dos_veces_no_duplica(): void
    {
        $this->seed(OptionSeeder::class);
        $this->seed(OptionSeeder::class);

        $this->assertSame(3, Option::withTrashed()->count());
    }

    /**
     * key es unica tambien entre las borradas. Una opcion que el administrador
     * borro no hace fallar el seeder ni vuelve a aparecer.
     */
    public function test_una_opcion_borrada_no_rompe_el_seeder_ni_se_revive(): void
    {
        $option = Option::forceCreate(['key' => 'site_description', 'name' => 'Descripción', 'value' => 'Borrada']);
        $option->delete();

        $this->seed(OptionSeeder::class);

        $this->assertTrue($option->fresh()->trashed());
        $this->assertSame(1, Option::withTrashed()->where('key', 'site_description')->count());
    }
}
