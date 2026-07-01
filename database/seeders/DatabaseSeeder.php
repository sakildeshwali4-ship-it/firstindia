<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            // AdminTableSeeder::class,
            // Smtp_Setting_Table::class,
            // General_Setting_Table::class,

            App_Section_Table::class,
            Avatar_Table::class,
            Banner_Table::class,
            Bookmark_Table::class,
            Cast_Table::class,
            Category_Table::class,
            Channel_Banner_Table::class,
            Channel_Section_Table::class,
            Channel_Table::class,
            Comment_Table::class,
            Currency_Table::class,
            Download_Table::class,
            Language_Table::class,
            Notification_Table::class,
            Package_Detail_Table::class,
            Package_Table::class,
            Payment_Option_TableSeeder::class,
            Rating_Table::class,
            Rent_Video_Table::class,
            Session_Table::class,
            Transaction_Table::class,
            Tv_Show_Table::class,
            Tv_Show_Video_Table::class,
            Type_Table::class,
            User_Table::class,
            Video_Table::class,
            Video_Watch_Table::class,
            Rent_Transction_Table::class,
        ]);
    }
}
