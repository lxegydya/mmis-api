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
        Program::create([
                'program_name' => 'Fullstack Web Development',
                'program_desc' => 'Kuy buat website!',
                'program_categorie' => 'Coding',
                'program_status' => 'Ongoing',
                'batch_id' => 1,
                'created_at' => date_create()
            ]);

        Program::create([
            'program_name' => 'Mobile Development',
            'program_desc' => 'Mobile? Kuy atuh!',
            'program_categorie' => 'Coding',
            'program_status' => 'Ongoing',
            'batch_id' => 1,
            'created_at' => date_create()
        ]);
    }
}
