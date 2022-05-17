<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use App\Account;
use App\AccountHistorial;
use App\Anticipo;
use App\Client;
use App\Company;
use App\Driver;
use App\Transport;
use App\Modelo;
use App\DetailVoucher;
use App\ExpensePayment;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\Http\Controllers\Validations\FacturaValidationController;
use App\Inventory;
use App\InventoryHistories;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PDF2LicController extends Controller
{

    function imprimirFactura($id_quotation,$coin = null)
    {


        $pdf = App::make('dompdf.wrapper');


             $quotation = null;

             if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);


             }else{
                return redirect('/invoiceslic')->withDanger('No llega el numero de la factura');
                }

             if(isset($quotation)){
                
                $transport = Transport::on(Auth::user()->database_name)->find($quotation->id_transport);
                $drivers = Driver::on(Auth::user()->database_name)->find($quotation->id_driver);
                $modelo = Modelo::on(Auth::user()->database_name)->find($transport->modelo_id);

                $payment_quotations = QuotationPayment::on(Auth::user()->database_name)
                                            ->where('id_quotation',$quotation->id)
                                            ->where('status',1)
                                            ->get();

                foreach($payment_quotations as $var){
                    $var->payment_type = $this->asignar_payment_type($var->payment_type);
                    if($coin == 'dolares'){
                        $var->amount = $var->amount / $var->rate;
                    }
                }


                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$quotation->id)
                ->where('quotation_products.status','C')
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                ,'quotation_products.retiene_islr as retiene_islr_quotation')
                ->get();


            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $company = Company::on(Auth::user()->database_name)->find(1);
            $tax_1   = $company->tax_1;
            $tax_3   = $company->tax_3;
 
            $global = new GlobalController();

            //Si la taza es automatica
            if($company->tiporate_id == 1){
                //esto es para que siempre se pueda guardar la tasa en la base de datos
                $bcv_quotation_product = $global->search_bcv();;
                $bcv = $global->search_bcv();;
            }else{
                //si la tasa es fija
                $bcv_quotation_product = $company->rate;
                $bcv = $company->rate;

            }

            if(($coin == 'bolivares') ){

                $coin = 'bolivares';
            }else{
                //$bcv = null;

                $coin = 'dolares';
            }

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva     = 0;
            $retiene_iva           = 0;
            $total_retiene_islr    = 0;
            $total_retiene         = 0;
            $total_iva             = 0;
            $total_base_impo_pcb   = 0;
            $total_iva_pcb         = 0;
            $total_venta           = 0;
            $retiene_islr          = 0;
            $variable_total        = 0;
            $base_imponible_pcb    = $tax_3;
            $iva                   = $tax_1;
            $rate                  = $quotation->bcv;



            if(empty($quotation->date_expiration)){
                $newVenc ="";
            } else {

                $newVenc = date("d-m-Y", strtotime($quotation->date_expiration));                
            }
 

                if($coin == 'bolivares'){
                    $bcv = null;

                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);

                 $pdf = $pdf->loadView('pdf.facturalic',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','rate','iva','newVenc','drivers','transport','modelo','tax_1','tax_3'))->setPaper('letter', 'landscape');
                 return $pdf->stream();

                }else{
                 return redirect('/invoiceslic')->withDanger('La factura no existe');
             }

    }

    
    function printQuotation($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
        $quotation = null;
            
        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        
                                
        }else{
            return redirect('/quotationslic/index')->withDanger('No se encontro la cotizacion');
        } 

        if(isset($quotation)){
            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
            ->where('id_client',$quotation->id_client)
            ->where(function ($query) use ($quotation){
                $query->where('id_quotation',null)
                    ->orWhere('id_quotation',$quotation->id);
            })
            ->where('coin','like','bolivares')
            ->sum('amount');


            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                ->where('id_client',$quotation->id_client)
                                ->where(function ($query) use ($quotation){
                                    $query->where('id_quotation',null)
                                        ->orWhere('id_quotation',$quotation->id);
                                })
                                ->where('coin','not like','bolivares')
                                ->select( DB::raw('SUM(anticipos.amount/anticipos.rate) As dolar'))
                                ->get();



            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo[0]->dolar)){
            $anticipos_sum_dolares = $total_dolar_anticipo[0]->dolar;
            }

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                        ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                        ->where('quotation_products.id_quotation',$quotation->id)
                                                        ->where('quotation_products.status','1')
                                                        ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                        'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                        ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                        ->get(); 
            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            $retiene_iva = 0;

            $total_retiene_islr = 0;
            $retiene_islr = 0;

            $total_mercancia= 0;
            $total_servicios= 0;

            foreach($inventories_quotations as $var){

                if($coin != "bolivares"){
                    $var->price = bcdiv($var->price / $var->rate, '1', 2);
                }

                //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;
                //----------------------------- 

                if($var->retiene_iva_quotation == 0){

                    $base_imponible += ($var->price * $var->amount_quotation) - $percentage; 

                }else{
                    $retiene_iva += ($var->price * $var->amount_quotation) - $percentage; 
                }

                if($var->retiene_islr_quotation == 1){

                    $retiene_islr += ($var->price * $var->amount_quotation) - $percentage; 

                }

                //me suma todos los precios de costo de los productos
                if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation;
                }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
                }

                if($coin != "bolivares"){
                    if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                        $total_mercancia += (($var->price * $var->amount_quotation) - $percentage) * $quotation->bcv;
                    }else{
                        $total_servicios += (($var->price * $var->amount_quotation) - $percentage) * $quotation->bcv;
                    }
                }else{
                    if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                        $total_mercancia += ($var->price * $var->amount_quotation) - $percentage;
                    }else{
                        $total_servicios += ($var->price * $var->amount_quotation) - $percentage;
                    }
                }
            }
            
            $quotation->total_factura = $total;
            $quotation->base_imponible = $base_imponible;
            
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
            $anticipos_sum = 0;
            if(isset($coin)){
                if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                }else{
                    $bcv = $quotation->bcv;
                    //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando 
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($quotation);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                }
            }else{
                $bcv = null;
            }
            
                                           
            
            if($coin == 'bolivares'){
                $bcv = null;
                
            }else{
                $bcv = $quotation->bcv;
            }

            $company = Company::on(Auth::user()->database_name)->find(1);
            
            // $lineas_cabecera = $company->format_header_line;

            $pdf = $pdf->loadView('pdf.quotationlic',compact('company','quotation','inventories_quotations','bcv','coin'));
            return $pdf->stream();
    
        }else{
            return redirect('/quotationslic/index')->withDanger('La cotizacion no existe');
        } 
        
    }

    public function anticipos_bolivares_to_dolars($quotation)
    {
        
        $anticipos_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
        ->where('id_client',$quotation->id_client)
        ->where(function ($query) use ($quotation){
            $query->where('id_quotation',null)
                ->orWhere('id_quotation',$quotation->id);
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
    
    function imprimirFactura_media($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                                     
             }else{
                return redirect('/invoiceslic')->withDanger('No llega el numero de la factura');
                } 
     
             if(isset($quotation)){

                 $payment_quotations = QuotationPayment::on(Auth::user()->database_name)
                                        ->where('id_quotation',$quotation->id)
                                        ->where('status',1)
                                        ->get();

                 foreach($payment_quotations as $var){
                    $var->payment_type = $this->asignar_payment_type($var->payment_type);
                    if($coin == 'dolares'){

                        
                        
                        $var->amount = $var->amount / $var->rate;



                    }
                 }
                 
                 $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                                ->where('quotation_products.id_quotation',$quotation->id)
                                                                ->where('quotation_products.status','C')
                                                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                                ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 
                 
                 if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);                
                
                 $pdf = $pdf->loadView('pdf.factura_media',compact('quotation','inventories_quotations','payment_quotations','bcv','company','coin'))->setPaper('letter','portrait');
                 return $pdf->stream();
         
                }else{
                 return redirect('/invoiceslic')->withDanger('La factura no existe');
             } 
             
        

        
    }

    function imprimirFactura_maq($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                                     
             }else{
                return redirect('/invoiceslic')->withDanger('No llega el numero de la factura');
                } 
     
             if(isset($quotation)){

                $payment_quotations = QuotationPayment::on(Auth::user()->database_name)
                                            ->where('id_quotation',$quotation->id)
                                            ->where('status',1)
                                            ->get();

                foreach($payment_quotations as $var){
                    $var->payment_type = $this->asignar_payment_type($var->payment_type);
                    if($coin == 'dolares'){
                        $var->amount = $var->amount / $var->rate;
                    }
                }


                 $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                                ->where('quotation_products.id_quotation',$quotation->id)
                                                                ->where('quotation_products.status','C')
                                                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                                ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 

                
                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->login;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                 $pdf = $pdf->loadView('pdf.factura_maq',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/invoiceslic')->withDanger('La factura no existe');
             } 
    }


    function deliverynotelic($id_quotation,$coin,$iva = null,$date = null,$serienote = null)
    {
      

        $quotation = null;

        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
       
       
            if(!(isset($quotation->date_delivery_note))){

                if(empty($quotation->number_delivery_note)){
                    //Me busco el ultimo numero en notas de entrega
                    $last_number = Quotation::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->orderBy('number_delivery_note','desc')->first();

                
                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $quotation->number_delivery_note = $last_number->number_delivery_note + 1;
                    }else{
                        $quotation->number_delivery_note = 1;
                    }
                }
                $global = new GlobalController();
                $retorno = $global->discount_inventory($id_quotation);

                if($retorno != 'exito'){
                    return redirect('quotationslic/register/'.$id_quotation.'/'.$coin.'')->withDanger($retorno);                     
                }
               

             }else{
                if(isset($quotation->bcv)){
                    $bcv = $quotation->bcv;
                 }
             }     
       
       
        }else{
                return redirect('/quotationslic')->withDanger('No llega el numero de la cotizacion');
        } 

        if(isset($quotation)){
            //$inventories_quotations = QuotationProduct::where('id_quotation',$quotation->id)->get();
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$quotation->id)
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                ->get();

                $inventories_quotationss = DB::connection(Auth::user()->database_name)->table('products')
                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$quotation->id)
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                ->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $company = Company::on(Auth::user()->database_name)->find(1);
            $tax_1   = $company->tax_1;
            $tax_3   = $company->tax_3;

            $global = new GlobalController();
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                //esto es para que siempre se pueda guardar la tasa en la base de datos
                $bcv_quotation_product = $global->search_bcv();
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv_quotation_product = $company->rate;
                $bcv = $company->rate;

            }

            if(($coin == 'bolivares') ){

                $coin = 'bolivares';
            }else{
                //$bcv = null;

                $coin = 'dolares';
            }

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva     = 0;
            $retiene_iva           = 0;
            $total_retiene_islr    = 0;
            $total_retiene         = 0;
            $total_iva             = 0;
            $total_base_impo_pcb   = 0;
            $total_iva_pcb         = 0;
            $total_venta           = 0;
            $retiene_islr          = 0;
            $variable_total        = 0;
            $base_imponible_pcb    = $tax_3;
            $iva                   = $tax_1;
            $rate                  = $quotation->bcv;


            foreach($inventories_quotationss as $vars){

                //Se calcula restandole el porcentaje de descuento (discount)
                $percentage = (($vars->price * $vars->amount_quotation) * $vars->discount)/100;
                $total += ($vars->price * $vars->amount_quotation) - $percentage;


                if( $vars->retiene_iva_quotation == 1 ){
                    $total_retiene         = 0;
                    $total_base_impo_pcb   = 0;
                    $total_iva_pcb         = 0;
                    $total_iva             = 0;
                    $total_venta        += $vars->price * $vars->amount_quotation ;

                }else{

                    $total_retiene         +=  ($vars->price * $vars->amount_quotation) - $percentage;
                    $base_imponible        += $total_retiene;
                    $total_iva             =  $total_retiene * ($iva / 100) ;
                    $total_base_impo_pcb   =  $total_retiene *($base_imponible_pcb /100) ;
                    $total_iva_pcb         =  $total_base_impo_pcb * ($iva /100);
                    $total_venta           =    $total_retiene + $total_iva + $total_iva_pcb;
                }

            }

            $quotation->amount = $total;
            $quotation->base_imponible = $base_imponible;
            $quotation->amount_iva = $base_imponible * $quotation->iva_percentage / 100;
            $quotation->amount_with_iva = $quotation->amount + $quotation->amount_iva;
            $quotation->iva_percentage = $iva;
            $quotation->date_delivery_note = $date;
            $quotation->serie_note = $serienote;   
            $quotation->save();

            $originalDate = $quotation->date_billing;
            $newDate = date("d/m/Y", strtotime($originalDate));

            $newVenc = date("d/m/Y", strtotime($quotation->date_expiration));

            if(empty($newVenc)){
                $newVenc ="";
            }

            $pdf = App::make('dompdf.wrapper');
            $pdf = $pdf->loadView('pdf.deliverynotelic',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','total','rate','total_retiene','total_iva','total_base_impo_pcb','total_iva_pcb','total_venta','iva','newDate','base_imponible','serienote','base_imponible_pcb'))->setPaper('letter', 'landscape');

            return $pdf->stream();

        }else{
            return redirect('/invoiceslic')->withDanger('La Nota de Entrega no existe');
        }
        
    }


    

    public function aggregate_movement_mercancia($quotation,$price_cost_total){
     
        if(isset($quotation)){
            $validation_factura = new FacturaValidationController($quotation);

            $return_validation_factura = $validation_factura->validate_movement_mercancia();
    
            
            if($return_validation_factura == true){
                
                if((isset($price_cost_total)) && ($price_cost_total != 0)){
                    $header_voucher  = new HeaderVoucher();
                    $header_voucher->setConnection(Auth::user()->database_name);
                    $date = Carbon::now();
                    $datenow = $date->format('Y-m-d');
                    $user       =   auth()->user();    
            
                    $header_voucher->description = "Ventas de Bienes o servicios.";
                    $header_voucher->date = $datenow;
                    
                
                    $header_voucher->status =  "1";
                
                    $header_voucher->save();
                    $account_mercancia_venta = Account::on(Auth::user()->database_name)->where('description', 'like', 'Mercancia para la Venta')->first();
    
                    if(isset($account_mercancia_venta)){
                        $this->add_movement($quotation->bcv,$header_voucher->id,$account_mercancia_venta->id,$quotation->id,$user->id,0,$price_cost_total);
                    }
    
                    //Costo de Mercancia
    
                    $account_costo_mercancia = Account::on(Auth::user()->database_name)->where('description', 'like', 'Costo de Mercancia')->first();
    
                    if(isset($account_costo_mercancia)){
                        $this->add_movement($quotation->bcv,$header_voucher->id,$account_costo_mercancia->id,$quotation->id,$user->id,$price_cost_total,0);
                    }
                }
            }
        }
    }

    public function add_movement($bcv,$id_header,$id_account,$id_invoice,$id_user,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);


        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        $detail->id_invoice = $id_invoice;

      /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
        $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/

       
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


    function order($id_quotation,$coin,$iva,$date)
    {
      

        $pdf = App::make('dompdf.wrapper');
    
             $quotation = null;
                 
            if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
                
                 if(!(isset($quotation->date_order))){
                    if(empty($quotation->number_order)){
                        //Me busco el ultimo numero en notas de entrega
                        $last_number = Quotation::on(Auth::user()->database_name)->where('number_order','<>',NULL)->orderBy('number_order','desc')->first();
                    
                        //Asigno un numero incrementando en 1
                        if(isset($last_number)){
                            $quotation->number_order = $last_number->number_order + 1;
                        }else{
                            $quotation->number_order = 1;
                        }
                    }
                    //if(!(isset($quotation->date_delivery_note)) && !(isset($quotation->date_order))){
                    $global = new GlobalController();
                    $retorno = $global->discount_inventory($id_quotation);

                    if($retorno != 'exito'){
                        return redirect('quotationslic/register/'.$id_quotation.'/'.$coin.'')->withDanger($retorno);                     
                    }
                    //}
                        

                 }else{
                    if(isset($quotation->bcv)){
                        $bcv = $quotation->bcv;
                     }
                 }
                 
                                     
            }else{
                return redirect('/quotationslic/index')->withDanger('No llega el numero de la cotizacion');
            } 
     
             if(isset($quotation)){
               
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                                ->where('quotation_products.id_quotation',$quotation->id)
                                                                ->whereIn('quotation_products.status',['1','C'])
                                                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                                ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 

                $total= 0;
                $base_imponible= 0;
                $price_cost_total= 0;

                //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
                $total_retiene_iva = 0;
                $retiene_iva = 0;

                $total_retiene_islr = 0;
                $retiene_islr = 0;

                foreach($inventories_quotations as $var){
                    if(isset($coin) && ($coin != 'bolivares')){
                        $var->price =  bcdiv(($var->price / ($var->rate ?? 1)), '1', 2);
                    }
                    //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;
                    //----------------------------- 

                    if($var->retiene_iva_quotation == 0){

                        $base_imponible += ($var->price * $var->amount_quotation) - $percentage; 

                    }

                    if($var->retiene_islr_quotation == 1){

                        $retiene_islr += ($var->price * $var->amount_quotation) - $percentage; 

                    }
                    //me suma todos los precios de costo de los productos
                    if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                        $price_cost_total += $var->price_buy * $var->amount_quotation;
                    }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                        $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
                    }
                
                }
                $rate = null;
                
                if(isset($coin) && ($coin != 'bolivares')){
                    $rate = $quotation->bcv;
                }
                $quotation->iva_percentage = $iva;
                $quotation->amount = $total * ($rate ?? 1);
                $quotation->base_imponible = $base_imponible * ($rate ?? 1);
                $quotation->amount_iva = $base_imponible * $quotation->iva_percentage / 100;
                $quotation->amount_with_iva = ($quotation->amount + $quotation->amount_iva);
                
                
                
                $quotation->date_order = $date;
                $quotation->save();

                if(isset($coin) && ($coin != 'bolivares')){
                   
                    $quotation->amount =  $quotation->amount / ($rate ?? 1);
                    $quotation->base_imponible = $quotation->base_imponible / ($rate ?? 1);
                    $quotation->amount_iva = $quotation->base_imponible / $quotation->iva_percentage / 100;
                    $quotation->amount_with_iva = ( $quotation->amount_with_iva) / ($rate ?? 1);
                }



                $quotation->total_factura = $total;
                //$quotation->base_imponible = $base_imponible;

                
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');    
                $anticipos_sum = 0;
                if(isset($coin)){
                    if($coin == 'bolivares'){
                        $bcv = null;
                    }else{
                        $bcv = $quotation->bcv;
                    }
                }else{
                    $bcv = null;
                }


                /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
                $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

                $this->aggregate_movement_mercancia($quotation,$price_cost_total);

                $company = Company::on(Auth::user()->database_name)->find(1);
                
                $pdf = $pdf->loadView('pdf.order',compact('quotation','inventories_quotations','bcv','company'
                                                                ,'total_retiene_iva','total_retiene_islr'));
                return $pdf->stream();
         
            }else{
                return redirect('/invoiceslic')->withDanger('La nota de entrega no existe');
            } 
             
        

        
    }

    function deliverynotemediacarta($id_quotation,$coin,$iva,$date_delivery)
    {
      

        $pdf = App::make('dompdf.wrapper');
    
             $quotation = null;
                 
            if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

                 

                 if(!(isset($quotation->date_delivery_note))){
                    if(empty($quotation->number_delivery_note)){
                        //Me busco el ultimo numero en notas de entrega
                        $last_number = Quotation::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->orderBy('number_delivery_note','desc')->first();
                        //Asigno un numero incrementando en 1
                        if(isset($last_number)){
                            $quotation->number_delivery_note = $last_number->number_delivery_note + 1;
                        }else{
                            $quotation->number_delivery_note = 1;
                        }
                    }
                    
                    $global = new GlobalController();
                    $retorno = $global->discount_inventory($id_quotation);

                    if($retorno != 'exito'){
                        return redirect('quotationslic/register/'.$id_quotation.'/'.$coin.'')->withDanger($retorno);                     
                    }
                    

                 }else{
                    if(isset($quotation->bcv)){
                        $bcv = $quotation->bcv;
                     }
                 }
                 
                                     
            }else{
                return redirect('/quotationslic/index')->withDanger('No llega el numero de la cotizacion');
            } 
     
             if(isset($quotation)){
               
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                                ->where('quotation_products.id_quotation',$quotation->id)
                                                                ->whereIn('quotation_products.status',['1','C'])
                                                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                                ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 

                $total= 0;

                $base_imponible= 0;
                $price_cost_total= 0;

                //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
                $total_retiene_iva = 0;
                $retiene_iva = 0;

                $total_retiene_islr = 0;
                $retiene_islr = 0;

                $price = 0;
                
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');    
                $anticipos_sum = 0;
                if(isset($coin)){
                    if($coin == 'bolivares'){
                        $bcv = null;
                    }else{
                        $bcv = $quotation->bcv;
                    }
                }else{
                    $bcv = null;
                }

                foreach($inventories_quotations as $var){

                    if(isset($coin) && ($coin != 'bolivares')){
                        $var->price =  bcdiv(($var->price / ($var->rate ?? 1)), '1', 2);
                    }

                    //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;
                    //----------------------------- 

                    if($var->retiene_iva_quotation == 0){

                        $base_imponible += ($var->price * $var->amount_quotation) - $percentage; 

                    }else{
                        $retiene_iva += ($var->price * $var->amount_quotation) - $percentage; 
                    }

                    if($var->retiene_islr_quotation == 1){

                        $retiene_islr += ($var->price * $var->amount_quotation) - $percentage; 

                    }
                    //me suma todos los precios de costo de los productos
                    if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                        $price_cost_total += $var->price_buy * $var->amount_quotation;
                    }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                        $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
                    }
                
                }

                if(isset($coin) && ($coin != 'bolivares')){
                    $rate = $quotation->bcv;
                }

                $quotation->iva_percentage = $iva;
                $quotation->amount = $total * ($rate ?? 1);
                $quotation->base_imponible = $base_imponible * ($rate ?? 1);
                $quotation->amount_iva = ($base_imponible * $quotation->iva_percentage / 100) * ($rate ?? 1);
               
                $quotation->amount_with_iva = ($quotation->amount + $quotation->amount_iva);
                
                
                $quotation->date_delivery_note = $date_delivery;
                $quotation->save();

                $global = new GlobalController();

                $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
                ->where('id_quotation', '=', $quotation->id)
                ->where('status','!=','X')
                ->get(); // Conteo de Productos para incluiro en el historial de inventario
                   
                
                foreach($quotation_products as $det_products){ // guardado historial de inventario 
                $global->transaction_inv('nota',$det_products->id_inventory,'nota',$det_products->amount,$det_products->price,$quotation->date_billing,1,1,0,$det_products->id_inventory_histories,$det_products->id,$quotation->id,0);
                }
                
               
                if(isset($coin) && ($coin != 'bolivares')){
                    $quotation->amount =  $quotation->amount / ($rate ?? 1);
                    $quotation->base_imponible = $quotation->base_imponible / ($rate ?? 1);
                    $quotation->amount_iva =    $quotation->amount_iva / ($rate ?? 1);
                    $quotation->amount_with_iva = ( $quotation->amount_with_iva) / ($rate ?? 1);
                }

                /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
                $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

                
                $company = Company::on(Auth::user()->database_name)->find(1);

                $this->aggregate_movement_mercancia($quotation,$price_cost_total);
                
                $pdf = $pdf->loadView('pdf.deliverynotemediacarta',compact('quotation','inventories_quotations','bcv','company'
                                                                ,'retiene_iva','total_retiene_islr'));
                return $pdf->stream();
         
            }else{
                return redirect('/invoiceslic')->withDanger('La nota de entrega no existe');
            } 
             
        

        
    }
    
    function deliverynote_expense($id_expense,$coin,$iva,$date)
    {
      

        $pdf = App::make('dompdf.wrapper');
    
             $expense = null;
                 
             if(isset($id_expense)){
                $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

                
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');   

                $expense->iva_percentage = $iva;
                $expense->date_delivery_note = $date;

                
                if(isset($expense->bcv)){
                    $bcv = $expense->bcv;
                    }
                
                 
                                     
             }else{
                return redirect('/expensesandpurchases')->withDanger('No llega el numero de la cotizacion');
                } 
     
             if(isset($expense)){
               
                $inventories_expenses = DB::connection(Auth::user()->database_name)->table('products')
                                                           ->rightJoin('expenses_details', 'products.id', '=', 'expenses_details.id_inventory')
                                                           ->where('expenses_details.id_expense',$expense->id)
                                                           ->where('expenses_details.status',['1','C'])
                                                           ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                                                           'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                                                           ,'expenses_details.islr as retiene_islr_expense','expenses_details.description as description_expense')
                                                           ->get(); 


                $total= 0;
                $base_imponible= 0;
                $price_cost_total= 0;

                //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
                $total_retiene_iva = 0;
                $retiene_iva = 0;

                $total_retiene_islr = 0;
                $retiene_islr = 0;

                foreach($inventories_expenses as $var){
                    //Se calcula restandole el porcentaje de descuento (discount)
                        
                        $total += ($var->price * $var->amount_expense);
                    //----------------------------- 
     
                    if($var->retiene_iva_expense == 0){
     
                        $base_imponible += ($var->price * $var->amount_expense); 
     
                    }else{
                        $retiene_iva += ($var->price * $var->amount_expense); 
                    }
     
                    if($var->retiene_islr_expense == 1){
     
                        $retiene_islr += ($var->price * $var->amount_expense); 
     
                    }
     
                }
     
                
                $expense->amount = $total;
                $expense->base_imponible = $base_imponible;
                $expense->amount_iva = $base_imponible * $expense->iva_percentage / 100;
                $expense->amount_with_iva = $expense->amount + $expense->amount_iva;
                $expense->save();


                $expense->total_factura = $total;
                //$expense->base_imponible = $base_imponible;

                
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');    
                $anticipos_sum = 0;
                if(isset($coin)){
                    if($coin == 'bolivares'){
                        $bcv = null;
                    }else{
                        $bcv = $expense->rate;
                    }
                }else{
                    $bcv = null;
                }


                $company = Company::on(Auth::user()->database_name)->find(1);
                
                $pdf = $pdf->loadView('pdf.deliverynote_expense',compact('expense','inventories_expenses','bcv','company'
                                                                ,'total_retiene_iva','total_retiene_islr'));
                return $pdf->stream();
         
            }else{
                return redirect('/expensesandpurchases')->withDanger('La nota de entrega no existe');
            } 
             
        

        
    }




    function asignar_payment_type($type){
      
        if($type == 1){
            return "Cheque";
        }
        if($type == 2){
            return "Contado";
        }
        if($type == 3){
            return "Contra Anticipo";
        }
        if($type == 4){
            return "Crédito";
        }
        if($type == 5){
            return "Depósito Bancario";
        }
        if($type == 6){
            return "Efectivo";
        }
        if($type == 7){
            return "Indeterminado";
        }
        if($type == 8){
            return "Tarjeta Coorporativa";
        }
        if($type == 9){
            return "Tarjeta de Crédito";
        }
        if($type == 10){
            return "Tarjeta de Débito";
        }
        if($type == 11){
            return "Transferencia";
        }
    }



    
    function imprimirinventory(){
      
        $pdf_inventory = App::make('dompdf.wrapper');

        //$inventories = Inventory::on(Auth::user()->database_name)->where('status','1')->orderBy('id','desc')->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        $inventories = InventoryHistories::on(Auth::user()->database_name)
        ->join('products','products.id','inventory_histories.id_product')
      
                    
        ->where(function ($query){
            $query->where('products.type','MERCANCIA')
                ->orWhere('products.type','COMBO');
        })
       
       ->where('inventory_histories.status','A')
       ->select('inventory_histories.id as id_inventory','inventory_histories.amount_real as amount_real','products.id as id','products.code_comercial as code_comercial','products.description as description','products.price as price','products.photo_product as photo_product')       
       ->orderBy('inventory_histories.id' ,'DESC')
       ->get();     
        
        
        $inventories = $inventories->unique('id');

        $inventories = $inventories->sortBydesc('amount_real');

        $company = Company::on(Auth::user()->database_name)->find(1);

        $pdf_inventory = $pdf_inventory->loadView('pdf.inventory',compact('inventories','datenow','company'));
        return $pdf_inventory->stream();
                 
    }


   



    function imprimirExpense($id_expense,$coin){
      

        $pdf = App::make('dompdf.wrapper');

        
             $expense = null;
                 
             if(isset($id_expense)){
                 $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
              
                                     
             }else{
                return redirect('/expensesandpurchases')->withDanger('No llega el numero del Gasto o Compra');
                } 
     
             if(isset($expense)){

                 $payment_expenses = ExpensePayment::on(Auth::user()->database_name)
                 ->where('status','NOT LIKE','X')
                 ->where('id_expense',$expense->id)->get();
                 
                 
                 if(!$payment_expenses->isEmpty()){
                    foreach($payment_expenses as $var){
                        $var->payment_type = $this->asignar_payment_type($var->payment_type);
                     }
                 }
                 


                $inventories_expenses = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
           

                 if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $expense->rate;
                }

                 $company = Company::on(Auth::user()->database_name)->find(1);

                 $pdf = $pdf->loadView('pdf.expense',compact('bcv','coin','expense','inventories_expenses','payment_expenses','company'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/expensesandpurchases')->withDanger('La Compra no existe');
             } 
             
        

        
    }

    function imprimirExpenseMedia($id_expense,$coin){
      

        $pdf = App::make('dompdf.wrapper');

        
             $expense = null;
                 
             if(isset($id_expense)){
                 $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
              
                                     
             }else{
                return redirect('/expensesandpurchases')->withDanger('No llega el numero del Gasto o Compra');
                } 
     
             if(isset($expense)){

                 $payment_expenses = ExpensePayment::on(Auth::user()->database_name)
                 ->where('status','NOT LIKE','X')
                 ->where('id_expense',$expense->id)->get();

                 if(!$payment_expenses->isEmpty()){
                    foreach($payment_expenses as $var){
                        $var->payment_type = $this->asignar_payment_type($var->payment_type);
                     }
                 }
                 


                $inventories_expenses = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
            
                /*$total= 0;
                $base_imponible= 0;
                $ventas_exentas= 0;
                foreach($inventories_expenses as $var){

                    $total += ($var->price * $var->amount);
                    //----------------------------- 

                    if($var->exento == 0){

                        $base_imponible += ($var->price * $var->amount); 

                    }
                    if($var->exento == 1){
    
                        $ventas_exentas += ($var->price * $var->amount); 
    
                    }
                }

                if($coin != 'bolivares'){
                    $total = $total / $expense->rate;
                    $base_imponible = $base_imponible / $expense->rate;
                    $ventas_exentas = $ventas_exentas / $expense->rate;
                }
    
                 $expense->sub_total = $total;
                 $expense->base_imponible = $base_imponible;
                 $expense->ventas_exentas = $ventas_exentas;
                    */

                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $expense->rate;
                }
                 $company = Company::on(Auth::user()->database_name)->find(1);

                 $pdf = $pdf->loadView('pdf.expense_media',compact('bcv','coin','expense','inventories_expenses','payment_expenses','company'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/expensesandpurchases')->withDanger('La Compra no existe');
             } 
             
    }


    function print_previousexercise($date_begin,$date_end){
      
        $pdf = App::make('dompdf.wrapper');

        $account_historial = AccountHistorial::on(Auth::user()->database_name)->where('date_begin',$date_begin)->where('date_end',$date_end)->orderBy('id','asc')->get();
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        $company = Company::on(Auth::user()->database_name)->find(1);

        $pdf = $pdf->loadView('pdf.previousexercise',compact('account_historial','datenow','company'));
        return $pdf->stream();
                 
    }

    

    public function calculation($coin)
    {
        
        $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                         ->orderBy('code_two', 'asc')
                         ->orderBy('code_three', 'asc')
                         ->orderBy('code_four', 'asc')
                         ->orderBy('code_five', 'asc')
                         ->get();
        
                       
        if(isset($accounts)) {
            
            foreach ($accounts as $var) 
            {
                if($var->code_one != 0)
                {
                    if($var->code_two != 0)
                    {
                        if($var->code_three != 0)
                        {
                            if($var->code_four != 0)
                            {
                                if($var->code_five != 0)
                                {
                                     /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                                
                                     if($coin == 'bolivares'){
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                        $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                        $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                                        $var->balance =  $var->balance_previus;
    
                                       
                                        }else{
                                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                            
                                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                           
                                            
                                            
                                        }
                                        $total_debe = $total_debe[0]->debe;
                                        $total_haber = $total_haber[0]->haber;
                                        if(isset($total_dolar_debe[0]->dolar)){
                                            $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                            $var->dolar_debe = $total_dolar_debe;
                                        }
                                        if(isset($total_dolar_haber[0]->dolar)){
                                            $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                            $var->dolar_haber = $total_dolar_haber;
                                        }
                                    
                                        $var->debe = $total_debe;
                                        $var->haber = $total_haber;

                                        if(($var->balance_previus != 0) && ($var->rate !=0)){
                                            $var->balance =  $var->balance_previus;
                                        }
                                
                                }else{
                            
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                                
                                    if($coin == 'bolivares'){
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                                    $var->balance =  $var->balance_previus;

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);
                                
                                    }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                        
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                        /*if(($var->balance_previus != 0) && ($var->rate !=0))
                                        $var->balance =  $var->balance_previus / $var->rate;*/
                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                    if(isset($total_dolar_debe[0]->dolar)){
                                        $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                        $var->dolar_debe = $total_dolar_debe;
                                    }
                                    if(isset($total_dolar_haber[0]->dolar)){
                                        $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                        $var->dolar_haber = $total_dolar_haber;
                                    }
                                
                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                }  
                            }else{          
                            
                                if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND
                                                
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND
                                                
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?  AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);
                                
                                }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                        
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);
                        
                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                
                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                      
                                            
                            }           
                        }else{
                                            
                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                
                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);
                                
                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);
                                    
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);
                    
                                }
                                
                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;

                                

                                $total_balance = $total_balance[0]->balance;
                                $var->balance = $total_balance;
                        }
                    }else{
                        if($coin == 'bolivares'){
                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,'C']);
                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,'C']);

                            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ?
                                            '
                                            , [$var->code_one]);
                            
                            }else{
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                FROM accounts a
                                INNER JOIN detail_vouchers d 
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                '
                                , [$var->code_one,'C']);
                                
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                FROM accounts a
                                INNER JOIN detail_vouchers d 
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                '
                                , [$var->code_one,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ?
                                            '
                                            , [$var->code_one]);
                
                            }
                            $total_debe = $total_debe[0]->debe;
                            $total_haber = $total_haber[0]->haber;
                            $var->debe = $total_debe;
                            $var->haber = $total_haber;

                            $total_balance = $total_balance[0]->balance;

                            $var->balance = $total_balance;

                    }
                }else{
                    return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                }
            } 
        
        }else{
            return redirect('/accounts/menu')->withDanger('No hay Cuentas');
        }              
                 
       
        
         return $accounts;
    }

    function previewfactura($id_quotation,$coin = null){ //imprimir previo de factura licores

        $quotation = null;
        $drivers = null;

        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation)){
           
            $transport = Transport::on(Auth::user()->database_name)->find($quotation->id_transport);
            $drivers = Driver::on(Auth::user()->database_name)->find($quotation->id_driver);
            $modelo = Modelo::on(Auth::user()->database_name)->find($transport->modelo_id);
            //$inventories_quotations = QuotationProduct::where('id_quotation',$quotation->id)->get();
          

   

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
            ->where('quotation_products.id_quotation',$quotation->id)
            ->where('quotation_products.status','1')
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                ->get();
        

            $inventories_quotationss = DB::connection(Auth::user()->database_name)->table('products')
            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
            ->where('quotation_products.id_quotation',$quotation->id)
            ->where('quotation_products.status','1')
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                ->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $company = Company::on(Auth::user()->database_name)->find(1);
            $tax_1   = $company->tax_1;
            $tax_3   = $company->tax_3;

            $global = new GlobalController();
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                //esto es para que siempre se pueda guardar la tasa en la base de datos
                $bcv_quotation_product = $global->search_bcv();
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv_quotation_product = $company->rate;
                $bcv = $company->rate;

            }

            if(($coin == 'bolivares') ){

                $coin = 'bolivares';
            }else{
                //$bcv = null;

                $coin = 'dolares';
            }

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva     = 0;
            $retiene_iva           = 0;
            $total_retiene_islr    = 0;
            $total_retiene         = 0;
            $total_iva             = 0;
            $total_base_impo_pcb   = 0;
            $total_iva_pcb         = 0;
            $total_venta           = 0;
            $retiene_islr          = 0;
            $variable_total        = 0;
            $base_imponible_pcb    = $tax_3;
            $iva                   = $tax_1;
            $rate                  = $quotation->bcv;




            $originalDate = $quotation->date_billing;
            $newDate = date("d-m-Y", strtotime($originalDate));

            if(empty($quotation->date_expiration)){
                $newVenc ="";
            } else {

                $newVenc = date("d-m-Y", strtotime($quotation->date_expiration));                
            }


            $pdf = App::make('dompdf.wrapper');
            $pdf = $pdf->loadView('pdf.previewfactura',compact('quotation','inventories_quotations','datenow','newDate','bcv','coin','bcv_quotation_product','rate','tax_1','tax_3','drivers','transport','modelo'))->setPaper('letter', 'landscape');
            return $pdf->stream();

        }else{
            return redirect('/invoiceslic')->withDanger('La factura no existe');
        }
    }


    function previewnote($id_quotation,$coin = null,$serienote){

        $quotation = null;

        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);

            if(!(isset($quotation->date_delivery_note))){

                if(empty($quotation->number_delivery_note)){
                    //Me busco el ultimo numero en notas de entrega
                    $last_number = Quotation::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->orderBy('number_delivery_note','desc')->first();

                
                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $quotation->number_delivery_note = $last_number->number_delivery_note + 1;
                    }else{
                        $quotation->number_delivery_note = 1;
                    }
                }
                $global = new GlobalController();
                $retorno = $global->discount_inventory($id_quotation);

                if($retorno != 'exito'){
                    return redirect('quotationslic/register/'.$id_quotation.'/'.$coin.'')->withDanger($retorno);                     
                }
               

             }else{
                if(isset($quotation->bcv)){
                    $bcv = $quotation->bcv;
                 }
             }     
       
       
        }else{
                return redirect('/quotationslic')->withDanger('No llega el numero de la cotizacion');
        } 

        if(isset($quotation)){
            //$inventories_quotations = QuotationProduct::where('id_quotation',$quotation->id)->get();


            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$quotation->id)
                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                ->get();


            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $company = Company::on(Auth::user()->database_name)->find(1);
            $tax_1   = $company->tax_1;
            $tax_3   = $company->tax_3;

            $global = new GlobalController();
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                //esto es para que siempre se pueda guardar la tasa en la base de datos
                $bcv_quotation_product = $global->search_bcv();
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv_quotation_product = $company->rate;
                $bcv = $company->rate;

            }

            if(($coin == 'bolivares') ){

                $coin = 'bolivares';
            }else{
                //$bcv = null;

                $coin = 'dolares';
            }

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva     = 0;
            $retiene_iva           = 0;
            $total_retiene_islr    = 0;
            $total_retiene         = 0;
            $total_iva             = 0;
            $total_base_impo_pcb   = 0;
            $total_iva_pcb         = 0;
            $total_venta           = 0;
            $retiene_islr          = 0;
            $variable_total        = 0;
            $base_imponible_pcb    = $tax_3;
            $iva                   = $tax_1;
            $rate                  = $quotation->bcv;
 


            $originalDate = $quotation->date_billing;
            $newDate = date("d/m/Y", strtotime($originalDate));

            if(empty($quotation->date_expiration)){
                $newVenc ="";
            } else {

                $newVenc = date("d-m-Y", strtotime($quotation->date_expiration));                
            }
            
          
            $transport = Transport::on(Auth::user()->database_name)->find($quotation->id_transport ?? 1);
            $drivers = Driver::on(Auth::user()->database_name)->find($quotation->id_driver ?? 1);
            $modelo = Modelo::on(Auth::user()->database_name)->find($transport->modelo_id ?? 1);
       

            $pdf = App::make('dompdf.wrapper');
            $pdf = $pdf->loadView('pdf.previewnote',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','rate','newDate','serienote','tax_1','tax_3','drivers','transport','modelo'))->setPaper('letter', 'landscape');

            return $pdf->stream();

        }else{
            return redirect('/invoiceslic')->withDanger('La Nota de Entrega no existe');
        }
    }



}
