<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\AnticipoExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AnticipoExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
     
        if(isset($request->id_provider)){
            
            $request->id_provider_or_provider = $request->id_provider;

        }

        $export = new AnticipoExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Pagos de Compras.xlsx');
    }

    function payment_pdf($coin,$date_begin,$date_end,$typeperson,$id_provider = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 

        $global = new GlobalController();

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
                                ->leftjoin('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                                ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                                ->where('expenses_and_purchases.amount','<>',null)
                                ->where('expenses_and_purchases.date','<=',$date_consult)
                                ->where('expenses_and_purchases.id_provider',$id_provider)
                                
                                ->select('expense_payments.*','providers.razon_social as name_provider','accounts.description as description_account')
                                ->orderBy('expense_payments.id','desc')
                                ->get();
          
              
        }else{
            $expense_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                    ->leftjoin('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                    ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                    ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                    ->where('expenses_and_purchases.amount','<>',null)
                    ->where('expenses_and_purchases.date','<=',$date_consult)
                    ->select('expense_payments.*','providers.razon_social as name_provider','accounts.description as description_account')
                    ->orderBy('expense_payments.id','desc')
                    ->get();
        }

       
        foreach($expense_payments as $var){
            $var->payment_type = $this->asignar_payment_type($var->payment_type);
           
        }
      
      
        return view('export_excel.payment_expense',compact('coin','expense_payments','datenow','date_end'));
                 
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
