<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scoring extends Model
{
    use HasFactory;

    protected $table = 'scoring';
    protected $fillable = ['assignment_id', 'mentee_id', 'score', 'status'];
}
