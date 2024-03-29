<?php

namespace App\Http\Controllers;

use App\Client;
use App\DetailVoucher;
use App\Http\Controllers\Historial\HistorialQuotationController;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Quotation;
use App\Anticipo;
use App\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryNoteLicController extends Controller
{


    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Notas de Entrega');
    }
 

    public function index(request $request,$id_quotation = null,$number_pedido = null,$saldar = null)
    {
 
        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

            if(isset($id_quotation)) {
                $quotationsupd = Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['number_pedido' => $number_pedido]);
               
                if($saldar == '0') {
                    $quotationsupdt = Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['status' => '1']);
                    
                    $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

                    $anticipo = Anticipo::on(Auth::user()->database_name)->where('id_quotation',$id_quotation)->update([ 'status' => '1' ]);
         
                }

            }

            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_delivery_note' ,'DESC')
                                    ->where('date_delivery_note','<>',null)
                                    ->where('date_billing',null)
                                    ->whereIn('status',[1,'M'])
                                    ->get();


            
            return view('admin.quotationslic.indexdeliverynote',compact('eliminarmiddleware','actualizarmiddleware','agregarmiddleware','quotations'));
       
    }
 

    public function indexsald($id_quotation = null,$number_pedido = null)
    {
        if($this->userAccess->validate_user_access($this->modulo)){
            $user       =   auth()->user();
            $users_role =   $user->role_id;

            if(isset($id_quotation)) {
                $quotationsupd = Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['number_pedido' => $number_pedido]);
                
            }

            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('updated_at' ,'DESC')
                                    ->where('date_delivery_note','<>',null)
                                    ->where('date_billing',null)
                                    ->whereIn('status',['C'])
                                    
                                    ->get();


            
            return view('admin.quotationslic.indexdeliverynotesald',compact('quotations'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function storesaldar($id=null,$anticipo=null,$totalfac)
    {

                
                $quotationsupdt = Quotation::on(Auth::user()->database_name)->where('id',$id)->update(['status' => 'C']);
                    
                $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id);

                $anticipo = Anticipo::on(Auth::user()->database_name)->where('id_quotation',$id)->update([ 'status' => 'C' ]);

                return redirect('quotationslic/indexnotasdeentrega')->withSuccess('Nota '.$quotation->number_delivery_note.' Saldada Exitosamente!');
    
    }  



    public function createdeliverynote($id_quotation,$coin)
    {   
        
        $quotation = null;
             
        if(isset($id_quotation)){
           $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
           
           $quotation->coin = $coin;
           
           $quotation->save();
        }

        if(isset($quotation)){
           
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                           ->where('quotation_products.id_quotation',$quotation->id)
                                                           ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                           'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                           ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                           ->get(); 

           
           $total= 0;
           $base_imponible= 0;

           //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
           $total_retiene_iva = 0;
           $retiene_iva = 0;

           $total_retiene_islr = 0;
           $retiene_islr = 0;

           foreach($inventories_quotations as $var){
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

           }

           $quotation->total_factura = $total;
           $quotation->base_imponible = $base_imponible;

           $date = Carbon::now();
           $datenow = $date->format('Y-m-d');    


           if($coin == 'bolivares'){
               $bcv = null;
               
           }else{
               $bcv = $quotation->bcv;
           }
           
           /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
           $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

           if($client->percentage_retencion_iva != 0){
               $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
           }

          
           if($client->percentage_retencion_islr != 0){
               $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
           }

           /*-------------- */
           $input = 54;
    
           
               //Me busco el ultimo numero en notas de entrega
           $last_number = Quotation::on(Auth::user()->database_name)
           ->where('serie_note','<>',NULL)
           ->where('number_delivery_note','<>',NULL)
           ->select('serie_note')
           ->orderBy('number_delivery_note','desc')->first();
           
           
               //Asigno un numero incrementando en 1
               if(!empty($last_number)){
                   
                   $input2 = $last_number->serie_note;
                   $int = preg_replace('/\D/', '', $input2);

                   $new = (int)$int + 1;

                   $newcontrol = str_pad($new, 4, "0", STR_PAD_LEFT); // rellenando 4 digitos

               
               }else{

                   $newcontrol = 0;
               }
           
           return view('admin.quotationslic.createdeliverynote',compact('coin','quotation','datenow','bcv','total_retiene_iva','total_retiene_islr','newcontrol'));
        
       }else{
            return redirect('/quotationslic')->withDanger('La cotizacion no existe');
        } 
        
    }

    public function reversar_delivery_note(Request $request)
    { 
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        $id_quotation = $request->id_quotation_modal;

        
        
       $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        QuotationProduct::on(Auth::user()->database_name)
                        ->join('inventories','inventories.id','quotation_products.id_inventory')
                        ->join('products','products.id','inventories.product_id')
                        ->where('products.type','MERCANCIA')
                        ->where('id_quotation',$quotation->id)
                        ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount') , 'quotation_products.status' => 'X']);
    
        $quotation->status = 'X';
        $quotation->save();
        
        $global = new GlobalController;                                                
        
        $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
        ->where('id_quotation', '=', $quotation->id)->get();

        foreach($quotation_products as $det_products){

        $global->transaction_inv('rev_nota',$det_products->id_inventory,'reverso',$det_products->amount,$det_products->price,$datenow,1,1,$quotation->number_delivery_note,$det_products->id_inventory_histories,$det_products->id,$id_quotation);

        }  


        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$id_quotation)
        ->update(['status' => 'X']);

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($quotation,"quotation","Se eliminó la Nota de Entrega");
       
        return redirect('quotationslic/indexnotasdeentrega')->withSuccess('Reverso de Nota de Entrega Exitoso!');
        
    }

}
