<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Setting;

class SocialLoginSettingController extends Controller
{
    public function index()
    {
        $this->data['sitetitle'] = 'Payment Setting';
        return view('admin.setting.payment', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames = [];
        if ($request->gateway1 == 'facebook') {
            $settingArray = $this->validate($request, $this->validate_stripe(), [], $niceNames);
        } else if ($request->gateway1 == 'google') {
            $settingArray = $this->validate($request, $this->validate_razorpay(), [], $niceNames);
        }
        unset($settingArray['gateway1']);

        if (!blank($settingArray)) {
            Setting::set($settingArray);
            Setting::save();
        }

        return redirect(route('admin.setting.all'))->withSuccess('The Login Setting Updated Successfully');
    }

    private function validate_stripe()
    {
        return [
            'gateway1'        => 'required|string|max:20',
            'facebook_key'    => 'required|string|max:255',
            'facebook_secret' => 'required|string|max:255',
            'facebook_url'    => 'required|string|max:255',
        ];
    }

    private function validate_razorpay()
    {
        return [
            'gateway1'      => 'required|string|max:20',
            'google_key'    => 'required|string|max:255',
            'google_secret' => 'required|string|max:255',
            'google_url'    => 'required|string|max:255',
        ];
    }
}
