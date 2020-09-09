<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 18/4/20
 * Time: 8:59 PM
 */

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopOrderRequest extends FormRequest
{
    protected $id;
    public function __construct($id)
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
        if ($this->id) {
            return [
                'items'            => ['json'],
                'delivery_charge'  => ['numeric'],
                'customer_mobile'  => ['string'],
                'delivery_address' => ['string'],
            ];
        } else {
            return [
                'name'             => ['required'],
                'phone'            => ['required'],
                'email'            => ['nullable', 'email', 'max:100'],
                'items'            => ['required', 'json'],
                'delivery_charge'  => ['required', 'numeric'],
                'customer_mobile'  => ['required', 'string'],
                'delivery_address' => ['required', 'string'],
                'customer_lat'     => ['required'],
                'customer_long'    => ['required'],
                'total'            => ['required'],
            ];
        }

    }
}
