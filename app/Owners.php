<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Owners extends Model
{
    protected $fillable = ['id','id_vendor','id_user','type_code','name','cedula_rif'
    ,'direction','city','country','phone1','phone2','days_credit','amount_max_credit','percentage_retencion_iva',
    'percentage_retencion_islr','status','created_at','updated_at'];
   
}
