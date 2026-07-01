<?php

namespace Database\Seeders;

use App\Models\General_Setting;
use Illuminate\Database\Seeder;

class General_Setting_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        General_Setting::truncate();
        $data = [
            // App Setting
            ['key' => 'App_Name', 'value' => ''],
            ['key' => 'host_email', 'value' => ''],
            ['key' => 'app_version', 'value' => ''],
            ['key' => 'Author', 'value' => ''],
            ['key' => 'email', 'value' => ''],
            ['key' => 'contact', 'value' => ''],
            ['key' => 'app_desripation', 'value' => ''],
            ['key' => 'privacy_policy', 'value' => ''],
            ['key' => 'app_name', 'value' => ''],
            ['key' => 'instrucation', 'value' => ''],
            ['key' => 'app_logo', 'value' => ''],
            ['key' => 'website', 'value' => ''],
            // Currency Settings
            ['key' => 'currency', 'value' => ''],
            ['key' => 'currency_code', 'value' => ''],
            // Payment
            // ['key' => 'payment_1', 'value' => ''],
            // ['key' => 'payment_2', 'value' => ''],
            // ['key' => 'payment_3', 'value' => ''],
            // ['key' => 'payment_4', 'value' => ''],
            // ['key' => 'payment_5', 'value' => ''],
            // ['key' => 'payment_status_1', 'value' => '0'],
            // ['key' => 'payment_status_2', 'value' => '0'],
            // ['key' => 'payment_status_3', 'value' => '0'],
            // ['key' => 'payment_status_4', 'value' => '0'],
            // ['key' => 'payment_status_5', 'value' => '0'],
            // ADMOB (Android)
            ['key' => 'banner_ad', 'value' => '0'],
            ['key' => 'banner_adid', 'value' => ''],
            ['key' => 'interstital_ad', 'value' => '0'],
            ['key' => 'interstital_adid', 'value' => ''],
            ['key' => 'interstital_adclick', 'value' => ''],
            ['key' => 'reward_ad', 'value' => '0'],
            ['key' => 'reward_adid', 'value' => ''],
            ['key' => 'reward_adclick', 'value' => ''],
            // ADMOB (IOS)
            ['key' => 'ios_banner_ad', 'value' => '0'],
            ['key' => 'ios_banner_adid', 'value' => ''],
            ['key' => 'ios_interstital_ad', 'value' => '0'],
            ['key' => 'ios_interstital_adid', 'value' => ''],
            ['key' => 'ios_interstital_adclick', 'value' => ''],
            ['key' => 'ios_reward_ad', 'value' => '0'],
            ['key' => 'ios_reward_adid', 'value' => ''],
            ['key' => 'ios_reward_adclick', 'value' => ''],
            // Facebook (Android)
            ['key' => 'fb_native_status', 'value' => '0'],
            ['key' => 'fb_native_id', 'value' => ''],
            ['key' => 'fb_banner_status', 'value' => '0'],
            ['key' => 'fb_banner_id', 'value' => ''],
            ['key' => 'fb_interstiatial_status', 'value' => '0'],
            ['key' => 'fb_interstiatial_id', 'value' => ''],
            ['key' => 'fb_rewardvideo_status', 'value' => '0'],
            ['key' => 'fb_rewardvideo_id', 'value' => ''],
            ['key' => 'fb_native_full_status', 'value' => '0'],
            ['key' => 'fb_native_full_id', 'value' => ''],
            // Facebook (IOS)
            ['key' => 'fb_ios_native_status', 'value' => '0'],
            ['key' => 'fb_ios_native_id', 'value' => ''],
            ['key' => 'fb_ios_banner_status', 'value' => '0'],
            ['key' => 'fb_ios_banner_id', 'value' => ''],
            ['key' => 'fb_ios_interstiatial_status', 'value' => '0'],
            ['key' => 'fb_ios_interstiatial_id', 'value' => ''],
            ['key' => 'fb_ios_rewardvideo_status', 'value' => '0'],
            ['key' => 'fb_ios_rewardvideo_id', 'value' => ''],
            ['key' => 'fb_ios_native_full_status', 'value' => '0'],
            ['key' => 'fb_ios_native_full_id', 'value' => ''],
            // Notification Setting
            ['key' => 'onesignal_apid', 'value' => ''],
            ['key' => 'onesignal_rest_key', 'value' => ''],
            // //  Package Setting
            // ['key' => 'purchase_code', 'value' => ''],
            // ['key' => 'package_name', 'value' => '']
        ];
        General_Setting::insert($data);
    }
}
