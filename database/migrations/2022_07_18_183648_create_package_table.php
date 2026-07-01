<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price',8,2);  
            $table->text('type_id');
            $table->string('watch_on_laptop_tv');  
            $table->integer('ads_free_movies_shows');
            $table->integer('no_of_device');
            $table->text('video_qulity');
            $table->string('type');
            $table->string('time');
            $table->string('android_product_package');
            $table->string('ios_product_package');
            $table->integer('status')->default(1);
            $table->integer('is_delete')->default(0);
            $table->date('date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('package');
    }
}
