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
        AssignmentType::create(["type" => 'LMS']);
        AssignmentType::create(["type" => 'Webinar']);
        AssignmentType::create(["type" => 'Forum Group Discussion']);
        AssignmentType::create(["type" => 'Pre-Test']);
        AssignmentType::create(["type" => 'Certification']);
    }
}
