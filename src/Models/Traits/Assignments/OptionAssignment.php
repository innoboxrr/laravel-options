<?php

namespace Innoboxrr\LaravelOptions\Models\Traits\Assignments;

/**
 * Asignar y quitar registros relacionados a traves de una pivote.
 *
 * Es un ejemplo, y por eso va comentado: llamaba a una relacion `models()` que
 * Option no tiene, asi que usarlo tal cual fallaba. Cambia `models()` por tu
 * relacion belongsToMany y `model_id` por su clave, y descomentalo.
 */
trait OptionAssignment
{
    /*
    public function assignModel($request)
    {
        $operationResult = $this->models()->syncWithoutDetaching([
            $request->model_id => [
                // Pivot values
            ],
        ]);

        return response()->json([
            'model_id' => $request->model_id,
            'option_id' => $request->option_id,
            'operation' => $operationResult,
        ]);
    }

    public function deallocateModel($request)
    {
        $operationResult = $this->models()->detach($request->model_id);

        return response()->json([
            'model_id' => $request->model_id,
            'option_id' => $request->option_id,
            'operation' => $operationResult,
        ]);
    }
    */
}
