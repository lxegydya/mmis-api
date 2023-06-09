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
        ActivityType::insert([
            ['type' => 'Onboarding', 'created_at' => date_create()],
            ['type' => 'Weekly Discussion', 'created_at' => date_create()],
            ['type' => 'Forum Group Discussion', 'created_at' => date_create()],
            ['type' => 'Webinar', 'created_at' => date_create()],
            ['type' => 'Pretest', 'created_at' => date_create()]
        ]);
    }
}
