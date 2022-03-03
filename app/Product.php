<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = ['id','segment_id','subsegment_id','twosubsegment_id','threesubsegment_id','unit_of_measure_id',
    'code_comercial','type','description','price','price_buy','cost_average','photo_product','money','id_user',
    'exento','islr','special_impuesto','status','created_at','updated_at'];

    public function segments(){
        return $this->belongsTo('App\Segment','segment_id');
    }

    public function subsegments(){
        return $this->belongsTo('App\Subsegment','subsegment_id');
    }
    public function twosubsegments(){
        return $this->belongsTo('App\TwoSubsegment','twosubsegment_id');
    }
    public function threesubsegments(){
        return $this->belongsTo('App\ThreeSubsegment','threesubsegment_id');
    }

    public function unitofmeasures(){
        return $this->belongsTo('App\Permission\Models\UnitOfMeasure','unit_of_measure_id');
    }
/*
    public function inventory(){
        return $this->hasMany('App\Inventory');
    } 
  */  
    public function quotation_products() {
        return $this->belongsTo('App\QuotationProduct', 'id_inventory');   
    }

    public function accounts(){
        return $this->belongsTo('App\Account','id_account');
    }
  

}
