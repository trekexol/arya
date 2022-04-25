<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\PaymentCobroExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PaymentCobroExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
     
        if(isset($request->id_client)){
            
            $request->id_client_or_provider = $request->id_client;

        }else if(isset($request->id_vendor)){

            $request->id_client_or_provider = $request->id_vendor;

        }

        $export = new PaymentCobroExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Cobros.xlsx');
    }

    function payment_pdf($coin,$date_begin,$date_end,$typeperson,$id_client_or_vendor = null)
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
        
        if(isset($typeperson) && ($typeperson == 'Cliente')){
           
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                                ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                                ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                                ->where('quotations.amount','<>',null)
                                ->where('quotations.date_quotation','<=',$date_consult)
                                ->where('quotations.id_client',$id_client_or_vendor)
                                
                                ->select('quotation_payments.*','quotations.number_invoice','clients.name as name_client','accounts.description as description_account')
                                ->orderBy('quotation_payments.id','desc')
                                ->get();
          
              
        }else if(isset($typeperson) && $typeperson == 'Vendedor'){

            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                    ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotation_payments.*','quotations.number_invoice','clients.name as name_client','accounts.description as description_account')
                    ->orderBy('quotation_payments.id','desc')
                    ->get();
                
        }else{
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                    ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->select('quotation_payments.*','quotations.number_invoice','clients.name as name_client','accounts.description as description_account')
                    ->orderBy('quotation_payments.id','desc')
                    ->get();
        }

       
        foreach($quotation_payments as $var){
            $var->payment_type = $this->asignar_payment_type($var->payment_type);
           
        }
      
      
        return view('export_excel.payment_cobro',compact('coin','quotation_payments','datenow','date_end'));
                 
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
