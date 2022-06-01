<?php

namespace App\Http\Controllers;


use App;
use App\Account;
use App\Branch;
use App\Client;
use App\DetailVoucher;
use App\ExpensePayment;
use App\MultipaymentExpense;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\Product;
use App\InventoryHistories;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Anticipo;
use App\Company;
use App\Http\Controllers\Historial\HistorialExpenseController;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Http\Controllers\Validations\ExpenseDetailValidationController;
use App\Inventory;
use App\IslrConcept;
use Illuminate\Support\Facades\Auth;


class ExpensesAndPurchaseController extends Controller
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
            

                $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                                            ->where('amount_with_iva','=',null)
                                                            ->where('status',1)
                                                            ->get();

            return view('admin.expensesandpurchases.index',compact('expensesandpurchases'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
   }


   public function index_historial()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       

        $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                                    ->where('amount_with_iva','<>',null)
                                                    ->get();
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  


       return view('admin.expensesandpurchases.index_historial',compact('expensesandpurchases','datenow'));
   }


   public function movements_expense($id_expense,$coin)
   {
       

       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
           $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
           $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_expense',$id_expense)->whereIn('status',['C','F'])->get();

            $multipayments_detail = null;
            $expenses = null;

            //Buscamos a la factura para luego buscar atraves del header a la otras facturas
            $multipayment = MultipaymentExpense::on(Auth::user()->database_name)->where('id_expense',$id_expense)->first();
            if(isset($multipayment)){
                $expenses = MultipaymentExpense::on(Auth::user()->database_name)->where('id_header',$multipayment->id_header)->get();     
                $multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)->get();
            }
           //-------
            

       
       return view('admin.expensesandpurchases.index_movement',compact('coin','detailvouchers','expense','expenses','multipayments_detail'));
   }


   public function index_delivery_note()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                ->where('date_delivery_note','<>',null)
                                ->get();
      

       return view('admin.expensesandpurchases.indexdeliverynote',compact('expenses'));
   }





   public function createdeliverynote($id_expense,$coin)
   {
       
        $expense = null;
            
        if(isset($id_expense)){
           $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);
           $expense->coin = $coin;
           $expense->save();
        }

        if(isset($expense)){
           
            $inventories_expenses = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                           ->rightJoin('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                                                           ->where('expenses_details.id_expense',$expense->id)
                                                           ->where('expenses_details.status',['1','C'])
                                                           ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                                                           'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                                                           ,'expenses_details.islr as retiene_islr_expense')
                                                           ->get(); 

            
           $total= 0;
           $base_imponible= 0;

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

           $expense->total_factura = $total;
           $expense->base_imponible = $base_imponible;

           $date = Carbon::now();
           $datenow = $date->format('Y-m-d');    

           if($coin == 'bolivares'){
               $bcv = null;
               
           }else{
               $bcv = $expense->rate;
           }
           

            return view('admin.expensesandpurchases.createdeliverynote',compact('coin','expense','datenow','bcv','total_retiene_iva','total_retiene_islr'));
        }else{
            return redirect('/expensesandpurchases')->withDanger('La compra no existe');
        } 
        
   }
   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    
    public function create_expense($id_provider = null)
    {
        $provider = null;

        if(isset($id_provider)){
            $provider = Provider::on(Auth::user()->database_name)->find($id_provider);
        }
    
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  

        return view('admin.expensesandpurchases.createexpense',compact('datenow','provider'));
    }

    public function retencion_iva($id_expense,$coin)
    {
        $pdf = App::make('dompdf.wrapper');
        $expense = null;
        $provider = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);
            $pago = ExpensePayment::on(Auth::user()->database_name)
                        ->where('id_expense',$id_expense)
                        ->select('created_at')
                        ->get()->last();     
        }
        
        if(isset($pago)){

            $periodo_pago = substr($pago->created_at,0,4);
            $mes_pago = substr($pago->created_at,5,2);
        
        } else {

            $periodo_pago = '';
            $mes_pago = '';
        }

        if((isset($expense)) && ($expense->retencion_iva != 0)){
            $date = Carbon::now();
            $datenow = $date->format('d-m-Y');  
            $period = $date->format('Y-m'); 
    
            $company = Company::on(Auth::user()->database_name)->find(1);
                    
            
            $pdf = $pdf->loadView('admin.expensesandpurchases.retencion_iva',compact('pago','company','expense','datenow','period','provider','periodo_pago','mes_pago'))->setPaper('a4', 'landscape');
            return $pdf->stream();
        }else{
            return redirect('/expensesandpurchases/expensevoucher/'.$id_expense.'/bolivares')->withDanger('Esta factura no retiene IVA!');
        }

    }

    public function retencion_islr($id_expense,$coin)
    {
        $pdf = App::make('dompdf.wrapper');
        $expense = null;
        $provider = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);
        }
    
        if((isset($expense)) && ($expense->retencion_islr != 0)){
            $date = Carbon::now();
            $datenow = $date->format('d-m-Y');  
            $period = $date->format('Y-m'); 
    
            $company = Company::on(Auth::user()->database_name)->find(1);
                  
            /*$expense_details = ExpensesDetail::on(Auth::user()->database_name)
                                ->where('id_expense',$expense->id)
                                ->where('islr',1)->sum('amount * price');*/

            $expense_details =  DB::connection(Auth::user()->database_name)->select('SELECT SUM(amount * price) AS total
                                FROM expenses_details
                                WHERE id_expense = ? AND
                                islr = 1 AND
                                status = ?
                                '
                                , [$expense->id,'C']);
                

            $total_islr_details = $expense_details[0]->total;

            
            
            $pdf = $pdf->loadView('admin.expensesandpurchases.retencion_islr',compact('total_islr_details','company','expense','datenow','period','provider'))->setPaper('a4', 'landscape');
            return $pdf->stream();
        }else{
            return redirect('/expensesandpurchases/expensevoucher/'.$id_expense.'/bolivares')->withDanger('Esta factura no retiene ISLR!');
        }

    }

    public function create_expense_detail($id_expense,$coin,$type = null,$id_product = null)
    {
        
        $expense = null;
        $provider = null;
        $expense_details = null;
        $inventory = null;

        $accounts_inventory = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
             
           // dd($id_inventory);

            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if(($inventory->type == 'MERCANCIA') || ($inventory->type == 'COMBO') || ($inventory->type == 'MATERIAP')){
                    $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',1)
                    ->where('code_two', 1)
                    ->where('code_three', 3)
                    ->where('code_four',1)
                    ->where('code_five', '<>',0)
                    ->orderBy('description','asc')
                    ->get();
                }else{
                    $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',5)
                    ->where('code_two', '<>',0)
                    ->where('code_three', '<>',0)
                    ->where('code_four', '<>',0)
                    ->where('code_five', '<>',0)
                    ->orderBy('description','asc')
                    ->get();
                }
                
            }
        }
            
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
      
        if(($coin == 'bolivares') || (!isset($coin)) ){
 
            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if( $inventory->money!= 'Bs'){
                    $inventory->price_buy = $inventory->price_buy * $expense->rate;
                }
            }
            $coin = 'bolivares';
        }else{
            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if( $inventory->money == 'Bs'){
                    $inventory->price_buy = $inventory->price_buy / $expense->rate;
                }
            }
            $coin = 'dolares';
        }

        return view('admin.expensesandpurchases.create',compact('type','coin','bcv','datenow','provider','expense','expense_details','branches','inventory','accounts_inventory'));
    }

    public function create_expense_voucher($id_expense,$coin)
    {

        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            
        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        } 

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  

        if($coin != 'bolivares'){
            $bcv = $expense->rate;
        }else{
            $bcv = null;
        }
        
        return view('admin.expensesandpurchases.create_payment_voucher',compact('coin','expense','datenow','bcv'));
         
    }
  


    public function create_payment($id_expense,$coin)
    {
      
        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        } 

            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                ->where('id_provider',$expense->id_provider)
                                                ->where(function ($query) use ($expense){
                                                    $query->where('id_expense',null)
                                                        ->orWhere('id_expense',$expense->id);
                                                })
                                                ->where('coin','like','bolivares')
                                                ->sum('amount');

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                ->where('id_provider',$expense->id_provider)
                                ->where(function ($query) use ($expense){
                                    $query->where('id_expense',null)
                                        ->orWhere('id_expense',$expense->id);
                                })
                                ->where('coin','like','dolares')
                                ->select(DB::connection(Auth::user()->database_name)->raw('SUM(amount/rate) as dolar'))->first();

                                
        
            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo->dolar)){
                $anticipos_sum_dolares = $total_dolar_anticipo->dolar;
                
            }

            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 2)
                        ->where('code_five', '<>',0)
                        ->where('description','not like', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();

            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 1)
                        ->where('code_five', '<>',0)
                        ->orderBy('description','asc')
                        ->get();

            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                        ->orderBy('description','asc')            
                        ->get();
          
             $total= 0;
             $base_imponible= 0;
             
             $retiene_iva = 0;

             $total_retiene_iva = 0;
             $total_retiene_islr = 0;

             foreach($expense_details as $var){
                 
                    $total += ($var->price * $var->amount);
               
                if($var->exento == 0){
                    $base_imponible += ($var->price * $var->amount); 
                }

                if($var->islr == 1){
                    $total_retiene_islr += ($var->price * $var->amount); 
                }
             }

            
            
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');    

             if($coin == 'bolivares'){
                $bcv = null;
               
             }else{
                $bcv = $expense->rate;
                $total = $total / $expense->rate;
                $base_imponible = $base_imponible / $expense->rate;
             }

             $expense->total_factura = $total;
             $expense->base_imponible = $base_imponible;

             $anticipos_sum = 0;
             if(isset($coin)){
                 if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }else{
                    $bcv = $expense->rate;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }
             }else{
                $bcv = null;
             }

             /*Aqui revisamos el porcentaje de retencion de iva que tiene el proveedor, para aplicarlo a productos que retengan iva */
             $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            
             
            $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();
     
             return view('admin.expensesandpurchases.create_payment',compact('coin','expense','datenow'
                                ,'expense_details','accounts_bank', 'accounts_efectivo'
                                ,'accounts_punto_de_venta','anticipos_sum'
                                ,'total_retiene_iva','total_retiene_islr','bcv','provider'
                                ,'islrconcepts'));
         
         
    }

    public function create_payment_after($id_expense,$coin)
    {
       
        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        } 

            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_provider',$expense->id_provider)
                                        ->where(function ($query) use ($expense){
                                            $query->where('id_expense',null)
                                                ->orWhere('id_expense',$expense->id);
                                        })
                                        ->where('coin','like','bolivares')
                                        ->sum('amount');

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_provider',$expense->id_provider)
                                        ->where(function ($query) use ($expense){
                                            $query->where('id_expense',null)
                                                ->orWhere('id_expense',$expense->id);
                                        })
                                        ->where('coin','like','dolares')
                                        ->select(DB::connection(Auth::user()->database_name)->raw('SUM(amount/rate) as dolar'))
                                        ->first();
        
                                   
            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo->dolar)){
                $anticipos_sum_dolares = $total_dolar_anticipo->dolar;
                
            }

            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 2)
                        ->where('code_five', '<>',0)
                        ->where('description','not like', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 1)
                        ->where('code_five', '<>',0)
                        ->orderBy('description','asc')
                        ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')            
                        ->orderBy('description','asc')
                        ->get();
          
             $total= 0;
             $base_imponible= 0;
             
             $retiene_iva = 0;
            // $retiene_islr = 0;

             $total_retiene_iva = 0;
             $total_retiene_islr = 0;

             foreach($expense_details as $var){
                 
                    $total += ($var->price * $var->amount);
               
                if($var->exento == 0){
                    $base_imponible += ($var->price * $var->amount); 
                }

                if($var->islr == 1){
                    $total_retiene_islr += ($var->price * $var->amount); 
                }
             }

            
            
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');    

             if($coin == 'bolivares'){
                $bcv = null;
               
             }else{
                $bcv = $expense->rate;
                $total = $total / $expense->rate;
                $base_imponible = $base_imponible / $expense->rate;
             }

             $expense->total_factura = $total;
             $expense->base_imponible = $base_imponible;

             $anticipos_sum = 0;
             if(isset($coin)){
                 if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }else{
                    $bcv = $expense->rate;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }
             }else{
                $bcv = null;
             }

             /*Aqui revisamos el porcentaje de retencion de iva que tiene el proveedor, para aplicarlo a productos que retengan iva */
             $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

           
            $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();
     
             return view('admin.expensesandpurchases.create_payment_after',compact('coin','expense','datenow'
                                ,'expense_details','accounts_bank', 'accounts_efectivo'
                                ,'accounts_punto_de_venta','anticipos_sum'
                                ,'total_retiene_iva','total_retiene_islr','bcv','provider'
                                ,'islrconcepts'));
         
         
    }


    public function selectprovider()
    {
            $providers     = Provider::on(Auth::user()->database_name)->get();
        
            return view('admin.expensesandpurchases.selectprovider',compact('providers'));
    }
    
    
    public function selectinventary($id_expense,$coin,$type)
    {
        if($type == 'mercancia' || $type == 'MERCANCIA'){
            $type = 'MERCANCIA';
        }

        if($type == 'servicio' || $type == 'SERVICIO'){
            $type = 'SERVICIO';
        }

        if($type == 'materiap' || $type == 'MATERIAP'){
            $type = 'MATERIAP';
        }



        $user       =   auth()->user();
        $users_role =   $user->role_id;
    
            $global = new GlobalController();
            $inventories = Product::on(Auth::user()->database_name)
        
            ->where(function ($query){
                $query->where('type','MERCANCIA')
                    ->orWhere('type','COMBO')
                    ->orWhere('type','MATERIAP')
                    ->orWhere('type','SERVICIO');
            })
    
    
            ->where('products.status',1)
            ->select('products.id as id_inventory','products.*')  
            ->get();     
            
            foreach ($inventories as $inventorie) {
                
                $inventorie->amount = 11;
    
            }

        
            return view('admin.expensesandpurchases.selectinventary',compact('type','coin','inventories','id_expense'));
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
            
            'id_user'         =>'required',
            'date-begin'         =>'required',
        
        ]);

        $var = new ExpensesAndPurchase();
        $var->setConnection(Auth::user()->database_name);

        $var->id_provider = request('id_provider');

        if(!isset($var->id_provider)){
            return redirect('expensesandpurchases/registerexpense')->withDelete('Debe seleccionar un proveedor!');
        }

        $var->id_user = request('id_user');

        $var->invoice = request('invoice');
        $var->serie = request('serie');
        $var->observation = request('observation');

        $var->date = request('date-begin');

        $var->coin = 'bolivares';

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        $var->rate = $bcv;

        $var->status =  "1";
    
        $var->save();

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($var,"expense","Creó la Compra");


        return redirect('expensesandpurchases/register/'.$var->id.'/bolivares')->withSuccess('Gasto o Compra Resgistrada Correctamente!');
    }


    public function store_detail(Request $request)
    {
    
        
        $data = request()->validate([
            
        
            'id_expense'    =>'required',
            
            'id_user'  =>'required',
            'amount'        =>'required',
            'description'   =>'required',
            'price'         =>'required',
            
        
        ]);

        
        $var = new ExpensesDetail();
        $var->setConnection(Auth::user()->database_name);

        $var->id_expense = request('id_expense');
        $var->rate = request('rate_expense');
        $coin = request('coin_hidde');

        $var->id_user = request('id_user');
        $var->id_account = request('Account');
        
        $sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));

        $var->amount = $sin_formato_amount;

        $var->description = request('description');

        $sin_formato_price = str_replace(',', '.', str_replace('.', '', request('price')));

        if($coin != 'bolivares'){
            $sin_formato_price = ($sin_formato_price) * $var->rate;
        }else{
            $sin_formato_price = $sin_formato_price;
        }
       
        $var->price = $sin_formato_price;
       
        $var->id_branch = request('centro_costo');

        $exento = request('exento');
        if($exento == null){
            $var->exento = false;
        }else{
            $var->exento = true;
        }
        
        $islr = request('islr');
        if($islr == null){
            $var->islr = false;
        }else{
            $var->islr = true;
        }

        $id_inventory = request('id_inventory');
        if($id_inventory != -1){
            $var->id_inventory = $id_inventory;
        }
        
        $var->status =  1;
    
        $var->save();

        $validation = new ExpenseDetailValidationController();

        if($var->expenses['status'] == 'P'){
            $validation->calculateExpenseModify($var->id_expense);
        }

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($var,"expense_product","Registró un Producto o Servicio");

        return redirect('expensesandpurchases/register/'.$var->id_expense.'/'.$coin.'')->withSuccess('Agregado Exitosamente!');
    }


    public function store_expense_payment(Request $request)
    { 
        $data = request()->validate([
            
        ]);
    
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        
        $total_pay = 0;

        //dd($request);
        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');

        $global = new GlobalController();

        $user_id = request('user_id');

        /*Validar cuales son los pagos a guardar */
            $validate_boolean1 = false;
            $validate_boolean2 = false;
            $validate_boolean3 = false;
            $validate_boolean4 = false;
            $validate_boolean5 = false;
            $validate_boolean6 = false;
            $validate_boolean7 = false;

        //-----------------------

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail(request('id_expense'));

        $bcv = $expense->rate;
        $coin = request('coin');

        $anticipo = request('anticipo_form');
        $retencion_iva = request('total_retiene_iva');
        $retencion_islr = request('total_retiene_islr');


        $sub_total = request('sub_total_form');
        $base_imponible = request('base_imponible_form');
        $sin_formato_amount = request('sub_total_form');
        $iva_percentage = request('iva_form');
        $sin_formato_total_pay = request('total_pay_form');
        $total_pay_form = request('total_pay_form');

        $date_payment = request('date_payment_form');


        $sin_formato_grandtotal = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount_form')));

        $total_iva = 0;

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }

        //Verifica el status del pago, si esta en C significa Cobrado y por tanto no se debe cobrar de nuevo 
        if($expense->status != "C"){
            //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
            if($sin_formato_total_pay > 0)
            {
                $payment_type = request('payment_type');
                if($come_pay >= 1){

                    /*-------------PAGO NUMERO 1----------------------*/

                    $var = new ExpensePayment();
                    $var->setConnection(Auth::user()->database_name);

                    $amount_pay = request('amount_pay');
            
                    if(isset($amount_pay)){
                        
                        $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 1!');
                    }
                        
            
                    $account_bank = request('account_bank');
                    $account_efectivo = request('account_efectivo');
                    $account_punto_de_venta = request('account_punto_de_venta');
            
                    $credit_days = request('credit_days');
            
                    
            
                    $reference = request('reference');
            
                    if($valor_sin_formato_amount_pay != 0){
            
                        if($payment_type != 0){
            
                            $var->id_expense = request('id_expense');
            
                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type == 1 || $payment_type == 11 || $payment_type == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank != 0)){
                                    if(isset($reference)){
            
                                        $var->id_account = $account_bank;
            
                                        $var->reference = $reference;
            
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                                }
                            }if($payment_type == 2){
                        
                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 

                                $var->id_account = $account_contado->id;
                            }
                            if($payment_type == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days)){
            
                                    $var->credit_days = $credit_days;
            
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                                }
                            }
            
                            if($payment_type == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo != 0)){
            
                                    $var->id_account = $account_efectivo;
            
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                                }
                            }
            
                            if($payment_type == 9 || $payment_type == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta != 0)){
                                    $var->id_account = $account_punto_de_venta;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                                }
                            }
            
                                
                        
            
                                $var->payment_type = request('payment_type');
                                $var->amount = $valor_sin_formato_amount_pay;
                                
                                if($coin != 'bolivares'){
                                    $var->amount = $var->amount * $bcv;
                                }

                                $var->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay;
            
                                $validate_boolean1 = true;
            
                            
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                        }
            
                        
                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                }   
                $payment_type2 = request('payment_type2');
                if($come_pay >= 2){

                    /*-------------PAGO NUMERO 2----------------------*/

                    $var2 = new ExpensePayment();
                    $var2->setConnection(Auth::user()->database_name);

                    $amount_pay2 = request('amount_pay2');

                    if(isset($amount_pay2)){
                        
                        $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 2!');
                    }
                        

                    $account_bank2 = request('account_bank2');
                    $account_efectivo2 = request('account_efectivo2');
                    $account_punto_de_venta2 = request('account_punto_de_venta2');

                    $credit_days2 = request('credit_days2');

                    

                    $reference2 = request('reference2');

                    if($valor_sin_formato_amount_pay2 != 0){

                    if($payment_type2 != 0){

                        $var2->id_expense = request('id_expense');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type2 == 1 || $payment_type2 == 11 || $payment_type2 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank2 != 0)){
                                if(isset($reference2)){

                                    $var2->id_account = $account_bank2;

                                    $var2->reference = $reference2;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                                }
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                            }
                        }if($payment_type2 == 2){
                        
                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 

                            $var2->id_account = $account_contado->id;
                        }
                        if($payment_type2 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days2)){

                                $var2->credit_days = $credit_days2;

                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                            }
                        }

                        if($payment_type2 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo2 != 0)){

                                $var2->id_account = $account_efectivo2;

                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                            }
                        }

                        if($payment_type2 == 9 || $payment_type2 == 10){
                                //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta2 != 0)){
                                $var2->id_account = $account_punto_de_venta2;
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                            }
                        }

                            
                    

                            $var2->payment_type = request('payment_type2');
                            $var2->amount = $valor_sin_formato_amount_pay2;
                            
                            if($coin != 'bolivares'){
                                $var2->amount = $var2->amount * $bcv;
                            }
                            
                            $var2->status =  1;
                        
                            $total_pay += $valor_sin_formato_amount_pay2;

                            $validate_boolean2 = true;

                        
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                    }

                    
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                    }
                    /*--------------------------------------------*/
                } 
                $payment_type3 = request('payment_type3');   
                if($come_pay >= 3){

                        /*-------------PAGO NUMERO 3----------------------*/

                        $var3 = new ExpensePayment();
                        $var3->setConnection(Auth::user()->database_name);

                        $amount_pay3 = request('amount_pay3');

                        if(isset($amount_pay3)){
                            
                            $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 3!');
                        }
                            

                        $account_bank3 = request('account_bank3');
                        $account_efectivo3 = request('account_efectivo3');
                        $account_punto_de_venta3 = request('account_punto_de_venta3');

                        $credit_days3 = request('credit_days3');

                    

                        $reference3 = request('reference3');

                        if($valor_sin_formato_amount_pay3 != 0){

                            if($payment_type3 != 0){

                                $var3->id_expense = request('id_expense');

                                //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                                if($payment_type3 == 1 || $payment_type3 == 11 || $payment_type3 == 5 ){
                                    //CUENTAS BANCARIAS
                                    if(($account_bank3 != 0)){
                                        if(isset($reference3)){

                                            $var3->id_account = $account_bank3;

                                            $var3->reference = $reference3;

                                        }else{
                                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                        }
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                    }
                                }if($payment_type3 == 2){
                        
                                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 
        
                                    $var3->id_account = $account_contado->id;
                                }
                                if($payment_type3 == 4){
                                    //DIAS DE CREDITO
                                    if(isset($credit_days3)){

                                        $var3->credit_days = $credit_days3;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                    }
                                }

                                if($payment_type3 == 6){
                                    //DIAS DE CREDITO
                                    if(($account_efectivo3 != 0)){

                                        $var3->id_account = $account_efectivo3;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                    }
                                }

                                if($payment_type3 == 9 || $payment_type3 == 10){
                                    //CUENTAS PUNTO DE VENTA
                                    if(($account_punto_de_venta3 != 0)){
                                        $var3->id_account = $account_punto_de_venta3;
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                    }
                                }

                            
                            

                                    $var3->payment_type = request('payment_type3');
                                    $var3->amount = $valor_sin_formato_amount_pay3;
                                    
                                    if($coin != 'bolivares'){
                                        $var3->amount = $var3->amount * $bcv;
                                    }
                                    
                                    $var3->status =  1;
                                
                                    $total_pay += $valor_sin_formato_amount_pay3;

                                    $validate_boolean3 = true;

                                
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                            }

                            
                        }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                            }
                        /*--------------------------------------------*/
                }
                $payment_type4 = request('payment_type4');
                if($come_pay >= 4){

                        /*-------------PAGO NUMERO 4----------------------*/

                        $var4 = new expensePayment();
                        $var4->setConnection(Auth::user()->database_name);

                        $amount_pay4 = request('amount_pay4');

                        if(isset($amount_pay4)){
                            
                            $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 4!');
                        }
                            

                        $account_bank4 = request('account_bank4');
                        $account_efectivo4 = request('account_efectivo4');
                        $account_punto_de_venta4 = request('account_punto_de_venta4');

                        $credit_days4 = request('credit_days4');

                    

                        $reference4 = request('reference4');

                        if($valor_sin_formato_amount_pay4 != 0){

                            if($payment_type4 != 0){

                                $var4->id_expense = request('id_expense');

                                //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                                if($payment_type4 == 1 || $payment_type4 == 11 || $payment_type4 == 5 ){
                                    //CUENTAS BANCARIAS
                                    if(($account_bank4 != 0)){
                                        if(isset($reference4)){

                                            $var4->id_account = $account_bank4;

                                            $var4->reference = $reference4;

                                        }else{
                                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                        }
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                    }
                                }if($payment_type4 == 2){
                        
                                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 
        
                                    $var4->id_account = $account_contado->id;
                                }
                                if($payment_type4 == 4){
                                    //DIAS DE CREDITO
                                    if(isset($credit_days4)){

                                        $var4->credit_days = $credit_days4;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                    }
                                }

                                if($payment_type4 == 6){
                                    //DIAS DE CREDITO
                                    if(($account_efectivo4 != 0)){

                                        $var4->id_account = $account_efectivo4;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                    }
                                }

                                if($payment_type4 == 9 || $payment_type4 == 10){
                                    //CUENTAS PUNTO DE VENTA
                                    if(($account_punto_de_venta4 != 0)){
                                        $var4->id_account = $account_punto_de_venta4;
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                    }
                                }

                            
                            

                                    $var4->payment_type = request('payment_type4');
                                    $var4->amount = $valor_sin_formato_amount_pay4;
                                    
                                    if($coin != 'bolivares'){
                                        $var4->amount = $var4->amount * $bcv;
                                    }
                                    
                                    $var4->status =  1;
                                
                                    $total_pay += $valor_sin_formato_amount_pay4;

                                    $validate_boolean4 = true;

                                
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                            }

                            
                        }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                            }
                        /*--------------------------------------------*/
                } 
                $payment_type5 = request('payment_type5');
                if($come_pay >= 5){

                    /*-------------PAGO NUMERO 5----------------------*/

                    $var5 = new expensePayment();
                    $var5->setConnection(Auth::user()->database_name);

                    $amount_pay5 = request('amount_pay5');

                    if(isset($amount_pay5)){
                        
                        $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 5!');
                    }
                        

                    $account_bank5 = request('account_bank5');
                    $account_efectivo5 = request('account_efectivo5');
                    $account_punto_de_venta5 = request('account_punto_de_venta5');

                    $credit_days5 = request('credit_days5');

                

                    $reference5 = request('reference5');

                    if($valor_sin_formato_amount_pay5 != 0){

                        if($payment_type5 != 0){

                            $var5->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type5 == 1 || $payment_type5 == 11 || $payment_type5 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank5 != 0)){
                                    if(isset($reference5)){

                                        $var5->id_account = $account_bank5;

                                        $var5->reference = $reference5;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                                }
                            }if($payment_type5 == 2){
                        
                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 

                                $var5->id_account = $account_contado->id;
                            }
                            if($payment_type5 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days5)){

                                    $var5->credit_days = $credit_days5;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                                }
                            }

                            if($payment_type5 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo5 != 0)){

                                    $var5->id_account = $account_efectivo5;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                                }
                            }

                            if($payment_type5 == 9 || $payment_type5 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta5 != 0)){
                                    $var5->id_account = $account_punto_de_venta5;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                                }
                            }

                        
                        

                                $var5->payment_type = request('payment_type5');
                                $var5->amount = $valor_sin_formato_amount_pay5;
                                
                                if($coin != 'bolivares'){
                                    $var5->amount = $var5->amount * $bcv;
                                }
                                
                                $var5->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay5;

                                $validate_boolean5 = true;

                            
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                        }

                        
                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                } 
                $payment_type6 = request('payment_type6');
                if($come_pay >= 6){

                    /*-------------PAGO NUMERO 6----------------------*/

                    $var6 = new expensePayment();
                    $var6->setConnection(Auth::user()->database_name);

                    $amount_pay6 = request('amount_pay6');

                    if(isset($amount_pay6)){
                        
                        $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 6!');
                    }
                        

                    $account_bank6 = request('account_bank6');
                    $account_efectivo6 = request('account_efectivo6');
                    $account_punto_de_venta6 = request('account_punto_de_venta6');

                    $credit_days6 = request('credit_days6');

                    

                    $reference6 = request('reference6');

                    if($valor_sin_formato_amount_pay6 != 0){

                        if($payment_type6 != 0){

                            $var6->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type6 == 1 || $payment_type6 == 11 || $payment_type6 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank6 != 0)){
                                    if(isset($reference6)){

                                        $var6->id_account = $account_bank6;

                                        $var6->reference = $reference6;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                                }
                            }if($payment_type6 == 2){
                        
                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 

                                $var6->id_account = $account_contado->id;
                            }
                            if($payment_type6 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days6)){

                                    $var6->credit_days = $credit_days6;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                                }
                            }

                            if($payment_type6 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo6 != 0)){

                                    $var6->id_account = $account_efectivo6;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                                }
                            }

                            if($payment_type6 == 9 || $payment_type6 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta6 != 0)){
                                    $var6->id_account = $account_punto_de_venta6;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                                }
                            }

                        
                        

                                $var6->payment_type = request('payment_type6');
                                $var6->amount = $valor_sin_formato_amount_pay6;

                                if($coin != 'bolivares'){
                                    $var6->amount = $var6->amount * $bcv;
                                }
                                
                                $var6->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay6;

                                $validate_boolean6 = true;

                            
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                        }

                        
                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                } 
                $payment_type7 = request('payment_type7');
                if($come_pay >= 7){

                    /*-------------PAGO NUMERO 7----------------------*/

                    $var7 = new expensePayment();
                    $var7->setConnection(Auth::user()->database_name);

                    $amount_pay7 = request('amount_pay7');

                    if(isset($amount_pay7)){
                        
                        $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 7!');
                    }
                        

                    $account_bank7 = request('account_bank7');
                    $account_efectivo7 = request('account_efectivo7');
                    $account_punto_de_venta7 = request('account_punto_de_venta7');

                    $credit_days7 = request('credit_days7');

                    

                    $reference7 = request('reference7');

                    if($valor_sin_formato_amount_pay7 != 0){

                        if($payment_type7 != 0){

                            $var7->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type7 == 1 || $payment_type7 == 11 || $payment_type7 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank7 != 0)){
                                    if(isset($reference7)){

                                        $var7->id_account = $account_bank7;

                                        $var7->reference = $reference7;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                                }
                            }if($payment_type7 == 2){
                        
                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 

                                $var7->id_account = $account_contado->id;
                            }
                            if($payment_type7 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days7)){

                                    $var7->credit_days = $credit_days7;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                                }
                            }

                            if($payment_type7 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo7 != 0)){

                                    $var7->id_account = $account_efectivo7;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                                }
                            }

                            if($payment_type7 == 9 || $payment_type7 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta7 != 0)){
                                    $var7->id_account = $account_punto_de_venta7;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                                }
                            }

                        
                        

                                $var7->payment_type = request('payment_type7');
                                $var7->amount = $valor_sin_formato_amount_pay7;
                                
                                if($coin != 'bolivares'){
                                    $var7->amount = $var7->amount * $bcv;
                                }
                                
                                $var7->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay7;

                                $validate_boolean7 = true;

                            
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                        }

                        
                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                } 
            }
                
         
                //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
            if(($total_pay == $total_pay_form) || ($sin_formato_total_pay <= 0))
            {

            
                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);


                $header_voucher->description = "Pago de Bienes o servicios.";
                $header_voucher->date = $date_payment ?? $datenow;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();

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
    

               
                
                if($validate_boolean1 == true){
                    $var->save();

                    $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$expense->id,$user_id,0,$var->amount);
                    
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var,"expense_payment","Se registró un Pago");

                }
                
                if($validate_boolean2 == true){
                    $var2->save();
                
                    $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$expense->id,$user_id,0,$var2->amount);
                    
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var2,"expense_payment","Se registró un Pago");
                }
                
                if($validate_boolean3 == true){
                    $var3->save();

                    $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$expense->id,$user_id,0,$var3->amount);
                
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var3,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean4 == true){
                    $var4->save();

                    $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$expense->id,$user_id,0,$var4->amount);
                
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var4,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean5 == true){
                    $var5->save();

                    $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$expense->id,$user_id,0,$var5->amount);
                
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var5,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean6 == true){
                    $var6->save();

                    $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$expense->id,$user_id,0,$var6->amount);
                
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var6,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean7 == true){
                    $var7->save();

                    $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$expense->id,$user_id,0,$var7->amount);
                
                    $historial_expense = new HistorialExpenseController();

                    $historial_expense->registerAction($var7,"expense_payment","Se registró un Pago");
                }

                
                /*Se agregan los movimientos de las retenciones si son diferentes a cero */

                if($retencion_iva !=0){
                    $account_iva_retenido = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)
                                                            ->where('code_three',4)->where('code_four',1)->where('code_five',2)->first();  

                    if(isset($account_iva_retenido)){
                        $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$expense->id,$user_id,0,$retencion_iva);
                    }
                    $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_iva','<>',NULL)->where('status','C')->orderBy('number_iva','desc')->first();

                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $expense->number_iva = $last_number->number_iva + 1;
                    }else{
                        $expense->number_iva = 1;
                    }
                }


                if($retencion_islr !=0){
                    $account_islr_pagago = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',4)
                                                    ->where('code_four',1)->where('code_five',4)->first();  

                    if(isset($account_islr_pagago)){
                        $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$expense->id,$user_id,0,$retencion_islr);
                    }
                    $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_islr','<>',NULL)->where('status','C')->orderBy('number_islr','desc')->first();

                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $expense->number_islr = $last_number->number_islr + 1;
                    }else{
                        $expense->number_islr = 1;
                    }
                    
                }
                /*------------------------------- */
               

                if(isset($anticipo) && ($anticipo != 0)){

                    $account_anticipo_proveedor = Account::on(Auth::user()->database_name)->where('code_one',1)
                                                            ->where('code_two',1)
                                                            ->where('code_three',4)
                                                            ->where('code_four',2)
                                                            ->where('code_five',1)->first(); 
                    if($sin_formato_total_pay < 0){
                        $global->checkAnticipoExpense($expense,$sin_formato_grandtotal);
                        $expense->anticipo =  $sin_formato_grandtotal;
                        
                        
                    }else{
                        $expense->anticipo =  $anticipo;
                        $global->associate_anticipos_expense($expense);
                        
                    }
                
                    if(isset($account_anticipo_proveedor)){
                        $this->add_movement($bcv,$header_voucher->id,$account_anticipo_proveedor->id,$expense->id,$user_id,0,$expense->anticipo);
                        $global->add_payment_expense($expense,$account_anticipo_proveedor->id,3,$expense->anticipo,$bcv);
                    }
                }else{
                    $expense->anticipo = 0;
                }

  
                //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
                $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first(); 
                    
                if(isset($account_cuentas_por_pagar_proveedores)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,$sin_formato_grandtotal,0);
                }

               
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');   

                if(($expense->status != 'C') && ($expense->status != 'P')){
                    
                    $header_voucher  = new HeaderVoucher();
                    $header_voucher->setConnection(Auth::user()->database_name);


                    $header_voucher->description = "Compras de Bienes o servicios.";
                    $header_voucher->date = $date_payment ?? $datenow;
                    
                
                    $header_voucher->status =  "1";
                
                    $header_voucher->save();
                
                    $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
                    
                    foreach($expense_details as $var){
                        $account = Account::on(Auth::user()->database_name)->find($var->id_account);
                        
                        if(isset($account)){
                            $this->add_movement($bcv,$header_voucher->id,$account->id,$expense->id,$user_id,$var->price * $var->amount,0);
                        }
                    }
    
                    //Credito Fiscal IVA por Pagar
    
                    $account_credito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA (Credito Fiscal)')->first();
                        
                    if(isset($account_credito_iva_fiscal)){
                        if($sin_formato_amount_iva != 0){
                            $this->add_movement($bcv,$header_voucher->id,$account_credito_iva_fiscal->id,$expense->id,$user_id,$sin_formato_amount_iva,0);
                        }
                    }
    
                    //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
                    $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first(); 
                    
                    if(isset($account_cuentas_por_pagar_proveedores)){
                        $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,0,$sin_formato_grandtotal);
                    }
                }
                
                /*Modifica la cotizacion */
                    $expense->date_payment = $date_payment ?? $datenow;

                    $expense->iva_percentage = $iva_percentage;

                   

                    $id_islr_concept = request('id_islr_concept');

                    if(isset($id_islr_concept) && ($id_islr_concept > 0)){
                        $expense->id_islr_concept = $id_islr_concept;
                    }    

                    $expense->base_imponible = $base_imponible;
                    $expense->amount =  $sin_formato_amount;
                    $expense->amount_iva =  $sin_formato_amount_iva;
                    $expense->amount_with_iva =  $sin_formato_grandtotal;
                    $iva_percentage = $iva_percentage;
                   
                    $expense->status = "C";

                    $expense->coin = $coin;

                    $expense->retencion_iva = $retencion_iva;
                    $expense->retencion_islr = $retencion_islr;

                    $expense->save();

                
                    //aumentamos el inventario
                    $retorno = $this->increase_inventory($expense->id,$expense->date);


          

                    if($retorno != "exito"){
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'');
                    }

               
             
                 //Aqui pasa los quotation_products a status C de Cobrado
                DB::connection(Auth::user()->database_name)->table('expenses_details')
                ->where('id_expense', '=', $expense->id)
                ->update(['status' => 'C']);

                $global = new GlobalController;                                                
                $global->procesar_anticipos_expense($expense,$sin_formato_total_pay);
                

            }else{
                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
            }

            $historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($expense,"expense","Se pagó la Compra");

            return redirect('expensesandpurchases/expensevoucher/'.$expense->id.'/'.$coin.'')->withSuccess('Factura Guardada con Exito!');

        }else{
            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Este pago ya ha sido realizado!');
        } 

    }


    public function store_expense_credit(Request $request)
    {
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        //dd($request);
        
        $sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('total_factura')));
        $sin_formato_base_imponible = str_replace(',', '.', str_replace('.', '', request('base_imponible')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount')));
        $sin_formato_amount_with_iva = str_replace(',', '.', str_replace('.', '', request('grand_total')));
         
        $retencion_iva_check = request('retencion_iva_check');
        
        if(isset($retencion_iva_check)){
            $sin_formato_iva_retencion = str_replace(',', '.', str_replace('.', '', request('iva_retencion')));
        }else{
            $sin_formato_iva_retencion = 0;
        }

        $retencion_islr_check = request('retencion_islr_check');
        
        if(isset($retencion_islr_check)){
            $sin_formato_islr_retencion = str_replace(',', '.', str_replace('.', '', request('islr_retencion')));       
        }else{
            $sin_formato_islr_retencion = 0;
        }

        
        
        $sin_formato_anticipo = str_replace(',', '.', str_replace('.', '', request('anticipo')));
        $sin_formato_total_pay = str_replace(',', '.', str_replace('.', '', request('total_pay')));
        
        $id_expense = request('id_expense');
        $user_id = request('user_id');

        $coin = request('coin');

        $date_payment = request('date_payment');

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        if($coin != 'bolivares'){
            $sin_formato_amount_iva = $sin_formato_amount_iva * $expense->rate;
            $sin_formato_amount_with_iva = $sin_formato_amount_with_iva * $expense->rate;
            $sin_formato_base_imponible = $sin_formato_base_imponible * $expense->rate;
            $sin_formato_amount = $sin_formato_amount * $expense->rate;

            $sin_formato_iva_retencion = $sin_formato_iva_retencion * $expense->rate;
            $sin_formato_islr_retencion = $sin_formato_islr_retencion * $expense->rate;
            $sin_formato_anticipo = $sin_formato_anticipo * $expense->rate;
            $sin_formato_total_pay = $sin_formato_total_pay * $expense->rate;
        }

        $id_islr_concept = request('id_islr_concept_credit');

        if(isset($id_islr_concept) && ($id_islr_concept > 0)){
            $expense->id_islr_concept = $id_islr_concept;
        }    

        $expense->base_imponible = $sin_formato_base_imponible;
        $expense->amount =  $sin_formato_amount;
        $expense->amount_iva =  $sin_formato_amount_iva;
        $expense->amount_with_iva =  $sin_formato_total_pay;

        $expense->retencion_iva =  $sin_formato_iva_retencion;
        $expense->retencion_islr =  $sin_formato_islr_retencion;
        $expense->anticipo =  $sin_formato_anticipo;

        $credit = request('credit');

        $expense->iva_percentage = request('iva');

        $expense->credit_days = $credit;

        $expense->status = "P";

        $expense->save();

         
         //preparando para guardar historial
        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get();
        
        if(isset($expense_detail)){  
           
           foreach($expense_detail as $var){
            
                if(isset($var->id_inventory)){
                  
                    $product = Product::on(Auth::user()->database_name)->find($var->id_inventory);

                    if(isset($product)){
                           
                        if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                            $global = new GlobalController; 
    
                            $global->transaction_inv('compra',$var->id_inventory,'compra_n',$var->amount,$var->price,$date,1,1,0,$var->id_inventory_histories,$var->id,0,$var->id_expense);
                            
                        }
                        
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario no existe!');
                    }
                }
           }         
            
                   
        }

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Compras de Bienes o servicios.";
        $header_voucher->date = $date_payment ?? $datenow;
        
    
        $header_voucher->status =  "1";
    
        $header_voucher->save();
    
        $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
                    
        foreach($expense_details as $var){
            $account = Account::on(Auth::user()->database_name)->find($var->id_account);
            
            if(isset($account)){
                $this->add_movement($expense->rate,$header_voucher->id,$account->id,$expense->id,$user_id,$var->price * $var->amount,0);
            }
        }

        //IVA credito Fiscal

        $account_credito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA (Credito Fiscal)')->first();
            
        if(isset($account_credito_iva_fiscal)){
            if($sin_formato_amount_iva != 0){
                $this->add_movement($expense->rate,$header_voucher->id,$account_credito_iva_fiscal->id,$expense->id,$user_id,$sin_formato_amount_iva,0);
            }
        }

        //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
        $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first(); 
                    
        if(isset($account_cuentas_por_pagar_proveedores)){
            $this->add_movement($expense->rate,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,0,$sin_formato_amount_with_iva);
        }

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se registró la Compra a Crédito");
         

        return redirect('expensesandpurchases/expensevoucher/'.$expense->id.'/'.$coin.'')->withSuccess('Gasto o Compra Guardada con Exito!');
    }


    public function increase_inventory($id_expense,$date)
    {
       
        
        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get();



          if(isset($expense_detail)){  
           
           foreach($expense_detail as $var){
            
                if(isset($var->id_inventory)){
                     
                    $product = Product::on(Auth::user()->database_name)->find($var->id_inventory);
                    
                    if(isset($product)){    

                         if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){
  
                            $global = new GlobalController; 
           
                            $global->transaction_inv('compra',$var->id_inventory,'compra_n',$var->amount,$var->price,$date,1,1,0,$var->id_inventory_histories,$var->id,0,$var->id_expense);
                            

                        }    
                        
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario no existe!');
                    } 
                }
           }         
            
                   
        }else{
            return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario de la cotizacion no existe!');
        } 

           

            return "exito";
   

    }




    public function add_movement($bcv,$id_header,$id_account,$id_expense,$id_user,$debe,$haber)
    {

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        $detail->id_expense = $id_expense;

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


    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$id_expense,$user_id,$amount_debe,$amount_haber)
    {


        //Cuentas por Cobrar Clientes

            //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
            if(isset($id_account)){
                $this->add_movement($bcv,$header_voucher,$id_account,$id_expense,$user_id,$amount_debe,$amount_haber);
            
            }//SIN DETERMINAR
            else if($payment_type == 7){
                
                $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Ingresos No Identificados')->first(); 
        
                if(isset($account_sin_determinar)){
                    $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            }//PAGO DE CONTADO
            else if($payment_type == 2){
                
                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 
        
                if(isset($account_contado)){
                    $this->add_movement($bcv,$header_voucher,$account_contado->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            }//CONTRA ANTICIPO
            else if($payment_type == 3){
                
                $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first(); 
        
                if(isset($account_contra_anticipo)){
                    $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            } 
           

    }
    


    public function refreshrate($id_expense,$coin,$rate)
    { 
        $sin_formato_rate = str_replace(',', '.', str_replace('.', '', $rate));

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);


        ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                                ->update(['rate' => $sin_formato_rate]);


        ExpensesAndPurchase::on(Auth::user()->database_name)->where('id',$id_expense)
                                ->update(['rate' => $sin_formato_rate]);

        
        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se actualizó la taza de: ".number_format($expense->rate, 2, ',', '.')." a ".$rate);
        
        
        return redirect('/expensesandpurchases/register/'.$id_expense.'/'.$coin.'')->withSuccess('Actualizacion de Tasa Exitosa!');

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
    public function edit($id,$coin)
    {
        /*$expense_detail = ExpensesDetail::on(Auth::user()->database_name)->find($id);
    
    
        return view('admin.expensesandpurchases.edit_product',compact('expense_detail','coin'));*/
    
    }
    public function editexpensesandpurchaseproduct($id)
    {
            $expensesandpurchase_product = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id);
        
            if(isset($expensesandpurchase_product)){

                $product= Product::on(Auth::user()->database_name)->find($expensesandpurchase_product->id_inventory);

                return view('admin.expensesandpurchases.edit_product',compact('expensesandpurchase_product','inventory'));
            }else{
                return redirect('/expensesandpurchases')->withDanger('No se Encontro el Producto!');
            }
        
        
    
    }
    public function editproduct($id,$coin)
    {
        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->find($id);
        $rate = null;

        if(isset($expense_detail)){

            if(isset($expense_detail->id_inventory))
            {
                $inventory = Inventory::on(Auth::user()->database_name)->find($expense_detail->id_inventory);
            }else{
                $inventory = null;
            }
            
            if($coin != 'bolivares'){
                $rate = $expense_detail->rate;
            }

            return view('admin.expensesandpurchases.edit_product',compact('rate','coin','expense_detail','inventory'));
        }else{
            return redirect('/expensesandpurchases')->withDanger('No se Encontro el Producto!');
        }
        
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

        $vars =  ExpensesAndPurchase::on(Auth::user()->database_name)->find($id);

        $vars_status = $vars->status;
        $vars_exento = $vars->exento;
        $vars_islr = $vars->islr;
    
        $data = request()->validate([
            
        
            'segment_id'         =>'required',
            'sub_segment_id'         =>'required',
            'unit_of_measure_id'         =>'required',


            'type'         =>'required',
            'description'         =>'required',
        
            'price'         =>'required',
            'price_buy'         =>'required',
            'cost_average'         =>'required',

            'money'         =>'required',
        
            'special_impuesto'         =>'required',
            'status'         =>'required',
        
        ]);

        $var = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id);

        $var->segment_id = request('segment_id');
        $var->subsegment_id= request('sub_segment_id');
        $var->unit_of_measure_id = request('unit_of_measure_id');

        $var->code_comercial = request('code_comercial');
        $var->type = request('type');
        $var->description = request('description');
        
        $var->price = request('price');
        $var->price_buy = request('price_buy');
    
        $var->cost_average = request('cost_average');
        $var->photo_expensesandpurchase = request('photo_expensesandpurchase');

        $var->money = request('money');


        $var->special_impuesto = request('special_impuesto');

        if(request('exento') == null){
            $var->exento = "0";
        }else{
            $var->exento = "1";
        }
        if(request('islr') == null){
            $var->islr = "0";
        }else{
            $var->islr = "1";
        }
    

        if(request('status') == null){
            $var->status = $vars_status;
        }else{
            $var->status = request('status');
        }
    
        $var->save();

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($var,"expense","Se actualizó la Compra");
        

        return redirect('/expensesandpurchases')->withSuccess('Actualizacion Exitosa!');
        }



        

        public function update_product(Request $request, $id)
        { 
            //dd($request);
            $data = request()->validate([
                
                'description'   =>'required',
                'amount'        =>'required',
                

                'coin'   =>'required',
                'price'   =>'required',
            
            ]);
            
            $var = ExpensesDetail::on(Auth::user()->database_name)->findOrFail($id);
            $validation = new ExpenseDetailValidationController();

            $price_old = $var->price;
            $amount_old = $var->amount;

            $coin = request('coin');

            $valor_sin_formato_price = str_replace(',', '.', str_replace('.', '', request('price')));
           
            $var->price = $valor_sin_formato_price;

            $rate_expense = request('rate_expense');

            if($coin != 'bolivares'){
                $var->price = $var->price * $rate_expense;
            }
            
            $var->description = request('description');

            $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
        
            $var->amount = $valor_sin_formato_amount;

            $exento = request('exento');

            if($exento == null){
                $exento = 0;
            }else{
                $exento = 1;
            }

            
            $var->exento = $exento;

            $islr = request('islr');
            if($islr == null){
                $var->islr = false;
            }else{
                $var->islr = true;
            }
        
            $var->save();
           
            if($var->expenses['status'] == 'P'){
                $validation->calculateExpenseModify($var->id_expense);
            }
            
            /*$historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($var,"expense_product","Actualizó el Producto: ".$var->inventories['code']."/ 
            Precio Viejo: ".number_format($price_old, 2, ',', '.')." Cantidad: ".$amount_old."/ Precio Nuevo: ".number_format($var->price, 2, ',', '.')." Cantidad: ".$var->amount);
        */
            return redirect('/expensesandpurchases/register/'.$var->id_expense.'/'.$coin.'')->withSuccess('Actualizacion Exitosa!');
        
        }

    /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
    public function destroy(Request $request)
    {
        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find(request('id_expense_modal')); 
        
        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$expense->id)
        ->update(['status' => 'X']);

        
        $global = new GlobalController();
        $global->deleteAllProductsExpense($expense->id);

        ExpensePayment::on(Auth::user()->database_name)
                        ->where('id_expense',$expense->id)
                        ->update(['status' => 'X']);

        $expense->status = 'X';
        $expense->save();
        
        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se elimino la Compra");
        
        return redirect('/expensesandpurchases')->withDanger('Reverso Exitoso!!');
    }



    public function reversar_expense($id_expense)
    { 
       
        $id_expense = $id_expense;

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        $exist_multipayment = MultipaymentExpense::on(Auth::user()->database_name)
                            ->where('id_expense',$expense->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  
                            
        if(empty($exist_multipayment)){
            if($expense != 'X'){
                $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                ->update(['status' => 'X']);
    
                
                $global = new GlobalController();
                $global->deleteAllProducts($expense->id);

                ExpensePayment::on(Auth::user()->database_name)
                                ->where('id_expense',$expense->id)
                                ->update(['status' => 'X']);
    
                $expense->status = 'X';
                $expense->save();

                $quotation_products = DB::connection(Auth::user()->database_name)->table('expenses_details')
                ->where('id_expense', '=', $expense->id)->get(); // Conteo de Productos para incluiro en el historial de inventario
        
                foreach($quotation_products as $det_products){ // guardando historial de inventario
                    
                $global->transaction_inv('rev_compra',$det_products->id_inventory,'compra_reverso',$det_products->amount,$det_products->price,$datenow,1,1,0,$det_products->id_inventory_histories,$det_products->id,0,$det_products->id_expense);
                
                } 
        

                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($expense->anticipo))&& ($expense->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipoProvider($datenow,$expense->id_provider,$account_anticipo->id,"bolivares",
                    $expense->anticipo,$expense->bcv,"reverso compra N°".$expense->id);
                    
                }

                $historial_expense = new HistorialExpenseController();

                $historial_expense->registerAction($expense,"expense","Se Reversó la Compra");

                return redirect('expensesandpurchases/indexhistorial')->withSuccess('Reverso de Compra Exitosa!');
            }
        }else{
            return redirect('expensesandpurchases/indexhistorial')->withDanger('No se pudo reversar la Compra');
        }
    }

    

    public function reversar_expense_with_id($id_expense)
    { 
       
        $id_expense = $id_expense;

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        $exist_multipayment = MultipaymentExpense::on(Auth::user()->database_name)
                            ->where('id_expense',$expense->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  
                            
        if(empty($exist_multipayment)){
            if($expense != 'X'){

                HeaderVoucher::on(Auth::user()->database_name)
                ->join('detail_vouchers','detail_vouchers.id_header_voucher','header_vouchers.id')
                ->where('detail_vouchers.id_expense',$id_expense)
                ->update(['header_vouchers.status' => 'X']);

                $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                ->update(['status' => 'X']);
    
                
                $global = new GlobalController();
                $global->deleteAllProducts($expense->id);

                ExpensePayment::on(Auth::user()->database_name)
                                ->where('id_expense',$expense->id)
                                ->update(['status' => 'X']);
    
                $expense->status = 'X';
                $expense->save();



                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($expense->anticipo))&& ($expense->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipoProvider($datenow,$expense->id_provider,$account_anticipo->id,"bolivares",
                    $expense->anticipo,$expense->bcv,"reverso compra N°".$expense->id);
                    
                }

                $historial_expense = new HistorialExpenseController();

                $historial_expense->registerAction($expense,"expense","Se Reversó la Factura");
            }
        }else{
            
            $this->reversar_expense_multipayment($id_expense,$exist_multipayment->id_header);
        }
    }

    public function reversar_expense_multipayment($id_expense,$id_header){

        
        if(isset($id_header)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            //aqui reversamos todo el movimiento del multipago
            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id','=',$id_header)
            ->update(['detail_vouchers.status' => 'X' , 'header_vouchers.status' => 'X']);

            //aqui se cambia el status de los pagos
            DB::connection(Auth::user()->database_name)->table('multipayments')
            ->join('expense_payments', 'expense_payments.id_expense','=','multipayments.id_expense')
            ->where('multipayments.id_header','=',$id_header)
            ->update(['expense_payments.status' => 'X']);

            //aqui aumentamos el inventario y cambiamos el status de los productos que se reversaron
            DB::connection(Auth::user()->database_name)->table('multipayments')
                ->join('expense_products', 'expense_products.id_expense','=','multipayments.id_expense')
                ->join('inventories','inventories.id','expense_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('multipayments.id_header','=',$id_header)
                ->update(['inventories.amount' => DB::raw('inventories.amount+expense_products.amount') ,
                        'expense_products.status' => 'X']);
    

            //aqui le cambiamos el status a todas las facturas a X de reversado
            MultipaymentExpense::on(Auth::user()->database_name)
            ->join('expenses', 'expenses.id','=','multipayments.id_expense')
            ->where('id_header',$id_header)->update(['expenses.status' => 'X']);

            MultipaymentExpense::on(Auth::user()->database_name)->where('id_header',$id_header)->delete();



            $historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($expense,"expense","Se Reversó MultiCompra");

          
        }
    }

    public function deleteDetail(Request $request)
    {
        $id_detail = request('id_detail_modal');
        $coin = request('coin_modal');
        
        $detail_old = ExpensesDetail::on(Auth::user()->database_name)->find($id_detail); 
        
        if(isset($detail_old) && $detail_old->status == "C"){
            ExpensesDetail::on(Auth::user()->database_name)
                ->join('inventories','inventories.id','expenses_details.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('expenses_details.id',$detail_old->id)
                ->update(['inventories.amount' => DB::raw('inventories.amount-expenses_details.amount'), 'expenses_details.status' => 'X']);
        }else{
            $detail_old->delete(); 
        }

        $validation = new ExpenseDetailValidationController();

        if($detail_old->expenses['status'] == 'P'){
            $validation->calculateExpenseModify($detail_old->id_expense);
        }

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($detail_old,"expense_product","Se elimino la Compra");

        return redirect('/expensesandpurchases/register/'.$detail_old->id_expense.'/'.$coin.'')->withDanger('Eliminacion exitosa!!');
    }
    

    public function listaccount(Request $request, $type = null)
    {
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                    $code_one = 0;  $code_two = 0;
                    

                if($type == 1 || $type == 2){
                    //Inventario
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')
                                                                        ->where('code_one',1)
                                                                        ->where('code_two', 2)
                                                                        ->where('code_three', 1)
                                                                        ->where('code_four',1)
                                                                        ->where('code_five', '<>',0)
                                                                        ->orderBy('description','asc')
                                                                        ->get();
                }
                
                if($type == 3){
                    $code_one = 5;  
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$code_one)
                                                                ->where('code_two', '<>',0)
                                                                ->where('code_three', '<>',0)
                                                                ->where('code_four', '<>',0)
                                                                ->where('code_five', '<>',0)
                                                                ->orderBy('description','asc')
                                                                ->get();
                    return response()->json($respuesta,200);
                }
                if($type == 4){
                    $code_one = 6; 
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$code_one)
                                        ->where('code_two', '<>',0)
                                        ->where('code_three', '<>',0)
                                        ->where('code_four', '<>',0)
                                        ->where('code_five', '<>',0)
                                        ->orderBy('description','asc')
                                        ->get();
                    return response()->json($respuesta,200);
                }
                if($type == 5){
                    $code_one = 6;  
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$code_one)
                                        ->where('code_two', '<>',0)
                                        ->where('code_three', '<>',0)
                                        ->where('code_four', '<>',0)
                                        ->where('code_five', '<>',0)
                                        ->orderBy('description','asc')
                                        ->get();
                    return response()->json($respuesta,200);
                }
                if($type == 6){
                    $code_one = 6;  
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$code_one)
                                        ->where('code_two', '<>',0)
                                        ->where('code_three', '<>',0)
                                        ->where('code_four', '<>',0)
                                        ->where('code_five', '<>',0)
                                        ->orderBy('description','asc')
                                        ->get();
                    return response()->json($respuesta,200);
                }
                if($type == 7){
                    $code_one = 6;  
                    $respuesta = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$code_one)
                                        ->where('code_two', '<>',0)
                                        ->where('code_three', '<>',0)
                                        ->where('code_four', '<>',0)
                                        ->where('code_five', '<>',0)
                                        ->orderBy('description','asc')
                                        ->get();
                    return response()->json($respuesta,200);
                }
                
                
                return response()->json($respuesta,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }

    public function listinventory(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                $respuesta = Inventory::on(Auth::user()->database_name)
                                        ->join('products','products.id','inventories.product_id')
                                        ->where('inventories.code',$var)
                                        ->select('inventories.id','products.type')
                                        ->get();
                                        
                return response()->json($respuesta,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }




}