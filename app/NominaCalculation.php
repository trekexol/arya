<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaCalculation extends Model
{
    public function nominas(){
        return $this->belongsTo('App\Permission\Models\Nomina','id_nomina');
    }

    public function nominaconcepts(){
        return $this->belongsTo('App\Permission\Models\NominaConcept','id_nomina_concept');
    }

    public function employees(){
        return $this->belongsTo('App\Permission\Models\Employee','id_employee');
    }
}
