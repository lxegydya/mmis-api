<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Assignment::create([
            "name" => "Chapter #1: Bagian 1",
            "description" => "Membuat rangkuman terkait materi Chapter 1 yang telah dipelajari saat Self Learning Minggu Pertama",
            "program_id" => 1,
            "type_id" => 1,
            "deadline" => "2023-02-21"
        ]);

        Assignment::create([
            "name" => "Chapter #1: Bagian 2",
            "description" => "Membuat rangkuman terkait materi Chapter 1 yang telah dipelajari saat Self Learning Minggu Pertama",
            "program_id" => 1,
            "type_id" => 1,
            "deadline" => "2023-02-21"
        ]);
    }
}
