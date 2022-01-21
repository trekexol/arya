<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use App\Account;
use App\DetailVoucher;
use App\Http\Controllers\Calculations\CalculationController;
use App\Http\Controllers\Calculations\CalculationIngresosEgresosController;
use App\Http\Controllers\UserAccess\UserAccessController;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BalanceIngresosController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';
 
    public function __construct()
    {
       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
    }

    public function index_ingresos()
    {
        
        if($this->userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
            $detail_old = DetailVoucher::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();
            $datebeginyear = $date->firstOfYear()->format('Y-m-d');
            
            $coin = 'bolivares';

        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

        return view('admin.reports.index_ingresos_egresos',compact('coin','datebeginyear','datenow','detail_old'));
      
    }
    
    
    public function store_ingresos(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $level = request('level');
        $coin = request('coin');
        
        return view('admin.reports.index_ingresos_egresos',compact('date_begin','date_end','level','coin'));
    }


    function balance_ingresos_pdf($coin = null,$date_begin = null,$date_end = null,$level = null)
    {
      
        $pdf = App::make('dompdf.wrapper');
        $utilidad = 0;
        $islr = 0;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 
        $detail_old = DetailVoucher::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();

        if(isset($date_begin)){
            $from = $date_begin;
        }else{
            $from = $detail_old->created_at->format('Y-m-d');
        }
        if(isset($date_end)){
            $to = $date_end;
        }else{
            $to = $datenow;
        }
        if(isset($level)){
            
        }else{
            $level = 5;
        }

        
        $global = new CalculationIngresosEgresosController();
      
        $accounts_all = $global->calculate_all($coin,$date_begin,$date_end);
       
        
        foreach($accounts_all as $account){
           
            if(($account->code_one == 3) && ($account->code_two == 2) && ($account->code_three == 1) && ($account->code_four == 1) && ($account->code_five == 1)){
                
                $utilidad = ($account->debe - $account->haber) * -1;
               
            }
            if(($account->code_one == 2) && ($account->code_two == 1) && ($account->code_three == 3) && ($account->code_four == 1) && ($account->code_five == 8)){
                
                $islr = ($account->debe - $account->haber) * -1;
                
            }
        }
        
       
    
        $accounts = $accounts_all->filter(function($account)
        { 
            if($account->code_one >= 4){
                $total = $account->debe - $account->haber;
                if ($total != 0) {
                    $account->balance = 0;
                    $account->balance_previus = 0;
                    return $account;
                }
            }
            
        
            
        });
        
        
        
        $foto = auth()->user()->company->foto_company ?? '';
        $code_rif = auth()->user()->company->code_rif ?? '';
        

        $pdf = $pdf->loadView('admin.reports.ingresos_egresos',compact('islr','utilidad','foto','code_rif','coin','datenow','accounts','level','detail_old','date_begin','date_end'));
        return $pdf->stream();
                
    }
}
