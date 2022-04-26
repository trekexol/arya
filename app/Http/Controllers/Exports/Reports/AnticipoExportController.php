<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Anticipo;
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
     
        if(isset($request->id_client_or_provider)){
            
            $request->id_client_or_provider = $request->id_client ?? $request->id_provider;

        }

       
        $export = new AnticipoExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Anticipos.xlsx');
    }

    function anticipo_pdf($coin,$date_end,$typeperson,$id_client_or_provider = null)
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
            if(empty($id_client_or_provider)){
                $anticipos = Anticipo::on(Auth::user()->database_name)
                                ->leftjoin('clients', 'clients.id','=','anticipos.id_client')
                                ->whereIn('anticipos.status',[1,'M'])->where('anticipos.id_client','<>',null)
                                ->orderBy('anticipos.id','desc')
                                ->select('anticipos.*','clients.name as name')
                                ->get();
            }
            if(isset($id_client_or_provider)){
                $anticipos = Anticipo::on(Auth::user()->database_name)
                                ->leftjoin('clients', 'clients.id','=','anticipos.id_client')
                                ->whereIn('anticipos.status',[1,'M'])
                                ->where('anticipos.id_client',$id_client_or_provider)
                                ->orderBy('anticipos.id','desc')
                                ->select('anticipos.*','clients.name as name')
                                ->get();
            }
              
        }else if(isset($typeperson) && $typeperson == 'Proveedor'){
            if(empty($id_client_or_provider)){
                $anticipos = Anticipo::on(Auth::user()->database_name)
                                ->leftjoin('providers', 'providers.id','=','anticipos.id_provider')
                                ->whereIn('anticipos.status',[1,'M'])->where('id_provider','<>',null)
                                ->orderBy('anticipos.id','desc')
                                ->select('anticipos.*','providers.razon_social as name')
                                ->get();
            }
            if(isset($id_client_or_provider)){
                $anticipos = Anticipo::on(Auth::user()->database_name)
                                ->leftjoin('providers', 'providers.id','=','anticipos.id_provider')
                                ->whereIn('anticipos.status',[1,'M'])
                                ->where('id_provider',$id_client_or_provider)
                                ->orderBy('anticipos.id','desc')
                                ->select('anticipos.*','providers.razon_social as name')
                                ->get();
            }
                
        }else{
            $anticipos = Anticipo::on(Auth::user()->database_name)
                                ->join('clients', 'clients.id','=','anticipos.id_client')
                                ->whereIn('anticipos.status',[1,'M'])
                                ->where('anticipos.id_client','<>',null)
                                ->orderBy('anticipos.id','desc')
                                ->get();
        }

        
        return view('export_excel.anticipos',compact('coin','anticipos','datenow','date_end','typeperson'));
                 
    }

   
}
