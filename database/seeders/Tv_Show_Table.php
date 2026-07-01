<?php

namespace Database\Seeders;

use App\Models\TVShow;
use Illuminate\Database\Seeder;

class Tv_Show_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TVShow::insert([
            'category_id' => "1",
            'language_id' => "1",
            'cast_id' => "1",
            'channel_id' => 1,
            'type_id' => 1,
            'director_id' => "",
            'starring_id' => "",
            'supporting_cast_id' => "",
            'networks' => "",
            'maturity_rating' => "",
            'studios' => "",
            'content_advisory' => "",
            'viewing_rights' => "",
            'content_advisory' => "",
            'content_advisory' => "",
            'video_type' => "1",
            'name' => "Taarak Mehta Ka Ooltah Chashmah",
            'description' => "The residents of a housing society help each other find solutions when they face common, real-life challenges and get involved in sticky situations.",
            'thumbnail' => "1.jpg",
            'landscape' => "1.jpg",
            'view' => 500,
            'status' => '1',
            'is_title' => "1",
            'is_premium' => '1',
            'imdb_rating' => 8.2,
        ]);
    }
}
