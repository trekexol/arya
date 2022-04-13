<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VendorCommissionController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';

 
    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
   }

   
    public function index($typeperson,$id_client_or_vendor = null)
    {        
      
        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $vendor = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id_client_or_vendor)){
                    $client    = Client::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }else if (isset($typeperson) && $typeperson == 'Vendedor'){
                if(isset($id_client_or_vendor)){
                    $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }
            
            return view('admin.reports.vendor_commissions.index_vendor_commissions',compact('client','datenow','typeperson','vendor'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function store(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $typeinvoice = request('typeinvoice');
        $coin = request('coin');
        $client = null;
        $vendor = null;
        $typeperson = 'ninguno';

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
                $id_client_or_vendor = $id_client;
            }
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $typeperson = 'Vendedor';
                $id_client_or_vendor = $vendor;
            }
        }

        return view('admin.reports.vendor_commissions.index_vendor_commissions',compact('coin','typeinvoice','date_end','client','vendor','typeperson'));
    }

    function pdf($coin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                                        ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                                        ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                    ->where('quotations.date_quotation','<=',$date_consult)
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
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        
                                        ->select('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                        ->groupBy('vendors.comision','quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }
      
        $pdf = $pdf->loadView('admin.reports.vendor_commissions.vendor_commissions',compact('coin','quotations','datenow','date_end'));
        return $pdf->stream();
                 
    }

    public function selectClient()
    { 
        $clients    = Client::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.vendor_commissions.selectclient',compact('clients'));
    }

    public function selectVendor()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.vendor_commissions.selectvendor',compact('vendors'));
    }

     
}
