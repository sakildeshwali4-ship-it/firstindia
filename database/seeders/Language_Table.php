<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class Language_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => "English", 'image' => "", 'status' => '1'],
        ];
        Language::insert($data);
    }
}
