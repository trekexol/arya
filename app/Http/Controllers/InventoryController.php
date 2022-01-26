<?php

namespace App\Http\Controllers;

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

class InventoryController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
   }

   public function index()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
        $inventories = Inventory::on(Auth::user()->database_name)
        ->join('products','products.id','inventories.product_id')
        ->where(function ($query){
            $query->where('products.type','MERCANCIA')
                ->orWhere('products.type','COMBO');
        })
        ->orderBy('products.description' ,'ASC')
        ->where('products.status',1)
        ->select('inventories.id as id_inventory','inventories.*','products.*')
        ->get();
        
       return view('admin.inventories.index',compact('inventories'));
   }

   public function indexmovements()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       

        $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                        ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                        ->join('quotations', 'quotations.id', '=', 'quotation_products.id_quotation')
                                                        ->where('quotations.date_billing','<>',null)
                                                        ->orwhere('quotations.date_delivery_note','<>',null)
                                                        ->select('products.*','quotation_products.discount as discount',
                                                        'quotation_products.amount as amount_quotation',
                                                        'quotation_products.id_quotation as id_quotation',
                                                        'quotations.date_billing as date_billing',
                                                        'quotations.date_delivery_note as date_delivery_note',
                                                        'quotations.id as id_quotation','quotations.coin as coin_quotation',
                                                        'inventories.amount as amount_inventory'
                                                        )
                                                        ->orderBy('quotations.id','desc')
                                                        ->get(); 

       
       return view('admin.inventories.indexmovement',compact('inventories_quotations'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */


    public function selectproduct()
    {
 
         $products    = Product::on(Auth::user()->database_name)->orderBy('description','asc')->get();
 
         return view('admin.inventories.selectproduct',compact('products'));
    }
 
   public function create($id)
   {
        $product = Product::on(Auth::user()->database_name)->find($id);

        return view('admin.inventories.create',compact('product'));
   }

   public function create_increase_inventory($id_inventory)
   {

       
        $inventory = Inventory::on(Auth::user()->database_name)->find($id_inventory);

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $this->search_bcv();
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
        $inventory = Inventory::on(Auth::user()->database_name)->find($id_inventory);
        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $this->search_bcv();
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
        $var = new Inventory;
        $var->setConnection(Auth::user()->database_name);
        
        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));

        $var->amount = $valor_sin_formato_amount;

        $var->product_id = request('product_id');

        $var->id_user = request('id_user');

        $var->code = request('code');

        $var->status = "1";
        
        $var->save();
        
        return redirect('/inventories')->withSuccess('El inventario del producto: '.$var->products['description'].' fue registrado Exitosamente!');
    

    
    }



    public function store_increase_inventory(Request $request)
    {
        
        $data = request()->validate([
            
            'id_inventory'    =>'required',
            'code'          =>'required',
            'amount'        =>'required',
            'rate'        =>'required',
            'amount_new'        =>'required',
            'price_buy'        =>'required',
            
        ]);
        
        $amount_old = request('amount_old');
        $id_user = request('id_user');

        $valor_sin_formato_amount_new = str_replace(',', '.', str_replace('.', '', request('amount_new')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));
        $valor_sin_formato_price_buy = str_replace(',', '.', str_replace('.', '', request('price_buy')));


        $id_inventory = request('id_inventory');


        if($valor_sin_formato_amount_new > 0){

            $inventory = Inventory::on(Auth::user()->database_name)->findOrFail($id_inventory);

            if($inventory->products['type'] == 'COMBO'){
                $global = new GlobalController;
                $global->aumentCombo($inventory,$valor_sin_formato_amount_new);
            }
            
        
            $inventory->code = request('code');
            
            $inventory->amount = $amount_old + $valor_sin_formato_amount_new;
            
            $inventory->save();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   

            $counterpart = request('Subcontrapartida');
            
            if(isset($counterpart) && $counterpart != 'Seleccionar'){
                
                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
    
                $header_voucher->description = "Incremento de Inventario";
                $header_voucher->date = $datenow;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();
    
                if($inventory->products['money'] == 'Bs'){
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy;
                }else{
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy * $valor_sin_formato_rate;
                }
    
                
    
               //$account = request('account');
                $account_mecancia_para_venta = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',3)->where('code_four',1)->where('code_five',1)->first();  
    
                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_mecancia_para_venta->id,
                                    $id_user,$total,0);
    
                
                $account_counterpart = Account::on(Auth::user()->database_name)->find(request('Subcontrapartida'));            
                //$account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('code_one',6)->where('code_two',1)->where('code_three',3)->where('code_four',2)->where('code_five',1)->first();  
    
                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_counterpart->id,
                                    $id_user,0,$total);

            }
            
        
            return redirect('/inventories')->withSuccess('Actualizado el inventario del producto: '.$inventory->products['description'].' Exitosamente!');
    
        }else{
            return redirect('/inventories/createincreaseinventory/'.$id_inventory.'')->withDanger('La cantidad nueva debe ser mayor a cero!');

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

                $inventory = Inventory::on(Auth::user()->database_name)->findOrFail($id_inventory);

                if($inventory->products['type'] == 'COMBO'){
                    $global = new GlobalController;
                    $global->discountCombo($inventory,$valor_sin_formato_amount_new);
                }

                $inventory->code = request('code');
                
                $inventory->amount = $amount_old - $valor_sin_formato_amount_new;
                
                $inventory->save();

                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');   

                /*$header_voucher  = new HeaderVoucher();

                $header_voucher->description = "Disminucion de Inventario";
                $header_voucher->date = $datenow;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();

                if($inventory->products['money'] == 'Bs'){
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy;
                }else{
                    $total = $valor_sin_formato_amount_new * $valor_sin_formato_price_buy * $valor_sin_formato_rate;
                }
    

                //$account = request('account');
                $account_mecancia_para_venta = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',3)->where('code_four',1)->where('code_five',1)->first();  

                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_mecancia_para_venta->id,
                                    $id_user,0,$total);

                $account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('code_one',6)->where('code_two',1)->where('code_three',3)->where('code_four',2)->where('code_five',1)->first();  

                $this->add_movement($valor_sin_formato_rate,$header_voucher->id,$account_gastos_ajuste_inventario->id,
                                    $id_user,$total,0);*/
            
                return redirect('/inventories')->withSuccess('Actualizado el inventario del producto: '.$inventory->products['description'].' Exitosamente!');
            
            }else{
                return redirect('/inventories/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad a disminuir no puede ser mayor a la cantidad antigua!');

            }
        }else{
            return redirect('/inventories/createdecreaseinventory/'.$id_inventory.'')->withDanger('La cantidad nueva debe ser mayor a cero!');

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

         /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
         
            $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

            if($account->status != "M"){
                $account->status = "M";
                $account->save();
            }
         
    
        $detail->save();

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

    return redirect('/inventories')->withSuccess('Actualizacion Exitosa!');
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

}
