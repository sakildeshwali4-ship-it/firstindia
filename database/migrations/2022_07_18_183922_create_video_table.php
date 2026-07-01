<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video', function (Blueprint $table) {
            $table->id();
            $table->text('category_id');
            $table->text('language_id');
            $table->text('cast_id');
            $table->integer('channel_id');
            $table->text('director_id');
            $table->text('starring_id');
            $table->text('supporting_cast_id');
            $table->text('networks');
            $table->text('maturity_rating');
            $table->string('name');
            $table->string('thumbnail');
            $table->string('landscape');
            $table->text('trailer_url');
            $table->string('release_year');
            $table->string('age_restriction');
            $table->string('max_video_quality');
            $table->string('release_tag');
            $table->string('type_id');
            $table->string('video_type')->comment('1- Video, 2- Show, 3- Language, 4- Category , 5- Session');
            $table->string('video_upload_type');
            $table->string('video_extension');
            $table->enum('is_premium',['0','1'])->default(0);
            $table->string('description');
            $table->integer('video_duration')->default(0);
            $table->integer('video_size')->default(0);
            $table->integer('view');
            $table->float('imdb_rating');
            $table->integer('download')->default(0);
            $table->enum('status',['0','1']);
            $table->string('is_title');
            $table->string('video_320');
            $table->string('video_480');
            $table->string('video_720');
            $table->string('video_1080');
            $table->string('subtitle_type');
            $table->string('subtitle_lang_1')->default("");
            $table->string('subtitle_lang_2')->default("");
            $table->string('subtitle_lang_3')->default("");
            $table->string('subtitle_1')->default("");
            $table->string('subtitle_2')->default("");
            $table->string('subtitle_3')->default("");
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
        Schema::dropIfExists('video');
    }
}
