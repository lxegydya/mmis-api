<?php

namespace Database\Seeders;

use App\Models\Absence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Absence::insert([
            ['activity_id' => 1, 'mentee_id' => 4550686, 'created_at' => date_create()],
            ['activity_id' => 2, 'mentee_id' => 4550686, 'created_at' => date_create()]
        ]);
    }
}
