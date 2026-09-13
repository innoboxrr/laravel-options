<?php

namespace Innoboxrr\LaravelOptions\Tests\Fixtures;

use Innoboxrr\LaravelOptions\Http\Requests\Option\ExportRequest;

/**
 * ExportRequest en una aplicacion que no instalo maatwebsite/excel.
 *
 * Excel esta en require-dev para probar la exportacion, asi que no se puede
 * desinstalar dentro del test: se sustituye la comprobacion.
 */
class ExportRequestWithoutExcel extends ExportRequest
{
    protected function excelIsInstalled(): bool
    {
        return false;
    }
}
