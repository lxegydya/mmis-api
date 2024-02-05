<?php

namespace Database\Seeders;

use App\Models\Batch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Batch::create([
            'batch_name' => 'Batch 06',
            'batch_start' => '2024-02-19',
            'batch_end' => '2023-06-30',
            'batch_status' => 'Upcoming'
        ]);
    }
}
