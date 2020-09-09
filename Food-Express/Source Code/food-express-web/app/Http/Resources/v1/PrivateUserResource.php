<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status'  => 200,
            'message' => 'Successfully Login',
            'data'    => [
                'id'       => $this->id,
                'email'    => $this->email,
                'username' => $this->username,
                'phone'    => $this->phone,
                'address'  => $this->address,
                'status'   => $this->status,
                'applied'  => $this->applied,
                'name'     => $this->first_name . ' ' . $this->last_name,
                'image'    => $this->images,
            ],
        ];
    }
}
