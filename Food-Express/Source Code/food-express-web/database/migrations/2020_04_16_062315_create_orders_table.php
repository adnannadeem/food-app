<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedDouble('total', 13, 2);
            $table->unsignedDouble('sub_total', 13, 2);
            $table->unsignedDouble('delivery_charge', 13, 2);
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('platform')->nullable();
            $table->string('device_id')->nullable();
            $table->string('ip')->nullable();
            $table->unsignedTinyInteger('payment_status');
            $table->unsignedDouble('paid_amount', 13, 2);
            $table->longText('address');
            $table->string('mobile');
            $table->string('lat');
            $table->string('long');
            $table->longText('misc')->nullable();
            $table->unsignedTinyInteger('payment_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
