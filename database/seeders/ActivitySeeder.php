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
        Activity::insert([
            [
                'name' => 'Onboarding Day #1', 
                'program_id' => 1, 
                'type_id' => 1, 
                'date' => '2023-02-16', 
                'created_at' => date_create()
            ],
            [
                'name' => 'Onboarding Day #2', 
                'program_id' => 1, 
                'type_id' => 1, 
                'date' => '2023-02-17', 
                'created_at' => date_create()
            ]
        ]);
    }
}
