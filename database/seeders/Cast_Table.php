<?php

namespace Database\Seeders;

use App\Models\Cast;
use Illuminate\Database\Seeder;

class Cast_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cast::insert([
            'name' => "Akshay Kumar",
            'image' => "1.jpg",
            'type' => "Actor",
            'personal_info' => "Rajiv Hari Om Bhatia, known professionally as Akshay Kumar, is an Indian-born naturalised Canadian actor and film producer who works in Hindi cinema. In over 30 years of acting, Kumar has appeared in some 100 films and has won several awards, including a National Film Award for Best Actor and two Filmfare Awards.",
            'status' => '1',
        ]);
    }
}
