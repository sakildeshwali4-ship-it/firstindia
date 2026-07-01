<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_section', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id')->comment('Type Table ID FK');
            $table->integer('category_id');
            $table->text('video_id')->comment('Multiple ID');
            $table->text('tv_show_id');
            $table->text('language_id');
            $table->text('category_ids');
            $table->text('title');
            $table->enum('video_type',['1','2','3','4'])->comment('1- Video, 2- Show, 3- Language, 4- Category, 5- Session');
            $table->string('screen_layout');
            $table->enum('is_home_screen',['1','2'])->comment('1- home screen, 2- other screen');
            $table->integer('status')->default('1');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_section');
    }
}
