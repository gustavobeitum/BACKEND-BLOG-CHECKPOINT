<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Buda',
            'last_name' => 'João',
            'username' => 'Batatinha123',
            'birthday' => '2003/01/01',
            'image' => 'img.png',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'is_admin' => 'admin'
        ]);
    }
}
