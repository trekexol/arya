<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Exports\Reports\IngresosEgresosExportFromView;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use App\DetailVoucher;
use App\Http\Controllers\Calculations\CalculationIngresosEgresosController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class IngresosEgresosExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
         $export = new IngresosEgresosExportFromView($request);

         $export->setter($request);
 
         $export->view();       
         
         return Excel::download($export, 'IngresosEgresos.xlsx');
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
        
        return view('export_excel.ingresos_egresos',compact('islr','utilidad','foto','code_rif','coin','datenow','accounts','level','detail_old','date_begin','date_end'));
       
    }
}
