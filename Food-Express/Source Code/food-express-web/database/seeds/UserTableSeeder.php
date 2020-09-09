<?php

use App\Enums\UserRole;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name'     => 'Super',
            'last_name'      => 'Admin',
            'username'       => 'admin',
            'email'          => 'admin@example.com',
            'phone'          => '+15005550006',
            'address'        => 'Mirpur 1, Dhaka, Bangladesh',
            'roles'          => UserRole::ADMIN,
            'password'       => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
    }
}
