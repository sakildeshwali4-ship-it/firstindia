<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class Channel_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Channel::insert([
            'name' => "Sony SAB",
            'image' => "1.jpg",
            'landscape' => "1.jpg",
            'is_title' => '1',
            'status' => 1,
        ]);
    }
}
