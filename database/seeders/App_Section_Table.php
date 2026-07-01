<?php

namespace Database\Seeders;

use App\Models\App_Section;
use Illuminate\Database\Seeder;

class App_Section_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App_Section::insert([
            'type_id' => 1,
            'category_id' => 1,
            'video_id' => "1,2",
            'tv_show_id' => "1",
            'tv_show_id' => "1",
            'language_id' => "1",
            'category_ids' => "1,2",
            'title' => "Best Of 2022",
            'video_type' => '1',
            'screen_layout' => "landscape",
            'is_home_screen' => '1',
            'status' => '1',
        ]);
    }
}
