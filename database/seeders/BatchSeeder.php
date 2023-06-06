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
        Batch::insert([
            'batch_name' => 'Batch 02',
            'batch_start' => date('Y-m-d'),
            'batch_end' => '2023-06-30',
            'batch_status' => 'Upcoming',
            'created_at' => date_create()
        ]);
    }
}
