<?php

namespace Database\Seeders;

use App\Models\Scoring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scoring::insert([
            [
                "assignment_id" => 1, 
                "mentee_id" => 4550686, 
                "score" => 100, 
                "status" => 'On-Time', 
                "created_at" => date_create()
            ],
            [
                "assignment_id" => 1, 
                "mentee_id" => 5320509, 
                "score" => 100, 
                "status" => 'On-Time', 
                "created_at" => date_create()
            ],
        ]);
    }
}
