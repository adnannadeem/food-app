<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Setting;

class NotificationSettingController extends BackendController
{
    public function index()
    {
        $this->data['sitetitle'] = 'Notification Setting';
        return view('admin.setting.notification', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames                = [];
        $notificationSettingArray = $this->validate($request, $this->validateArray(), [], $niceNames);

        Setting::set($notificationSettingArray);
        Setting::save();

        return redirect(route('admin.setting.all'))->withSuccess('The Notification Setting Updated Successfully');
    }

    private function validateArray()
    {
        return [
            'fcm_secret_key' => 'required|string|max:255',
        ];
    }
}
