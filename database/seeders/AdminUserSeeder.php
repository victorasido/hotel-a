<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles('Super Admin');

        $fo = User::firstOrCreate(
            ['email' => 'fo@hotel.com'],
            [
                'name' => 'Resepsionis',
                'password' => Hash::make('password'),
            ]
        );
        $fo->syncRoles('Front Office');

        $fnb = User::firstOrCreate(
            ['email' => 'fnb@hotel.com'],
            [
                'name' => 'Staf F&B',
                'password' => Hash::make('password'),
            ]
        );
        $fnb->syncRoles('FnB');

        $hk = User::firstOrCreate(
            ['email' => 'housekeeping@hotel.com'],
            [
                'name' => 'Staf Housekeeping',
                'password' => Hash::make('password'),
            ]
        );
        $hk->syncRoles('Housekeeping');
    }
}
