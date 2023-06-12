<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    
    protected $table = 'activity';
    protected $fillable = ['name', 'program_id', 'type_id', 'date'];
}
