<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Setting;

class EmailSettingController extends BackendController
{
    public function index()
    {
        $this->data['sitetitle'] = 'Email Setting';
        return view('admin.setting.email', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames         = [];
        $emailSettingArray = $this->validate($request, $this->validateArray(), [], $niceNames);

        if ($request->has('mail_disabled')) {
            $emailSettingArray['mail_disabled'] = 1;
        } else {
            $emailSettingArray['mail_disabled'] = 0;
        }

        Setting::set($emailSettingArray);
        Setting::save();

        return redirect(route('admin.setting.all'))->withSuccess('The Email Setting Updated Successfully');
    }

    private function validateArray()
    {
        return [
            'mail_host'         => 'required|string|max:100',
            'mail_port'         => 'required|string|max:100',
            'mail_username'     => 'required|string|max:100',
            'mail_password'     => 'required|string|max:100',
            'mail_from_name'    => 'required|string|max:100',
            'mail_from_address' => 'required|string|max:200',
            'mail_disabled'     => 'numeric',
        ];
    }
}
