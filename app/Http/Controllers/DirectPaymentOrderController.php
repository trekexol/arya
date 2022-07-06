<?php

namespace App\Http\Controllers;

use App\Account;
use App\BankMovement;
use App\BankVoucher;
use App\Branch;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\PaymentOrder;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DirectPaymentOrderController extends Controller
{
    public function createretirement()
   {
        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                        ->where('code_two', 1)
                                        ->where('code_three', 1)
                                        ->whereIn('code_four', [1,2])
                                        ->where('code_five','<>',0)
                                        ->orderBY('description','asc')->pluck('description','id')->toArray();

         $accounts_inventory = null;
        
                                        /* $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',2)->get();


        $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',1)
                        ->where('code_two', 1)
                        ->where('code_three', 3)
                        ->where('code_four',1)
                        ->where('code_five', '<>',0)
                        ->get();*/
        
        if(isset($accounts)){   

            $contrapartidas     = Account::on(Auth::user()->database_name)
                                            ->where('code_one', '<>',0)
                                            ->where('code_one', '<>',4)
                                            ->where('code_two', '<>',0)
                                            ->where('code_three', '<>',0)
                                            ->where('code_four', '<>',0)
                                            ->where('code_five', '=',0)
                                        ->orderBY('description','asc')->pluck('description','id')->toArray();
           
           
                                        $date = Carbon::now();
            $datenow = $date->format('Y-m-d');  

            $branches = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();

            $coin = 'bolivares';

            /*Revisa si la tasa de la empresa es automatica o fija*/
            $company = Company::on(Auth::user()->database_name)->find(1);
            $global = new GlobalController();
            
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }

           return view('admin.directpaymentorder.createretirement',compact('accounts_inventory','accounts','datenow','contrapartidas','branches','bcv','coin'));

        }else{
            return redirect('/directpaymentorders')->withDanger('No hay Cuentas!');
       }
   }


    public function store(Request $request)
    {

       // dd($request);
       
        $data = request()->validate([
            
        
            'account'        =>'required',
            'Account_counterpart'  =>'required',

            'beneficiario'      =>'required',
            'Subbeneficiario'      =>'required',

            'user_id'           =>'required',
            'amount'            =>'required',
            
            'date'              =>'required',
        
        
        ]);
        //dd($request);
        $account = request('account');
        $contrapartida = request('Account_counterpart');
        $coin = request('coin');

        if($account != $contrapartida){

            $amount = str_replace(',', '.', str_replace('.', '', request('amount')));
            $rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            if($coin != 'bolivares'){
                $amount = $amount * $rate;
            }

            if($rate == 0){
                return redirect('/directpaymentorders')->withDanger('La tasa no puede ser cero!');
            }

            $total_amount = $this->returnTotalAmount($request);  


            /*$check_amount = $this->check_amount($account);
            se desabilita esta validacion por motivos que el senor nestor queria ingresar datos y que queden en negativo
            if($check_amount->saldo_actual >= $amount){*/

                $payment_order = new PaymentOrder();
                $payment_order->setConnection(Auth::user()->database_name);

                if(request('beneficiario') == 1){
                    $payment_order->id_client = request('Subbeneficiario');
                    
                }else{
                    $payment_order->id_provider = request('Subbeneficiario');
                }
                $payment_order->id_user = request('user_id');
                if(request('branch') != 'ninguno'){
                    $payment_order->id_branch = request('branch');
                }
                $payment_order->date = request('date');
                $payment_order->reference = request('reference');
                $payment_order->description = request('description');
                $payment_order->amount = $total_amount;
                $payment_order->rate = $rate;
                $payment_order->coin = $coin;
                $payment_order->status = 1;

                $payment_order->save();

                $header = new HeaderVoucher();
                $header->setConnection(Auth::user()->database_name);

                $header->description = "Orden de Pago ". request('description');
                $header->date = request('date');
                $header->reference = request('reference');
                $header->id_payment_order = $payment_order->id;
                $header->status =  1;
            
                $header->save();

                $movement_counterpart = new DetailVoucher();
                $movement_counterpart->setConnection(Auth::user()->database_name);

                $movement_counterpart->id_header_voucher = $header->id;
                $movement_counterpart->id_account = $contrapartida;
                $movement_counterpart->user_id = request('user_id');
                $movement_counterpart->debe = $amount;
                $movement_counterpart->haber = 0;
                $movement_counterpart->tasa = $rate;
                $movement_counterpart->status = "C";

                $movement_counterpart->save();

              

                if($request->amount_of_payments >= 2){
                    if($this->storeMore($request,$header) != 'ok'){
                        return redirect('/bankmovements/orderpaymentlist')->withDanger('No se puede hacer un movimiento a la misma cuenta!');
                    }
                }

                $movement = new DetailVoucher();
                $movement->setConnection(Auth::user()->database_name);

                $movement->id_header_voucher = $header->id;
                $movement->id_account = $account;
                $movement->user_id = request('user_id');
                $movement->debe = 0;
                $movement->haber = $total_amount;
                $movement->tasa = $rate;
                $movement->status = "C";
            
                $movement->save();
                
                $account = Account::on(Auth::user()->database_name)->findOrFail($account);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }

                $account = Account::on(Auth::user()->database_name)->findOrFail($contrapartida);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }

                $account = Account::on(Auth::user()->database_name)->findOrFail($movement->id_account);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }
            
                
                return redirect('/bankmovements/orderpaymentlist')->withSuccess('Registro Exitoso!');

           /* }else{
                return redirect('/directpaymentorders'.request('id_account').'')->withDanger('El saldo de la Cuenta '.$check_amount->description.' es menor al monto del retiro!');
            }*/

        }else{
            return redirect('/bankmovements/orderpaymentlist')->withDanger('No se puede hacer un movimiento a la misma cuenta!');
        }
    }

    public function returnTotalAmount(Request $request){
        
        $amount = str_replace(',', '.', str_replace('.', '', request('amount')));

        $total = $amount;

        if($request->amount_of_payments >= 2){

            $amount2 = str_replace(',', '.', str_replace('.', '', request('amount2')));
          
            $total += $amount2;
        }
        if($request->amount_of_payments >= 3){
            $amount3 = str_replace(',', '.', str_replace('.', '', request('amount3')));
          
            $total += $amount3;
        }
        if($request->amount_of_payments >= 4){
            $amount4 = str_replace(',', '.', str_replace('.', '', request('amount4')));
          
            $total += $amount4;
        }
        if($request->amount_of_payments >= 5){
            $amount5 = str_replace(',', '.', str_replace('.', '', request('amount5')));
          
            $total += $amount5;
        }
        if($request->amount_of_payments >= 6){
            $amount6 = str_replace(',', '.', str_replace('.', '', request('amount6')));
          
            $total += $amount6;
        }
        if($request->amount_of_payments >= 7){
            $amount7 = str_replace(',', '.', str_replace('.', '', request('amount7')));
          
            $total += $amount7;
        }

        return $total;
    }


    public function storeMore(Request $request,$header)
    {
        if($request->amount_of_payments >= 2){

        $account = request('account');
        $contrapartida2 = request('Account_counterpart2');
        $coin = request('coin');

        if($account != $contrapartida2){

            $amount2 = str_replace(',', '.', str_replace('.', '', request('amount2')));
            $rate2 = str_replace(',', '.', str_replace('.', '', request('rate2')));

            if($coin != 'bolivares'){
                $amount2 = $amount2 * $rate2;
            }

            if($rate2 == 0){
                return 'La tasa no puede ser cero!';
            }


                $movement_counterpart = new DetailVoucher();
                $movement_counterpart->setConnection(Auth::user()->database_name);

                $movement_counterpart->id_header_voucher = $header->id;
                $movement_counterpart->id_account = $contrapartida2;
                $movement_counterpart->user_id = request('user_id');
                $movement_counterpart->debe = $amount2;
                $movement_counterpart->haber = 0;
                $movement_counterpart->tasa = $rate2;
                $movement_counterpart->status = "C";

                $movement_counterpart->save();

                $account = Account::on(Auth::user()->database_name)->findOrFail($contrapartida2);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }


        }else{
            return "No se puede hacer un movimiento a la misma cuenta!";
        }
        
        }
        if($request->amount_of_payments >= 3){

            $account = request('account');
            $contrapartida3 = request('Account_counterpart3');
            $coin = request('coin');
    
            if($account != $contrapartida3){
    
                $amount3 = str_replace(',', '.', str_replace('.', '', request('amount3')));
                $rate3 = str_replace(',', '.', str_replace('.', '', request('rate3')));
    
                if($coin != 'bolivares'){
                    $amount3 = $amount3 * $rate3;
                }
    
                if($rate3 == 0){
                    return 'La tasa no puede ser cero!';
                }
    
                /*$check_amount3 = $this->check_amount3($account3);
                se desabilita esta validacion por motivos que el senor nestor queria ingresar datos y que queden en negativo
                if($check_amount3->saldo_actual >= $amount3){*/
    
                    $movement_counterpart = new DetailVoucher();
                    $movement_counterpart->setConnection(Auth::user()->database_name);
    
                    $movement_counterpart->id_header_voucher = $header->id;
                    $movement_counterpart->id_account = $contrapartida3;
                    $movement_counterpart->user_id = request('user_id');
                    $movement_counterpart->debe = $amount3;
                    $movement_counterpart->haber = 0;
                    $movement_counterpart->tasa = $rate3;
                    $movement_counterpart->status = "C";
    
                    $movement_counterpart->save();
    
                    $account = Account::on(Auth::user()->database_name)->findOrFail($contrapartida3);
    
                    if($account->status != "M"){
                        $account->status = "M";
                        $account->save();
                    }
    
    
                  
    
            }else{
                return "No se puede hacer un movimiento a la misma cuenta!";
            }
            
            }
            if($request->amount_of_payments >= 4){

                $account = request('account');
                $contrapartida4 = request('Account_counterpart4');
                $coin = request('coin');
        
                if($account != $contrapartida4){
        
                    $amount4 = str_replace(',', '.', str_replace('.', '', request('amount4')));
                    $rate4 = str_replace(',', '.', str_replace('.', '', request('rate4')));
        
                    if($coin != 'bolivares'){
                        $amount4 = $amount4 * $rate4;
                    }
        
                    if($rate4 == 0){
                        return 'La tasa no puede ser cero!';
                    }
        
                
        
                        $movement_counterpart = new DetailVoucher();
                        $movement_counterpart->setConnection(Auth::user()->database_name);
        
                        $movement_counterpart->id_header_voucher = $header->id;
                        $movement_counterpart->id_account = $contrapartida4;
                        $movement_counterpart->user_id = request('user_id');
                        $movement_counterpart->debe = $amount4;
                        $movement_counterpart->haber = 0;
                        $movement_counterpart->tasa = $rate4;
                        $movement_counterpart->status = "C";
        
                        $movement_counterpart->save();
        
                        $account = Account::on(Auth::user()->database_name)->findOrFail($contrapartida4);
        
                        if($account->status != "M"){
                            $account->status = "M";
                            $account->save();
                        }
        
                    
        
                }else{
                    return "No se puede hacer un movimiento a la misma cuenta!";
                }
                
                }
        return 'ok';
    }

    public function check_amount($id_account)
    {       
        
        $var = Account::on(Auth::user()->database_name)->find($id_account);

                      
       if(isset($var)) {
               
               if($var->code_one != 0){
                   
                   if($var->code_two != 0){
   
   
                       if($var->code_three != 0){
   
   
                           if($var->code_four != 0){
                             
                            /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->where('accounts.code_two', $var->code_two)
                                                       ->where('accounts.code_three', $var->code_three)
                                                       ->where('accounts.code_four', $var->code_four)
                                                       ->where('accounts.code_five', $var->code_five)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('debe');
   
                            $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->where('accounts.code_two', $var->code_two)
                                                       ->where('accounts.code_three', $var->code_three)
                                                       ->where('accounts.code_four', $var->code_four)
                                                       ->where('accounts.code_five', $var->code_five)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('haber');   
                            /*---------------------------------------------------*/

                          
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;
  
                                $var->saldo_actual = ($var->balance_previus + $var->debe) - $var->haber;
                                                          
   
                           }else{
                              
                             
                         
                        /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */ 
                           $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->where('accounts.code_two', $var->code_two)
                                                       ->where('accounts.code_three', $var->code_three)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('debe');
   
                           $total_haber =  DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->where('accounts.code_two', $var->code_two)
                                                       ->where('accounts.code_three', $var->code_three)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('haber');      
                        /*---------------------------------------------------*/                               
  
                        

                        
                            $var->debe = $total_debe;
                        
                            $var->haber = $total_haber;       
                                          
                            $var->saldo_actual = ($var->balance_previus + $var->debe) - $var->haber;
                           
                   }
                       }else{
                           
                      
                        /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                   
                           $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                           ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                           ->where('accounts.code_one', $var->code_one)
                                                           ->where('accounts.code_two', $var->code_two)
                                                           ->whereIn('detail_vouchers.status', ['F','C'])
                                                           ->sum('debe');
   
                         
                           $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                           ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                           ->where('accounts.code_one', $var->code_one)
                                                           ->where('accounts.code_two', $var->code_two)
                                                           ->whereIn('detail_vouchers.status', ['F','C'])
                                                           ->sum('haber');
                        /*---------------------------------------------------*/
                                 
                       


                      
                            $var->debe = $total_debe;
                       
                            $var->haber = $total_haber;
                    
                            $var->saldo_actual = ($var->balance_previus + $var->debe) - $var->haber;
                       }
                   }else{
                       //Cuentas NIVEL 2 EJEMPLO 1.0.0.0
                     /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */
                            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('debe');
   
                        
                          
                           $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                       ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                       ->where('accounts.code_one', $var->code_one)
                                                       ->whereIn('detail_vouchers.status', ['F','C'])
                                                       ->sum('haber');
                    /*---------------------------------------------------*/

                        $var->debe = $total_debe;
                      
                        $var->haber = $total_haber;           
                      
                        $var->saldo_actual = ($var->balance_previus + $var->debe) - $var->haber;
   
                   }
               }else{
                   return redirect('/accounts')->withDanger('El codigo uno es igual a cero!');
               }
           } 
       
      
       
        return $var;
    }

    public function listbeneficiary(Request $request, $id_var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                if($id_var == 1){
                    $clients = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
                    return response()->json($clients,200);
                }else{
                    $providers = Provider::on(Auth::user()->database_name)->orderBy('razon_social','asc')->get();
                    return response()->json($providers,200);
                }
               
                
                
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }
    
   

    public function listcontrapartida(Request $request, $id_var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{

                $account = Account::on(Auth::user()->database_name)->find($id_var);
                $subcontrapartidas = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$account->code_one)
                                                                    ->where('code_two',$account->code_two)
                                                                    ->where('code_three',$account->code_three)
                                                                    ->where('code_four',$account->code_four)
                                                                    ->where('code_five','<>',0)
                                                                    ->orderBy('description','asc')->get();
                    
                return response()->json($subcontrapartidas,200);
               
                
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }

}
