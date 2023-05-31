<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasaBcv extends Model
{
    public $timestamps = false;
    protected $connection = 'logins';
    protected $table = 'tasa_bcv';

}
