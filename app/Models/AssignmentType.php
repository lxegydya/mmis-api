<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentType extends Model
{
    use HasFactory;

    protected $table = 'assignment_type';
    protected $fillable = ['type'];
}
