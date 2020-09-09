<?php


use App\Models\OrderHistory;
use Illuminate\Database\Seeder;

class OrderHistoryTableSeeder extends Seeder
{
    public function run()
    {
        factory(OrderHistory::class, 30)->create();
    }
}