<?php

namespace Database\Seeders;

use App\Models\Payment_Option;
use Illuminate\Database\Seeder;

class Payment_Option_TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payment_Option::truncate();

        $data = [
            ['name' => "inapppurchage", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "paypal", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "razorpay", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "flutterwave", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "payumoney", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "paytm", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "stripe", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
        ];
        Payment_Option::insert($data);
    }
}
