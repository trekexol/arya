<?php

namespace App\Http\Controllers;
use App;
use App\Account;
use App\BankMovement;
use App\BankVoucher;
use App\Branch;
use App\ChargeOrder;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DirectChargeOrderController extends Controller
{

    public function index()
    {
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        if($users_role == '1'){

             $detailvouchers = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                 ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                 ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                 ->where('header_vouchers.status','LIKE','1')
                                 ->where(function ($query) {
                                     $query->where('header_vouchers.description','LIKE','Orden de Cobro%');
                                 })
                                 
                                 ->select('detail_vouchers.*','header_vouchers.description as header_description', 
                                 'header_vouchers.reference as header_reference','header_vouchers.date as header_date',
                                 'accounts.description as account_description','accounts.code_one as account_code_one',
                                 'accounts.code_two as account_code_two','accounts.code_three as account_code_three',
                                 'accounts.code_four as account_code_four','accounts.code_five as account_code_five')
                                 ->orderBy('header_vouchers.id','desc')
                                 ->get();
 
             //dd($detailvouchers);
 
             $accounts     = Account::on(Auth::user()->database_name)->orderBy('description','asc')->get();
 
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d'); 
 
             return view('admin.directchargeorder.index',compact('detailvouchers','accounts','datenow'));
 
         }else{
             return redirect('/directchargeorders')->withDanger('No Tiene Acceso!');
        }
    }
 
   

    public function destroy($id){
        if(isset($id)){
            $header = HeaderVoucher::on(Auth::user()->database_name)->findOrFail($id);
    
            $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$header->id)
                ->update(['status' => 'X']);
    
            $header->status = "X";
            $header->save();
    
            return redirect('/directchargeorders')->withSuccess('Se deshabilitó con éxito el movimiento!');
           
           }else{
            return redirect('/directchargeorders')->withDanger('Debe buscar un movimiento primero !!');
           
           }
      }

      public function orderPaymentPdfDetail($id_header_voucher)
      {
          
          $pdf = App::make('dompdf.wrapper');
   
          $date = Carbon::now();
          $datenow = $date->format('Y-m-d');    
         
           $movements = DetailVoucher::on(Auth::user()->database_name)
               ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
               ->join('accounts','accounts.id','detail_vouchers.id_account')
               //->leftJoin('charge_orders','charge_orders.id','header_vouchers.id_charge_orders')
               //->leftJoin('clients','clients.id','payment_orders.id_client')
               //->leftJoin('providers','providers.id','payment_orders.id_provider')
               ->where('header_vouchers.id',$id_header_voucher)
               ->where('detail_vouchers.status','C')
               ->select('header_vouchers.description', 'header_vouchers.id as header_id',
               'detail_vouchers.debe', 'detail_vouchers.haber', 'detail_vouchers.haber', 'detail_vouchers.tasa',
               'accounts.code_one','accounts.code_two','accounts.code_three','accounts.code_four','accounts.code_five','accounts.description as account_description'
              // ,'clients.name as client_name','providers.razon_social as provider_name'
              // ,'providers.code_provider as code_provider'
               ,'header_vouchers.reference as reference_order','header_vouchers.date as date_order'
               ,'header_vouchers.id as id_order')
               ->get();
           
          
          $pdf = $pdf->loadView('admin.bankmovements.reports.directchargeorder_payment_pdf',compact('movements','datenow'));
          return $pdf->stream();
                   
      }


    public function create()
   {
        $accounts = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                        ->where('code_two', 1)
                                        ->where('code_three', 1)
                                        ->whereIn('code_four', [1,2])
                                        ->where('code_five','<>',0)
                                        ->orderBY('description','asc')->pluck('description','id')->toArray();


        if(isset($accounts)){   

            $contrapartidas     = Account::on(Auth::user()->database_name)->where('code_one', '<>',0)
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

           return view('admin.directchargeorder.create',compact('accounts','datenow','contrapartidas','branches','bcv','coin'));

        }else{
            return redirect('/directchargeorders')->withDanger('No hay Cuentas!');
       }
   }


    public function store(Request $request)
    {
        
        $data = request()->validate([
            
        
            'account'        =>'required',
            'Subcontrapartida'  =>'required',

            'beneficiario'      =>'required',
            'Subbeneficiario'      =>'required',

            'user_id'           =>'required',
            'amount'            =>'required',
            
            'date'              =>'required',
        
        
        ]);
        
        $account = request('account');
        $contrapartida = request('Subcontrapartida');
        $coin = request('coin');

        if($account != $contrapartida){

            $amount = str_replace(',', '.', str_replace('.', '', request('amount')));
            $rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            if($coin != 'bolivares'){
                $amount = $amount * $rate;
            }

            if($rate == 0){
                return redirect('/directchargeorders')->withDanger('La tasa no puede ser cero!');
            }

            /*$check_amount = $this->check_amount($account);

            se desabilita esta validacion por motivos que el senor nestor queria ingresar datos y que queden en negativo
            if($check_amount->saldo_actual >= $amount){*/

                $charge_order = new ChargeOrder();
                $charge_order->setConnection(Auth::user()->database_name);

                if(request('beneficiario') == 1){
                    $charge_order->id_client = request('Subbeneficiario');
                    
                }else{
                    $charge_order->id_provider = request('Subbeneficiario');
                }
                $charge_order->id_user = request('user_id');

                if(request('branch') != 'ninguno'){
                    $charge_order->id_branch = request('branch');
                }

                $charge_order->date = request('date');
                $charge_order->reference = request('reference');
                $charge_order->description = request('description');
                $charge_order->amount = $amount;
                $charge_order->rate = $rate;
                $charge_order->coin = $coin;

                $charge_order->status = 1;

                $charge_order->save();

                $header = new HeaderVoucher();
                $header->setConnection(Auth::user()->database_name);

                $header->description = "Orden de Cobro ". request('description');
                $header->date = request('date');
                $header->reference = request('reference');
                $header->status =  1;
            
                $header->save();


                $movement = new DetailVoucher();
                $movement->setConnection(Auth::user()->database_name);

                $movement->id_header_voucher = $header->id;
                $movement->id_account = $account;
                $movement->user_id = request('user_id');
                $movement->debe = $amount;
                $movement->haber = 0;
                $movement->tasa = $rate;
                $movement->status = "C";
            
                $movement->save();
                
                $account = Account::on(Auth::user()->database_name)->findOrFail($account);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }

                $movement_counterpart = new DetailVoucher();
                $movement_counterpart->setConnection(Auth::user()->database_name);

                $movement_counterpart->id_header_voucher = $header->id;
                $movement_counterpart->id_account = $contrapartida;
                $movement_counterpart->user_id = request('user_id');
                $movement_counterpart->debe = 0;
                $movement_counterpart->haber = $amount;
                $movement_counterpart->tasa = $rate;
                $movement_counterpart->status = "C";

                $movement_counterpart->save();

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

                return redirect('/directchargeorders')->withSuccess('Registro Exitoso!');

           /* }else{
                return redirect('/directchargeorders'.request('id_account').'')->withDanger('El saldo de la Cuenta '.$check_amount->description.' es menor al monto del retiro!');
            }*/

        }else{
            return redirect('/directchargeorders')->withDanger('No se puede hacer un movimiento a la misma cuenta!');
        }
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
