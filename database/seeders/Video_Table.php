<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class Video_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Video::insert([
            'category_id' => "1",
            'language_id' => "1",
            'cast_id' => "1",
            'channel_id' => 1,
            'director_id' => "",
            'starring_id' => "",
            'supporting_cast_id' => "",
            'networks' => "",
            'maturity_rating' => "",
            'name' => "K.G.F:Chapter 2",
            'thumbnail' => "1.jpg",
            'landscape' => "1.jpg",
            'trailer_url' => "https://www.google.com/",
            'release_year' => "2018",
            'age_restriction' => "",
            'max_video_quality' => "HD",
            'release_tag' => "",
            'type_id' => "1",
            'video_type' => "1",
            'video_upload_type' => "",
            'video_extension' => "mp4",
            'is_premium' => '1',
            'description' => "Rocky takes control of the Kolar Gold Fields and his newfound power makes the government as well as his enemies jittery. However, he still has to confront Ramika, Adheera and Inayat.",
            'video_duration' => 3600,
            'video_size' => 0,
            'view' => 500,
            'imdb_rating' => 8.2,
            'download' => 0,
            'is_title' => "1",
            'status' => '1',
            'video_320' => "1.mp4",
            'video_480' => "1.mp4",
            'video_720' => "1.mp4",
            'video_1080' => "1.mp4",
        ]);
    }
}
