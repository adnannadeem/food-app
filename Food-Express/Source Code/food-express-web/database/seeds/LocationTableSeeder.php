<?php

use App\Enums\LocationStatus;
use App\Models\Location;
use App\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Location::class, 10)->create();
    }
}
