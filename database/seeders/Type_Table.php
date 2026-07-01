<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class Type_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Type::insert([
            'name' => "Movie",
            'type' => "1",
        ]);
    }
}
