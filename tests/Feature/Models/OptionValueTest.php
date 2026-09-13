<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionValueTest extends TestCase
{
    private function option(string $key, ?string $value): Option
    {
        return Option::forceCreate(['key' => $key, 'name' => $key, 'value' => $value]);
    }

    public function test_devuelve_el_valor_de_una_clave(): void
    {
        $this->option('site_name', 'Mi Sitio');

        $this->assertSame('Mi Sitio', Option::value('site_name'));
    }

    public function test_decodifica_los_objetos_y_arreglos_json(): void
    {
        $this->option('theme', '{"home":{"title":"Inicio","sections":[]}}');
        $this->option('menu', '["inicio","contacto"]');

        $this->assertSame(['home' => ['title' => 'Inicio', 'sections' => []]], Option::value('theme'));
        $this->assertSame(['inicio', 'contacto'], Option::value('menu'));
    }

    /**
     * "123" o "true" tambien son JSON valido, pero no son estructuras: se
     * devuelven como se guardaron.
     */
    public function test_lo_que_no_es_objeto_ni_arreglo_se_devuelve_como_texto(): void
    {
        $this->option('max_items', '123');
        $this->option('signups_open', 'true');
        $this->option('quoted', '"hola"');

        $this->assertSame('123', Option::value('max_items'));
        $this->assertSame('true', Option::value('signups_open'));
        $this->assertSame('"hola"', Option::value('quoted'));
    }

    public function test_devuelve_el_valor_por_defecto_si_falta_esta_borrada_o_es_nula(): void
    {
        $this->option('deleted', 'x')->delete();
        $this->option('empty', null);

        $this->assertSame('defecto', Option::value('missing', 'defecto'));
        $this->assertSame('defecto', Option::value('deleted', 'defecto'));
        $this->assertSame('defecto', Option::value('empty', 'defecto'));
        $this->assertNull(Option::value('missing'));
    }

    public function test_leer_el_atributo_value_sin_haber_cargado_la_columna_no_lo_confunde_con_una_relacion(): void
    {
        $this->option('site_name', 'Mi Sitio');

        $option = Option::query()->select(['id', 'key'])->firstOrFail();

        $this->assertNull($option->value);
        $this->assertSame('Mi Sitio', Option::query()->firstOrFail()->value);
    }
}
