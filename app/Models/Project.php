<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable     =   [
        'name',
        'regionId',
        'countryId',
        'managerId',
        'inicia',
        'semanas',
        'notes',
        'week_free'
    ];

    public function region() {
        return $this->belongsTo('App\Models\Region', 'regionId');
    }
    public function country() {
        return $this->belongsTo('App\Models\Country', 'countryId');
    }

    public function directories() {
        return $this->hasMany('App\Models\Directory', 'projectId');
    }

    public function gerente() {
        return $this->belongsTo('App\Models\User', 'managerId');
    }
}
