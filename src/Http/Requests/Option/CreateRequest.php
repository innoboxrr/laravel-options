<?php

namespace Innoboxrr\LaravelOptions\Http\Requests\Option;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Http\Resources\Models\OptionResource;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\CreateEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{

    /**
     * Hay opciones con contenido estructurado, como theme. El panel puede
     * mandarlas como arreglo: se guardan como JSON, que es como las siembra
     * el seeder y como las devuelve decodificadas Option::value().
     */
    protected function prepareForValidation()
    {
        if (is_array($this->input('value'))) {
            $this->merge(['value' => json_encode($this->input('value'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        }
    }

    public function authorize()
    {

        return $this->user()->can('create', Option::class);

    }

    /**
     * value admite vacio: la columna es nullable y una opcion puede existir
     * sin valor todavia. Laravel convierte "" en null, y con required no se
     * podia crear.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'key' => ['required', 'string', 'max:255', Rule::unique('options', 'key')],
            'value' => 'nullable|string',
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

        $option = (new Option)->createModel($this);

        $response = new OptionResource($option);

        event(new CreateEvent($option, $this->all(), $response));

        return $response;

    }

}
