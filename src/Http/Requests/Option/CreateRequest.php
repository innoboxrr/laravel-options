<?php

namespace Innoboxrr\LaravelOptions\Http\Requests\Option;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Http\Resources\Models\OptionResource;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\CreateEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        //
    }

    public function authorize()
    {

        return $this->user()->can('create', Option::class);

    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:options,key',
            'value' => 'required|string',
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
