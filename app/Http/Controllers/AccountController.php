<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountHistorial;
use App\BankMovement;
use App\Company;
use App\DetailVoucher;
use App\Quotation;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
   }

   public function index($coin = null,$level = null)
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       
        if($coin == null){
            $coin = 'bolivares';
        }
        
        $total_saldo_anterior = 0;
        $total_debe = 0;
        $total_haber = 0;

        $total_saldo_anterior1 = 0;
        $total_saldo_anterior2 = 0;
        $total_saldo_anterior3 = 0;

        $accounts = $this->calculation($coin);

        foreach($accounts as $account){

            if($account->level == 5){
                if(($account->code_one == 3) && ($account->code_two == 2) && ($account->code_three == 1) && ($account->code_four == 1) && ($account->code_five == 1) ){     
                    if($coin == 'bolivares'){
                        $total_debe_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
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
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);
                        $total_haber_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
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
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);

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
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);

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
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);

                                        $account->balance =  $account->balance_previus;

                       
                        }else{
                            $total_debe_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
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
                            , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);
                            
                            $total_haber_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
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
                            , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C']);

                           
                            
                            
                        }
                        $total_debe_account = $total_debe_account[0]->debe;
                        $total_haber_account = $total_haber_account[0]->haber;
                        
                        $total_debe += $total_debe_account;
                        $total_haber += $total_haber_account;
                }else{
                    $total_debe += $account->debe;
                    $total_haber += $account->haber;
                }
            }
            
           
            if(($account->code_one == 1)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
            
                $total_saldo_anterior1 += $account->balance;
            }
            if(($account->code_one == 2)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                $total_saldo_anterior2 += $account->balance;
            }
            if(($account->code_one == 3)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                $total_saldo_anterior3 += $account->balance;
            }
            
        }

        
        $total_saldo_anterior = $total_saldo_anterior1 + $total_saldo_anterior2 + $total_saldo_anterior3;

       
        
       return view('admin.accounts.index',compact('total_debe','total_haber','total_saldo_anterior','accounts','coin','level'));
   }

   public function index_previous_exercise()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       if($users_role == '1'){
        
        $account_historial = AccountHistorial::on(Auth::user()->database_name)->select('date_begin','date_end')->groupBy('date_begin','date_end')->get();

        }else if($users_role == '2'){
           return view('admin.index');
       }

       return view('admin.accounts.index_previous_exercises',compact('account_historial'));
   }


    public function movements($id_account,$coin = null)
    {
        

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        if($users_role == '1'){
             
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_account',$id_account)->orderBy('id','desc')->get();
            $account = Account::on(Auth::user()->database_name)->find($id_account);
            dd($detailvouchers);
            if(empty($coin)){
                $coin = "bolivares";
            }

         }else if($users_role == '2'){
            return view('admin.index');
        }
        
        return view('admin.accounts.index_account_movement',compact('detailvouchers','account','coin'));
    }

    public function header_movements($id,$type,$id_account)
    {
        

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        $detailvouchers = null;
        $var = null;
        $type = null;

        if($users_role == '1'){
             
            if($type == 'header_voucher'){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_header_voucher',$id)->orderBy('id','desc')->get();
                
                $var = null;
                $type = $detailvouchers[0]['headers']->description;
            }
            else if($type == 'invoice'){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_invoice',$id)->get();
                $var = Quotation::on(Auth::user()->database_name)->find($id);
                $type = 'Factura';
            }else{
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_header_voucher',$id)->orderBy('id','desc')->get();
                
                $var = null;
                $type = $detailvouchers[0]['headers']->description;
            }
            
         }else if($users_role == '2'){
            return view('admin.index');
        }
        
        return view('admin.accounts.index_header_movement',compact('detailvouchers','type','var','id_account'));
    }
 
   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {

        $date = Carbon::now();
        $datenow = $date->format('Y');

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $this->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

        return view('admin.accounts.create',compact('datenow','rate'));
   }

    public function createlevel($id_account)
    {
    
            $var = DB::connection(Auth::user()->database_name)->table('accounts')->where('id', $id_account)->first();
                                    
            if(isset($var)){          
                            
                    if($var->code_one != 0){
                        
                        if($var->code_two != 0){


                            if($var->code_three != 0){


                                if($var->code_four != 0){

                                                $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                            ->where('code_two', $var->code_two)
                                                                            ->where('code_three', $var->code_three)
                                                                            ->where('code_four', $var->code_four)
                                                                            ->max('code_five');
                                                $var->code_five = $level + 1;
                                                $var->level = 5;
    
                                }else{
                                
                                    $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                ->where('code_two', $var->code_two)
                                                                ->where('code_three', $var->code_three)
                                                        ->max('code_four');
                                    $var->code_four = $level + 1;
                                    $var->level = 4;
                                
                                }
                            }else{
                                
                                $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                ->where('code_two', $var->code_two)
                                                        ->max('code_three');
                                $var->code_three = $level + 1;
                                $var->level = 3;
                            
                            }
                        }else{
                            //Cuentas NIVEL 2
                        //level trae el valor de code_two mas alto
                            $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                        ->max('code_two');
                            
                            //luego que tenemos el valor del codigo two mas alto, le sumamos uno para crear el proximo
                            $var->code_two = $level + 1;
                            $var->level = 2;
                            
                        
                        }
                    }else{
                        return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                    }
                

                $date = Carbon::now();
                $datenow = $date->format('Y');

                $company = Company::on(Auth::user()->database_name)->find(1);
                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $rate = $this->search_bcv();
                }else{
                    //si la tasa es fija
                    $rate = $company->rate;
                }
                
            
                return view('admin.accounts.createlevel',compact('var','datenow','rate'));

            }else{
                return redirect('/accounts/menu')->withDanger('No existe la Cuenta!');
        }
    }


 
   

   public function store(Request $request)
    {

        $exist = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', request('code_one'))
                                ->where('code_two', request('code_two'))
                                ->where('code_three', request('code_three'))
                                ->where('code_four', request('code_four'))
                                ->where('period', request('period'))->first();

        if(!isset($exist)){

    
            $data = request()->validate([
                
                

                'period'            =>'required',
                'description'       =>'required',
                'type'              =>'required',
                'level'             =>'required',
                'balance_previus'   =>'required',
               
            ]);

            $var = new Account();
            $var->setConnection(Auth::user()->database_name);
            $var->code_one = request('code_one');
            $var->code_two = request('code_two');
            $var->code_three = request('code_three');
            $var->code_four = request('code_four');

            $var->period = request('period');
            $var->description = request('description');
            $var->type = request('type');
            $var->level = request('level');
            
            $valor_sin_formato = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            $var->balance_previus =$valor_sin_formato;
            $var->rate = $valor_sin_formato_rate;

            if(request('coin') != 'BsS'){
                $var->coin = request('coin');
                
            }else{
                $var->coin = null;
            }
           

            $var->status =  "1";
        
            $var->save();

            return redirect('/accounts/menu')->withSuccess('Registro Exitoso!');

        }else{
            return redirect('/accounts/menu')->withDanger('La Cuenta ya existe!');
       }
    }


    public function storeNewLevel(Request $request)
    {

       
        $exist = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', request('code_one'))
                                ->where('code_two', request('code_two'))
                                ->where('code_three', request('code_three'))
                                ->where('code_four', request('code_four'))
                                ->where('code_five', request('code_five'))
                                ->where('period', request('period'))->first();

        if(!isset($exist)){

            //dd($request);
            $data = request()->validate([
                
                

                'period'            =>'required',
                'description'       =>'required',
                'type'              =>'required',
                'level'             =>'required',
                
               
            ]);

            $var = new Account();
            $var->setConnection(Auth::user()->database_name);

            $var->code_one = request('code_one');
            $var->code_two = request('code_two');
            $var->code_three = request('code_three');
            $var->code_four = request('code_four');
            $var->code_five = request('code_five');

            $var->period = request('period');
            $var->description = request('description');
            $var->type = request('type');
            $var->level = request('level');

            $valor_sin_formato = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            $var->balance_previus =$valor_sin_formato;
            $var->rate = $valor_sin_formato_rate;

            if(request('coin') != 'BsS'){
                $var->coin = request('coin');
                
            }else{
                $var->coin = null;
            }

            $var->status =  "1";
        
            $var->save();
            

            return redirect('/accounts/menu')->withSuccess('Registro Exitoso!');

        }else{
            return redirect('/accounts/menu')->withDanger('La Cuenta ya existe!');
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
        $var = Account::on(Auth::user()->database_name)->find($id);

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $this->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }
       
        return view('admin.accounts.edit',compact('var','rate'));
  
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
            'description'       =>'required',
            'balance_previus'   =>'required',
        
        ]);

        $var = Account::on(Auth::user()->database_name)->findOrFail($id);

        $sin_formato_balance_previus = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
        $sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));
            
        $var->description = request('description');
       

        if(request('coin') != 'BsS'){
            //Dolares
            $var->coin = request('coin');
            //Guardo el monto de los dolares multiplicados por la tasa para que sea en bolivares
            $var->balance_previus = $sin_formato_balance_previus * $sin_formato_rate;
        }else{
            //Bolivares
            $var->coin = null;
            $var->balance_previus = $sin_formato_balance_previus;
        }
        $var->rate = $sin_formato_rate;

        
    
        $var->save();

        return redirect('/accounts/menu')->withSuccess('Actualizacion Exitosa!');
    }


    public function year_end()
   {
      
        $coin = 'bolivares';

        $accounts = $this->calculation($coin);

        $date = Carbon::now();
        $datenow = $date->format('Y');
        $datenow2 = $date->format('Y-m-d');
        $last_detail_activate = DetailVoucher::on(Auth::user()->database_name)->where('status', 'C')->orderBy('id', 'desc')->first();
        $last_detail_desactivate = DetailVoucher::on(Auth::user()->database_name)->where('status', 'F')->orderBy('id', 'desc')->first();

        
        //Verifica que existan movimientos con los cuales hacer el cierre
        if(isset($last_detail_activate)){

            //Verifica que no se haga el cierre 2 veces un mismo dia, empty es para cuando no existen cierres anteriores
            if( empty($last_detail_desactivate->date_end) || ((isset($last_detail_desactivate->date_end)) && $last_detail_desactivate->date_end != $datenow2))
            {
                
                foreach($accounts as $account){ 
                    
                    
                    $var = new AccountHistorial();
                    $var->setConnection(Auth::user()->database_name);
                    
                    $var_account = Account::on(Auth::user()->database_name)->findOrFail($account->id);

                    $var->id_account =  $account->id;
                    $var->period =  $datenow;
                    $var->date_begin = $last_detail_activate->created_at->format('Y-m-d');
                    $var->date_end = $datenow2;

                    $var->balance_previous = $account->balance_previus;

                        $var->coin =  $account->coin;
                        $var->rate =  $account->rate;
                        $var->balance_current = $account->balance_previus + $account->debe - $account->haber;
                        
                        $var->debe =  $account->debe ?? 0;
                        $var->haber =  $account->haber ?? 0;
                        $var->debe_coin =  $account->dolar_debe ?? 0;
                        $var->haber_coin =  $account->dolar_haber ?? 0;
                
                    $var->status =  "F";
                
                    $var->save();
                    if($account->level == 5){
                        $var_account->balance_previus = $var->balance_current;
                        
                        $var_account->save();
                    }
                    

                }
            
                DetailVoucher::on(Auth::user()->database_name)->where('status', 'C')
                        ->update(['status' => 'F' , 'date_end' => $datenow2]);

                
                return redirect('/accounts/menu')->withSuccess('Se realizo el Cierre Exitosamente!');

            }else{
                return redirect('/accounts/menu')->withDanger('No se puede realizar el cierre en un mismo dia!');
            }

        }else{
                return redirect('/accounts/menu')->withDanger('No hay movimientos para realizar un cierre!');
        }
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
                                //Calculo de superavit
                                if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                ($var->code_four == 1) && ($var->code_five == 1)){
                                    
                                    $var = $this->calculation_superavit($var,4,$coin);
                                    
                                }else
                                {
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */    
                                    if($coin == 'bolivares')
                                    {
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

                                        if(($var->balance_previus != 0) && ($var->rate !=0)){
                                            $var->balance_previus =  $var->balance_previus / $var->rate;
                                        }
                                        
                                        
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

                                    
                                }

                            }else{
                        
                                //Calculo de superavit
                                if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                ($var->code_four == 1) ){
                                    $var = $this->calculation_superavit($var,4,$coin);
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
                            }
                        }else{    
                            //Calculo de superavit
                            if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1)){
                                $var = $this->calculation_superavit($var,4,$coin);
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
                        }           
                    }else{
                        //Calculo de superavit
                        if(($var->code_one == 3) && ($var->code_two == 2)){
                            $var = $this->calculation_superavit($var,4,$coin);
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
                    }
                }else{
                    //Calcular patrimonio con las cuentas mayores o iguales a 3.0.0.0.0
                    if($var->code_one == 3){
                        $var = $this->calculation_capital($var,$coin);
                       
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

public function calculation_capital($var,$coin)
{
    if($coin == 'bolivares'){
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$var->code_one,'C']);
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
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
            WHERE a.code_one >= ? AND
            d.status = ?
            '
            , [$var->code_one,'C']);
            
            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
            FROM accounts a
            INNER JOIN detail_vouchers d 
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
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

        return $var;
}

