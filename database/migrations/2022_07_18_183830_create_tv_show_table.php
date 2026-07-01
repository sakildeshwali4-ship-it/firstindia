<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvShowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_show', function (Blueprint $table) {
            $table->id();
            $table->text('category_id');
            $table->text('language_id');
            $table->text('cast_id');
            $table->integer('channel_id');
            $table->integer('type_id')->comment('Type Table ID FK')->default(0);
            $table->text('director_id');
            $table->text('starring_id');
            $table->text('supporting_cast_id');
            $table->text('networks');
            $table->text('maturity_rating');
            $table->text('studios');
            $table->text('content_advisory');
            $table->text('viewing_rights');
            $table->integer('video_type')->comment('1- Video, 2- Show, 3- Language, 4- Category , 5- Session');
            $table->string('name');
            $table->text('description');
            $table->string('thumbnail');
            $table->string('landscape');
            $table->integer('view')->default(0);
            $table->enum('status',['0','1']);
            $table->enum('is_title',['0','1']);
            $table->integer('is_premium');
            $table->float('imdb_rating');
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
        Schema::dropIfExists('tv_show');
    }
}
