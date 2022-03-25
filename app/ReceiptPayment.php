<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptPayment extends Model
{
    public function accounts(){
        return $this->belongsTo('App\Permission\Models\Account','id_account');
    }

    public function receipts(){
        return $this->belongsTo('App\Receipts','id_quoation');
    }
}
