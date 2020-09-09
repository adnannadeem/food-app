<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 19/4/20
 * Time: 5:59 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\MyTransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $transactions = Transaction::orderBy('id', 'desc')->get();
        $types        = trans('transaction_types');
        $methods      = trans('payment_methods');

        $i                = 1;
        $transactionArray = [];
        if (!blank($transactions)) {
            foreach ($transactions as $transaction) {
                if (!$this->showTransactionItem($transaction)) {
                    continue;
                }

                $transactionArray[$i]['id'] = $i;
                if (UserRole::CUSTOMER == auth()->user()->roles) {
                    $name = ($transaction->destination_balance_id != auth()->user()->balance_id) ? $transaction->shop->name : $methods[$transaction->meta['payment_method']] ?? '';

                    $transactionArray[$i]['user'] = $name;
                } else if(UserRole::ADMIN == auth()->user()->roles) {
                    $name = ($transaction->source_balance_id == 1) ? $transaction->shop->name : $transaction->destinationuser->name;
                    $transactionArray[$i]['user'] = $name;
                } else {
                    $name = ($transaction->source_balance_id == 1) ? $transaction->user->name : $methods[$transaction->meta['payment_method']] ?? '';
                    $transactionArray[$i]['user'] = $name;
                }

                $transactionArray[$i]['order_id']   = $transaction->meta['order_id'];
                $transactionArray[$i]['invoice_id'] = $transaction->meta['invoice_id'];
                $transactionArray[$i]['type']       = $types[$transaction->type] ?? '';
                $transactionArray[$i]['date']       = $transaction->created_at->format('l, d M Y h:i A');
                
                if(UserRole::SHOPOWNER == auth()->user()->roles) {
                    $transactionArray[$i]['amount']     = (($transaction->source_balance_id == 1) ? '+' : '-') . " " . currencyFormat($transaction->amount);
                } else {
                    $transactionArray[$i]['amount']     = (($transaction->source_balance_id == null) ? '+' : '-') . " " . currencyFormat($transaction->amount);
                }

                $i++;
            }
        }

        return response()->json([
            'status' => 200,
            'data'   => MyTransactionResource::collection($transactionArray),
        ], 200);
    }

    private function showTransactionItem($transaction)
    {
        if ((auth()->user()->balance_id == 1) && ($transaction->destination_balance_id != 1)) {
            return true;
        } else if ((auth()->user()->roles == UserRole::CUSTOMER) && (($transaction->source_balance_id == null && ($transaction->destination_balance_id == auth()->user()->balance_id)) || ($transaction->source_balance_id == auth()->user()->balance_id))) {
            return true;
        } else if (auth()->user()->roles == UserRole::SHOPOWNER) {
            if (($transaction->source_balance_id == null && ($transaction->destination_balance_id == auth()->user()->balance_id)) || ($transaction->source_balance_id == auth()->user()->balance_id) || ($transaction->destination_balance_id == auth()->user()->balance_id)) {
                return true;
            }
        }
        return false;
    }

}
