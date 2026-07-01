<?php

namespace Database\Seeders;

use App\Models\Channel_Banner;
use Illuminate\Database\Seeder;

class Channel_Banner_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Channel_Banner::insert([
            'name' => "Live IPL Match",
            'image' => "1,jpg",
            'link' => "https://www.iplt20.com/",
            'order_no' => 1,
            'status' => '1',
        ]);
    }
}
