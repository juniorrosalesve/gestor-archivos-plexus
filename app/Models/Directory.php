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
        'week_from',
        'week_to',
        'file_week',
        'created_by',
        'edit_by',
        'deleted',
        'created_at'
    ];

    public function project() {
        return $this->belongsTo('App\Models\Project', 'projectId');
    }
}
