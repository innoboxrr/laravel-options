<?php

namespace Innoboxrr\LaravelOptions\Http\Requests\Option;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Http\Resources\Models\OptionResource;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\ForceDeleteEvent;
use Illuminate\Foundation\Http\FormRequest;

class ForceDeleteRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        //
    }

    public function authorize()
    {

        $option = Option::withTrashed()->findOrFail($this->option_id);
        
        return $this->user()->can('forceDelete', $option);

    }

    public function rules()
    {
        return [
            'option_id' => 'required|numeric'
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

        $option = Option::withTrashed()->findOrFail($this->option_id);

        $option->forceDeleteModel();

        $response = new OptionResource($option);

        event(new ForceDeleteEvent($option, $this->all(), $response));

        return $response;

    }
    
}
