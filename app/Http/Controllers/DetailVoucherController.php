<?php

namespace App\Http\Controllers;

use App\Account;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\Calculations\CalculationController;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class DetailVoucherController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
   }

   public function index()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       if($users_role == '1'){
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        }

       return view('admin.detailvouchers.index');
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create($coin,$id_header = null,$id_account = null)
   {
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        $header_disponible = HeaderVoucher::on(Auth::user()->database_name)->orderBy('id','desc')->first();
        $header_number = 1;

        if(isset($header_disponible)){
            $header_number = $header_disponible->id + 1;
        }
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $this->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }


        if(($coin == 'bolivares') ){
            $coin = 'bolivares';
        }else{
            $coin = 'dolares';
        }
        $header = null;
        $detailvouchers = null;
        $account = null;
        $detailvouchers_last = null;
        $tasa_calculada = null;
        $saldo_total_bs = null;
        $saldo_total_dolares = null;
        
        if(isset($id_header)){
            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);
            if(isset($header) && $header->status != 'X'){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();
                //se usa el ultimo movimiento agregado de la cabecera para tomar cual fue la tasa que se uso
                $detailvouchers_last = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->orderBy('id','desc')->first();
                if(isset($id_account)){
                    $account = Account::on(Auth::user()->database_name)->find($id_account);

                    $calculationController = new CalculationController();

                    $account_bolivares = $calculationController->calculate_account_all($account,"bolivares");

                    $account_dolares = $calculationController->calculate_account_all($account,"dolares");

                    $saldo_total_bs = $account_bolivares->balance_previus + $account_bolivares->debe - $account_bolivares->haber;

                    $saldo_total_dolares = $account_dolares->balance_previus + $account_dolares->debe - $account_dolares->haber;

                    if($saldo_total_dolares != 0){
                        $tasa_calculada = ($saldo_total_bs / ($saldo_total_dolares ?? 1));
                    }else{
                        $tasa_calculada = 0;
                    }
                    
                   
               }
            }else{
               return redirect('/detailvouchers/register/bolivares')->withDanger('Este movimiento fue Deshabilitado!');
            }
        }
        

        return view('admin.detailvouchers.create',compact('saldo_total_bs','saldo_total_dolares','tasa_calculada','detailvouchers_last','account','datenow','header_number','coin','bcv','header','detailvouchers'));
   }

   public function createvalidation($coin,$id_header = null,$id_account = null)
   {
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        $header_disponible = HeaderVoucher::on(Auth::user()->database_name)->orderBy('id','desc')->first();
        $header_number = 1;

        if(isset($header_disponible)){
            $header_number = $header_disponible->id + 1;
        }
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $this->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }


        if(($coin == 'bolivares') ){
            $coin = 'bolivares';
        }else{
            //$bcv = null;
            $coin = 'dolares';
        }
        $header = null;
        $detailvouchers = null;
        $account = null;
        $detailvouchers_last = null;

        $details = HeaderVoucher::on(Auth::user()->database_name)
                                    ->join('detail_vouchers','detail_vouchers.id_header_voucher','header_vouchers.id')
                                    ->where('detail_vouchers.debe','<>','detail_vouchers.haber')
                                    ->where('detail_vouchers.id_header_voucher','<>',1)
                                    ->select('detail_vouchers.*')
                                    ->get();
        /*
        $old_header = 0;
        $sum_debe = 0;
        $sum_haber = 0;
        foreach($details as $detail){
            
            if(($old_header == 0) || ($old_header <> $detail->id_header_voucher)){
                if($sum_debe <> $sum_haber){
                    dd($old_header);
                    $id_header = $old_header;
                }
                $old_header = $detail->id_header_voucher;

                $sum_debe = 0;
                $sum_haber = 0;
                $sum_debe += $detail->debe;
                $sum_haber += $detail->haber;
            }else{
                $sum_debe += $detail->debe;
                $sum_haber += $detail->haber;
            }

        }

        */
        
        if(isset($id_header)){
            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();
            //se usa el ultimo movimiento agregado de la cabecera para tomar cual fue la tasa que se uso
            $detailvouchers_last = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->orderBy('id','desc')->first();
            if(isset($id_account)){
                $account = Account::on(Auth::user()->database_name)->find($id_account);
            }
        }
        

        return view('admin.detailvouchers.create',compact('detailvouchers_last','account','datenow','header_number','coin','bcv','header','detailvouchers'));
   }


   public function createselect($id_header)
   {
        $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header); 

        if(isset($header)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
    
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();
    
            return view('admin.detailvouchers.create',compact('header','datenow','detailvouchers'));
        }else{
            return redirect('/detailvouchers/register')->withDanger('No existe el Header!');
        }
        
   }

  


   public function selectaccount($coin,$id_header,$id_detail)
   {
       
       if($id_header != -1){

            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);
            $accounts = $this->calculation($coin);

            if($id_detail == 'detail'){
                $id_detail = null;
            }
            
            return view('admin.detailvouchers.selectaccount',compact('coin','accounts','header','id_detail'));
            
       }else{
        return redirect('/detailvouchers/register/'.$coin.'')->withDanger('Seleccione informacion de Cabecera!');
       }
        
   }
   
   public function selectheader()
   {
        $headervouchers = HeaderVoucher::on(Auth::user()->database_name)->where('status','LIKE','U')->get();
        

        return view('admin.detailvouchers.selectheadervouche',compact('headervouchers'));
   }


   public function contabilizar($coin,$id_header)
   {

        //  dd($id_header);
        $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header); 

        if(isset($header)){  

            $affected = DB::connection(Auth::user()->database_name)->table('detail_vouchers')->where('id_header_voucher', '=', $id_header)->update(array('status' => 'C'));

            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();

           
             /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
             foreach($detailvouchers as $var){
                 
                $account = Account::on(Auth::user()->database_name)->findOrFail($var->id_account);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }
             }
            
             /*----------------------------- */

            $company = Company::on(Auth::user()->database_name)->find(1);
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $this->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
                                
            return view('admin.detailvouchers.create',compact('bcv','coin','header','datenow','detailvouchers'));
                                        
            
        }else{
            return redirect('/detailvouchers/register/bolivares')->withDanger('No existe el Header!');
        }                              

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
                
                

                
                'id_account'     =>'required',
               
                'id_header_voucher'     =>'required',
                'debe'                  =>'required',
                'haber'                 =>'required',
                
               
            
            ]);
           
            $var = new DetailVoucher();
            $var->setConnection(Auth::user()->database_name);

            $coin = request('coin');
            
            $var->id_account = request('id_account');
            $var->id_header_voucher = request('id_header_voucher');
            $var->user_id = request('id_user');

            $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', request('debe')));
            $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', request('haber')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            if($coin == 'bolivares'){
                $var->debe = $valor_sin_formato_debe;
                $var->haber = $valor_sin_formato_haber;
                $var->tasa = $valor_sin_formato_rate;
               
            }else{
                $var->debe = $valor_sin_formato_debe * $valor_sin_formato_rate;
                $var->haber = $valor_sin_formato_haber * $valor_sin_formato_rate;
                $var->tasa = $valor_sin_formato_rate;
               
            }

            $var->status =  "N";
        
            $var->save();

            return redirect('/detailvouchers/register/'.$coin.'/'.$var->id_header_voucher.'')->withSuccess('Agregado el movimiento Correctamente, para procesarlo debe contabilizar!');
           
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
   public function edit($coin,$id,$id_account = null)
   {
        $var = DetailVoucher::on(Auth::user()->database_name)->find($id);

        if(isset($id_account)){
            $var->id_account = $id_account;
        }
        

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $this->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if($coin != 'bolivares'){
            $var->debe = $var->debe / $var->tasa;
            $var->haber = $var->haber / $var->tasa;
        }
       
        return view('admin.detailvouchers.edit',compact('var','bcv','coin','id_account'));
  
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id = null)
    {
        
        
        $data = request()->validate([
                    
            'type'      =>'required',
            'amount'    =>'required',
            'rate'    =>'required',
            'coin'    =>'required',
            'id_account'    =>'required',
        
        
        ]);
        
        if(isset($id)){
            $var = DetailVoucher::on(Auth::user()->database_name)->findOrFail($id);

            $coin = request('coin');
            $type = request('type');
            $id_account = request('id_account');
    
            if($id_account != -1){
                $var->id_account = $id_account;
            }
    
            $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));
    
            if($coin == 'bolivares'){
                if($type == 'debe'){
                    $var->debe = $valor_sin_formato_amount;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->haber = 0;
                }else{
                    $var->haber = $valor_sin_formato_amount;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->debe = 0;
                }
            }else{
                if($type == 'debe'){
                    $var->debe = $valor_sin_formato_amount * $valor_sin_formato_rate;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->haber = 0;
                }else{
                    $var->haber = $valor_sin_formato_amount * $valor_sin_formato_rate;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->debe = 0;
                }
            }
            $var->save();
    
            $affected = DB::connection(Auth::user()->database_name)->table('detail_vouchers')->where('id_header_voucher', '=', $var->id_header_voucher)->update(array('status' => 'N'));
    
            $this->check_exist_movement_in_account();
    
            return redirect('/detailvouchers/register/'.$coin.'/'.$var->id_header_voucher.'')->withSuccess('Actualizacion Exitosa!');
        }
        
    }

    public function check_exist_movement_in_account()
    {
        $account_with_movement = Account::on(Auth::user()->database_name)->where('status','M')->get();

        foreach($account_with_movement as $var){
           $exist_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_account',$var->id)->first();

           if(!isset($exist_detail)){

                $account = Account::on(Auth::user()->database_name)->findOrFail($var->id);
                $account->status = '1';
                $account->save();

           }
        }
    }
   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id = null)
   {
       if(isset($id)){
        $header = HeaderVoucher::on(Auth::user()->database_name)->findOrFail($id);

        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$header->id)
            ->update(['status' => 'X']);

        $header->status = "X";
        $header->save();

        return redirect('/detailvouchers/register/bolivares')->withSuccess('Se deshabilitó con éxito el movimiento!');
       
       }else{
        return redirect('/detailvouchers/register/bolivares')->withDanger('Debe buscar un movimiento primero !!');
       
       }
        

   }


   public function listheader(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                $respuesta = HeaderVoucher::on(Auth::user()->database_name)->select('id','description')->where('id',$var)->orderBy('description','asc')->get();
                return response()->json($respuesta,200);
            }catch(Throwable $th){
                return response()->json(false,500);
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
        }else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

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

}
