<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Super Admin');

        $fnb = User::create([
            'name' => 'Staf F&B',
            'email' => 'fnb@hotel.com',
            'password' => Hash::make('password'),
        ]);
        $fnb->assignRole('FnB');

        $hk = User::create([
            'name' => 'Staf Housekeeping',
            'email' => 'housekeeping@hotel.com',
            'password' => Hash::make('password'),
        ]);
        $hk->assignRole('Housekeeping');
    }
}
