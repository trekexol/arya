<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempMovimientos extends Model
{
    /*protected $connection = 'logins';

    protected $table = 'bvc_rates_social_benefits';*/
    protected $table = 'TempMovimientos';

    protected $fillable = [
        'banco', 'referencia_bancaria', 'descripcion', 'fecha', 'haber','debe', 'moneda'
    ];
}
