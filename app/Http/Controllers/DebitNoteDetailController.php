<?php

namespace App\Http\Controllers;

use App\Account;
use App\Anticipo;
use App\Client;
use App\DebitNote;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DebitNoteDetailController extends Controller
{
    public function createfacturar($id_creditnote,$coin)
    {
         $creditnote = null;
             
         if(isset($id_creditnote)){
             $creditnote = DebitNote::on(Auth::user()->database_name)->find($id_creditnote);
         }
 
         if(isset($creditnote)){
                                                            
            
            
            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_client',$creditnote->id_client)
                                        ->where(function ($query) use ($creditnote){
                                            $query->where('id_quotation',null)
                                                ->orWhere('id_quotation',$creditnote->id_quotation);
                                        })
                                        ->where('coin','like','bolivares')
                                        ->sum('amount');
            

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                ->where('id_client',$creditnote->id_client)
                                                ->where(function ($query) use ($creditnote){
                                                    $query->where('id_quotation',null)
                                                        ->orWhere('id_quotation',$creditnote->id_quotation);
                                                })
                                                ->where('coin','not like','bolivares')
                                                ->select( DB::raw('SUM(anticipos.amount/anticipos.rate) As dolar'))
                                                ->get();
             
           
            
            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo[0]->dolar)){
                $anticipos_sum_dolares = $total_dolar_anticipo[0]->dolar;
            }
            

            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 2)
                                            ->where('code_five', '<>',0)
                                            ->where('description','not like', 'Punto de Venta%')
                                            ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_creditnotes = DB::connection(Auth::user()->database_name)->table('products')
                                            ->join('debit_note_details', 'products.id', '=', 'debit_note_details.id_inventory')
                                            ->where('debit_note_details.id_debit_note',$id_creditnote)
                                            ->whereIn('debit_note_details.status',['1','C'])
                                            ->select('products.*','debit_note_details.id_inventory as id_inventory','debit_note_details.price as price','debit_note_details.rate as rate','debit_note_details.id as debit_note_details_id','products.code_comercial as code','debit_note_details.discount as discount',
                                            'debit_note_details.amount as amount_creditnote','debit_note_details.exento as exento','debit_note_details.islr as islr')
                                            ->get(); 


             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;

             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;

             $total_mercancia= 0;
             $total_servicios= 0;

             foreach($inventories_creditnotes as $var){
                 //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_creditnote) * $var->discount)/100;

                    $total += ($var->price * $var->amount_creditnote) - $percentage;
                //----------------------------- 

                if($var->exento == 0){

                    $base_imponible += ($var->price * $var->amount_creditnote) - $percentage; 

                }else{
                    $retiene_iva += ($var->price * $var->amount_creditnote) - $percentage; 
                }

                if($var->islr == 1){

                    $retiene_islr += ($var->price * $var->amount_creditnote) - $percentage; 

                }

                //me suma todos los precios de costo de los productos
                 if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_creditnote;
                }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_creditnote * $creditnote->bcv;
                }

                if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                    $total_mercancia += ($var->price * $var->amount_creditnote) - $percentage;
                }else{
                    $total_servicios += ($var->price * $var->amount_creditnote) - $percentage;
                }
             }

             $creditnote->total_factura = $total;
             $creditnote->base_imponible = $base_imponible;
            
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');    
             $anticipos_sum = 0;
             if(isset($coin)){
                 if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $creditnote->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }else{
                    $bcv = $creditnote->bcv;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando 
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($creditnote);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }
             }else{
                $bcv = null;
             }
             

            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
             $client = Client::on(Auth::user()->database_name)->find($creditnote->id_client ?? $creditnote->quotations['id_client']);

               
                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */
     
            $is_after = false;
            if(empty($creditnote->credit_days)){
                $is_after = true;
            }
             return view('admin.debit_notes.createfacturar',compact('price_cost_total','coin','creditnote'
                        , 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_islr','is_after'
                        ,'total_mercancia','total_servicios','client'));
         }else{
             return redirect('/debitnotes')->withDanger('La nota de Débito no existe');
         } 
         
    }


    public function anticipos_bolivares_to_dolars($creditnote)
    {
        
        $anticipos_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
        ->where('id_client',$creditnote->id_client)
        ->where(function ($query) use ($creditnote){
            $query->where('id_quotation',null)
                ->orWhere('id_quotation',$creditnote->id_quotation);
        })
        ->where('coin','like','bolivares')
        ->get();

        $total_dolar = 0;

        if(isset($anticipos_bolivares)){
            foreach($anticipos_bolivares as $anticipo){
                $total_dolar += bcdiv(($anticipo->amount / $anticipo->rate), '1', 2);
            }
        }
        

        return $total_dolar;
    }


    public function storefactura(Request $request)
    {
        

        $creditnote = DebitNote::on(Auth::user()->database_name)->findOrFail(request('id_creditnote'));

        $creditnote_status = $creditnote->status;

        if($creditnote->status == 'C' ){
            return redirect('debitnotes/facturar/'.$creditnote->id.'/'.$creditnote->coin.'')->withDanger('Ya esta factura fue procesada!');
        }
            
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        
        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $user_id = request('user_id');

        $bcv = $creditnote->rate;

        $coin = request('coin');

        $price_cost_total = request('price_cost_total');

        $anticipo = request('anticipo_form');
        $retencion_iva = request('total_retiene_iva');
        $retencion_islr = request('total_retiene_islr');
        $anticipo = request('anticipo_form');

        $sub_total = request('sub_total_form');
        $base_imponible = request('base_imponible_form');
        $sin_formato_amount = request('sub_total_form');
        $iva_percentage = request('iva_form');
        $sin_formato_total_pay = request('total_pay_form');

        $sin_formato_grandtotal = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount_form')));


        $total_mercancia = request('total_mercancia');
        $total_servicios = request('total_servicios');

        $date_payment = request('date-payment-form');

        $total_iva = 0;

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }
     
            /*---------------- */

            if($coin != 'bolivares'){
                $anticipo =  $anticipo * $bcv;
                $retencion_iva = $retencion_iva * $bcv;
                $retencion_islr = $retencion_islr * $bcv;
              
                $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
                $base_imponible = $base_imponible * $bcv;
                $sin_formato_amount = $sin_formato_amount * $bcv;
                $sin_formato_total_pay = $sin_formato_total_pay * $bcv;

                $sin_formato_grandtotal = $sin_formato_grandtotal * $bcv;

                $sub_total = $sub_total * $bcv;
    
            }

            $date_begin = request('date-begin-form');

            $creditnote->base_imponible = $base_imponible;
            $creditnote->amount =  $sin_formato_amount;
            $creditnote->amount_iva =  $sin_formato_amount_iva;
            $creditnote->amount_with_iva = $sin_formato_grandtotal;
            $creditnote->iva_percentage = $iva_percentage;
        

            $creditnote->status =  'C';
            $creditnote->save();
            
            /*---------------------- */

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   

            if(($creditnote_status != 'C') && ($creditnote_status != 'P')){

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);

                $header_voucher->description = "Nota de Debito ".$creditnote->id;
                $header_voucher->date = $date_payment;
            
                $header_voucher->status =  "1";

                //$header_voucher->id_credit_note = $creditnote->id;
            
                $header_voucher->save();

                /*Busqueda de Cuentas*/

                //Cuentas por Cobrar Clientes

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();  
            
                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,$sin_formato_grandtotal,0);
                }

                if($total_mercancia != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();
        
                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_mercancia);
                    }
                }
                
                if($total_servicios != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();
        
                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_servicios);
                    }
                }
                //Debito Fiscal IVA por Pagar

                $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();
                
                if($base_imponible != 0){
                    $total_iva = ($base_imponible * $iva_percentage)/100;

                    if(isset($account_cuentas_por_cobrar)){
                        $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$user_id,0,$total_iva);
                    }
                }

                $account_descuento_pago = Account::on(Auth::user()->database_name)->where('description', 'like', 'Descuentos en Pago')->first();
                
                if(isset($account_descuento_pago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_descuento_pago->id,$user_id,0,$sin_formato_grandtotal);
                }
                
                /*$account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                
                if(isset($account_anticipo)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo->id,$user_id,$sin_formato_grandtotal,0);
                } */
            }
             
            //Aqui pasa los creditnote_products a status C de Cobrado
            DB::connection(Auth::user()->database_name)->table('debit_note_details')
                                                        ->where('id_debit_note', '=', $creditnote->id)
                                                        ->update(['status' => 'C']);


            /* $anticipoController = new AnticipoController();

            $id_client = $creditnote->id_client ?? $creditnote->quotations['id_client'];

            $anticipoController->registerAnticipo($date_begin,$id_client,$account_descuento_pago->id,$coin,$sin_formato_grandtotal,$creditnote->rate,
            "Nota de Debito",$creditnote->id_quotation ?? null);
           ------------------------------------------------- */

            return redirect('debitnotes/facturado/'.$creditnote->id.'/'.$coin.'')->withSuccess('Nota de Débito Guardada con Exito!');

        
        
    }

    

    public function add_movement($bcv,$id_header,$id_account,$id_user,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);


        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        
     
        $detail->debe = $debe;
        $detail->haber = $haber;
       
      
        $detail->status =  "C";

         /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
         
            $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

            if($account->status != "M"){
                $account->status = "M";
                $account->save();
            }
         
    
        $detail->save();

    }


    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$creditnote_id,$user_id,$amount_debe,$amount_haber){


            //Cuentas por Cobrar Clientes

                //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
                if(isset($id_account)){
                    $this->add_movement($bcv,$header_voucher,$id_account,$creditnote_id,$user_id,$amount_debe,0);
                
                }//SIN DETERMINAR
                else if($payment_type == 7){
                            //------------------Sin Determinar
                    $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Ingresos No Identificados')->first(); 
            
                    if(isset($account_sin_determinar)){
                        $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$creditnote_id,$user_id,$amount_debe,0);
                    }
                }//PAGO DE CONTADO
                else if($payment_type == 2){
                    
                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 
            
                    if(isset($account_contado)){
                        $this->add_movement($bcv,$header_voucher,$account_contado->id,$creditnote_id,$user_id,$amount_debe,0);
                    }
                }//CONTRA ANTICIPO
                else if($payment_type == 3){
                            //--------------
                    $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first(); 
            
                    if(isset($account_contra_anticipo)){
                        $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$creditnote_id,$user_id,$amount_debe,0);
                    }
                } 
              

    }



    public function createfacturado($id_creditnote,$coin,$reverso = null)
    {
         $creditnote = null;
             
         if(isset($id_creditnote)){
             $creditnote = DebitNote::on(Auth::user()->database_name)->find($id_creditnote);
                                 
         }
 
         if(isset($creditnote)){
                
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');    

             if(isset($coin)){
                if($coin == 'bolivares'){
                   $bcv = null;
                }else{
                    $bcv = $creditnote->bcv;
                    $creditnote->anticipo = $creditnote->anticipo;
                }
            }else{
               $bcv = null;
            }
             
             return view('admin.debit_notes.createfacturado',compact('creditnote', 'datenow','bcv','coin','reverso'));
            }else{
             return redirect('/debitnotes')->withDanger('La Nota de Débito no existe');
         } 
         
    }


    public function movements($id_credit_note,$coin = null)
    {
        

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
            $creditnote = DebitNote::on(Auth::user()->database_name)->find($id_credit_note);
            
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
                                            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                            ->where('header_vouchers.description','LIKE','%Nota de Debito '.$id_credit_note.'%')
                                            ->where('detail_vouchers.status','C')
                                            ->get();

          
            
            if(empty($coin)){
                $coin = 'bolivares';
            }
         
        
        return view('admin.movements.index_movement_debitnote',compact('detailvouchers','coin','creditnote'));
    }

}
