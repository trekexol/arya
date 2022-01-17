<?php

namespace App\Http\Controllers;

use App\Anticipo;
use App\ComboProduct;
use App\ExpensePayment;
use App\ExpensesDetail;
use App\Inventory;
use App\QuotationPayment;
use App\QuotationProduct;
use App\UserAccess;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GlobalController extends Controller
{
    public function procesar_anticipos($quotation,$total_pay)
    {
        
        if($total_pay >= 0){
            $anticipos_old = DB::connection(Auth::user()->database_name)->table('anticipos')
                                ->where('id_client', '=', $quotation->id_client)
                                ->where(function ($query) use ($quotation){
                                    $query->where('id_quotation',null)
                                        ->orWhere('id_quotation',$quotation->id);
                                })
                                ->where('status', '=', '1')->get();

            foreach($anticipos_old as $anticipo){
                DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
            } 


            /*Verificamos si el cliente tiene anticipos activos */
            DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_client', '=', $quotation->id_client)
                    ->where(function ($query) use ($quotation){
                        $query->where('id_quotation',null)
                            ->orWhere('id_quotation',$quotation->id);
                    })
                    ->where('status', '=', '1')
                    ->update(['status' => 'C']);

            //los que quedaron en espera, pasan a estar activos
            DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
            ->where(function ($query) use ($quotation){
                $query->where('id_quotation',null)
                    ->orWhere('id_quotation',$quotation->id);
            })
            ->where('status', '=', 'M')
            ->update(['status' => '1']);
        }
    }

    public function procesar_anticipos_expense($expense,$total_pay)
    {
        
        if($total_pay >= 0){
            
            $anticipos_old = DB::connection(Auth::user()->database_name)->table('anticipos')
                                ->where('id_provider', '=', $expense->id_provider)
                                ->where(function ($query) use ($expense){
                                    $query->where('id_expense',null)
                                        ->orWhere('id_expense',$expense->id);
                                })
                                ->where('status', '=', '1')->get();

            foreach($anticipos_old as $anticipo){
                DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
            } 


            /*Verificamos si el proveedor tiene anticipos activos */
            DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_provider', '=', $expense->id_provider)
                    ->where(function ($query) use ($expense){
                        $query->where('id_expense',null)
                            ->orWhere('id_expense',$expense->id);
                    })
                    ->where('status', '=', '1')
                    ->update(['status' => 'C']);

            //los que quedaron en espera, pasan a estar activos
            DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
            ->where(function ($query) use ($expense){
                $query->where('id_expense',null)
                    ->orWhere('id_expense',$expense->id);
            })
            ->where('status', '=', 'M')
            ->update(['status' => '1']);
        }
    }

    public function check_anticipo($quotation,$total_pay)
    {
        
            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
                                                                                    ->where(function ($query) use ($quotation){
                                                                                        $query->where('id_quotation',null)
                                                                                            ->orWhere('id_quotation',$quotation->id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $quotation->bcv;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                   
                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
                                                         
                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                                                    
                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
                      

                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);
                    
                    $var->id_anticipo_restante = $anticipo->id;
                    $var->date = $quotation->date_billing;
                    $var->id_client = $quotation->id_client;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $quotation->bcv;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }

            
    }

    public function checkAnticipoExpense($expense,$total_pay)
    {
        
            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
                                                                                    ->where(function ($query) use ($expense){
                                                                                        $query->where('id_expense',null)
                                                                                            ->orWhere('id_expense',$expense->id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $expense->rate;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                   
                    DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
                                                         
                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                                                    
                    DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
                      

                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);

                    $var->id_anticipo_restante = $anticipo->id;
                    $var->date = $expense->date;
                    $var->id_provider = $expense->id_provider;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $anticipo->rate;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }
    }
   
    public function associate_anticipos_quotation($quotation){

        $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
        ->where(function ($query) use ($quotation){
            $query->where('id_quotation',null)
                ->orWhere('id_quotation',$quotation->id);
        })
        ->where('status', '=', '1')->get();

        foreach($anticipos as $anticipo){
            DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
        }
                  
    }

    public function associate_anticipos_expense($expense){

        $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
        ->where(function ($query) use ($expense){
            $query->where('id_expense',null)
                ->orWhere('id_expense',$expense->id);
        })
        ->where('status', '=', '1')->get();

        foreach($anticipos as $anticipo){
            DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
        }
                  
    }

    public function check_anticipo_multipayment($quotation,$quotations_id,$total_pay)
    {
        
            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
                                                                                    ->where(function ($query) use ($quotations_id){
                                                                                        $query->where('id_quotation',null)
                                                                                            ->orWhereIn('id_quotation', $quotations_id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();
            

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $quotation->bcv;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                   
                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
                                                         
                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);
                                                    
                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
                      

                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);
                    
                    $var->date = $quotation->date_billing;
                    $var->id_client = $quotation->id_client;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $quotation->bcv;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }

            
    }

    public function discount_inventory($id_quotation)
    {
            /*Primero Revisa que todos los productos tengan inventario suficiente*/
            $no_hay_cantidad_suficiente = DB::connection(Auth::user()->database_name)->table('inventories')
                                    ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                                    ->join('products', 'products.id','=','inventories.product_id')
                                    ->where('quotation_products.id_quotation','=',$id_quotation)
                                    ->where('quotation_products.amount','<','inventories.amount')
                                    ->where('quotation_products.status','1')
                                    ->where(function ($query){
                                        $query->where('products.type','MERCANCIA')
                                            ->orWhere('products.type','COMBO');
                                    })
                                    ->select('inventories.code as code','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                                    'quotation_products.amount as amount_quotation')
                                    ->first(); 
        
            if(isset($no_hay_cantidad_suficiente)){
                return "no_hay_cantidad_suficiente";
            }

            /*Luego, descuenta del Inventario*/
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
            ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
            ->where('quotation_products.id_quotation',$id_quotation)
            ->where('quotation_products.status','1')
            ->select('products.*','quotation_products.id as id_quotation','quotation_products.discount as discount',
            'quotation_products.amount as amount_quotation')
            ->get(); 

            foreach($inventories_quotations as $inventories_quotation){

                $quotation_product = QuotationProduct::on(Auth::user()->database_name)->findOrFail($inventories_quotation->id_quotation);

                if(isset($quotation_product)){
                    if(($inventories_quotation->type == 'MERCANCIA') || ($inventories_quotation->type == 'COMBO')){
                        $inventory = Inventory::on(Auth::user()->database_name)->findOrFail($quotation_product->id_inventory);

                        if(isset($inventory)){
                            //REVISO QUE SEA MAYOR EL MONTO DEL INVENTARIO Y LUEGO DESCUENTO
                            if($inventory->amount >= $quotation_product->amount){
                                $inventory->amount -= $quotation_product->amount;
                                $inventory->save();
                                if($inventories_quotation->type == 'COMBO'){
                                    $global = new GlobalController;
                                    $global->discountCombo($inventory,$quotation_product->amount);
                                }
                            }else{
                                return 'El Inventario de Codigo: '.$inventory->code.' no tiene Cantidad suficiente!';
                            }
                        }else{
                            return 'El Inventario no existe!';
                        }
                    }
                    //CAMBIAMOS EL ESTADO PARA SABER QUE ESE PRODUCTO YA SE COBRO Y SE RESTO DEL INVENTARIO
                    $quotation_product->status = 'C';  
                    $quotation_product->save();
                }else{
                return 'El Inventario de la cotizacion no existe!';
                }

            }

            return "exito";

    }

    public function check_amount($id_quotation,$id_inventory,$amount_new)
    {
        $inventories_quotations = DB::connection(Auth::user()->database_name)
                ->table('products')
                ->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->where('inventories.id',$id_inventory)
                ->select('products.*')
                ->first(); 

        //si es un servicio no se chequea que posea inventario

        if(isset($inventories_quotations) && (($inventories_quotations->type == "MERCANCIA") || ($inventories_quotations->type == "COMBO"))){
            $inventory = Inventory::on(Auth::user()->database_name)->find($id_inventory);

            $sum_amount = DB::connection(Auth::user()->database_name)->table('quotation_products')
                            ->where('id_quotation',$id_quotation)
                            ->where('id_inventory',$id_inventory)
                            ->sum('amount');


            if ($sum_amount <> $amount_new) {
                $total_in_quotation = $amount_new;
            } else {
                $total_in_quotation = $sum_amount;
            }
            
            if ($inventory->amount >= $total_in_quotation){
                return "exito";
            }else{
                return "no_hay_cantidad_suficiente";
            } 

        }else{
            return "exito";
        }
        

        
    
    }

    public function add_payment($quotation,$id_account,$payment_type,$amount,$bcv){
        $var = new QuotationPayment();
        $var->setConnection(Auth::user()->database_name);

        $var->id_quotation = $quotation->id;
        $var->id_account = $id_account;
   
        $var->payment_type = $payment_type;
        $var->amount = $amount;
        
        
        $var->rate = $bcv;
        
        $var->status =  1;
        $var->save();
        
        return $var->id;
    }

    public function add_payment_expense($expense,$id_account,$payment_type,$amount,$bcv){
        $var = new ExpensePayment();
        $var->setConnection(Auth::user()->database_name);

        $var->id_expense = $expense->id;
        $var->id_account = $id_account;
   
        $var->payment_type = $payment_type;
        $var->amount = $amount;
        
        $var->status =  1;
        $var->save();
        
        return $var->id;
    }

    public function aumentCombo($inventory,$amount_discount)
    {
        $product = ComboProduct::on(Auth::user()->database_name)
                    ->join('products','products.id','combo_products.id_product')
                    ->join('inventories','inventories.product_id','products.id')
                    ->where('combo_products.id_combo',$inventory->product_id)
                    ->update(['inventories.amount' => DB::raw('inventories.amount - (combo_products.amount_per_product *'.$amount_discount.')')]);


    }

    public function discountCombo($inventory,$amount_discount)
    {
        $product = ComboProduct::on(Auth::user()->database_name)
                    ->join('products','products.id','combo_products.id_product')
                    ->join('inventories','inventories.product_id','products.id')
                    ->where('combo_products.id_combo',$inventory->product_id)
                    ->update(['inventories.amount' => DB::raw('inventories.amount + (combo_products.amount_per_product *'.$amount_discount.')')]);


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

    public function deleteAllProducts($id_quotation)
    {
        $quotation_products = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$id_quotation)->get(); 
        
        if(isset($quotation_products)){
            foreach($quotation_products as $quotation_product){
                if(isset($quotation_product) && $quotation_product->status == "C"){
                    QuotationProduct::on(Auth::user()->database_name)
                        ->join('inventories','inventories.id','quotation_products.id_inventory')
                        ->join('products','products.id','inventories.product_id')
                        ->where(function ($query){
                            $query->where('products.type','MERCANCIA')
                                ->orWhere('products.type','COMBO');
                        })
                        ->where('quotation_products.id',$quotation_product->id)
                        ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount'), 'quotation_products.status' => 'X']);
                }
            }
        }
    }

    public function deleteAllProductsExpense($id_expense)
    {
        
        $expense_products = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get(); 
        
        
        if(isset($expense_products)){
            foreach($expense_products as $expense_product){
                if(isset($expense_product) && $expense_product->status == "C"){
                    ExpensesDetail::on(Auth::user()->database_name)
                        ->join('inventories','inventories.id','expenses_details.id_inventory')
                        ->join('products','products.id','inventories.product_id')
                        ->where(function ($query){
                            $query->where('products.type','MERCANCIA')
                                ->orWhere('products.type','COMBO');
                        })
                        ->where('expenses_details.id',$expense_product->id)
                        ->update(['inventories.amount' => DB::raw('inventories.amount-expenses_details.amount'), 'expenses_details.status' => 'X']);
                }
            }
        }
    }

    public function search_bcv()
    {
        /*Buscar el indice bcv*/
        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        } else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

    }

    public function data_last_month_day() { 
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
   
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }
   
    /** Actual month first day **/
    public function data_first_month_day() {
        $month = date('m');
        $year = date('Y');
        $dia = date('1');
        return date('Y-m-').'01';
    }  



    function consul_prod_invt($id_product,$sucursal = 'Matriz'){ // buscar solo la cantidad actual del producto

        if ($sucursal == 'Matriz') {
            $inventories_quotations = DB::connection(Auth::user()->database_name)
            ->table('inventory_histories')
            ->where('id_product','=',$id_product)
            ->select('amount_real')
            ->last(); 
        } else {
            $inventories_quotations = DB::connection(Auth::user()->database_name)
            ->table('inventory_histories')
            ->where('id_product','=',$id_product)
            ->select('inventory_histories.*')
            ->last(); 
        }
      
          if (empty($inventories_quotations)) {
          $amount_real = 0;
          } else {
             
             $amount_real = 0;
             $amount_real = $inventories_quotations->amount_real;
         }
      
          return $amount_real;
    }

    
    function transaccion_inv($type,$id_product,$description = '-',$amount,$price = 0,$date,$branch = 'Matriz',$centro_cost = 'Matriz',$number_fac_note = 0,$id_historial_inv = 0){
      
        $msg = '';    
    
        if ($branch == 'Matriz') { // todo
            $inventories_quotations = DB::connection(Auth::user()->database_name)
            ->table('inventory_histories')
            ->where('id_product','=',$id_product)
            ->select('inventory_histories.*')
            ->last(); 
        } else { // sucursal
            $inventories_quotations = DB::connection(Auth::user()->database_name)
            ->table('inventory_histories')
            ->where('id_product','=',$id_product)
            ->select('inventory_histories.*')
            ->last(); 	
        }
 

        
        /*DB::connection(Auth::user()->database_name)->table('anticipos')
                                                    ->where('id', $anticipo->id)
                                                    ->update(['status' => 'C']); */
       
        //DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
           
        
        if (empty($inventories_quotations)) {
        $msg = 'El Producto no tiene inventario o no existe.';
        } else {
            
            $amount_real = 0;
            $amount_real = $inventories_quotations->amount_real;
    
        }
    
        $datev = date("Y-m-d",strtotime($date)); // validando date y convirtiendo a formato de la base de datos Y-m-d
        
        $transaccion = 0;
    

        if ($amount > 0 ) {

            switch ($type) {
                case 'compra':
                  $transaccion = $amount_real+$amount;
                  break;
                case 'venta':
      
                      if ($id_historial_inv != 0) {
 
                             $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                             ->table('inventory_histories')
                             ->where('id','=',$id_historial_inv)
                             ->select('inventory_histories.*')
                             ->get();   
                        
                            if (!empty($inventories_quotations_hist)) {
                                
                                
                                    if ($inventories_quotations_hist->amount == $amount) {
                                    $transaccion = $amount_real;
                                    } else {
                                    $transaccion = $amount_real-$amount;	
                                    }
                                
                            }

                      } else {
                      
                      $transaccion = $amount_real-$amount;
      
                      }    
      
                  break;          
                case 'entrada':
                  $transaccion = $amount_real+$amount;
                  break;      
                case 'salida':
                  $transaccion = $amount_real-$amount;
                  break;
                case 'nota':
                         
                         if ($id_historial_inv != 0) {
                            
                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('inventory_histories.*')
                            ->get();  

                             if (!empty($inventories_quotations_hist)) {
                         
                                
                                if ($inventories_quotations_hist->amount == $amount) {
                                    $amount_nota = 0;
                                    $agregar = false;   
                                } else {
                                    $amount_nota = $inventories_quotations_hist->amount;	
                                    $agregar = true;
                                }

                                 
                             } 
      
                         } else { 
                         
                         $amount_nota = 0;
                         $agregar = true;
      
                         }
      
                      $transaccion = ($amount_real+$amount_nota)-$amount;
      
      
                      break;               
                    case 'rev_nota':
                      $transaccion = $amount_real+$amount;
                      break;
      
                    case 'rev_venta':
      
                     $transaccion = $amount_real+$amount;
      
                     break;  
      
                }


                    if ($transaccion < 0) {
                    $msg = "La cantidad es mayor a la disponible en inventario";
                 
                    } else {

                        $user       =   auth()->user();
                       
                               
           $new = DB::connection(Auth::user()->database_name)->table('inventory_histories')->insert([
            'id ' => 'AUTO',
            'date' => $date,
            'id_product' => $id_product,
            'description' => $description,
            'type' => $type,
            'price' => $price,
            'amount' => $amount,
            'amount_real' => $transaccion,
            'status' => 'A',
            'branch' => $branch,
            'centro_cost' => $centro_cost,
            'number_invoice' => $number_fac_note,
            'user' => $user->id]);


                      switch ($type) {
                        case 'compra':

                          $msg = 'La Compra fue registrada con exito';
                          break;
                        case 'venta':
       
                          $msg = 'La Venta fue registrada con exito';
                         
                          break;
                        case 'nota':
                          
                         /*if ($agregar == true) {
                          $new = DBinsert($conn,'inventario_historial',$a_campos);	
                          DBupdate($conn,"facturas_detalle",array('id_historial_inv'=>$new),'num_factura = ? and status = ? and id = ?',array($num_nota,'E',$id_factura));               
                                  
                          } */
                          $msg = 'La Nota fue registrada con exito';
                          break;  

                         case 'rev_nota':
                          
                          $msg = 'Reverso de Nota exitoso';
                          break;   

                        case 'rev_venta':
                       
                          $msg = 'Reverso de Factura exitoso';
                          
                          break;                                           
                        case 'entrada':
                          
                          $msg = 'Agregado a inventario exitosamente';
                          break;
                        case 'salida':
                         
                          $msg = 'Salida de inventario exitoso';
                          break;
                        default:
                          $msg = 'La operacion no es valida';
                          break;
                        }
                  }



        } else { // condicion cantidad

            $msg = "La cantidad de la oprecion debe ser mayor a cero";
  
       }

       return $msg;

    } // fin de funcion transaccion
   
}
