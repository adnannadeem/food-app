<?php

use App\Enums\UpdateStatus;
use App\Models\Update;
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 26/4/20
 * Time: 12:50 PM
 */

class UpdateTableSeeder extends Seeder
{
    public function run()
    {
        Update::create(['version' => '1.3', 'status' => UpdateStatus::SUCCESS, 'log' => '<h5>+ [Install] Initial Release</h5>' ]);
    }

}