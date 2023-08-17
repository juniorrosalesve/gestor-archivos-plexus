<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;

    protected $fillable     =   [
        'projectId',
        'name',
        'route',
        'link',
        'file_path',
        'file_ext',
        'type',
        'required'
    ];
}
