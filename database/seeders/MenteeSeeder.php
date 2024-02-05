<?php

namespace Database\Seeders;

use App\Models\Mentee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mentee::create([
            'id' => 5690518,
            'name' => 'Ipung Giri Wijaya',
            'gender' => 'Male',
            'university' => 'Universitas Gunadarma',
            'major' => 'Sistem Informasi',
            'semester' => 4,
            'email' => 'ipunggiri13@gmail.com',
            'phone' => '0895350923656',
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create()
        ]);

        Mentee::create([
            'id' => 4550686,
            'name' => 'Alldo Faiz Ramadhani',
            'gender' => 'Male',
            'university' => 'Universitas Telkom',
            'major' => 'Teknik Telekomunikasi',
            'semester' => 7,
            'email' => 'alldo.ramadhani@gmail.com',
            'phone' => '081314044804',
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create()
        ]);

        Mentee::create([
            'id' => 5689272,
            'name' => 'Hani Nurul Khairunnisa',
            'gender' => 'Female',
            'university' => 'Universitas Gunadarma',
            'major' => 'Sistem Informasi',
            'semester' => 4,
            'email' => 'hani.khairunnisa02@gmail.com',
            'phone' => '087848347917',
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create()
        ]);

        Mentee::create([
            'id' => 4316309,
            'name' => 'Alexs Sandro',
            'gender' => 'Male',
            'university' => 'Universitas Negeri Semarang',
            'major' => 'Statistika Terapan dan Komputasi',
            'semester' => 3,
            'email' => 'alexxxssandro@students.unnes.ac.id',
            'phone' => '089629052002',
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create()
        ]);

        Mentee::create([
            'id' => 5387329,
            'name' => 'Nurul Hidayah Sianipar',
            'gender' => 'Female',
            'university' => 'STIKOM Tunas Bangsa',
            'major' => 'Sistem Informasi',
            'semester' => 4,
            'email' => 'nurulhidayahsianipar1@gmail.com',
            'phone' => '085207314444',
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create()
        ]);
    }
}
