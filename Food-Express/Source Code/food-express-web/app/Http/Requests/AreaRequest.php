<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => ['required', 'string', Rule::unique("areas", "name")->ignore($this->area), 'max:255'],
            'location_id' => ['required', 'numeric'],
            'status'      => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'name'        => trans('validation.attributes.name'),
            'location_id' => trans('validation.attributes.location_id'),
            'status'      => trans('validation.attributes.status'),
        ];
    }

}
