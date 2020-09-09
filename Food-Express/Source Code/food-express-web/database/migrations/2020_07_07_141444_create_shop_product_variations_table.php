<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_product_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_product_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shop_id');
            $table->string('name');
            $table->unsignedDouble('price');
            $table->unsignedInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_variations');
    }
}
