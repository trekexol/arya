<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Exports\Reports\AccountReceivableNoteExportFromView;
use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AccountReceivableNoteExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
        $export = new AccountReceivableNoteExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Reporte_Notas_de_Entrega.xlsx');
    }


 
    function accounts_receivable_note_pdf($coin,$date_end,$typeinvoice,$typepersone = 'todo',$id_client_or_vendor = 'todo',$date_frist = '0001-01-01')
    {
       // dd('Moneda: '.$coin.' Hasta: '.$date_end.' ID-Cliente-Vend: '.$id_client_or_vendor.' Tipo: '.$typeinvoice.' Persona: '.$typepersone.' Fecha frist ');
    
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
       // $datenow = $date->format('d-m-Y'); 
        
        $global = new GlobalController();
        
       /* if (empty($date_frist) || $date_frist == null){
            $date_frist = $global->data_first_month_day();   
        } */
        

        $date_consult = $date_end;
    
        $period = $date->format('Y'); 
         

        if($typepersone == 'cliente'){ // cliente
            if(isset($coin) && $coin == 'bolivares'){ // nota cliente bs
                if($typeinvoice == 'notas'){  // nota cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                if($typeinvoice == 'notast'){  // nota cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                   
                
               if($typeinvoice == 'facturas'){ // nota a factura pendiente por cobrar cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                } 
                
                if($typeinvoice == 'facturasc'){ // facturas cobradas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                 if($typeinvoice == 'todo'){ // todas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult )          
                                        ->where('quotations.id_client',$id_client_or_vendor)                                    
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }

            }else{ // notas cliente en dolares

                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if($typeinvoice == 'notas'){ // nota cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                if($typeinvoice == 'notast'){ // nota cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                

                
                if($typeinvoice == 'facturas'){ // factura cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                if($typeinvoice == 'facturasc'){ // facturas cobradas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }                
                if($typeinvoice == 'todo'){ // Todas cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult )          
                                        ->where('quotations.id_client',$id_client_or_vendor) 
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }
        
        if($typepersone == 'vendor'){ // Vendedor
            if(isset($coin) && $coin == 'bolivares'){ // nota vendedor bs
                if($typeinvoice == 'notas'){  // nota vendedor bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                if($typeinvoice == 'notast'){  // nota cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }                   
               if($typeinvoice == 'facturas'){ // nota a factura pendiente por cobrar vendedor bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                } 
                
                if($typeinvoice == 'facturasc'){ // facturas cobradas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada vendedor bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                 if($typeinvoice == 'todo'){ // todas vendedor bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult )          
                                        ->where('quotations.id_vendor',$id_client_or_vendor)                                    
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }

            }else{ // notas id_vendor en dolares

                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if($typeinvoice == 'notas'){ // nota id_vendor $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                if($typeinvoice == 'notast'){ // nota vendedor $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                if($typeinvoice == 'facturas'){ // factura id_vendor $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                if($typeinvoice == 'facturasc'){ // facturas cobradas id_vendor bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada id_vendorbs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }                
                if($typeinvoice == 'todo'){ // Todas id_vendor $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult)
                                        ->where('quotations.id_vendor',$id_client_or_vendor)
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }  
            
        }
        
        if($typepersone == 'todo' || $typepersone == null){ // todas Bs
         
            if(isset($coin) && $coin == 'bolivares'){ // nota cliente bs
                if($typeinvoice == 'notas'){  // nota cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                if($typeinvoice == 'notast'){  // nota cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

               if($typeinvoice == 'facturas'){ // nota a factura pendiente por cobrar cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                } 
                
                if($typeinvoice == 'facturasc'){ // facturas cobradas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                 if($typeinvoice == 'todo'){ // todas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult )                                            
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }

            }else{ // notas cliente en dolares

                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if($typeinvoice == 'notas'){ // nota cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }
                if($typeinvoice == 'notast'){ // nota cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.status','<>','X')
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }

                
                                 
                if($typeinvoice == 'facturas'){ // factura cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['P'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                if($typeinvoice == 'facturasc'){ // facturas cobradas cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['C'])
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.number_invoice','<>',null)
                    ->where('quotations.number_delivery_note','<>',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }
                
                if($typeinvoice == 'notase'){ // nota eliminada cliente bs
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',['X'])
                    ->where('quotations.date_delivery_note','>=',$date_frist)
                    ->where('quotations.date_delivery_note','<=',$date_consult)
                    ->where('quotations.date_billing',null)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }                
                if($typeinvoice == 'todo'){ // Todas cliente $
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_delivery_note','>=',$date_frist)
                                        ->where('quotations.date_delivery_note','<=',$date_consult)
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','vendors.surname as surname_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.status','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','vendors.surname','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }

        }

        return view('export_excel.accounts_receivable_note',compact('coin','quotations','date_end','date_frist','typepersone','id_client_or_vendor'));
                 
    }

}
