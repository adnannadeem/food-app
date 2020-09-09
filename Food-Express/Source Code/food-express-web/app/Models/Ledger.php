<?php

namespace App\Models;

class Ledger extends BaseModel
{
    protected $fillable = [
        'type',
        'amount',
        'balance_id',
        'balance',
    ];

    public function onModelCreated()
    {
        $balance          = Balance::find($this->balance_id);
        $balance->balance = $this->balance;
        $balance->save();
    }
}
