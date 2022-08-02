<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{ 
    public function parroquias(){
    return $this->belongsTo('App\Permission\Models\Parroquia','parroquia_id');
    }
    public function comisions(){
        return $this->belongsTo('App\Permission\Models\ComisionType','comision_id');
    }
    public function employees(){
        return $this->belongsTo('App\Permission\Models\Employee','employee_id');
    }
    public function users(){
        return $this->belongsTo('App\Permission\Models\User','user_id');
    }
 
}
