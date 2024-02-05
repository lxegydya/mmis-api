<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mentor::create([
            'fullname' => 'Egy Dya Hermawan',
            'email' => 'egydya.edh12@gmail.com',
            'password' => 'a17b616514c29883763f21b409e403e471ec12d21d65a6131ed53dbc869a4a48',
            'phone' => '082387655402',
            'skill' => 'Web Fullstack, UI/UX Design',
            'status' => 'Active',
            'image' => 'uploads/mentors/profile_picture/default-profile.jpg'
        ]);
    }
}
