<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray( $request )
    {
        return [
            "id"                        => $this->id,
            "type"                      => $this->type,
            "source_balance_id"         => $this->source_balance_id,
            "destination_balance_id"    => $this->destination_balance_id,
            "amount"                    => $this->amount,
            "status"                    => trans('transaction_status.' . $this->status),
            "meta"                      => $this->meta,
            "invoice_id"                => $this->invoice_id,
            'created_at'                => $this->created_at->format('d M Y, h:i A'),
            'updated_at'                => $this->updated_at->format('d M Y, h:i A'),
        ];
    }
}
