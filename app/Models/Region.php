<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable     =   [
        'name',
        'userId',
        'countries'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'userId');
    }

    public function countries() {
        return $this->hasMany('App\Models\Country', 'regionId');
    }
}
