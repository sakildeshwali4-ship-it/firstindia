<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->string('title');
            $table->text('description');
            $table->integer('status')->default(1);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        $data = [
            ['page_name' => "about-us", 'title' => "About Us", 'description' => "About Us Page"],
            ['page_name' => "privacy-policy", 'title' => "Privacy Policy", 'description' => "Privacy Policy"],
            ['page_name' => "terms-and-conditions", 'title' => "Terms & Conditions", 'description' => "Terms & Conditions Page"],
            ['page_name' => "refund-policy", 'title' => "Refund Policy", 'description' => "Refund Policy"],
        ];
        Page::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_option');
    }
}
