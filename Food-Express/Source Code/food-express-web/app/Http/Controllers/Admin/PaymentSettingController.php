<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Setting;

class PaymentSettingController extends BackendController
{
    public function index()
    {
        $this->data['sitetitle'] = 'Payment Setting';
        return view('admin.setting.payment', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames = [];
        if ($request->gateway2 == 'stripe') {
            $settingArray = $this->validate($request, $this->validate_stripe(), [], $niceNames);
        } else if ($request->gateway2 == 'razorpay') {
            $settingArray = $this->validate($request, $this->validate_razorpay(), [], $niceNames);
        } else if ($request->gateway2 == 'paystack') {
            $settingArray = $this->validate($request, $this->validate_paystack(), [], $niceNames);
        } 
        unset($settingArray['gateway2']);

        if (!blank($settingArray)) {
            Setting::set($settingArray);
            Setting::save();
        }

        return redirect(route('admin.setting.all'))->withSuccess('The Payment Setting Updated Successfully');
    }

    private function validate_stripe()
    {
        return [
            'gateway2'       => 'required|string|max:20',
            'stripe_key'    => 'required|string|max:255',
            'stripe_secret' => 'required|string|max:255',
        ];
    }

    private function validate_razorpay()
    {
        return [
            'gateway2'         => 'required|string|max:20',
            'razorpay_key'    => 'required|string|max:255',
            'razorpay_secret' => 'required|string|max:255',
        ];
    }

    private function validate_paystack()
    {
        return [
            'gateway2'         => 'required|string|max:20',
            'paystack_key'    => 'required|string|max:255',
        ];
    }
}
