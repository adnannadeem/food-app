<?php

use Illuminate\Database\Seeder;
use Setting as SeederSetting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingArray['site_name']                   = 'Food Express';
        $settingArray['site_logo']                   = 'logo.png';
        $settingArray['site_footer']                 = '@ All Rights Reserved';
        $settingArray['site_description']            = 'Food Express is the best online food order management system.';
        $settingArray['currency_name']               = 'USD';
        $settingArray['currency_code']               = '$';
        $settingArray['geolocation_distance_radius'] = 20;
        $settingArray['shopowner_terms_condition']   = '';
        $settingArray['customer_terms_condition']    = '';
        $settingArray['order_commission_percentage'] = 0;
        $settingArray['timezone']                    = '';
        $settingArray['twilio_auth_token']           = '';
        $settingArray['twilio_account_sid']          = '';
        $settingArray['twilio_from']                 = '';
        $settingArray['twilio_disabled']             = 1;
        $settingArray['stripe_key']                  = '';
        $settingArray['stripe_secret']               = '';
        $settingArray['razorpay_key']                = '';
        $settingArray['razorpay_secret']             = '';
        $settingArray['paystack_key']                = '';
        $settingArray['mail_host']                   = '';
        $settingArray['mail_port']                   = '';
        $settingArray['mail_username']               = '';
        $settingArray['mail_password']               = '';
        $settingArray['mail_from_name']              = '';
        $settingArray['mail_from_address']           = '';
        $settingArray['mail_disabled']               = 1;
        $settingArray['fcm_secret_key']              = '';
        $settingArray['facebook_key']                = '';
        $settingArray['facebook_secret']             = '';
        $settingArray['facebook_url']                = '';
        $settingArray['google_key']                  = '';
        $settingArray['google_secret']               = '';
        $settingArray['google_url']                  = '';
        $settingArray['otp_type_checking']           = 'email';
        $settingArray['otp_digit_limit']             = 6;
        $settingArray['otp_expire_time']             = 10;
        $settingArray['purchase_code']               = session()->has('purchase_code') ? session()->get('purchase_code') : "";
        $settingArray['purchase_username']           = session()->has('purchase_username') ? session()->get('purchase_username') : "";
        SeederSetting::set($settingArray);
        SeederSetting::save();
    }
}
