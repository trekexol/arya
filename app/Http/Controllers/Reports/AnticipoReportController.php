<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Client;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnticipoReportController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';

 
    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
   }

   
    public function index($typeperson,$id_client = null)
    {        
      
        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 

            if(isset($typeperson) && $typeperson == 'Proveedor'){
                if(isset($id_client)){
                    $client    = Client::on(Auth::user()->database_name)->find($id_client);
                }
            }
            
            return view('admin.reports.payments_expenses.index_payments',compact('client','datenow','typeperson'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function store(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $coin = request('coin');
        $client = null;
        $typeperson = 'ninguno';

        

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Proveedor';
            }
            
        }
        
        
        return view('admin.reports.payments_expenses.index_payments',compact('coin','date_end','client','typeperson'));
    }

    function pdf($coin,$date_end,$typeperson,$id_client = null)
    {
       
        $pdf = App::make('dompdf.wrapper');
        $expense_payments = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }

       
        $period = $date->format('Y'); 

      
        if(isset($typeperson) && ($typeperson == 'Proveedor')){
           
            $expense_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                ->leftjoin('clients', 'clients.id','=','expenses_and_purchases.id_client')
                                ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                                ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                                ->where('expenses_and_purchases.amount','<>',null)
                                ->where('expenses_and_purchases.date','<=',$date_consult)
                                ->where('expenses_and_purchases.id_client',$id_client)
                                
                                ->select('expense_payments.*','clients.name as name_client','accounts.description as description_account')
                                ->orderBy('expense_payments.id','desc')
                                ->get();
          
              
        }else{
            $expense_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                    ->leftjoin('clients', 'clients.id','=','expenses_and_purchases.id_client')
                    ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                    ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                    ->where('expenses_and_purchases.amount','<>',null)
                    ->where('expenses_and_purchases.date','<=',$date_consult)
                    ->select('expense_payments.*','clients.name as name_client','accounts.description as description_account')
                    ->orderBy('expense_payments.id','desc')
                    ->get();
        }

       
        foreach($expense_payments as $var){
            $var->payment_type = $this->asignar_payment_type($var->payment_type);
           
        }
      
        $pdf = $pdf->loadView('admin.reports.payments_expenses.payments',compact('coin','expense_payments','datenow','date_end'));
        return $pdf->stream();
                 
    }

    public function selectClient()
    { 
        $clients    = Client::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.payments_expenses.selectclient',compact('clients'));
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
}
