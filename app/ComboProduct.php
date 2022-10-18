<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComboProduct extends Model
{
    public function products(){
        return $this->belongsTo('App\Product','id_product');
    }


}
