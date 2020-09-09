<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('area_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedDouble('delivery_charge', 13, 2)->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->longText('address')->nullable();
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('current_status');
            $table->auditColumn();
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
        Schema::dropIfExists('shops');
    }
}
