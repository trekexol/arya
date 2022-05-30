<?php

namespace App\Http\Controllers;

use App\Account;
use App\Anticipo;
use App\Client;
use App\Color;
use App\Company;
use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\HeaderVoucher;
use App\Http\Controllers\Historial\HistorialAnticipoController;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Modelo;
use App\Provider;
use App\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnticipoController extends Controller
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
            
            
                $anticipos = Anticipo::on(Auth::user()->database_name)
                ->whereIn('status',[1,'M'])->where('id_client','<>',null)
                ->orderBy('id','desc')->get();
                
                $control = 'index';

                
                    foreach ($anticipos as $key => $anticipo) {
                    
                        $headervoucher = HeaderVoucher::on(Auth::user()->database_name)
                        ->where('id_anticipo',$anticipo->id)
                        ->first();
                        if (isset($headervoucher)) {
                        $anticipo->comprobante = $headervoucher->id;
                        } else {
                         $anticipo->comprobante = ''; 
                        }
                    }



            return view('admin.anticipos.index',compact('anticipos','control'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
   }

   public function index_provider()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
       
        $anticipos = Anticipo::on(Auth::user()->database_name)->whereIn('status',[1,'M'])->where('id_provider','<>',null)->orderBy('id','desc')->get();
        
        $control = 'index';
                
        foreach ($anticipos as $key => $anticipo) {
                    
            $headervoucher = HeaderVoucher::on(Auth::user()->database_name)
            ->where('id_anticipo',$anticipo->id)
            ->first();
            if (isset($headervoucher)) {
            $anticipo->comprobante = $headervoucher->id;
            } else {
             $anticipo->comprobante = ''; 
            }
        }

       
       return view('admin.anticipos.index_provider',compact('anticipos','control'));
   }

   public function indexhistoric_provider()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
       
        $anticipos = Anticipo::on(Auth::user()->database_name)->where('status','C')->where('id_provider','<>',null)->orderBy('id','desc')->get();

        $control = 'historic';
                
        foreach ($anticipos as $key => $anticipo) {
                    
            $headervoucher = HeaderVoucher::on(Auth::user()->database_name)
            ->where('id_anticipo',$anticipo->id)
            ->first();
            if (isset($headervoucher)) {
            $anticipo->comprobante = $headervoucher->id;
            } else {
             $anticipo->comprobante = ''; 
            }
        }

       
       return view('admin.anticipos.index_provider',compact('anticipos','control'));
   }
   
   public function indexhistoric()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
       
        $anticipos = Anticipo::on(Auth::user()->database_name)->where('status','C')->where('id_client','<>',null)->orderBy('id','desc')->get();
        $control = 'historic';
                
        foreach ($anticipos as $key => $anticipo) {
                    
            $headervoucher = HeaderVoucher::on(Auth::user()->database_name)
            ->where('id_anticipo',$anticipo->id)
            ->first();
            if (isset($headervoucher)) {
            $anticipo->comprobante = $headervoucher->id;
            } else {
             $anticipo->comprobante = ''; 
            }
        }

       
       return view('admin.anticipos.index',compact('anticipos','control'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function selectclient($id_anticipo = null)
    {
        $clients = Client::on(Auth::user()->database_name)->orderBy('name' ,'asc')->get();

        return view('admin.anticipos.selectclient',compact('clients','id_anticipo'));
    }

    public function selectprovider($id_anticipo = null)
    {
        
        $providers = Provider::on(Auth::user()->database_name)->orderBy('razon_social' ,'asc')->get();

        return view('admin.anticipos.selectprovider',compact('providers','id_anticipo'));
    }
    
    public function selectanticipo($id_client,$coin,$id_quotation)
    {
        $anticipos = Anticipo::on(Auth::user()->database_name)->where('id_client',$id_client)
                                                                ->where(function ($query) use ($id_quotation){
                                                                    $query->where('id_quotation',null)
                                                                        ->orWhere('id_quotation',$id_quotation);
                                                                })
                                                                ->whereIn('status',[1,'M'])->get();
                                                                
        $client = Client::on(Auth::user()->database_name)->find($id_client);

        return view('admin.anticipos.selectanticipo',compact('anticipos','client','id_quotation','coin'));
    }
    
    public function selectanticipo_provider($id_provider,$coin,$id_expense)
    {
       
        $anticipos = Anticipo::on(Auth::user()->database_name)
                                                            ->where('id_provider',$id_provider)
                                                            ->where(function ($query) use ($id_expense){
                                                                $query->where('id_expense',null)
                                                                    ->orWhere('id_expense',$id_expense);
                                                            })
                                                            ->whereIn('status',[1,'M'])->orderBy('id' ,'DESC')->get();
        

        $provider = Provider::on(Auth::user()->database_name)->find($id_provider);

        return view('admin.anticipos.selectanticipo_provider',compact('anticipos','provider','id_expense','coin'));
    }
  

   public function create()
   {
        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->whereIn('code_four', [1, 2])
                                            ->where('code_five', '<>',0)
                                            ->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        return view('admin.anticipos.create',compact('datenow','accounts','bcv'));
   }

   public function create_provider($id_provider = null)
   {
        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->whereIn('code_four', [1, 2])
                                            ->where('code_five', '<>',0)
                                            ->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $provider = null;
        $expenses = null;

        if(isset($id_provider)){
            $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)
            ->whereIn('status',['1','P'])->where('id_provider',$id_provider)->get();
        }
       
        

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if(isset($id_provider)){
            $provider =  Provider::on(Auth::user()->database_name)->find($id_provider);
        }

        return view('admin.anticipos.create_provider',compact('expenses','datenow','accounts','bcv','provider'));
   }

   public function createclient($id_client)
   {

        $client =  Client::on(Auth::user()->database_name)->find($id_client);
        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three',1)
                                            ->whereIn('code_four', [1, 2])
                                            ->where('code_five', '<>',0)
                                            ->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        
        $invoices_to_pay = Quotation::on(Auth::user()->database_name)->whereIn('status',['1','P'])->where('id_client',$id_client)->get();
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        
        return view('admin.anticipos.create',compact('datenow','client','accounts','bcv','invoices_to_pay'));
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
            
        
            'date_begin'         =>'required',
            'id_account'         =>'required',
            'id_user'         =>'required',

            'amount'         =>'required',
            'rate'         =>'required',
            'coin'         =>'required',

        ]);
        
        $var = new Anticipo();
        $var->setConnection(Auth::user()->database_name);
        
        $var->date = request('date_begin');
        if(request('id_client') != -1){
            $var->id_client = request('id_client');
        }
        
        $id_invoice = request('id_invoice');

        if(isset($id_invoice)){
            $var->id_quotation = request('id_invoice');
            $quotation =  Quotation::on(Auth::user()->database_name)->findOrFail($var->id_quotation);
            $var->id_client = $quotation->id_client;
        } else {
            $var->id_quotation = null;   
        }

        $var->id_account = request('id_account');
        $var->id_user = request('id_user');
        $var->coin = request('coin');

        if((empty($var->id_client) || $var->id_client == -1) && (empty($var->id_quotation) || $var->id_quotation == -1)){
            return redirect('/anticipos/register')->withDanger('Debe Seleccionar un Cliente o una Factura!');
        }
        
        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

        if($var->coin != 'bolivares'){
            $var->amount = $valor_sin_formato_amount * $valor_sin_formato_rate; 
            $var->rate = $valor_sin_formato_rate;
        }else{
            $var->amount = $valor_sin_formato_amount;
            $var->rate = $valor_sin_formato_rate;
        }

        
        $var->reference = request('reference');
        
    
        $var->status = 1;

        $var->save();

        /*Aplicamos el movimiento contable*/
        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);

        $datenow = request('date_begin');
        $header_voucher->id_anticipo =  $var->id;
        $header_voucher->description = "Anticipo";
        $header_voucher->date = $datenow;
        $header_voucher->status =  "1";
        $header_voucher->save();

        $this->add_movement($header_voucher->id,$var->id_account,$var->id_user,$var->amount,0,$var->rate);


        $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes Nacionales')->first();  
            
        if(isset($account_anticipo)){
            $this->add_movement($header_voucher->id,$account_anticipo->id,$var->id_user,0,$var->amount,$var->rate);
        }
        
        $historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($var,"Se registro el Anticipo");

        if((isset($var->id_client)) || (isset($var->id_quotation))){

            return redirect('/anticipos')->withSuccess('Registro Exitoso!');
        }else{
            return redirect('/anticipos/indexprovider')->withSuccess('Registro Exitoso!');
        }
        
    }

    public function registerAnticipo($date_begin,$id_client,$id_account,$coin,$amount,$rate,$reference,$id_quotation = null)
    {
   
        $user       =   auth()->user();
        $var = new Anticipo();
        $var->setConnection(Auth::user()->database_name);
        
        $var->date = $date_begin;
       
        $var->id_client = $id_client;
        
        $var->id_quotation = $id_quotation;
          

        $var->id_account = $id_account;
        $var->id_user =  $user->id;
        $var->coin = $coin;
        
       
        $var->amount = $amount; 
        $var->rate = $rate;
        
        
        $var->reference = $reference;
        $var->status = 1;

        $var->save();
        
    }

    public function registerAnticipoProvider($date_begin,$id_provider,$id_account,$coin,$amount,$rate,$reference,$id_quotation = null)
    {
   
        $user       =   auth()->user();
        $var = new Anticipo();
        $var->setConnection(Auth::user()->database_name);
        
        $var->date = $date_begin;
       
        $var->id_provider = $id_provider;
        
        $var->id_quotation = $id_quotation;
          

        $var->id_account = $id_account;
        $var->id_user =  $user->id;
        $var->coin = $coin;
        
       
        $var->amount = $amount; 
        $var->rate = $rate;
        
        
        $var->reference = $reference;
        $var->status = 1;

        $var->save();
        
    }


    public function store_provider(Request $request)
    {
        
        
        $data = request()->validate([
            
        
            'date_begin'         =>'required',
            'id_account'         =>'required',
            'id_user'         =>'required',

            'amount'         =>'required',
            'rate'         =>'required',
            'coin'         =>'required',

        ]);

        $var = new Anticipo();
        $var->setConnection(Auth::user()->database_name);

        $var->date = request('date_begin');
        
        $var->id_provider = request('id_provider');
        
        $var->id_account = request('id_account');
        $var->id_user = request('id_user');
        $var->coin = request('coin');
        $id_expense = request('id_expense');
        
        if(isset($id_expense) && request('id_expense') != -1){
            $var->id_expense = request('id_expense');
            $expense =  ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($var->id_expense);
            $var->id_provider = $expense->id_provider;
        }
        
        if(($var->id_provider == -1 || empty($var->id_provider)) && ($var->id_expense == -1 || empty($var->id_expense))){
            return redirect('/anticipos/registerprovider')->withDanger('Debe Seleccionar un Proveedor o una Compra!');
        }
        

        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

        if($var->coin != 'bolivares'){
            $var->amount = $valor_sin_formato_amount * $valor_sin_formato_rate; 
            $var->rate = $valor_sin_formato_rate;
        }else{
            $var->amount = $valor_sin_formato_amount;
            $var->rate = $valor_sin_formato_rate;
        }

        
        $var->reference = request('reference');
        
    
        $var->status = 1;

        $var->save();

        /*Aplicamos el movimiento contable*/
        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);

        $date = Carbon::now();
        $datenow = request('date_begin');
        $header_voucher->id_anticipo =  $var->id;
        $header_voucher->description = "Anticipo Proveedor";
        $header_voucher->date = $datenow;
        $header_voucher->status =  "1";
        $header_voucher->save();

        $this->add_movement($header_voucher->id,$var->id_account,$var->id_user,0,$var->amount,$var->rate);


        $account_anticipo_proveedor = Account::on(Auth::user()->database_name)->where('code_one',1)
                                    ->where('code_two',1)
                                    ->where('code_three',4)
                                    ->where('code_four',2)
                                    ->where('code_five',1)->first();  
            
        if(isset($account_anticipo_proveedor)){
            $this->add_movement($header_voucher->id,$account_anticipo_proveedor->id,$var->id_user,$var->amount,0,$var->rate);
        }
        
        $historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($var,"Se registro el Anticipo");

        if(isset($var->id_client)){
            return redirect('/anticipos')->withSuccess('Registro Exitoso!');
        }else{
           
            return redirect('/anticipos/indexprovider')->withSuccess('Registro Exitoso!');
        }
        
    }

  



    public function add_movement($id_header,$id_account,$id_user,$debe,$haber,$tasa){

       

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;

        $detail->debe = $debe;
        $detail->haber = $haber;
        $detail->tasa = $tasa;
      
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
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit($id,$id_client = null,$id_provider = null)
   {
        $anticipo = Anticipo::on(Auth::user()->database_name)->find($id);

        $invoices_to_pay = null;
        $expenses_to_pay = null;

        if(isset($id_client) && ($id_client != -1)){
            $client = Client::on(Auth::user()->database_name)->find($id_client);
            $invoices_to_pay = Quotation::on(Auth::user()->database_name)->whereIn('status',['1','P'])->where('id_client',$id_client)->get();
           
        }else{
            $client = null;
            if(isset($anticipo->id_client)){
                $invoices_to_pay = Quotation::on(Auth::user()->database_name)->whereIn('status',['1','P'])->where('id_client',$anticipo->id_client)->get();
            }
        }

        if(isset($id_provider) && ($id_provider != -1)){
            $provider = Provider::on(Auth::user()->database_name)->find($id_provider);
            $expenses_to_pay = ExpensesAndPurchase::on(Auth::user()->database_name)->whereIn('status',['1','P'])->where('id_provider',$id_provider)->get();
            
        }else{
            $provider = null;
            if(isset($anticipo->id_provider)){
                $expenses_to_pay = ExpensesAndPurchase::on(Auth::user()->database_name)->whereIn('status',['1','P'])->where('id_provider',$anticipo->id_provider)->get();
            }
        }

        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three',1)
                                            ->whereIn('code_four', [1, 2])
                                            ->where('code_five', '<>',0)
                                            ->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if(isset($anticipo->coin) && $anticipo->coin != 'bolivares'){
            
            $anticipo->amount = $anticipo->amount / $anticipo->rate;
            
        }
      
        return view('admin.anticipos.edit',compact('anticipo','accounts','datenow','bcv','client','provider','invoices_to_pay','expenses_to_pay'));
  
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
       
        $data = request()->validate([
                
            'date_begin'         =>'required',
            'id_client'         =>'required',
            'id_account'         =>'required',
            'id_user'         =>'required',

            'amount'         =>'required',
            'rate'         =>'required',
            'coin'         =>'required',

        ]);

        $var = Anticipo::on(Auth::user()->database_name)->findOrFail($id);

       
        $amount_old = $var->amount;
        $rate_old = $var->rate;

        if(request('id_quotation') != null){
            $var->id_quotation = request('id_quotation');
        }

        if(request('id_expense') != null){
            $var->id_expense = request('id_expense');
           
        }

        $var->date = request('date_begin');

        if(request('id_client') != -1){
            $var->id_client = request('id_client');
        }
        if(request('id_provider') != -1){
            $var->id_provider = request('id_provider');
        }
        
        
        $var->id_account = request('id_account');
        $var->id_user = request('id_user');
        $var->coin = request('coin');

        if((empty($var->id_client)) && (empty($var->id_provider))){
            return redirect('/anticipos/edit/'.$id.'')->withDanger('Debe Seleccionar un Cliente o un Proveedor!');
        }
        
        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
        $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

        if($var->coin != 'bolivares'){
            $var->amount = $valor_sin_formato_amount * $valor_sin_formato_rate; 
            $var->rate = $valor_sin_formato_rate;
        }else{
            $var->amount = $valor_sin_formato_amount;
            $var->rate = $valor_sin_formato_rate;
        }

        
        $var->reference = request('reference');
        
        if(request('status') != null){
            $var->status = request('status');
        }
    
        //Actualiza los movimientos contables del anticipo
        DB::connection(Auth::user()->database_name)->table('detail_vouchers as d')
                        ->join('header_vouchers as h', 'h.id', '=', 'd.id_header_voucher')
                        ->where('h.id_anticipo',$var->id)
                        ->where('d.haber',0)
                        ->update([ 'd.debe' => $var->amount, 'd.tasa' => $var->rate,'d.id_account' => $var->id_account,'h.date' => $var->date]);
        
        DB::connection(Auth::user()->database_name)->table('detail_vouchers as d')
                        ->join('header_vouchers as h', 'h.id', '=', 'd.id_header_voucher')
                        ->where('h.id_anticipo',$var->id)
                        ->where('d.debe',0)
                        ->update([ 'd.haber' => $var->amount , 'd.tasa' => $var->rate]);
        //------------------
        
       
        $var->save();

        /*$historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($var,"quotation_product","Actualizó el Anticipo: ".$var->inventories['code']."/ 
        Monto Viejo: ".number_format($amount_old, 2, ',', '.')." Tasa: ".$rate_old."/ Monto Nuevo: ".number_format($var->amount, 2, ',', '.')." Tasa: ".$var->rate);
        */
        if(isset($var->id_client)){
            return redirect('/anticipos')->withSuccess('Actualizacion Exitosa!');
        }else{
            return redirect('/anticipos/indexprovider')->withSuccess('Actualizacion Exitosa!');
        }
        
    }
    public function delete_anticipo(Request $request)
    {
        $anticipo = Anticipo::on(Auth::user()->database_name)->find(request('id_anticipo_modal')); 

        $historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($anticipo,"Se eliminó el Anticipo "."monto: ".$anticipo->amount." tasa: ".$anticipo->rate);

        if(isset($anticipo)){
           
            $this->disableMovementsAnticipo($anticipo);

            $anticipo->delete();

            return redirect('/anticipos')->withSuccess('Eliminacion exitosa!!');
        }else{
            return redirect('/anticipos')->withDanger('No se pudo encontrar el anticipo!!');
        }
        
    }
    public function delete_anticipo_provider(Request $request)
    {
        $anticipo = Anticipo::on(Auth::user()->database_name)->find(request('id_anticipo_modal'));
        
        $historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($anticipo,"Se eliminó el Anticipo "."monto: ".$anticipo->amount." tasa: ".$anticipo->rate);

        if(isset($anticipo)){
           
            $this->disableMovementsAnticipo($anticipo);

            $anticipo->delete();

            return redirect('/anticipos/indexprovider')->withSuccess('Eliminacion exitosa!!');
        }else{
            return redirect('/anticipos/indexprovider')->withDanger('No se pudo encontrar el anticipo!!');
        }
        
    }

    public function delete_anticipo_with_id($id_anticipo)
    {
        $anticipo = Anticipo::on(Auth::user()->database_name)->find($id_anticipo); 

        $historial_anticipo = new HistorialAnticipoController();

        $historial_anticipo->registerAction($anticipo,"Se eliminó el Anticipo "."monto: ".$anticipo->amount." tasa: ".$anticipo->rate);

        if(isset($anticipo)){
           
            $this->disableMovementsAnticipo($anticipo);

            $anticipo->delete();

            return redirect('/anticipos')->withSuccess('Eliminacion exitosa!!');
        }else{
            return redirect('/anticipos')->withDanger('No se pudo encontrar el anticipo!!');
        }
        
    }

    public function disableMovementsAnticipo($anticipo){

        DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id_anticipo','=',$anticipo->id)
            ->delete();

        DB::connection(Auth::user()->database_name)->table('header_vouchers')
            ->where('header_vouchers.id_anticipo','=',$anticipo->id)
            ->delete();
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

    public function changestatus(Request $request, $id_anticipo, $verify){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                if($verify == 'true'){
                    $anticipo = Anticipo::on(Auth::user()->database_name)->where('id',$id_anticipo)->update([ 'status' => 1 ]);
                    
                }else{
                    $anticipo = Anticipo::on(Auth::user()->database_name)->where('id',$id_anticipo)->update([ 'status' => 'M' ]);
                   
                }
                return response()->json($anticipo,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }
}

