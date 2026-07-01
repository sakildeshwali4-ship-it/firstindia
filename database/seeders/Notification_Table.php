<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class Notification_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Notification::insert([
            'title' => "DTLive App Launch",
            'message' => "DTLive New Version Is Launch",
            'image' => "1.jpg",
        ]);
    }
}
