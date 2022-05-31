<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Anticipo;
use App\Client;
use App\HeaderVoucher;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnticipoReportController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';

 
    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
   }

   
    public function index($typeperson,$id_client_or_provider = null)
    {        
      
        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $provider = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id_client_or_provider)){
                    $client    = Client::on(Auth::user()->database_name)->find($id_client_or_provider);
                }
            }else if (isset($typeperson) && $typeperson == 'Proveedor'){
                if(isset($id_client_or_provider)){
                    $provider   = Provider::on(Auth::user()->database_name)->find($id_client_or_provider);
                }
            }
            
            return view('admin.reports.anticipos.index_anticipos',compact('client','datenow','typeperson','provider'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function store(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_provider = request('id_provider');
        $coin = request('coin');
        $client = null;
        $provider = null;
        $typeperson = 'Cliente';

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
                $id_client_or_provider = $id_client;
            }
            if(isset($id_provider)){
                $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
                $typeperson = 'Proveedor';
                $id_client_or_provider = $id_provider;
            }
            if($type == 'Proveedor'){
                $typeperson = 'Proveedor';
            }
        }

      
        return view('admin.reports.anticipos.index_anticipos',compact('coin','date_end','client','provider','typeperson'));
    }

    function pdf($coin,$date_end,$typeperson,$id_client_or_provider = null)
    {
       
        $pdf = App::make('dompdf.wrapper');
        $quotation_anticipos = null;
        
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
                                ->whereIn('anticipos.status',[1,'M'])
                                ->where('anticipos.id_client','<>',null)
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

        foreach ($anticipos as $key => $anticipo) {
                    
            $headervoucher = HeaderVoucher::on(Auth::user()->database_name)
            ->where('id_anticipo',$anticipo->id)
            ->first();
            if (isset($headervoucher)) {
            $anticipo->comprobante = $headervoucher->id;
            } else {
             $anticipo->comprobante = ''; 
            }
        }


        $pdf = $pdf->loadView('admin.reports.anticipos.anticipos',compact('coin','anticipos','datenow','date_end','typeperson'));
        return $pdf->stream();
                 
    }

    public function selectClient()
    { 
        $clients    = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
    
        return view('admin.reports.anticipos.selectClient',compact('clients'));
    }

    public function selectProvider()
    {
        $providers    = Provider::on(Auth::user()->database_name)->orderBy('razon_social','asc')->get();
    
        return view('admin.reports.anticipos.selectProvider',compact('providers'));
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
