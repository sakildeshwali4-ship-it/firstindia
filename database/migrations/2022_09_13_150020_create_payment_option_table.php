<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment_Option;

class CreatePaymentOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_option', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('visibility');
            $table->string('is_live');
            $table->string('live_key_1');
            $table->string('live_key_2');
            $table->string('live_key_3');
            $table->string('test_key_1');
            $table->string('test_key_2');
            $table->string('test_key_3');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        
        $data =  [
            ['name' => "inapppurchage", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "paypal", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "razorpay	", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "flutterwave", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "payumoney", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "paytm", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
            ['name' => "stripe", 'visibility' => "0", 'is_live' => "0", 'live_key_1' => "", 'live_key_2' => "", 'live_key_3' => "", 'test_key_1' => "", 'test_key_2' => "", 'test_key_3' => ""],
        ];
        Payment_Option::insert($data);
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
