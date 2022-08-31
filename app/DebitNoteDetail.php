<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitNoteDetail extends Model
{
    public function inventories(){
        return $this->belongsTo('App\Inventory','id_inventory');
    }
   
    
    public function quotations(){
        return $this->belongsTo('App\Quotation','id_quotation');
    }
}
