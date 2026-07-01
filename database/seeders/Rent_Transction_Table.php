<?php

namespace Database\Seeders;

use App\Models\RentTransction;
use Illuminate\Database\Seeder;

class Rent_Transction_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RentTransction::insert([
            'user_id' => 1,
            'unique_id' => 0,
            'video_id' => 1,
            'price' => 99,
            'type_id' => 1,
            'video_type' => 1,
            'status' => 1,
            'date' => "2022-10-04 08:04:07.000000",
        ]);
    }
}
