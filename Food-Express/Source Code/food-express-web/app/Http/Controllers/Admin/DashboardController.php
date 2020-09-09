<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\BackendController;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends BackendController
{
    public function index()
    {
        $this->data['months'] = [1=>'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $this->data['orders']      = Order::orderBy('id', 'desc')->whereYear('created_at', date('Y-m-d'))->get();
        $this->data['todayOrders'] = Order::orderBy('id', 'desc')->whereDate('created_at', date('Y-m-d'))->get();

        $orderPending = 0;
        $orderCancel  = 0;
        $orderProcess = 0;
        $todayIncome  = 0;
        if (!blank($this->data['todayOrders'])) {
            foreach ($this->data['todayOrders'] as $todayOrder) {
                if (OrderStatus::PENDING == $todayOrder->status) {
                    $orderPending++;
                }
                if (OrderStatus::CANCEL == $todayOrder->status) {
                    $orderCancel++;
                }
                if (OrderStatus::PROCESS == $todayOrder->status) {
                    $orderProcess++;
                }
                if (OrderStatus::CANCEL != $todayOrder->status) {
                    $todayIncome = $todayIncome + $todayOrder->paid_amount;
                }
            }
        }
        $this->data['orderPending'] = $orderPending;
        $this->data['orderCancel']  = $orderCancel;
        $this->data['orderProcess'] = $orderProcess;
        $this->data['todayIncome']  = $todayIncome;

        $monthWiseTotalIncome    = [];
        $monthDayWiseTotalIncome = [];
        $monthWiseTotalOrder     = [];
        $monthDayWiseTotalOrder  = [];
        if (!blank($this->data['orders'])) {
            foreach ($this->data['orders'] as $order) {

                $monthNumber = (int) date('m', strtotime($order->created_at));
                $dayNumber   = (int) date('d', strtotime($order->created_at));

                if (OrderStatus::CANCEL != $order->status) {
                    if (!isset($monthDayWiseTotalIncome[$monthNumber][$dayNumber])) {
                        $monthDayWiseTotalIncome[$monthNumber][$dayNumber] = 0;
                    }
                    $monthDayWiseTotalIncome[$monthNumber][$dayNumber] += $order->paid_amount;

                    if (!isset($monthWiseTotalIncome[$monthNumber])) {
                        $monthWiseTotalIncome[$monthNumber] = 0;
                    }
                    $monthWiseTotalIncome[$monthNumber] += $order->paid_amount;
                }

                if (!isset($monthDayWiseTotalOrder[$monthNumber][$dayNumber])) {
                    $monthDayWiseTotalOrder[$monthNumber][$dayNumber] = 0;
                }
                $monthDayWiseTotalOrder[$monthNumber][$dayNumber] += 1;

                if (!isset($monthWiseTotalOrder[$monthNumber])) {
                    $monthWiseTotalOrder[$monthNumber] = 0;
                }
                $monthWiseTotalOrder[$monthNumber] += 1;
            }
        }

        $this->data['monthWiseTotalIncome']    = $monthWiseTotalIncome;
        $this->data['monthDayWiseTotalIncome'] = $monthDayWiseTotalIncome;
        $this->data['monthWiseTotalOrder']     = $monthWiseTotalOrder;
        $this->data['monthDayWiseTotalOrder']  = $monthDayWiseTotalOrder;

        return view('admin.dashboard.index', $this->data);
    }

    public function daywiseIncomeOrder(Request $request)
    {
        $type          = $request->type;
        $monthID       = $request->monthID;
        $dayWiseData   = $request->dayWiseData;
        $showChartData = [];

        if ($type && $monthID) {

            $days        = date('t', mktime(0, 0, 0, $monthID, 1, date('Y')));
            $dayWiseData = json_decode($dayWiseData, true);

            for ($i = 1; $i <= $days; $i++) {
                $showChartData[$i] = isset($dayWiseData[$i]) ? $dayWiseData[$i] : 0;
            }
        } else {
            for ($i = 1; $i <= 31; $i++) {
                $showChartData[$i] = 0;
            }
        }

        echo json_encode($showChartData);
    }

}
