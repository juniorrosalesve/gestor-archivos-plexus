<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronograma extends Model
{
    use HasFactory;

    protected $fillable     =   [
        'projectId',
        'n_factura',
        'fecha_factura',
        'fecha_vencimiento',
        'fecha_pagoreal',
        'moneda',
        'monto'
    ];

    public function project() {
        return $this->belongsTo('App\Models\Project', 'projectId');
    }
}
