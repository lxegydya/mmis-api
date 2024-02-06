<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityType::create(['type' => 'Onboarding']);
        ActivityType::create(['type' => 'Weekly Discussion']);
        ActivityType::create(['type' => 'Forum Group Discussion']);
        ActivityType::create(['type' => 'Webinar']);
        ActivityType::create(['type' => 'Pretest']);
    }
}
