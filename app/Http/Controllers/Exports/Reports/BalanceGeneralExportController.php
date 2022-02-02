<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App;
use App\DetailVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\BalanceGeneralExportFromView;
use App\Http\Controllers\Calculations\CalculationController;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BalanceGeneralExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
         $export = new BalanceGeneralExportFromView($request);

         $export->setter($request);
 
         $export->view();       
         
         return Excel::download($export, 'BalanceGeneral.xlsx');
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

        return view('export_excel.balance_general',compact('foto','code_rif','coin','datenow','accounts','level','detail_old','date_begin','date_end'));
                
    }
}
