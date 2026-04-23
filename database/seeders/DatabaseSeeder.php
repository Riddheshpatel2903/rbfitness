<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Facility;
use App\Models\Trainer;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User (Required for initial login)
        User::firstOrCreate(
            ['email' => 'info@rbadmin.in'],
            [
                'name' => 'RB Admin',
                'password' => Hash::make('Rbfitness@2026!'),
            ]
        );

        // All other data (Plans, Facilities, Trainers, Settings) 
        // to be added manually by the admin via the panel.
    }
}
