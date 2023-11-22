<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WarehouseHistories extends Model
{
    public $timestamps = true;
    /*
    protected $table = 'inventory_history';
protected $fillable = ['id','date','id_product','description','type','price','amount','amount_real','status','branch','centro_cost','number_invoice','user'];

    protected

    public function products(){
        return $this->belongsTo('App\Permission\Models\Product','product_id');
    } */



}
