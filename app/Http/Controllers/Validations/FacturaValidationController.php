<?php

namespace App\Http\Controllers\Validations;

use App\DetailVoucher;
use App\Http\Controllers\Controller;
use App\Account;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacturaValidationController extends Controller
{
    public $quotation;

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }


   public function validate_movement_mercancia(){
        //VALIDA QUE NO SE HAYA CREADO YA UN MOVIMIENTO DE MERCANCIA PARA LA VENTA EN LA FACTURA, PARA NO REPETIR EL MOVIMIENTO EN NOTAS DE ENTREGA, PEDIDOS Y FACTURAS
       
        if(isset($this->quotation)){
            $account_mercancia_venta = Account::on(Auth::user()->database_name)->where('description', 'like', 'Mercancia para la Venta')->first();

            $details = DetailVoucher::on(Auth::user()->database_name)
                        ->where('id_invoice',$this->quotation->id)
                        ->where('id_account',$account_mercancia_venta->id)
                        ->where('status','C')
                        ->orderBy('id','desc')
                        ->first();

                       
            if(isset($details)){
                return false;
            }else{
                return true;
            }
        }

   }
}
