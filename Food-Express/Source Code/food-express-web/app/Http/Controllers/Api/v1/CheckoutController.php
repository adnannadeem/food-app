<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 24/4/20
 * Time: 1:30 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public $owner_id  = 1;
    public $comission = 20;
    public $order_id  = 2;

    public function payment(Request $request)
    {
        $order_id = $request->order_id;
        $order    = Order::where(['payment_status' => PaymentStatus::UNPAID])->findOrFail($order_id);
        if (!blank($order)) {
            $this->generateLedger($order);

            return response()->json([
                'status'  => 200,
                'message' => 'You paid order payment successfully',
            ], 200);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'Your order not found',
        ], 404);
    }

    public function cancel(Request $request)
    {
        $order_id = $request->order_id;
        $order    = Order::where(['payment_status' => PaymentStatus::PAID])->where('status', '!=', OrderStatus::CANCEL)->findOrFail($order_id);

        if (!blank($order)) {
            $this->generateCancelLedger($order);
            return response()->json([
                'status'  => 200,
                'message' => 'You order payment cancel successfully',
            ], 200);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'Your order not found',
        ], 404);
    }

    public function invoice(Request $request)
    {
        $order_id = $request->order_id;
        $order    = Order::where('invoice_id', null)->find($order_id);
        if (!blank($order)) {
            $this->generateInvoice($order);
            return response()->json([
                'status'  => 200,
                'message' => 'Your invoice created successfully',
            ], 200);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'Your invoice already created or order not found',
        ], 404);
    }

    private function generateLedger($order)
    {
        $invoice = Invoice::findOrFail($order->invoice_id);
        $user    = User::find($order->user_id);

        $shopowner_balance_id = $order->items->first()->shop->user->balance_id;

        $payment_method = PaymentMethod::CASH_ON_DELIVERY;
        $meta           = [
            'invoice_id'     => $invoice->id,
            'user_id'        => $user->id,
            'payment_method' => $payment_method,
        ];

        $this->addTransaction(TransactionType::ADDFUND, null, $user->balance_id, $invoice->meta['amount'], $meta);
        $this->addTransaction(TransactionType::PAYMENT, $user->balance_id, $this->owner_id, $invoice->meta['amount'], $meta);

        $amount = $invoice->meta['amount'] ? ($invoice->meta['amount'] - (($this->comission / 100) * $invoice->meta['amount'])) : 0;

        $this->addTransaction(TransactionType::PAYMENT, $this->owner_id, $shopowner_balance_id, $amount, $meta);

        $order->payment_status = PaymentStatus::PAID;
        $order->payment_method = $meta['payment_method'];
        $order->paid_amount    = $invoice->meta['amount'];
        $order->save();
    }

    private function generateCancelLedger($order)
    {
        $invoice = Invoice::findOrFail($order->invoice_id);
        $user    = User::find($order->user_id);

        $shopowner_balance_id = $order->items->first()->shop->user->balance_id;

        $payment_method = PaymentMethod::CASH_ON_DELIVERY;
        $meta           = [
            'invoice_id'     => $invoice->id,
            'user_id'        => $user->id,
            'payment_method' => $payment_method,
        ];

        $amount = $invoice->meta['amount'] ? ($invoice->meta['amount'] - (($this->comission / 100) * $invoice->meta['amount'])) : 0;

        $this->addTransaction(TransactionType::LIFTING, $shopowner_balance_id, $this->owner_id, $amount, $meta);
        $this->addTransaction(TransactionType::LIFTING, $this->owner_id, $user->balance_id, $invoice->meta['amount'], $meta);

        $order->payment_status = PaymentStatus::UNPAID;
        $order->payment_method = $meta['payment_method'];
        $order->paid_amount    = 0;
        $order->status         = OrderStatus::CANCEL;
        $order->save();
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
        $transaction->creator_type           = User::class;
        $transaction->editor_type            = User::class;
        $transaction->creator_id             = 1;
        $transaction->editor_id              = 1;
        $transaction->save();
    }

    private function generateInvoice($order)
    {
        $invoice_id = Str::uuid();

        $invoice               = new Invoice;
        $invoice->id           = $invoice_id;
        $invoice->meta         = ['order_id' => $order->id, 'amount' => $order->total, 'user_id' => $order->user_id];
        $invoice->creator_type = User::class;
        $invoice->editor_type  = User::class;
        $invoice->creator_id   = 1;
        $invoice->editor_id    = 1;
        $invoice->save();

        $order->invoice_id = $invoice_id;
        $order->save();
    }
}
