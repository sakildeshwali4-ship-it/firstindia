<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class Package_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::insert([
            'name' => "Super",
            'price' => "899",
            'type_id' => "1",
            'watch_on_laptop_tv' => "0",
            'ads_free_movies_shows' => 0,
            'no_of_device' => 0,
            'video_qulity' => "480p",
            'type' => "Month",
            'time' => "1",
            'status' => '1',
            'is_delete' => 0,
        ]);
    }
}
