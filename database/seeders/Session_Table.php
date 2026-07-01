<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

class Session_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Session::insert([
            'name' => "Session 1",
            'status' => '1',
        ]);
    }
}
