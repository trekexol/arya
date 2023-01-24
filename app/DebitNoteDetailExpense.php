<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitNoteDetailExpense extends Model
{

    protected $table = 'debit_note_details_expenses';


    public function inventories(){
        return $this->belongsTo('App\Inventory','id_inventory');
    }

}
