<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Account;
use App\DetailVoucher;
use App\Http\Controllers\Calculations\CalculationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BalanceGeneralController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';

 
    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
   }

    public function index()
    {
        
        if($this->userAccess->validate_user_access($this->modulo)){
           
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
            $detail_old = DetailVoucher::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();
            $date_begin = $detail_old;
            $datebeginyear = $date->firstOfYear()->format('Y-m-d');

            return view('admin.reports.index_balance_general',compact('datebeginyear','datenow','detail_old','date_begin'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

        
    
        
      
    }
    public function store(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $level = request('level');
        $coin = request('coin');
        
        
        return view('admin.reports.index_balance_general',compact('date_begin','date_end','level','coin'));
    }

    function balance_pdf($coin = null,$date_begin = null,$date_end = null,$level = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        
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

        $global = new CalculationController();
      
        $accounts_all = $global->calculate_all($coin,$date_begin,$date_end);
      
       
        $accounts = $accounts_all->filter(function($account)
        {
            if($account->code_one <= 3){
                
                $total = $account->balance_previus + $account->debe - $account->haber;
               
                if ($total != 0) {
                    /*if(($account->code_one == 1) && ($account->code_two == 1) && ($account->code_three == 3) && 
                                    ($account->code_four == 1) && ($account->code_five == 1) ){
                        dd($account);
                    }*/
                    return $account;
                }
            }
            
        });

        
        $foto = auth()->user()->company->foto_company ?? '';
        $code_rif = auth()->user()->company->code_rif ?? '';

        
        $pdf = $pdf->loadView('admin.reports.balance_general',compact('foto','code_rif','coin','datenow','accounts','level','detail_old','date_begin','date_end'));
        return $pdf->stream();
                 
    }

     
}
