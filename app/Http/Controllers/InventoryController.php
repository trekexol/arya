<?php

namespace App\Http\Controllers;

use App;
use App\Account;
use App\Combo;
use App\ComboProduct;
use App\Company;
use App\UserAccess;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Inventory;
use App\InventoryHistories;
use App\Product;
use App\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDO;

class InventoryController extends Controller
{
 
    public $modulo = "Reportes";

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Inventario');
        $this->middleware('valimodulo:Inventario')->only('indexmovements');
  
       }


   public function index(request $request,$type = 'todos')
   {
    /* para hacer el submenu "dinamico" */
    $user       =   auth()->user();
    $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
                ->get();

    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');
    $namemodulomiddleware = $request->get('namemodulomiddleware');

        $global = new GlobalController();
        

        if ($type == 'todos') {
            $cond = '!=';
            $valor = null;
        } 
        if ($type == 'MERCANCIA') {
            $cond = '=';
            $valor = $type;
        }   
        if ($type == 'MATERIAP') {
            $cond = '=';
            $valor = $type;
        }
        if ($type == 'COMBO') {
            $cond = '=';
            $valor = $type;
        }   

        $inventories = Product::on(Auth::user()->database_name)
        ->orderBy('id' ,'DESC')
        ->where('status',1)
        ->where('type',$cond,$valor)
        ->select('id as id_inventory','products.*')  
        ->get();   


        foreach ($inventories as $inventorie) {
            $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);
            if ($inventorie->type == 'COMBO') {
            $inventorie->combos_disponibles = $global->consul_cant_combo($inventorie->id_inventory,1);
            } else {
            $inventorie->combos_disponibles = 0;    
            }
        }

        $company = Company::on(Auth::user()->database_name)->find(1);

       return view('admin.inventories.index',compact('sistemas','namemodulomiddleware','actualizarmiddleware','inventories','company','type'));
   }



   public function indexmovements(request $request,$coin = 'dolares',$date_frist = 'todo',$date_end = 'todo',$type = 'todo',$id_inventory = 'todos',$id_account = 'todas')
   {

    $namemodulomiddleware = $request->get('namemodulomiddleware');

       $user       =   auth()->user();
       $users_role =   $user->role_id;

         /* para hacer el submenu "dinamico" */
    $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
                ->select('modulos.name','modulos.ruta')
                ->get();

       $global = new GlobalController();

       if($date_frist == 'todo'){
        $date_frist = $global->data_first_month_day();
        }

       if($date_end == 'todo'){
        $date_end =  $global->data_last_month_day();
       }   
        //$inventories = InventoryHistories::on(Auth::user()->database_name)
        $inventories = Product::on(Auth::user()->database_name)
        ->where(function ($query){
            $query->where('type','MERCANCIA')
                ->orWhere('type','COMBO')
                ->orWhere('type','MATERIAP');
        })
        ->where('products.status',1)
        ->select('products.id as id_inventory','products.*')  
        ->get();     

        $accounts = Account::on(Auth::user()->database_name) // Cuentas de Inventario
        ->Where('code_one',1)
        ->Where('code_two',1)
        ->Where('code_three',3)
        ->Where('code_four',1)
        ->Where('level',5)
        ->orderBY('description','asc')->get(); 


       
         return view('admin.inventories.indexmovement',compact('namemodulomiddleware','sistemas','inventories','coin','date_frist','date_end','type','id_inventory','accounts','id_account'));
   }

   public function storemovements(Request $request)
   {
    $namemodulomiddleware = $request->get('namemodulomiddleware');
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        $date_end = request('date_end');
        $date_frist = request('date_begin');
        $type = request('type');
        $coin = request('coin');
        $id_inventory = request('id_inventories');
        $id_account = request('id_account');
        $global = new GlobalController();
        
        $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
                ->select('modulos.name','modulos.ruta')
                ->get();
   

        if($date_frist == 'todo'){
            $date_frist = $global->data_first_month_day();
            }

        if($date_end == 'todo'){
            $date_end =  $global->data_last_month_day();
        }   

        //$inventories = InventoryHistories::on(Auth::user()->database_name)
        $inventories = Product::on(Auth::user()->database_name)
        ->where(function ($query){
            $query->where('type','MERCANCIA')
                ->orWhere('type','COMBO')
                ->orWhere('type','MATERIAP');
        })

        ->where('products.status',1)
        ->select('products.id as id_inventory','products.*')  
        ->get();     

                   
        $accounts = Account::on(Auth::user()->database_name) // Cuentas de Inventario
        ->Where('code_one',1)
        ->Where('code_two',1)
        ->Where('code_three',3)
        ->Where('code_four',1)
        ->Where('level',5)
        ->orderBY('description','asc')->get(); 

       
        return view('admin.inventories.indexmovement',compact('namemodulomiddleware','sistemas','inventories','coin','date_frist','date_end','type','id_inventory','id_account','accounts'));

    }


   
    public function movements_pdf($coin = 'dolares',$date_frist = 'todo',$date_end = 'todo',$type = 'todo',$id_inventory = 'todos',$id_account = 'todas') 
   {
 
        $pdf = App::make('dompdf.wrapper');

        $global = new GlobalController();

        $invoice = null;
        $note = null;
        $expense = null;
        
        if($date_frist == 'todo'){
            $date_frist = $global->data_first_month_day();
            }

        if($date_end == 'todo'){
            $date_end =  $global->data_last_month_day();
        } 
                
        if ($type == 'todo') {
                $cond = '!=';
                $type = '';
            
            } else {
                $cond = '=';
                
            }


            if($id_inventory == 'todos') {
                $cond2 = '!=';
                $id_inventory = 'r';
            
            } else {
                $cond2 = '=';
                
            }



            if($id_account == 'todas') {
                $cond3 = '!=';
                $id_account = 'r';
            
            } else {
                $cond3 = '=';
                
            }

        

        $inventories = InventoryHistories::on(Auth::user()->database_name) 
        ->join('products','products.id','inventory_histories.id_product')
        ->where('inventory_histories.date','>=',$date_frist)
        ->where('inventory_histories.date','<=',$date_end)
        ->where('inventory_histories.type',$cond,$type)
        ->where('inventory_histories.id_product',$cond2,$id_inventory)
        ->orderBy('inventory_histories.id' ,'ASC')
        ->select('inventory_histories.*','products.id as id_product_pro','products.code_comercial as code_comercial','products.description as description')  
        ->get();     


        foreach ($inventories as $inventory) {


                if ($inventory->type == 'compra' or $inventory->type == 'rev_compra' or $inventory->type == 'aju_compra') {   

                    $invoice = DB::connection(Auth::user()->database_name)
                    ->table('expenses_and_purchases')
                    ->where('id','=',$inventory->id_expense_detail)
                    ->select('invoice')
                    ->get()->last(); 
                    
                } else  {

                    $invoice = DB::connection(Auth::user()->database_name)
                    ->table('quotations')
                    ->where('id','=',$inventory->id_quotation_product)
                    ->select('number_invoice')
                    ->get()->last(); 


                }
                $note = DB::connection(Auth::user()->database_name)
                ->table('quotations')
                ->where('id','=',$inventory->id_quotation_product)
                ->select('number_delivery_note')
                ->get()->last();

                
                $branch = DB::connection(Auth::user()->database_name)
                ->table('branches')
                ->where('id','=',$inventory->id_branch)
                ->select('description')
                ->get()->last();         
                

                if (isset($invoice->number_invoice)) {
                $inventory->invoice = $invoice->number_invoice;
                } elseif (isset($invoice->invoice)) {
                $inventory->invoice = $invoice->invoice;
                } else {
                $inventory->invoice = '';   
                }
                
                if (isset($note->number_delivery_note)) {
                $inventory->note = $note->number_delivery_note; 
                } else {
                $inventory->note= '';   
                }

                if (!empty($branch)) {
                $inventory->branch = $branch->description;
                } else {
                    $inventory->branch = '';
                }
    }



    $pdf = $pdf->loadView('admin.reports.movements',compact('coin','inventories'))->setPaper('a4', 'landscape');
    return $pdf->stream();  
    

   }
   


    public function selectproduct()
    {
 
         $user       =   auth()->user();
         $users_role =   $user->role_id;
  
          $global = new GlobalController();
  
          $inventories = Product::on(Auth::user()->database_name)
          ->where(function ($query){
              $query->where('type','MERCANCIA')
                  ->orWhere('type','COMBO')
                  ->orWhere('type','SERVICIO')
                  ->orWhere('type','MATERIAP');
          })
  
          ->where('products.status',1)
          ->select('products.id as id_inventory','products.*')  
          ->get();     
  
          foreach ($inventories as $inventorie) {
              
              $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);
  
          }


         return view('admin.inventories.selectproduct',compact('inventories'));
    }
 
   public function create($id)
   {
        $product = Product::on(Auth::user()->database_name)->find($id);

        return view('admin.inventories.create',compact('product'));
   }

   public function create_increase_inventory(request $request,$id_inventory)
   {

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $inventory = Product::on(Auth::user()->database_name)->find($id_inventory);
        $global = new GlobalController; 
        $inventory->amount = $global->consul_prod_invt($inventory->id);    
        $company = Company::on(Auth::user()->database_name)->find(1);

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }
        
        $contrapartidas     = Account::on(Auth::user()->database_name)
                                                        ->orWhere('description', 'LIKE','Bancos')
                                                        ->orWhere('description', 'LIKE','Caja')
                                                        ->orWhere('description', 'LIKE','Cuentas por Pagar Comerciales')
                                                        ->orWhere('description', 'LIKE','Capital Social Suscrito y Pagado')
                                                        ->orWhere('description', 'LIKE','Capital Social Suscripto y No Pagado')
                                                        ->orderBY('description','asc')->pluck('description','id')->toArray();

        return view('admin.inventories.create_increase_inventory',compact('inventory','bcv','contrapartidas'));
    }else{
        return redirect('/inventories')->withDanger('No Tiene Permiso');

    }
   }

   public function create_decrease_inventory(request $request,$id_inventory)
   {
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
    $inventory = Product::on(Auth::user()->database_name)->find($id_inventory);
    $global = new GlobalController; 
    $inventory->amount = $global->consul_prod_invt($inventory->id);    
    $company = Company::on(Auth::user()->database_name)->find(1);
        
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        return view('admin.inventories.create_decrease_inventory',compact('inventory','bcv'));

    }else{
        return redirect('/inventories')->withDanger('No Tiene Permiso');

    }
   }



    public function store(Request $request)
    {
        
        $data = request()->validate([
            
            'product_id'    =>'required',
            'code'          =>'required',
            'amount'        =>'required',
            
        ]);
        
        /*$var = new Inventory;
        $var->setConnection(Auth::user()->database_name);
        
        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));

        $var->amount = $valor_sin_formato_amount;

        $var->product_id = request('product_id');

        $var->id_user = request('id_user');

        $var->code = request('code');

        $var->status = "1";
        
        $var->save(); */
        
        return redirect('inventories/index/todos')->withSuccess('El inventario del producto: '.$var->products['description'].' fue registrado Exitosamente!');
    

    
    }



    public function store_increase_inventory(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $data = request()->validate([
            
            'id_inventory'    =>'required',
            'code'          =>'required',
            'amount'        =>'required',
            'rate'        =>'required',
            'amount_new'        =>'required',
            'price_buy'        =>'required'
            
        ]);
        
        $amount_old = request('amount_old');
        $id_user = request('id_user');

        $valor_sin_formato_amount_new = str_replace(',', '.', str_replace('.', '', request('amount_new')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));
       
        $valor_sin_formato_price_buy = str_replace(',', '.', str_replace('.', '', request('price_buy')));


        $id_inventory = request('id_inventory');


        if($valor_sin_formato_amount_new > 0){

            $inventory = Product::on(Auth::user()->database_name)->findOrFail($id_inventory);


            if($inventory->type == 'COMBO'){
                $global = new GlobalController;
                $global->aumentCombo($inventory,$valor_sin_formato_amount_new);
            }
            
        
           /* $inventory->code = request('code');
            
            $inventory->amount = $amount_old + $valor_sin_formato_amount_new;
            
            $inventory->save();*/


            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   

            $counterpart = request('Subcontrapartida');

            $global = new GlobalController; 
            $global->transaction_inv('entrada',$inventory->id,'Entrada de Inventario',$valor_sin_formato_amount_new,$valor_sin_formato_price_buy,$datenow,1,1,0,0,0,0,0);

            if($counterpart != 'Seleccionar'){
                
                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
    
                $header_voucher->description = "Incremento de Inventario";
                $header_voucher->date = $datenow;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();
    
                if($inventory->money == 'Bs'){
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy;
                }else{
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy * $valor_sin_formato_rate;
                }

                if ($inventory->id_account == null) {
                    $inventory->id_account = 17; 
                }
                
                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$inventory->id_account,$id_user,$total,0);
    
                                
                $account_counterpart = Account::on(Auth::user()->database_name)->find(request('Subcontrapartida'));            
                //$account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('code_one',6)->where('code_two',1)->where('code_three',3)->where('code_four',2)->where('code_five',1)->first();  
    
                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_counterpart->id,
                                    $id_user,0,$total);
            
            }
            
            return redirect('inventories/index/todos')->withSuccess('Actualizado el inventario del producto: '.$inventory->description.' Exitosamente!');
                
           

        }else{

           return redirect('inventories/index/todos/createincreaseinventory/'.$id_inventory.'')->withDanger('La cantidad nueva debe ser mayor a cero!');

        }
    }else{
        return redirect('/inventories')->withDanger('No Tiene Permiso');

    }

    }


   


    public function store_decrease_inventory(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $data = request()->validate([
            
            'id_inventory'  =>'required',
            'code'          =>'required',
            'amount'        =>'required',

            'rate'          =>'required',
            'amount_new'    =>'required',
            'price_buy'     =>'required',
            
        ]);

        $amount_old = request('amount_old');
        $id_user = request('id_user');

        $valor_sin_formato_amount_new = str_replace(',', '.', str_replace('.', '', request('amount_new')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));
        $valor_sin_formato_price_buy = str_replace(',', '.', str_replace('.', '', request('price_buy')));
        
        $id_inventory = request('id_inventory');

        if($valor_sin_formato_amount_new > 0){
            if($valor_sin_formato_amount_new <= $amount_old){

                $inventory = Product::on(Auth::user()->database_name)->findOrFail($id_inventory);

                if($inventory->type == 'COMBO'){
                    $global = new GlobalController;
                    $global->discountCombo($inventory,$valor_sin_formato_amount_new);
                }

              /*  $inventory->code = request('code');
                
                $inventory->amount = $amount_old - $valor_sin_formato_amount_new;
                
                $inventory->save();*/

                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');   

                $global = new GlobalController; 
                $global->transaction_inv('salida',$id_inventory,'Salida de Inventario',$valor_sin_formato_amount_new,$valor_sin_formato_price_buy,$datenow,1,1,0,0,0,0,0);

                $header_voucher  = new HeaderVoucher();

                $header_voucher->description = "Disminucion de Inventario";
                $header_voucher->date = $datenow;
                
                $header_voucher->status =  "1";
            
                $header_voucher->save();

                if($inventory->money == 'Bs'){
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy;
                }else{
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy * $valor_sin_formato_rate;
                }

                if ($inventory->id_account == null) {
                    $inventory->id_account = 17; 
                }
                
                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$inventory->id_account,
                                    $id_user,0,$total);

                $account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('code_one',6)->where('code_two',1)->where('code_three',3)->where('code_four',2)->where('code_five',1)->first();  

                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_gastos_ajuste_inventario->id,
                                    $id_user,$total,0);
            
                return redirect('inventories/index/todos')->withSuccess('Actualizado el inventario del producto: '.$inventory->description.' Exitosamente!');
            
            }else{
                return redirect('inventories/index/todos/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad a disminuir no puede ser mayor a la cantidad actual!');

            }
        }else{
            return redirect('inventories/index/todos/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad a disminuir debe ser mayor a cero!');

        }
    }else{
        return redirect('/inventories')->withDanger('No Tiene Permiso');

    }
    
    }

     
    
    public function store_inventory_combo(Request $request)
    {
        
          /*  $data = request()->validate([
                
                'disponible'    =>'required',
                'id_product'    =>'required'
                
            ]); */
        
            $type_add = request('type_add');
            $id_product = request('id_product');
            $cantidad_disponible = request('cant_disponible');
            $cantidad_actual = request('cant_actual');
            $description = request('name_combo');
            $serie = request('serie');

            $cantidad = str_replace(',', '.', str_replace('.', '', request('disponible')));
  

 
            $inventory = Product::on(Auth::user()->database_name)->find($id_product);

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $counterpart = request('Subcontrapartida');
            $agregar = 'true';
          
            if($type_add == '1') {
                if($cantidad_disponible <= 0) {
                    $agregar == 'false';
                    return redirect('inventories/index/todos')->withDanger('No pose inventario suficiente para realizar el combo ID '.$id_product.' Código '.$serie.' '.$description.'. Revisar en los productos del combo y agregue primero al inventario la cantidad de productos que se requiere para la elavoración del combo.');
                    exit;
                }

                if($cantidad_disponible < $cantidad){
                    $agregar == 'false';
                    return redirect('inventories/index/todos')->withDanger('No hay disponibilidad para la cantidad de combos ingresados ID '.$id_product.' '.$serie.' '.$description.'. Intente con una cantidad menor');
                    exit;
                }

                if($cantidad == 0) {
                    $agregar == 'false';
                    return redirect('inventories/index/todos')->withDanger('Para agregar un combo al inventario la cantidad debe ser mayor a cero');
                    exit;
                }
            }
            if($type_add == '0') {

                if( $cantidad_actual < $cantidad){
                    $agregar == 'false';
                    return redirect('inventories/index/todos')->withDanger('La cantidad que desea rebajar del combo ID'.$id_product.' Código '.$serie.' '.$description.' es mayor a la cantidad actual');
                    exit;
                }

                if($cantidad == 0) {
                    $agregar == 'false';
                    return redirect('inventories/index/todos')->withDanger('Para deshacer un combo y regresar sus productos a inventario la cantidad debe ser mayor a cero');
                    exit;
                }
            }
                   
            

                if ($agregar == 'true'){

                    $global = new GlobalController; 

                    if($type_add == '1') {
                        $transaccion = $global->transaction_inv('entrada',$inventory->id,'Entrada de Inventario tipo Combo',$cantidad,$inventory->price,$datenow,1,1,0,0,0,0,0);
                    } 

                    if($type_add == '0') {
                        $transaccion = $global->transaction_inv('salida',$inventory->id,'Salida de Inventario tipo Combo',$cantidad,$inventory->price,$datenow,1,1,0,0,0,0,0);
                    } 

                    if($transaccion != ''){   
                    return redirect('inventories/index/todos')->withSuccess($transaccion); 
                    } 

                } else {

                    return redirect('inventories/index/todos')->withDanger('Transacción no disponible');

                }



    }


    public function add_movement($tasa,$id_header,$id_account,$id_user,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $tasa;

      /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
        $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/


        $detail->debe = $debe;
        $detail->haber = $haber;
      
        $detail->status =  "C";
         
        $detail->save();

         /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
         
         $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

         if($account->status != "M"){
             $account->status = "M";
             $account->save();
         }


    }



   public function edit($id)
   {
        $inventory = Inventory::on(Auth::user()->database_name)->find($id);
       
        $products   = Product::on(Auth::user()->database_name)->get();
       
        return view('admin.inventories.edit',compact('inventory','products'));
  
   }

   
   public function update(Request $request, $id)
   {

    $vars =  Inventory::on(Auth::user()->database_name)->find($id);

    $vars_status = $vars->status;
   
  
    $data = request()->validate([
        
       
        'code'         =>'required',
      
        'amount'         =>'required',

        'status'         =>'required',
       
    ]);

    $var = Inventory::on(Auth::user()->database_name)->findOrFail($id);

    $var->code = request('code');
   
    $var->amount = request('amount');
    
    $var->status =  request('status');


   
    if(request('status') == null){
        $var->status = $vars_status;
    }else{
        $var->status = request('status');
    }
   
    $var->save();

    return redirect('inventories')->withSuccess('Actualizacion Exitosa!');
    }


}
