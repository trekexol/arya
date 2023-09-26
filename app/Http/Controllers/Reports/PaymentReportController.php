<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Client;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentReportController extends Controller
{
    /// antes PaymentLicReportController
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
            $global = new GlobalController();
            $datenow = $date->format('d-m-Y');

            $date_ini = $global->data_first_month_day();
            $date_end = $global->data_last_month_day();

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

            return view('admin.reports.payments.index_payments',compact('client','datenow','typeperson','vendor','date_ini','date_end'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function store(Request $request)
    {

        $date_ini = request('date_ini');
        $date_end = request('date_end');
    
        $type = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
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


        return view('admin.reports.payments.index_payments',compact('coin','date_end','date_ini','client','vendor','typeperson','date_ini'));
    }

    function pdf($coin,$date_end,$date_ini,$typeperson,$id_client_or_vendor = null)
    {

        $pdf = App::make('dompdf.wrapper');
        $quotation_payments = null;

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
        $global = new GlobalController();

        if(empty($date_ini)){
            $date_ini = $global->data_first_month_day();
        }else{
            $date_ini = Carbon::parse($date_ini)->format('Y-m-d');
        }


        if(empty($date_end)){
            $date_end = $global->data_last_month_day();
        }else{
            $date_end = Carbon::parse($date_end)->format('Y-m-d');
        }

        $period = $date->format('Y');

        if(isset($typeperson) && ($typeperson == 'Cliente')){

            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                                ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                                ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                                ->leftjoin('detail_vouchers', 'detail_vouchers.id_invoice','=','quotations.id')
                                ->leftjoin('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
                                ->where('quotations.amount','<>',null)
                                ->whereRaw("(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
                                [$date_ini,$date_end])
                                ->where('quotations.id_client',$id_client_or_vendor)
                                ->select('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','clients.name as name_client','accounts.description as description_account','header_vouchers.date as date')
                                ->groupBy('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','name_client','description_account','date')
                                ->orderBy('quotation_payments.id','desc')
                                ->orderBy('header_vouchers.date','desc')
                                ->get();
            


        }else if(isset($typeperson) && $typeperson == 'Vendedor'){

            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                    ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                    ->leftjoin('detail_vouchers', 'detail_vouchers.id_invoice','=','quotations.id')
                    ->leftjoin('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
                    ->where('quotations.amount','<>',null)
                    ->whereRaw("(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
                    [$date_ini,$date_end])
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    ->select('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','clients.name as name_client','accounts.description as description_account','header_vouchers.date as date')
                    ->groupBy('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','name_client','description_account','date')
                    ->orderBy('quotation_payments.id','desc')
                    ->orderBy('header_vouchers.date','desc')
                    ->get();

        }else{
            $quotation_payments = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->join('quotation_payments', 'quotation_payments.id_quotation','=','quotations.id')
                    ->join('accounts', 'accounts.id','=','quotation_payments.id_account')
                    ->leftjoin('detail_vouchers', 'detail_vouchers.id_invoice','=','quotations.id')
                    ->leftjoin('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
                    ->where('quotations.amount','<>',null)
                    ->where('header_vouchers.status','1')
                    ->where('detail_vouchers.status','C')
                    ->whereRaw("(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
                    [$date_ini,$date_end])
                    ->select('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','clients.name as name_client','accounts.description as description_account','header_vouchers.date as date')
                    ->groupBy('quotation_payments.id','quotation_payments.id_quotation','quotation_payments.id_account','quotation_payments.payment_type','quotation_payments.amount','quotation_payments.rate','quotation_payments.IGTF_percentage','quotation_payments.credit_days','quotation_payments.reference','quotation_payments.status','quotations.number_invoice','name_client','description_account','date')
                    ->orderBy('quotation_payments.id','desc')
                    ->orderBy('header_vouchers.date','desc')
                    ->get();

        }

        $quotation_payments = $quotation_payments ->unique('id');

        foreach($quotation_payments as $var){
            $var->payment_type = $this->asignar_payment_type($var->payment_type);

        }

        $pdf = $pdf->loadView('admin.reports.payments.payments',compact('coin','quotation_payments','datenow','date_end','date_ini'));
        return $pdf->stream();

    }

    public function selectClient()
    {
        $clients    = Client::on(Auth::user()->database_name)->get();

        return view('admin.reports.payments.selectclient',compact('clients'));
    }

    public function selectVendor()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();

        return view('admin.reports.payments.selectvendor',compact('vendors'));
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
