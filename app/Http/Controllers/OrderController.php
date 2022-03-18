<?php

namespace App\Http\Controllers;

use App\Client;
use App\DetailVoucher;
use App\Http\Controllers\UserAccess\UserAccessController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Inventory;
use App\Multipayment;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;

class OrderController extends Controller
{
    public $userAccess;
    public $modulo = 'Cotizacion';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }
 
    public function index()
    {
        if($this->userAccess->validate_user_access($this->modulo)){
            $user       =   auth()->user();
            $users_role =   $user->role_id;
            
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_order' ,'DESC')
                                    ->where('date_order','<>',null)
                                    ->where('date_billing',null)
                                    ->where('date_delivery_note',null)
                                    ->whereIn('status',[1,'M'])
                                    ->get();
        
    
            return view('admin.orders.index',compact('quotations'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }
 




    public function create_order($id_quotation,$coin)
    {
        
         $quotation = null;
             
         if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
            
            $quotation->coin = $coin;
            
            $quotation->save();
         }
 
         if(isset($quotation)){
            
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                            ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
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
             
     
             return view('admin.orders.create',compact('coin','quotation','datenow','bcv','total_retiene_iva','total_retiene_islr'));
         }else{
             return redirect('/orders')->withDanger('La cotizacion no existe');
         } 
         
    }

    public function reversar_order($id_quotation)
    { 
        
        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        QuotationProduct::on(Auth::user()->database_name)
                        ->join('inventories','inventories.id','quotation_products.id_inventory')
                        ->join('products','products.id','inventories.product_id')
                        ->where(function ($query){
                            $query->where('products.type','MERCANCIA')
                                ->orWhere('products.type','COMBO');
                        })
                        ->where('id_quotation',$quotation->id)
                        ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount') , 'quotation_products.status' => 'X']);
    
        $quotation->status = 'X';
        $quotation->save();

        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$id_quotation)
        ->update(['status' => 'X']);
       
        return redirect('orders')->withSuccess('Reverso de Pedido Exitoso!');

    }

}
