<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AssignmentType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MentorSeeder::class,
            BatchSeeder::class,
            ProgramSeeder::class,
            GroupSeeder::class,
            MenteeSeeder::class,
            ActivityTypeSeeder::class,
            AssignmentTypeSeeder::class,
            ActivitySeeder::class,
            AssignmentSeeder::class
        ]);
    }
}
