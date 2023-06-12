<?php

namespace Database\Seeders;

use App\Models\AssignmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssignmentType::insert([
            ["type" => 'LMS', "created_at" => date_create()],
            ["type" => 'Webinar', "created_at" => date_create()],
            ["type" => 'Forum Group Discussion', "created_at" => date_create()],
            ["type" => 'Pre-Test', "created_at" => date_create()],
            ["type" => 'Certification', "created_at" => date_create()]
        ]);
    }
}
