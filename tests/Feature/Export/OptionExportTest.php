<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Export;

use Illuminate\Support\Facades\Storage;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * La exportacion en una aplicacion recien instalada: sin configurar S3, sin
 * tabla de notificaciones y con la configuracion que el paquete trae.
 */
class OptionExportTest extends TestCase
{
    public function test_un_administrador_exporta_las_opciones_a_un_excel_en_el_disco_local(): void
    {
        Storage::fake('local');

        $this->signIn(admin: true);

        Option::forceCreate(['key' => 'site_name', 'name' => 'Nombre del sitio', 'value' => 'Mi Sitio']);
        Option::forceCreate(['key' => 'site_description', 'name' => 'Descripción', 'value' => 'Un sitio']);

        $this->postJson(route('api.laravel-options.option.export'))
            ->assertOk()
            ->assertExactJson(['status' => true]);

        $files = Storage::disk('local')->files('exports');

        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.xlsx', $files[0]);

        $rows = IOFactory::load(Storage::disk('local')->path($files[0]))->getActiveSheet()->toArray();

        $this->assertSame(['name', 'key', 'value'], $rows[0]);
        $this->assertContains(['Nombre del sitio', 'site_name', 'Mi Sitio'], $rows);
        $this->assertContains(['Descripción', 'site_description', 'Un sitio'], $rows);

        $this->assertCount(1, app('mail.manager')->mailer()->getSymfonyTransport()->messages());
    }

    /**
     * Una configuracion publicada con una version anterior dice
     * innoboxrrlaraveloptions::excel., un prefijo de vistas que no existe.
     */
    public function test_exporta_aunque_la_configuracion_publicada_traiga_el_prefijo_viejo(): void
    {
        Storage::fake('local');

        config(['laravel-options.excel_view' => 'innoboxrrlaraveloptions::excel.']);

        $this->signIn(admin: true);

        Option::factory()->create();

        $this->postJson(route('api.laravel-options.option.export'))->assertOk();

        $this->assertCount(1, Storage::disk('local')->files('exports'));
    }
}
