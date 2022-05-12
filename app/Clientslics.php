<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientslics extends Model
{
    public function vendors(){
        return $this->belongsTo('App\Vendor','id_vendor');
    }
}
