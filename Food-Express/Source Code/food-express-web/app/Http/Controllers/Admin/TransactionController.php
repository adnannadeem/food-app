<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Enums\TransactionType;
use App\Http\Controllers\BackendController;
use App\Models\Transaction;
use App\User;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class TransactionController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Transactions';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['users'] = User::get();
        return view('admin.transaction.index', $this->data);
    }

    public function getTransaction(Request $request)
    {
        if (request()->ajax()) {
            $form_date = date('Y-m-d H:i:s', strtotime($request->form_date. "00:00:00"));
            $to_date   = date('Y-m-d H:i:s', strtotime($request->to_date. "23:59:59"));

            if(strtotime($to_date) < strtotime($form_date)) {
                $to_date   = date('Y-m-d'). " 23:59:59";
            }
            
            $transactions = Transaction::whereBetween('created_at', [$form_date, $to_date])->orderBy('id', 'desc')->get();

            $types = trans('transaction_types');

            $i                = 1;
            $transactionArray = [];
            if (!blank($transactions)) {
                foreach ($transactions as $transaction) {
                    if ((int) $request->user_id && ($transaction->meta['user_id'] != $request->user_id)) {
                        continue;
                    }

                    if(!$this->showTransactionItem($transaction)) {
                        continue;
                    }

                    $transactionArray[$i]           = $transaction;
                    $transactionArray[$i]['user']   = $transaction->destinationuser->name;
                    $transactionArray[$i]['type']   = $types[$transaction->type] ?? '';
                    $transactionArray[$i]['date']   = $transaction->created_at->format('l, d M Y h:i A');
                    $transactionArray[$i]['amount'] = (($transaction->source_balance_id ==  null) ? '-' : '+') . " ". currencyFormat($transaction->amount);;
                    $transactionArray[$i]['setID']  = $i;
                    $i++;
                }
            }
            return Datatables::of($transactionArray)
                ->editColumn('id', function ($transaction) {
                    return $transaction->setID;
                })
                ->make(true);
        }
    }

    private function showTransactionItem($transaction) {
        if(auth()->user()->balance_id == 1) {
            if($transaction->destination_balance_id != 1) {
                return true;
            }
        } else {
            if(($transaction->source_balance_id == null && ($transaction->destination_balance_id == auth()->user()->balance_id)) || ($transaction->source_balance_id == auth()->user()->balance_id)) {
                return true;
            }    
        }
        return false;
    }
}
