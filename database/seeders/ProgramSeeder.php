<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::insert([
            [
                'program_name' => 'Web Development Fullstack',
                'program_desc' => 'lorem ipsum dolor sit amet',
                'program_categorie' => 'Coding',
                'program_status' => 'Ongoing',
                'batch_id' => 1,
                'created_at' => date_create()
            ],
            [
                'program_name' => 'UI/UX',
                'program_desc' => 'lorem ipsum dolor sit amet',
                'program_categorie' => 'Design',
                'program_status' => 'Ongoing',
                'batch_id' => 1,
                'created_at' => date_create()
            ]
        ]);
    }
}
