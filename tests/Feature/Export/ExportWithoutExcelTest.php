<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Export;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\ExportEvent;
use Innoboxrr\LaravelOptions\Http\Requests\Option\ExportRequest;
use Innoboxrr\LaravelOptions\Tests\Fixtures\ExportRequestWithoutExcel;
use Innoboxrr\LaravelOptions\Tests\TestCase;

/**
 * maatwebsite/excel es sugerido: una aplicacion puede no tenerlo.
 */
class ExportWithoutExcelTest extends TestCase
{
    public function test_sin_maatwebsite_excel_responde_un_error_claro(): void
    {
        Storage::fake('local');
        Event::fake([ExportEvent::class]);

        $this->app->bind(ExportRequest::class, ExportRequestWithoutExcel::class);

        $this->signIn(admin: true);

        $this->postJson(route('api.laravel-options.option.export'))
            ->assertStatus(501)
            ->assertJsonPath('message', fn (string $message) => str_contains($message, 'composer require maatwebsite/excel'));

        Event::assertNotDispatched(ExportEvent::class);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }
}
