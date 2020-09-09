<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Transaction;
use App\User;

class CheckoutService
{
    public $owner_id = 1;
    public $comission;
    public $order_id;
    public $payment_method = PaymentMethod::CASH_ON_DELIVERY;

    public function __construct($order_id, $payment_method)
    {
        $this->comission      = setting('order_commission_percentage');
        $this->order_id       = $order_id;
        $this->payment_method = $payment_method;
    }

    public function payment()
    {
        $order = Order::where(['payment_status' => PaymentStatus::UNPAID])->find($this->order_id);
        if (!blank($order)) {
            return $this->generateLedger($order);
        }
        return [
            'status'  => false,
            'message' => 'Your order not found or you already paid',
        ];
    }

    public function cancel()
    {
        $order = Order::where(['payment_status' => PaymentStatus::PAID])->where('status', '!=', OrderStatus::CANCEL)->find($this->order_id);

        if (!blank($order)) {
            return $this->generateCancelLedger($order);
        }
        return [
            'status'  => false,
            'message' => 'Your order not found or you already unpaid',
        ];
    }

    private function generateLedger($order)
    {
        $invoice = Invoice::findOrFail($order->invoice_id);
        $user    = User::find($order->user_id);

        $shopowner_balance_id = isset($order->items->first()->shop->user->balance_id) ? $order->items->first()->shop->user->balance_id : 0;

        if (!$shopowner_balance_id) {
            return [
                'status'  => false,
                'message' => 'Shop owner balance missing',
            ];
        }

        $meta = [
            'shop_id'        => $order->shop_id,
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'user_id'        => $user->id,
            'payment_method' => $this->payment_method,
        ];

        $this->addTransaction(TransactionType::ADDFUND, null, $user->balance_id, $invoice->meta['amount'], $meta);
        $this->addTransaction(TransactionType::PAYMENT, $user->balance_id, $this->owner_id, $invoice->meta['amount'], $meta);

        $amount = $invoice->meta['amount'] ? ($invoice->meta['amount'] - (($this->comission / 100) * $invoice->meta['amount'])) : 0;

        $this->addTransaction(TransactionType::PAYMENT, $this->owner_id, $shopowner_balance_id, $amount, $meta);

        $order->payment_status = PaymentStatus::PAID;
        $order->payment_method = $meta['payment_method'];
        $order->paid_amount    = $invoice->meta['amount'];
        $order->save();

        return [
            'status'  => true,
            'message' => 'You paid order payment successfully',
        ];
    }

    private function generateCancelLedger($order)
    {
        $invoice = Invoice::findOrFail($order->invoice_id);
        $user    = User::find($order->user_id);

        $shopowner_balance_id = isset($order->items->first()->shop->user->balance_id) ? $order->items->first()->shop->user->balance_id : 0;
        if (!$shopowner_balance_id) {
            return [
                'status'  => false,
                'message' => 'Shop owner missing',
            ];
        }

        $meta = [
            'shop_id'        => $order->shop_id,
            'order_id'       => $order->id,
            'invoice_id'     => $order->invoice_id,
            'user_id'        => $user->id,
            'payment_method' => $this->payment_method,
        ];

        $amount = $invoice->meta['amount'] ? ($invoice->meta['amount'] - (($this->comission / 100) * $invoice->meta['amount'])) : 0;

        $this->addTransaction(TransactionType::REFUND, $shopowner_balance_id, $this->owner_id, $amount, $meta);
        $this->addTransaction(TransactionType::REFUND, $this->owner_id, $user->balance_id, $invoice->meta['amount'], $meta);

        $order->payment_status = PaymentStatus::UNPAID;
        $order->payment_method = $meta['payment_method'];
        $order->paid_amount    = 0;
        $order->status         = OrderStatus::CANCEL;
        $order->save();

        return [
            'status'  => true,
            'message' => 'You order payment cancel successfully',
        ];
    }

    private function addTransaction($type, $source, $destination, $amount, $meta)
    {
        $transaction                         = new Transaction;
        $transaction->type                   = $type;
        $transaction->source_balance_id      = $source;
        $transaction->destination_balance_id = $destination;
        $transaction->amount                 = $amount;
        $transaction->status                 = 1;
        $transaction->meta                   = $meta;
        $transaction->invoice_id             = $meta['invoice_id'];
        $transaction->creator_type           = User::class;
        $transaction->editor_type            = User::class;
        $transaction->creator_id             = 1;
        $transaction->editor_id              = 1;
        $transaction->save();
    }
}
