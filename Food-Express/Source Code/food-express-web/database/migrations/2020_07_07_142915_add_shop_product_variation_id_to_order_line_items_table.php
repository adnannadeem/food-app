<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopProductVariationIdToOrderLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_line_items', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_product_variation_id')->nullable();
            $table->longText('options')->nullable();
            $table->unsignedDouble('options_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_line_items', function (Blueprint $table) {
            $table->dropColumn('shop_product_variation_id');
            $table->dropColumn('options');
            $table->dropColumn('options_total');
        });
    }
}
