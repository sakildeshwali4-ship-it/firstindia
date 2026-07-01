<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->integer('language_id')->default(0)->nullable();
            $table->string('name');
            $table->string('user_name')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->enum('gender',['male','female'])->default('male')->nullable();
            $table->string('image')->nullable();
            $table->integer('status')->default('1');
            $table->integer('type')->default('1');
            $table->string('api_token')->nullable();
            $table->string('email_verify_token')->nullable();
            $table->string('is_email_verify')->nullable();
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
        Schema::dropIfExists('user');
    }
}
