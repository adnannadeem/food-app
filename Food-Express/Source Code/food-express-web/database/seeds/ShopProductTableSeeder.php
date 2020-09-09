<?php

use App\Models\ShopProduct;
use Illuminate\Database\Seeder;


class ShopProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(  )
    {
        factory(ShopProduct::class, 20)->create();
    }
}