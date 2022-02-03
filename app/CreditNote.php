<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    public function clients(){
        return $this->belongsTo('App\Client','id_client');
    }
    public function vendors(){
        return $this->belongsTo('App\Vendor','id_vendor');
    }

    public function quotations(){
        return $this->belongsTo('App\Quotation','id_quotation');
    }
}
