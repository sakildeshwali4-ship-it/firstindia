<?php

namespace Database\Seeders;

use App\Models\Transction;
use Illuminate\Database\Seeder;

class Transaction_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transction::insert([
            'user_id' => 1,
            'unique_id' => 0,
            'package_id' => 1,
            'description' => "Best Plan Subscription",
            'amount' => "899",
            'payment_id' => "112334455",
            'currency_code' => "$",
            'status' => '1',
        ]);
    }
}
