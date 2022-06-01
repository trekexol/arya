<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailVoucher extends Model
{
    public function accounts(){
        return $this->belongsTo('App\Permission\Models\Account','id_account');
    }

    public function headers(){
        return $this->belongsTo('App\Permission\Models\HeaderVoucher','id_header_voucher');
    }

    public function banks(){
        return $this->belongsTo('App\Permission\Models\BankVoucher','id_bank_voucher');
    }

    public function quotations(){
        return $this->belongsTo('App\Quotation','id_invoice');
    }

    public function expenses(){
        return $this->belongsTo('App\ExpensesAndPurchase','id_expense');
    }

}
