<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Setting;

class SmsSettingController extends BackendController
{
    public function index()
    {
        $this->data['sitetitle'] = 'SMS Settings';
        return view('admin/setting/sms', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->validateArray(), [], $niceNames);
        if ($request->has('twilio_disabled')) {
            $settingArray['twilio_disabled'] = 1;
        } else {
            $settingArray['twilio_disabled'] = 0;
        }

        Setting::set($settingArray);
        Setting::save();

        return redirect(route('admin.setting.all'))->withSuccess('SMS Setting Updated Successfully');
    }

    private function validateArray()
    {
        return [
            'twilio_auth_token'         => 'required|string|max:200',
            'twilio_account_sid'        => 'required|string|max:200',
            'twilio_from'               => 'required|string|max:20',
            'twilio_disabled'           => 'numeric',
        ];
    }
}
