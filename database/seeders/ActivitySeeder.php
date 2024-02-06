<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::create([
            'name' => 'Onboarding Day #1',
            'program_id' => 1,
            'type_id' => 1,
            'date' => '2024-02-16',
        ]);

        Activity::create([
            'name' => 'Onboarding Day #1',
            'program_id' => 1,
            'type_id' => 1,
            'date' => '2024-02-16',
        ]);

        Activity::create([
            'name' => 'Weekly Discussion #1',
            'program_id' => 1,
            'type_id' => 2,
            'date' => '2024-02-16',
        ]);

        Activity::create([
            'name' => 'Webinar #1',
            'program_id' => 1,
            'type_id' => 3,
            'date' => '2024-02-16',
        ]);
    }
}
