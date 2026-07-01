<?php

namespace Database\Seeders;

use App\Models\Users;
use Illuminate\Database\Seeder;

class User_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Users::insert([
            'name' => "admin",
            'mobile' => "1234567890",
            'email' => "admin@admin.com",
            'gender' => "male",
            'image' => "1.jpg",
            'type' => 1,
            'status' => '1',
        ]);
    }
}