public function calculation_superavit($var,$code,$coin)
{
    //Primero el suma todos los movimientos del 4 para arriba, y luego suma los movimientos que se le hayan hecho al superavit
 if($coin == 'bolivares'){
     $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                     FROM accounts a
                     INNER JOIN detail_vouchers d 
                         ON d.id_account = a.id
                     WHERE a.code_one >= ? AND
                     d.status = ?
                     '
                     , [$code,'C']);
     $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                     FROM accounts a
                     INNER JOIN detail_vouchers d 
                         ON d.id_account = a.id
                     WHERE a.code_one >= ? AND
                     d.status = ?
                     '
                     , [$code,'C']);
    $total_debe2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                     FROM accounts a
                     INNER JOIN detail_vouchers d 
                         ON d.id_account = a.id
                     WHERE a.code_one >= 3 AND a.code_two >= 2 AND a.code_three >= 1 AND a.code_four >= 1 AND a.code_five >= 1 AND
                     d.status = ?
                     '
                     , [$code,'C']);
     $total_haber2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                     FROM accounts a
                     INNER JOIN detail_vouchers d 
                         ON d.id_account = a.id
                     WHERE a.code_one >= 3 AND a.code_two >= 2 AND a.code_three >= 1 AND a.code_four >= 1 AND a.code_five >= 1 AND
                     d.status = ?
                     '
                     , [$code,'C']);
     
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
         WHERE a.code_one >= ? AND
         d.status = ?
         '
         , [$code,'C']);
         
         $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
         FROM accounts a
         INNER JOIN detail_vouchers d 
             ON d.id_account = a.id
         WHERE a.code_one >= ? AND
         d.status = ?
         '
         , [$code,'C']);

         $total_debe2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
         FROM accounts a
         INNER JOIN detail_vouchers d 
             ON d.id_account = a.id
         WHERE a.code_one >= 3 AND a.code_two >= 2 AND a.code_three >= 1 AND a.code_four >= 1 AND a.code_five >= 1 AND
         d.status = ?
         '
         , [$code,'C']);
         
         $total_haber2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
         FROM accounts a
         INNER JOIN detail_vouchers d 
             ON d.id_account = a.id
         WHERE a.code_one >= 3 AND a.code_two >= 2 AND a.code_three >= 1 AND a.code_four >= 1 AND a.code_five >= 1 AND
         d.status = ?
         '
         , [$code,'C']);

         $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        WHERE a.code_one = ? AND
        a.code_two = ?  
        '
        , [$var->code_one,$var->code_two]);
     }
     
     $total_debe = $total_debe[0]->debe + $total_debe2[0]->debe;
     $total_haber = $total_haber[0]->haber + $total_haber2[0]->haber;
     $var->debe = $total_debe;
     $var->haber = $total_haber;

     $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;

     $var->balance = $var->balance_previus;

     return $var;

}

