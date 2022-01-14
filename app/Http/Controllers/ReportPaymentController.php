<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Client;
use App\Provider;
use App\Vendor;
use App;

class ReportPaymentController extends Controller
{
    public function index_payment($typeperson,$id = null)
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        if($users_role == '1'){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $provider = null; 
            $vendor = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id)){
                    $client = Client::on(Auth::user()->database_name)->find($id);
                }
            }else if (isset($typeperson) && $typeperson == 'Proveedor'){
                if(isset($id)){
                    $provider = Provider::on(Auth::user()->database_name)->find($id);
                }
            }else if (isset($typeperson) && $typeperson == 'Vendedor'){
                if(isset($id)){
                    $vendor = Vendor::on(Auth::user()->database_name)->find($id);
                }
            }
            
        }elseif($users_role == '2'){
            return view('admin.index');
        }

        return view('admin.reports_payment.index_payment',compact('client','datenow','typeperson','provider','vendor'));
      
    }

    public function store_payment(Request $request)
    {
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_provider = request('id_provider');
        $id_vendor = request('id_vendor');
        $coin = request('coin');
        $client = null;
        $provider = null;
        $vendor = null;
        $typeperson = 'ninguno';

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');   

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
            }
            if(isset($id_provider)){
                $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
                $typeperson = 'Proveedor';
            }
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $typeperson = 'Vendedor';
            }
        }

        return view('admin.reports_payment.index_payment',compact('datenow','coin','date_begin','date_end','client','provider','vendor','typeperson'));
    }

    function payment_pdf($coin,$date_begin,$date_end,$typeperson,$id_client_or_provider = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 

        $global = new GlobalController();

        if(empty($date_end)){
            $date_end = $datenow;

            $date_end_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_end_consult = Carbon::parse($date_end)->format('Y-m-d');
        }

        if(isset($date_begin)){
            $date_begin = Carbon::parse($date_begin)->format('Y-m-d');
        }
        
        $period = $date->format('Y'); 

        $client = null;
        $provider = null;
        $vendor = null;
        

        if(isset($typeperson) && ($typeperson == 'Cliente')){
           
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
            ->join('clients', 'clients.id','=','quotations.id_client')
            ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
            ->leftJoin('accounts', 'accounts.id','=','quotation_payments.id_account')
            ->leftJoin('vendors', 'vendors.id','=','quotations.id_vendor')
            ->where('quotations.status','C')
            ->whereRaw("(DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end_consult])
            ->where('quotations.id_client',$id_client_or_provider)
            ->select('quotation_payments.*','accounts.description as account_description',
            'quotations.number_invoice as number','vendors.name as name_vendor','vendors.surname as surname_vendor')
            ->orderBy('quotations.date_billing','desc')
            ->get();

            $client = Client::on(Auth::user()->database_name)->find($id_client_or_provider);
            
        }elseif(isset($typeperson) && ($typeperson == 'Proveedor')){
            
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
            ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
            ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
            ->leftJoin('accounts', 'accounts.id','=','expense_payments.id_account')
            ->where('expenses_and_purchases.status','C')
            ->whereRaw("(DATE_FORMAT(expense_payments.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(expense_payments.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end_consult])
            ->where('expenses_and_purchases.id_provider',$id_client_or_provider)
            ->select('expense_payments.*','accounts.description as account_description',
            'expense_payments.id as number','expenses_and_purchases.rate as rate')
            ->orderBy('expenses_and_purchases.date','desc')
            ->get();
        
            $provider = Provider::on(Auth::user()->database_name)->find($id_client_or_provider);
           
        }elseif(isset($typeperson) && ($typeperson == 'Vendedor')){
           
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
            ->join('clients', 'clients.id','=','quotations.id_client')
            ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
            ->leftJoin('accounts', 'accounts.id','=','quotation_payments.id_account')
            ->where('quotations.status','C')
            ->whereRaw("(DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end_consult])
            ->where('quotations.id_vendor',$id_client_or_provider)
            ->select('quotation_payments.*','accounts.description as account_description',
            'quotations.number_invoice as number')
            ->orderBy('quotation_payments.created_at','desc')
            ->get();

            $vendor = Vendor::on(Auth::user()->database_name)->find($id_client_or_provider);
            
        }else{
            
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
            ->join('clients', 'clients.id','=','quotations.id_client')
            ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
            ->leftJoin('accounts', 'accounts.id','=','quotation_payments.id_account')
            ->leftJoin('vendors', 'vendors.id','=','quotations.id_vendor')
            ->where('quotations.status','C')
            ->where('quotation_payments.status','1')
            ->whereRaw("(DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end_consult])
            ->select('quotation_payments.*','accounts.description as account_description','quotations.number_invoice as number','vendors.name as name_vendor','vendors.surname as surname_vendor')
            ->orderBy('quotation_payments.created_at','desc')
            ->get();

            $client = Client::on(Auth::user()->database_name)->find($id_client_or_provider);
            

        }

        foreach($quotation_payments as $quotation){

            $quotation->payment_type = $global->asignar_payment_type($quotation->payment_type);
           

            if ($typeperson == 'Cliente' || $typeperson == 'Vendedor') {
            
            $anticiposs = DB::connection(Auth::user()->database_name)->table('anticipos')
            ->where('id_quotation', '=',$quotation->id_quotation)
            ->select('id_account')->get();
            } else {

                if ($typeperson == 'Proveedor'){
                     $anticiposs = DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_expense', '=',$quotation->id_quotation) 
                    ->select('id_account')->get();
                } else {
                     $anticiposs = DB::connection(Auth::user()->database_name)->table('anticipos')
                     ->where('id_quotation', '=',$quotation->id_quotation) 
                    ->orwhere('id_expense', '=',$quotation->id_quotation)->get();
                   
                    
                   // $anticiposs ='nin';

                } 
                
                
            } 
            dd($anticiposs);
            //$array_antticipos[] = [$anticiposs->id_account];

        }

        $pdf = $pdf->loadView('admin.reports_payment.payment',compact('coin','quotation_payments','datenow','date_end','client','provider','vendor'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    public function select_client()
    {
        $clients    = Client::on(Auth::user()->database_name)->get();
    
        return view('admin.reports_payment.selectclient',compact('clients'));
    }

    public function select_vendor()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();
    
        return view('admin.reports_payment.selectvendor',compact('vendors'));
    }

   
    public function select_provider()
    {
        $providers    = Provider::on(Auth::user()->database_name)->get();
    
        return view('admin.reports_payment.selectprovider',compact('providers'));
    }
}
