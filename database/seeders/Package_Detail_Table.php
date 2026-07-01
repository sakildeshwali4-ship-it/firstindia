<?php

namespace Database\Seeders;

use App\Models\Package_Detail;
use Illuminate\Database\Seeder;

class Package_Detail_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package_Detail::insert([
            'package_id' => 1,
            'package_key' => "All Content",
            'package_value' => "1",
        ]);
    }
}
