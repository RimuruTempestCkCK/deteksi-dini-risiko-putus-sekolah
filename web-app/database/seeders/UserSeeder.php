<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smkn1bungo.sch.id'],
            [
                'name' => 'Administrator Sistem Cerdas',
                'username' => 'admin',
                'email' => 'admin@smkn1bungo.sch.id',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
