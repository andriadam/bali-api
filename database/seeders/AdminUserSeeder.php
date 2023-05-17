<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => date('Y-m-d H:i:s', time()),
            'password' => '$2y$10$mMXF38XVGYmmFCnp2mhejuBaPHuwjyPk1pNw3KaL2F0MqQ.OsIvYm', // 'password'
            'role' => 'admin'
        ]);
        User::create([
            'username' => 'user',
            'email' => 'user@gmail.com',
            'email_verified_at' => date('Y-m-d H:i:s', time()),
            'password' => '$2y$10$m6spNcop7OJXM7VaXwQsQerIDDt/UwjyL58nMIRIHg1IBgu7IOmI6', // 'password'
            'role' => 'user'
        ]);
    }
}
