<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';
    protected $fillable = ['program_name', 'program_desc', 'program_categorie', 'program_status', 'batch_id'];
}
