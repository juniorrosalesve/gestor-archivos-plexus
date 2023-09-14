<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemanaLibre extends Model
{
    use HasFactory;

    protected $fillable     =   [
        'projectId',
        'week_free'
    ];
}
