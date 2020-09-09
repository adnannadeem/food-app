<?php

namespace App\Models;

use App\Models\Invoice;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Order extends Model implements HasMedia
{
    use HasModelEvents, HasMediaTrait;

    protected $table    = 'orders';
    protected $fillable = ['shop_id', 'user_id', 'total', 'sub_total', 'delivery_charge', 'status', 'payment_status', 'paid_amount', 'address', 'payment_method', 'mobile', 'lat', 'long', 'misc', 'invoice_id'];

    public function items()
    {
        return $this->hasMany(OrderLineItem::class)->with('product')->with('shop');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id', 'id');
    }

    public function getOrderCodeAttribute()
    {
        return json_decode($this->misc)->order_code;
    }

    public function getRemarksAttribute()
    {
        return json_decode($this->misc)->remarks;
    }

    private function onModelCreated()
    {
        $invoice_id = Str::uuid();

        $invoice               = new Invoice;
        $invoice->id           = $invoice_id;
        $invoice->meta         = ['order_id' => $this->id, 'amount' => $this->total, 'user_id' => $this->user_id];
        $invoice->creator_type = User::class;
        $invoice->editor_type  = User::class;
        $invoice->creator_id   = 1;
        $invoice->editor_id    = 1;
        $invoice->save();

        $this->invoice_id = $invoice_id;
        $this->save();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Invoice::class);
    }

    public function getGetOrderStatusAttribute()
    {
        return __('order_status.' . $this->status);
    }

    public function getGetPaymentStatusAttribute()
    {
        return __('payment_status.' . $this->payment_status);
    }

    public function getGetPaymentMethodAttribute()
    {
        return __('payment_method.' . $this->payment_method);
    }

    public function getImageAttribute()
    {
        if (!empty($this->getFirstMediaUrl('order'))) {
            return asset($this->getFirstMediaUrl('order'));
        }
        return asset('assets/img/default/order.png');
    }
}
