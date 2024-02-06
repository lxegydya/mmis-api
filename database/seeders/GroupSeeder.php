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
        Group::create([
            'program_id' => 1,
            'mentor_id' => 1,
            'name' => 'Group 1',
            'status' => 'Active'
        ]);
    }
}
