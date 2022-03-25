<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptProduct extends Model
{
    public function product() {
        return $this->hasMany('App\Product');   
    }

    public function receipts(){
        return $this->belongsTo('App\Receipts','id_quotation');
    }
}
