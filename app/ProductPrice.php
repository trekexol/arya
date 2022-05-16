<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    public function products(){
        return $this->belongsTo('App\Products', 'id_product');
    }
}
