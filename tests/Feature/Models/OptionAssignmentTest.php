<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionAssignmentTest extends TestCase
{
    /**
     * assignModel() y deallocateModel() llamaban a models(), una relacion que
     * Option no tiene: cualquier llamada fallaba. Mientras no haya relacion
     * que asignar, Option no los ofrece.
     */
    public function test_option_no_ofrece_asignaciones_sobre_una_relacion_que_no_existe(): void
    {
        $this->assertFalse(method_exists(Option::class, 'models'));
        $this->assertFalse(method_exists(Option::class, 'assignModel'));
        $this->assertFalse(method_exists(Option::class, 'deallocateModel'));
    }
}
