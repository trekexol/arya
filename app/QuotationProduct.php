<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationProduct extends Model
{
    protected $fillable = ['amount','price'];


    public function inventories(){
        return $this->belongsTo('App\Inventory','id_inventory');
    }
    public function product() {
        return $this->hasMany('App\Product');
    }

    public function quotations(){
        return $this->belongsTo('App\Quotation','id_quotation');
    }
}
