<?php

namespace Database\Seeders;

use App\Models\Smtp;
use Illuminate\Database\Seeder;

class Smtp_Setting_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Smtp::truncate();

        Smtp::insert([
            'protocol' => 'smtp123',
            'host' => 'ssl://smtp.gmail.com',
            'port' => '123',
            'user' => 'admin@admin.com',
            'pass' => '123456',
            'from_name' => 'DivineTechs',
            'from_email' => 'admin@admin.com',
        ]);
    }
}
