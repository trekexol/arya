<?php

namespace App\Http\Controllers;

use App;
use App\Account;
use App\Combo;
use App\ComboProduct;
use App\Company;
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
   }


   public function index($type = 'todos')
   {

       $user       =   auth()->user();
       $users_role =   $user->role_id;

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

        }

        $company = Company::on(Auth::user()->database_name)->find(1);

       return view('admin.inventories.index',compact('inventories','company','type'));
   }



   public function indexmovements($coin = 'dolares',$date_frist = 'todo',$date_end = 'todo',$type = 'todo',$id_inventory = 'todos',$id_account = 'todas')
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;

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



         return view('admin.inventories.indexmovement',compact('inventories','coin','date_frist','date_end','type','id_inventory','id_account','accounts'));
   }

  
    public function getinventory(Request $request, $id_account = 'todas'){

         if($request->ajax()){
            try{

                
                if($id_account == 'todas') {
                    $cond = '!=';
                    $id_account = 'r';
                
                } else {
                    $cond = '=';
                    
                }

                $inventories = Product::on(Auth::user()->database_name)
                ->where(function ($query){
                    $query->where('type','MERCANCIA')
                        ->orWhere('type','COMBO')
                        ->orWhere('type','MATERIAP');
                })
                ->where('products.status',1)
                ->where('products.id_account',$cond,$id_account)
                ->select('products.*')  
                ->get();    
                 

               return response()->json($inventories);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }

   }

   public function storemovements(Request $request)
   {
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        $date_end = request('date_end');
        $date_frist = request('date_begin');
        $type = request('type');
        $coin = request('coin');
        $id_inventory = request('id_inventories');
        $id_account = request('id_account');
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
       
        return view('admin.inventories.indexmovement',compact('inventories','coin','date_frist','date_end','type','id_inventory','accounts','id_account'));
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
        ->where('products.id_account',$cond3,$id_account)
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



    $pdf = $pdf->loadView('admin.reports.movements',compact('coin','inventories'));
    return $pdf->stream();  

   }
   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */


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

   public function create_increase_inventory($id_inventory)
   {

       
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
   }

   public function create_decrease_inventory($id_inventory)
   {
            
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
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

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

    }


    public function store_decrease_inventory(Request $request)
    {
   
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
                
               // $counterpart = request('Subcontrapartida');

                $global = new GlobalController; 
                $global->transaction_inv('salida',$id_inventory,'Salida de Inventario',$valor_sin_formato_amount_new,$valor_sin_formato_price_buy,$datenow,1,1,0,0,0,0,0);

               // if($counterpart != 'Seleccionar'){

                    $header_voucher  = new HeaderVoucher();
                    $header_voucher->setConnection(Auth::user()->database_name);

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

                    $account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('description','LIKE','%Gastos de ajuste de inventario%')->first();  

                    $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_gastos_ajuste_inventario->id,
                                      $id_user,$total,0);
               // }

                return redirect('inventories/index/todos')->withSuccess('Actualizado el inventario del producto: '.$inventory->description.' Exitosamente!');
            
            }else{
                return redirect('inventories/index/todos/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad a disminuir no puede ser mayor a la cantidad actual!');

            }
        }else{
            return redirect('inventories/index/todos/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad a disminuir debe ser mayor a cero!');

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



   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
       //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit($id)
   {
        $inventory = Inventory::on(Auth::user()->database_name)->find($id);
       
        $products   = Product::on(Auth::user()->database_name)->get();
       
        return view('admin.inventories.edit',compact('inventory','products'));
  
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
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


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
       //
   }

}
