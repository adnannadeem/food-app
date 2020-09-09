<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use App\Libraries\MyString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Setting;

class SettingController extends BackendController
{

    public function all()
    {
        $types = Setting::all();
        return view('admin/setting/settings', compact('types'));
    }

    public function index()
    {
        $this->data['sitetitle'] = 'Settings';
        return view('admin/setting/index', $this->data);
    }

    public function update(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->validateArray(), [], $niceNames);

        if ($request->hasFile('site_logo')) {
            $site_logo                 = request('site_logo');
            $settingArray['site_logo'] = $site_logo->getClientOriginalName();
            request()->site_logo->move(public_path('images'), $settingArray['site_logo']);
        } else {
            unset($settingArray['site_logo']);
        }

        if (isset($settingArray['timezone'])) {
            MyString::setEnv('APP_TIMEZONE', $settingArray['timezone']);
            Artisan::call('optimize:clear');
        }

        Setting::set($settingArray);
        Setting::save();

        return redirect(route('admin.setting.all'))->withSuccess('Setting Updated Successfully');
    }

    private function validateArray()
    {
        return [
            'site_name'                   => 'required|string|max:100',
            'site_logo'                   => 'nullable|mimes:jpeg,jpg,png,gif|max:3096',
            'site_footer'                 => 'required|string|max:200',
            'site_description'            => 'required|string|max:500',
            'currency_name'               => 'required|string|max:20',
            'currency_code'               => 'required|string|max:20',
            'shopowner_terms_condition'   => 'required|string',
            'customer_terms_condition'    => 'required|string',
            'geolocation_distance_radius' => 'required|numeric',
            'order_commission_percentage' => 'required|numeric',
            'timezone'                    => 'required|string',
        ];
    }

}
