<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
{
    User::updateOrCreate(
        ['email' => 'adminbook@gmail.com'], // supaya tidak duplicate
        [
            'name' => 'Admin Digishelf',
            'password' => Hash::make('perpustakaanbuku'),
            'role' => 'admin'
        ]
    );
}
}

