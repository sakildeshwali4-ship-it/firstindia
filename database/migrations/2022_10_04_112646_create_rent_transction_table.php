<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentTransctionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_transction', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('unique_id')->default(0);
            $table->integer('video_id');
            $table->integer('price');
            $table->integer('type_id');
            $table->integer('video_type')->comment('1- Video, 2- Show, 3- Language, 4- Category	');
            $table->date('expiry_date');
            $table->integer('status');
            $table->datetime('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_transction');
    }
}
