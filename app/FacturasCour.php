<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacturasCour extends Model
{

    
    protected $table = 'facturascour';
    protected $primaryKey = 'id_fac';
    protected $fillable = [
        'id_expense','tipo_fac', 'tipo_movimiento', 'numero', 'monto','estatus'
    ];
}