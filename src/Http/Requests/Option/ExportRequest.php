<?php

namespace Innoboxrr\LaravelOptions\Http\Requests\Option;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\ExportEvent;
use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
{

    protected function prepareForValidation()
    {

        $this->merge([
            'paginate' => 0,
            'managed' => true,
            'except_view_any' => true,
        ]);

    }

    public function authorize()
    {
        return $this->user()->can('export', Option::class);
    }

    public function rules()
    {
        return [
            //
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }

    public function attributes()
    {
        return [
            //
        ];
    }

    protected function passedValidation()
    {
        //
    }

    public function handle()
    {

        // maatwebsite/excel es sugerido, no requerido. Sin el, el listener
        // reventaba con "Class not found" y el panel enseñaba un 500 opaco.
        if (! $this->excelIsInstalled()) {
            return response()->json([
                'message' => 'La exportación de opciones necesita maatwebsite/excel. Instálalo con: composer require maatwebsite/excel',
            ], 501);
        }

        try {
            event(new ExportEvent($this->all(), $this->user()));
        } catch (\Throwable $exception) {
            // El detalle va al log; a quien exporta, un mensaje que entienda.
            report($exception);

            return response()->json(['message' => 'No se pudo generar la exportación.'], 500);
        }

        return response()->json(['status' => true]);

    }

    protected function excelIsInstalled(): bool
    {
        return class_exists(\Maatwebsite\Excel\Excel::class);
    }

}
