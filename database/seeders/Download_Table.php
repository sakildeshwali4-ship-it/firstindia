<?php

namespace Database\Seeders;

use App\Models\Download;
use Illuminate\Database\Seeder;

class Download_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Download::insert([
            'user_id' => 1,
            'video_id' => 1,
            'type_id' => 1,
            'video_type' => 1,
        ]);
    }
}
