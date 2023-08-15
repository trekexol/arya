<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentExpenseReportController extends Controller
{
    public $userAccess;
    public $modulo = 'Reportes';


    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
   }


    public function index($typeperson,$id_provider = null)
    {

        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
            $provider = null;

            if(isset($typeperson) && $typeperson == 'Proveedor'){
                if(isset($id_provider)){
                    $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
                }
            }

            return view('admin.reports.payments_expenses.index_payments',compact('provider','datenow','typeperson'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function store(Request $request)
    {
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $type = request('type');
        $id_provider = request('id_provider');
        $coin = request('coin');
        $provider = null;
        $typeperson = 'ninguno';

        if($type != 'todo'){
            if(isset($id_provider)){
                $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
                $typeperson = 'Proveedor';
            }

        }

        return view('admin.reports.payments_expenses.index_payments',compact('coin','date_end','date_begin','provider','typeperson','id_provider'));
    }

    function pdf($coin,$date_begin,$date_end,$typeperson,$id_provider = null)
    {


        $pdf = App::make('dompdf.wrapper');
        $expense_payments = null;

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');

      /*  if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d');
        }else{
            $date_begin = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }*/


        $period = $date->format('Y');


        if(isset($typeperson) && ($typeperson == 'Proveedor')){

            $expense_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                ->leftjoin('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                                ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                                ->where('expenses_and_purchases.amount','<>',null)
                                ->whereRaw(
                                    "(DATE_FORMAT(expenses_and_purchases.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(expenses_and_purchases.date, '%Y-%m-%d') <= ?)",
                                    [$date_begin, $date_end])
                                ->where('expenses_and_purchases.id_provider',$id_provider)
                                ->where('expenses_and_purchases.status','<>','X')
                                ->select('expenses_and_purchases.rate','expense_payments.*','providers.razon_social as name_provider','accounts.description as description_account','expenses_and_purchases.date')
                                ->orderBy('expenses_and_purchases.date','desc')
                                ->get();


        }else{
            $expense_payments = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                    ->leftjoin('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                    ->join('expense_payments', 'expense_payments.id_expense','=','expenses_and_purchases.id')
                    ->join('accounts', 'accounts.id','=','expense_payments.id_account')
                    ->where('expenses_and_purchases.amount','<>',null)
                    ->where('expenses_and_purchases.status','<>','X')

                    ->whereRaw(
                        "(DATE_FORMAT(expenses_and_purchases.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(expenses_and_purchases.date, '%Y-%m-%d') <= ?)",
                        [$date_begin, $date_end])
                    ->select('expenses_and_purchases.rate','expense_payments.*','providers.razon_social as name_provider','accounts.description as description_account','expenses_and_purchases.date')
                    ->orderBy('expenses_and_purchases.date','desc')
                    ->get();


        }


        foreach($expense_payments as $var){
            $var->payment_type = $this->asignar_payment_type($var->payment_type);

        }

        $pdf = $pdf->loadView('admin.reports.payments_expenses.payments',compact('coin','expense_payments','datenow','date_end'));
        return $pdf->stream();

    }

    public function selectProvider()
    {
        $providers    = Provider::on(Auth::user()->database_name)->get();

        return view('admin.reports.payments_expenses.selectprovider',compact('providers'));
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
