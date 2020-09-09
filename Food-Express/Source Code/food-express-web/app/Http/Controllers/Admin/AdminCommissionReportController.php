<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use App\Models\Shop;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminCommissionReportController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Admin Sales Report';
    }

    public function index(Request $request)
    {
        $this->data['showView']      = false;
        $this->data['set_shop_id']   = '';
        $this->data['set_from_date'] = '';
        $this->data['set_to_date']   = '';

        $this->data['shops'] = Shop::all();

        if ($_POST) {

            $request->validate([
                'shop_id'   => 'required|numeric',
                'from_date' => 'nullable|date',
                'to_date'   => 'nullable|date|after_or_equal:from_date',
            ]);

            $this->data['showView']      = true;
            $this->data['set_shop_id']   = $request->shop_id;
            $this->data['set_from_date'] = $request->from_date;
            $this->data['set_to_date']   = $request->to_date;

            if ((int) $request->shop_id) {
                $shop_id = $request->shop_id;
            }

            $dateBetween = [];
            if ($request->from_date != '' && $request->to_date != '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($request->to_date)) . ' 23:59:59';
            }

            if (!blank($dateBetween)) {
                $transactions = Transaction::where(['source_balance_id' => 1])->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->get();
            } else {
                $transactions = Transaction::where(['source_balance_id' => 1])->get();
            }

            $this->data['transactions'] = [];
            if($transactions) {
                foreach ($transactions as $key => $transaction) {
                    if(isset($transaction->meta['shop_id']) && ($transaction->meta['shop_id'] == $shop_id)) {
                        $this->data['transactions'][] = $transaction;
                    }
                }
            }
        }

        return view('admin.report.admincommission.index', $this->data);
    }

}
