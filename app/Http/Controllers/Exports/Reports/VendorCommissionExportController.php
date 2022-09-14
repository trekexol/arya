<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\VendorCommissionExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Quotation;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class VendorCommissionExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
      

        $export = new VendorCommissionExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Comisión_de_Vendedores.xlsx');
    }

  

    function pdf($coin,$date_begin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
    {
       
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }

        $date_begin = Carbon::parse($date_begin)->format('Y-m-d');
        
        $period = $date->format('Y'); 

       
        
        if(isset($typeperson) && ($typeperson == 'Cliente')){
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P','C'])
                                        ->where('quotations.amount','<>',null)
                                        ->whereRaw(
                                            "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                                            [$date_begin, $date_consult])
                                        ->where('quotations.id_client',$id_client_or_vendor)
                                        
                                        ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }else{
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P','C'])
                                        ->where('quotations.amount','<>',null)
                                        ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                                        ->where('quotations.id_client',$id_client_or_vendor)
                                        
                                        ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }else if(isset($typeperson) && $typeperson == 'Vendedor'){
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_quotation','desc')
                    ->get();
                }
            }else{
                
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_quotation','desc')
                    ->get();
                }
            }
        }else{
            
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                   
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                        ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P','C'])
                                        ->where('quotations.amount','<>',null)
                                        ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                                        
                                        ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();

                    
                }
            }else{
                
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P','C'])
                    ->where('quotations.amount','<>',null)
                    ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                    ->where('quotations.date_billing','<>',null)
                    
                    ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                        ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P','C'])
                                        ->where('quotations.amount','<>',null)
                                        ->whereRaw(
                        "(DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_quotation, '%Y-%m-%d') <= ?)", 
                        [$date_begin, $date_consult])
                                        
                                        ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                        ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }
      
        return view('export_excel.vendor_commissions',compact('coin','quotations','datenow','date_end'));
          
                 
    }

}
