<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::insert([
            [
                'program_id' => 1,
                'mentor_id' => 11,
                'name' => 'Kelompok 1',
                'status' => 'Active',
                'created_at' => date_create()
            ],
            [
                'program_id' => 1,
                'mentor_id' => 9,
                'name' => 'Kelompok 2',
                'status' => 'Active',
                'created_at' => date_create()
            ],
            [
                'program_id' => 1,
                'mentor_id' => 12,
                'name' => 'Kelompok 3',
                'status' => 'Active',
                'created_at' => date_create()
            ],
            [
                'program_id' => 1,
                'mentor_id' => 13,
                'name' => 'Kelompok 4',
                'status' => 'Active',
                'created_at' => date_create()
            ],
            [
                'program_id' => 1,
                'mentor_id' => 14,
                'name' => 'Kelompok 5',
                'status' => 'Active',
                'created_at' => date_create()
            ]
        ]);
    }
}
