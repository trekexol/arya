<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    
    
    public function companies(){
        return $this->belongsTo('App\Permission\Models\Company','company_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }



}