public function calculation_superavit_level4($var,$code,$coin)
{
    if($coin == 'bolivares'){
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                        FROM accounts a
                                        WHERE a.code_one = ? AND
                                        a.code_two = ?  AND
                                        a.code_three = ? 
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);
        
    }else{
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
        FROM accounts a
        INNER JOIN detail_vouchers d 
            ON d.id_account = a.id
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);
        
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
        FROM accounts a
        INNER JOIN detail_vouchers d 
            ON d.id_account = a.id
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        WHERE a.code_one = ? AND
        a.code_two = ?  AND
        a.code_three = ? AND
        a.code_four = ?
        '
        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);


    }
    
    $total_debe = $total_debe[0]->debe;
    $total_haber = $total_haber[0]->haber;
    $var->debe = $total_debe;
    $var->haber = $total_haber;

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

public function calculation_superavit_level3($var,$code,$coin)
{
    if($coin == 'bolivares'){
    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                    FROM accounts a
                    INNER JOIN detail_vouchers d 
                        ON d.id_account = a.id
                    WHERE a.code_one >= ? AND
                    d.status = ?
                    '
                    , [$code,'C']);
    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                    FROM accounts a
                    INNER JOIN detail_vouchers d 
                        ON d.id_account = a.id
                    WHERE a.code_one >= ? AND
                    d.status = ?
                    '
                    , [$code,'C']);

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
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);
        
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
        FROM accounts a
        INNER JOIN detail_vouchers d 
            ON d.id_account = a.id
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        WHERE a.code_one = ? AND
        a.code_two = ?  AND
        a.code_three = ? 
        '
        , [$var->code_one,$var->code_two,$var->code_three]);


    }
    
    $total_debe = $total_debe[0]->debe;
    $total_haber = $total_haber[0]->haber;
    $var->debe = $total_debe;
    $var->haber = $total_haber;

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

public function calculation_superavit_level2($var,$code,$coin)
{
    if($coin == 'bolivares'){
    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                    FROM accounts a
                    INNER JOIN detail_vouchers d 
                        ON d.id_account = a.id
                    WHERE a.code_one >= ? AND
                    d.status = ?
                    '
                    , [$code,'C']);
    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                    FROM accounts a
                    INNER JOIN detail_vouchers d 
                        ON d.id_account = a.id
                    WHERE a.code_one >= ? AND
                    d.status = ?
                    '
                    , [$code,'C']);

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
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);
        
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
        FROM accounts a
        INNER JOIN detail_vouchers d 
            ON d.id_account = a.id
        WHERE a.code_one >= ? AND
        d.status = ?
        '
        , [$code,'C']);

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

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

    public function search_bcv()
    {
        /*Buscar el indice bcv*/
        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        }else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

     
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