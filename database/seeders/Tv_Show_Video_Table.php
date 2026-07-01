<?php

namespace Database\Seeders;

use App\Models\TVShowVideo;
use Illuminate\Database\Seeder;

class Tv_Show_Video_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TVShowVideo::insert([
            'show_id' => 1,
            'session_id' => 1,
            'thumbnail' => "1.jpg",
            'landscape' => "1.jpg",
            'video_upload_type' => "server_video",
            'video_type' => "1",
            'video_extension' => "mp4",
            'video_duration' => 3600,
            'is_premium' => '1',
            'description' => "Rocky takes control of the Kolar Gold Fields and his newfound power makes the government as well as his enemies jittery. However, he still has to confront Ramika, Adheera and Inayat.",
            'view' => 500,
            'download' => 0,
            'status' => '1',
            'is_title' => '1',
            'video_320' => "1.mp4",
            'video_480' => "1.mp4",
            'video_720' => "1.mp4",
            'video_1080' => "1.mp4",
        ]);
    }
}
