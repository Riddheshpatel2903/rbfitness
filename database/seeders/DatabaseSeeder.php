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
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@rbfitness.com'],
            [
                'name' => 'RB Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Plan Categories
        $monthly = \App\Models\PlanCategory::updateOrCreate(['name' => 'Monthly'], ['slug' => 'monthly', 'is_active' => true]);
        $threeMonths = \App\Models\PlanCategory::updateOrCreate(['name' => '3 Months'], ['slug' => '3-months', 'is_active' => true]);
        $yearly = \App\Models\PlanCategory::updateOrCreate(['name' => 'Yearly'], ['slug' => 'yearly', 'is_active' => true]);

        // 3. Membership Plans
        $plans = [
            [
                'category_id' => $monthly->id,
                'name' => 'Monthly Standard',
                'price' => 1200,
                'duration_days' => 30,
                'features' => json_encode([
                    'Gym Floor Access',
                    'Cardio & Strength Area',
                    'Basic Locker Access',
                    'Expert Guidance'
                ])
            ],
            [
                'category_id' => $monthly->id,
                'name' => 'Monthly Premium',
                'price' => 6000,
                'duration_days' => 30,
                'features' => json_encode([
                    'All Basic Features',
                    'Dedicated Personal Trainer',
                    'Personalized Diet Plan',
                    'Progress Tracking',
                    'Priority Support'
                ])
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['name' => $plan['name']], $plan);
        }

        // 3. Facilities (Using original assets)
        $facilities = [
            [
                'title' => 'Cardio Zone',
                'description' => 'Equipped with modern treadmills, ellipticals, and stationary bikes for effective cardiovascular training.',
                'image' => 'cardio.MOV' // Asset path logic handled in view
            ],
            [
                'title' => 'Free Weights',
                'description' => 'A comprehensive range of dumbbells and barbells for strength and muscle building.',
                'image' => 'weight.mp4'
            ],
            [
                'title' => 'Machines',
                'description' => 'State-of-the-art resistance machines for targeted muscle isolation and safety.',
                'image' => 'machine2.jpeg'
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(['title' => $facility['title']], $facility);
        }

        // 4. Trainers
        Trainer::updateOrCreate(
            ['name' => 'Akshat Patel'],
            [
                'specialization' => 'Head Trainer',
                'bio' => 'Head Trainer at RB Fitness with extensive experience in bodybuilding and functional fitness.',
                'image' => 'TRAINER.JPEG',
                'status' => true
            ]
        );

        // 5. Site Settings
        $settings = [
            'gym_name' => 'RB Fitness Club',
            'contact_phone' => '+91 91730 82488',
            'whatsapp_number' => '919173082488',
            'contact_address' => 'Atmiya Complex, Gandevi, Navsari, Gujarat - 396360',
            'hours_morning' => '5:30 AM – 11:00 AM',
            'hours_evening' => '4:00 PM – 8:30 PM',
            'hours_sun' => 'OFF ( GYM CLOSED )',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
