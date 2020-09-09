<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopRequest extends FormRequest
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
        if ($this->shop) {
            $email    = ['required', 'email', 'string', Rule::unique("users", "email")->ignore($this->shop->user->id)];
            $username = ['required', 'string', Rule::unique("users", "username")->ignore($this->shop->user->id)];
        } else {
            $email    = ['required', 'email', 'string', 'unique:users,email'];
            $username = ['required', 'string', 'unique:users,username'];
        }

        return [
            'location_id'     => ['required', 'numeric'],
            'area_id'         => ['required', 'numeric'],
            'name'            => ['required', 'string', Rule::unique("shops", "name")->ignore($this->shop), 'max:191'],
            'description'     => ['nullable', 'string'],
            'delivery_charge' => ['nullable', 'numeric'],
            'lat'             => ['nullable'],
            'long'            => ['nullable'],
            'opening_time'    => ['nullable'],
            'closing_time'    => ['nullable'],
            'shopaddress'     => ['required', 'max:200'],
            'current_status'  => ['required', 'numeric'],
            'status'          => ['required', 'numeric'],
            'first_name'      => ['required', 'string'],
            'last_name'       => ['required', 'string'],
            'email'           => $email,
            'password'        => [$this->shop ? 'nullable' : 'required', 'min:6'],
            'image'           => 'image|mimes:jpeg,png,jpg|max:5098',
            'username'        => request('username') ? $username : ['nullable'],
            'phone'           => ['required', 'max:40'],
            'address'         => ['required', 'max:200'],
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
            'first_name'      => trans('validation.attributes.first_name'),
            'last_name'       => trans('validation.attributes.last_name'),
            'email'           => trans('validation.attributes.email'),
            'username'        => trans('validation.attributes.username'),
            'phone'           => trans('validation.attributes.phone'),
            'address'         => trans('validation.attributes.address'),
            'image'           => trans('validation.attributes.image'),
        ];
    }
}
