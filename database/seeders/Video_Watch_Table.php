<?php

namespace Database\Seeders;

use App\Models\Video_Watch;
use Illuminate\Database\Seeder;

class Video_Watch_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Video_Watch::insert([
            'user_id' => 1,
            'video_id' => 1,
            'type_id' => 1,
            'video_type' => 1,
            'stop_time' => "3600",
            'status' => '1',
            'is_delete' => '1',
        ]);
    }
}
