<?php

namespace App\Models;

use App\Enums\LedgerType;
use App\Enums\TransactionType;
use App\User;
use Shipu\Watchable\Traits\HasModelEvents;

class Transaction extends BaseModel
{
    use HasModelEvents;
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $casts = [
        'meta' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function destinationuser()
    {
        return $this->belongsTo(User::class, 'destination_balance_id', 'balance_id');
    }

    public function shop() {
        return $this->belongsTo(Shop::class, 'meta->shop_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'meta->user_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'meta->order_id');
    }

    public function onModelCreated()
    {
        if ($this->type == TransactionType::ADDFUND) {
            $ledger = Ledger::where(['balance_id' => $this->destination_balance_id])->orderBy('id', 'desc')->first();

            $led               = new Ledger();
            $led->type         = LedgerType::CR;
            $led->amount       = $this->amount;
            $led->balance_id   = $this->destination_balance_id;
            $led->balance      = !blank($ledger) ? $ledger->balance + $this->amount : $this->amount;
            $led->creator_type = User::class;
            $led->editor_type  = User::class;
            $led->creator_id   = 1;
            $led->editor_id    = 1;
            $led->save();
        } elseif ($this->type == TransactionType::PAYMENT) {
            $ledger = Ledger::where(['balance_id' => $this->source_balance_id])->orderBy('id', 'desc')->first();

            $led               = new Ledger();
            $led->type         = LedgerType::DR;
            $led->amount       = $this->amount;
            $led->balance_id   = $this->source_balance_id;
            $led->balance      = !blank($ledger) ? $ledger->balance - $this->amount : $this->amount;
            $led->creator_type = User::class;
            $led->editor_type  = User::class;
            $led->creator_id   = 1;
            $led->editor_id    = 1;
            $led->save();

            $ledger = Ledger::where(['balance_id' => $this->destination_balance_id])->orderBy('id', 'desc')->first();

            $led               = new Ledger();
            $led->type         = LedgerType::CR;
            $led->amount       = $this->amount;
            $led->balance_id   = $this->destination_balance_id;
            $led->balance      = !blank($ledger) ? $ledger->balance + $this->amount : $this->amount;
            $led->creator_type = User::class;
            $led->editor_type  = User::class;
            $led->creator_id   = 1;
            $led->editor_id    = 1;
            $led->save();
        } elseif ($this->type == TransactionType::REFUND) {
            $ledger = Ledger::where(['balance_id' => $this->source_balance_id])->orderBy('id', 'desc')->first();

            $led               = new Ledger();
            $led->type         = LedgerType::DR;
            $led->amount       = $this->amount;
            $led->balance_id   = $this->source_balance_id;
            $led->balance      = !blank($ledger) ? $ledger->balance - $this->amount : $this->amount;
            $led->creator_type = User::class;
            $led->editor_type  = User::class;
            $led->creator_id   = 1;
            $led->editor_id    = 1;
            $led->save();

            $ledger = Ledger::where(['balance_id' => $this->destination_balance_id])->orderBy('id', 'desc')->first();

            $led               = new Ledger();
            $led->type         = LedgerType::CR;
            $led->amount       = $this->amount;
            $led->balance_id   = $this->destination_balance_id;
            $led->balance      = !blank($ledger) ? $ledger->balance + $this->amount : $this->amount;
            $led->creator_type = User::class;
            $led->editor_type  = User::class;
            $led->creator_id   = 1;
            $led->editor_id    = 1;
            $led->save();
        }
    }

    public function scopeUsermeta($query, $id) {
        return $query->where(['meta->user_id' => $id]); 
    }
}
