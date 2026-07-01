<?php

namespace Database\Seeders;

use App\Models\RentVideo;
use Illuminate\Database\Seeder;

class Rent_Video_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RentVideo::insert([
            'video_id' => 1,
            'price' => 99,
            'type_id' => "1",
            'video_type' => "1",
            'status' => '1',
        ]);
    }
}
