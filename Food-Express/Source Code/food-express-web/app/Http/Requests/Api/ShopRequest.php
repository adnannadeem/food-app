<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopRequest extends FormRequest
{
    protected $id;

    public function __construct($id = 0)
    {
        parent::__construct();
        $this->id = $id;
    }
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
            'location_id'     => ['required', 'numeric'],
            'area_id'         => ['required', 'numeric'],
            'name'            => ['required', 'string', Rule::unique("shops", "name")->ignore($this->id), 'max:191'],
            'description'     => ['nullable', 'string'],
            'delivery_charge' => ['nullable', 'numeric'],
            'lat'             => ['nullable'],
            'long'            => ['nullable'],
            'opening_time'    => ['nullable'],
            'closing_time'    => ['nullable'],
            'shopaddress'     => ['required', 'max:200'],
            'current_status'  => ['required', 'numeric'],
            'status'          => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'location_id'     => trans('validation.attributes.location_id'),
            'area_id'         => trans('validation.attributes.area_id'),
            'name'            => trans('validation.attributes.name'),
            'description'     => trans('validation.attributes.description'),
            'delivery_charge' => trans('validation.attributes.delivery_charge'),
            'lat'             => trans('validation.attributes.lat'),
            'long'            => trans('validation.attributes.long'),
            'opening_time'    => trans('validation.attributes.opening_time'),
            'closing_time'    => trans('validation.attributes.closing_time'),
            'shopaddress'     => trans('validation.attributes.address'),
            'current_status'  => trans('validation.attributes.current_status'),
            'status'          => trans('validation.attributes.status'),
        ];
    }
}
