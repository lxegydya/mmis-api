<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mentor::create([
            'fullname' => 'Egy Dya Hermawan',
            'email' => 'egy@gmail.com',
            'password' => Crypt::encryptString('egy123'),
            'phone' => '082387655402',
            'skill' => 'Web Fullstack, UI/UX Design',
            'status' => 'Active',
            'image' => 'uploads/mentors/profile_picture/default-profile.jpg'
        ]);
    }
}
