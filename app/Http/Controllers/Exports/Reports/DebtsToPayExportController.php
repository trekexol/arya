<?php

namespace App\Http\Controllers\Exports\Reports;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\DebtsToPayExportFromView;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DebtsToPayExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
         $export = new DebtsToPayExportFromView($request);

         $export->setter($request);
 
         $export->view();       
         
         return Excel::download($export, 'cuentas_por_pagar.xlsx');
    }

    function debtstopay_pdf($coin,$date_end,$id_provider = null)
    {
      
        $pdf = App::make('dompdf.wrapper');

       
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
        
        if(empty($coin)){
            $coin = "bolivares";
        }
      
        if(isset($id_provider)){
            
            if((isset($coin)) && ($coin == "bolivares")){
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->where('expenses_and_purchases.id_provider',$id_provider)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }else{
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->where('expenses_and_purchases.id_provider',$id_provider)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }
           
        }else{
            if((isset($coin)) && ($coin == "bolivares")){
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }else{
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }
        }
        
       
        return view('export_excel.debtstopay',compact('date_consult','period','coin','expenses','datenow','date_end'));
                 
    }
}


