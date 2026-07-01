<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvShowVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_show_video', function (Blueprint $table) {
            $table->id();
            $table->integer('show_id');
            $table->integer('session_id');
            $table->string('thumbnail');
            $table->string('landscape');
            $table->string('video_upload_type');
            $table->string('video_type');
            $table->string('video_extension');
            $table->integer('video_duration')->default(0);
            $table->enum('is_premium',['0','1']);
            $table->text('description');
            $table->integer('view');
            $table->integer('download')->default(0);
            $table->enum('status',['0','1']);
            $table->enum('is_title',['0','1']);
            $table->string('video_320');
            $table->string('video_480');
            $table->string('video_720');
            $table->string('video_1080');
            $table->string('subtitle_type');
            $table->string('subtitle')->default("");
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
        Schema::dropIfExists('tv_show_video');
    }
}
