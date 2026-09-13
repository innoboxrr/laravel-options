<?php

namespace Innoboxrr\LaravelOptions\Http\Requests\Option;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Http\Resources\Models\OptionResource;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\UpdateEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{

    /**
     * Igual que al crear: un valor estructurado llega como arreglo y se
     * guarda como JSON.
     */
    protected function prepareForValidation()
    {
        if (is_array($this->input('value'))) {
            $this->merge(['value' => json_encode($this->input('value'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        }
    }

    public function authorize()
    {

        $option = Option::findOrFail($this->option_id);

        return $this->user()->can('update', $option);

    }

    /**
     * key puede no venir, pero si viene no puede ser nula: la columna es NOT
     * NULL y con nullable la peticion pasaba la validacion y terminaba en un
     * 500. La unicidad ignora la propia opcion con ignore(), que enlaza el id
     * como valor en vez de concatenarlo a la regla.
     */
    public function rules()
    {
        return [
            'option_id' => 'required|integer|exists:options,id',
            'name' => 'nullable|string|max:255',
            'key' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('options', 'key')->ignore($this->option_id)],
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

        $option = Option::findOrFail($this->option_id);

        $option = $option->updateModel($this);

        $response = new OptionResource($option);

        event(new UpdateEvent($option, $this->all(), $response));

        return $response;

    }

}
