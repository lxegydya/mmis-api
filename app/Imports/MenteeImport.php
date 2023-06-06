<?php

namespace App\Imports;

use App\Models\Mentee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MenteeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Mentee([
            'id' => $row[0],
            'name' => ucwords(strtolower($row[1])),
            'gender' => $row[2],
            'university' => $row[3],
            'major' => $row[4],
            'semester' => $row[5],
            'email' => $row[6],
            'phone' => '0' . $row[7],
            'status' => 'Active',
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'group_id' => null,
            'created_at' => date_create(),
            'updated_at' => null
        ]);
    }
}
