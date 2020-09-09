<?php

use App\Models\OrderLineItem;
use Illuminate\Database\Seeder;

class OrderLineItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(OrderLineItem::class, 200)->create();
    }
}
