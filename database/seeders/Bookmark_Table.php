<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use Illuminate\Database\Seeder;

class Bookmark_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bookmark::insert([
            'user_id' => 1,
            'video_id' => 1,
            'type_id' => 1,
            'video_type' => 1,
            'user_id' => 1,
            'status' => 1,
        ]);
    }
}
