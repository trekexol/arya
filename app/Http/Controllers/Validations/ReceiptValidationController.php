<?php

namespace App\Http\Controllers\Validations;

use App\DetailVoucher;
use App\Http\Controllers\Controller;
use App\Account;
use App\Receipts;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptValidationController extends Controller
{
    public $quotation;

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }


   public function validate_movement_mercancia(){
        //VALIDA QUE NO SE HAYA CREADO YA UN MOVIMIENTO DE MERCANCIA PARA LA VENTA EN LA FACTURA, PARA NO REPETIR EL MOVIMIENTO EN NOTAS DE ENTREGA, PEDIDOS Y FACTURAS
       
        if(isset($this->quotation)){
            $account_mercancia_venta = Account::on(Auth::user()->database_name)->where('description', 'like', 'Costo de Mercancía')->first();

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

   public function validateNumberInvoice(){

        $last_number = Receipts::on(Auth::user()->database_name)
        ->where('number_invoice','<>',NULL)->orderBy('number_invoice','desc')->first();

        //Asigno un numero incrementando en 1
        if(empty($this->quotation->number_invoice)){
            if(isset($last_number)){
                $this->quotation->number_invoice = $last_number->number_invoice + 1;
            }else{
                $this->quotation->number_invoice = 1;
            }
        }
        return $this->quotation;
   }
   
}
