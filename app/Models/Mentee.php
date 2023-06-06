<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentee extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'mentee';
    protected $fillable = ['id', 'name', 'gender', 'university', 'major', 'semester', 'email', 'phone', 'status', 'image'];
}
