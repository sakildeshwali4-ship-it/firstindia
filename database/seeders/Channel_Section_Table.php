<?php

namespace Database\Seeders;

use App\Models\Channel_Section;
use Illuminate\Database\Seeder;

class Channel_Section_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Channel_Section::insert([
            'type_id' => 1,
            'category_id' => 1,
            'channel_id' => "1",
            'video_id' => "1",
            'tv_show_id' => "1",
            'language_id' => "1",
            'category_ids' => "1",
            'title' => "Sony SAB",
            'video_type' => '1',
            'section_type' => 1,
            'screen_layout' => "landscape",
            'status' => '1',
        ]);
    }
}
