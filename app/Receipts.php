<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    public function clients(){
        return $this->belongsTo('App\Condominiums','id_client');
    }

    public function receipt_product() {
        return $this->hasMany('App\receiptProduct');   
    }

    public function anticipos() {
        return $this->hasMany('App\Anticipo');   
    }

    public function datails() {
        return $this->hasMany('App\DetailVoucher');   
    }
}
