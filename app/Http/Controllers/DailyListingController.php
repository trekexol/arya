<?php

namespace App\Http\Controllers;

use App;
use App\Account;
use App\Company;
use App\Quotation;
use App\QuotationPayment;
use App\ExpensesAndPurchase;
use App\Anticipo;
use App\Client;
use App\DetailVoucher;
use App\ExpensePayment;
use App\HeaderVoucher;
use App\Http\Controllers\Calculations\AccountCalculationController;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyListingController extends Controller
{


    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Listado Diario');
   }

    public function index(request $request)
    {

    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');
    $namemodulomiddleware = $request->get('namemodulomiddleware');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $detailvouchers = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                            ->where('header_vouchers.date', $datenow)
                            ->whereIn('detail_vouchers.status', ['F','C'])
                            ->select('detail_vouchers.*','header_vouchers.*'
                            ,'accounts.description as account_description')->get();

        $accounts = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one','<>',0)
                            ->where('code_two','<>',0)
                            ->where('code_three','<>',0)
                            ->where('code_four','<>',0)
                            ->where('code_five', '<>',0)
                            ->orderBy('description','asc')
                            ->get();





        return view('admin.daily_listing.index',compact('namemodulomiddleware','detailvouchers','datenow','accounts'));
    }


    public function store(Request $request)
    {

        $namemodulomiddleware = $request->get('namemodulomiddleware');
        $data = request()->validate([


            'date_begin'        =>'required',
            'date_end'  =>'required'
        ]);

        $date_begin = request('date_begin');
        $date_end = request('date_end');

        $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
                                ->whereIn('detail_vouchers.status', ['F','C'])
                                ->select('detail_vouchers.*','header_vouchers.*'
                                ,'accounts.description as account_description')
                                ->orderBy('detail_vouchers.id','desc')->get();

        $accounts = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one','<>',0)
                                ->where('code_two','<>',0)
                                ->where('code_three','<>',0)
                                ->where('code_four','<>',0)
                                ->where('code_five', '<>',0)
                                ->get();



        return view('admin.daily_listing.index',compact('namemodulomiddleware','detailvouchers','date_begin','date_end','accounts'));

    }

    public function print_journalbook(Request $request)
    {
        $date_begin = request('date_begin');
        $date_end = request('date_end');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');

        $pdf = App::make('dompdf.wrapper');

        $id_account = request('id_account');

        $company = Company::on(Auth::user()->database_name)->find(1);

        if(isset($id_account)){
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('header_vouchers')
            ->join('detail_vouchers', 'detail_vouchers.id_header_voucher', '=', 'header_vouchers.id')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->whereIn('header_vouchers.id', function($query) use ($id_account){
                $query->select('id_header_voucher')
                ->from('detail_vouchers')
                ->where('id_account',$id_account);
            })
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')->get();
        }else{
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')->get();
        }

        $date_begin = Carbon::parse($date_begin)->format('d-m-Y');

        $date_end = Carbon::parse($date_end)->format('d-m-Y');


        ///////////MODIFICACIONES DONA PAULA////////////////////////////

        foreach ($detailvouchers as $detail){

            $id_quotation = $detail->id_invoice;
            $id_expense = $detail->id_expense;


            $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
            ->where('id','=',$id_quotation)
            ->get()->first();

            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            /*
            $expense = ExpensePayment::on(Auth::user()->database_name) // buscar referencia
            ->where('id_expense','=',$id_expese)->get();  */

            $anticipo = Anticipo::on(Auth::user()->database_name) // buscar anticipo
                ->where('id','=',$detail->id_anticipo)
                ->get()->first();


            if (isset($quotation)) {

                $detail->header_description .= ' Factura: '.$quotation->number_invoice;

                $client = Client::on(Auth::user()->database_name) // buscar factura
                ->where('id','=',$quotation->id_client)
                ->get()->first();

                $detail->header_description .= '. '.$client->name.'. '.$quotation->coin;

                $referenciab = QuotationPayment::on(Auth::user()->database_name) // buscar referencia
                ->where('id_quotation','=',$quotation->id)
                ->first();

                if($referenciab != null ){

                         $detail->reference = $referenciab->reference;

                }

            } else {


                if (isset($anticipo)) {
                    $id_client = '';
                    $coin_mov = '';
                   if ($anticipo->id_quotation != null){ //con anticipo


                        $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','!=',null)
                        ->get()->first();


                        $quotation_delivery = Quotation::on(Auth::user()->database_name) // buscar Nota de entrega
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','=',null)
                        ->where('number_invoice','=',null)
                        ->get()->first();

                        if (isset($quotation)) { // descriocion  Anticipo factura
                        $detail->header_description .= ' Factura: '.$quotation->number_invoice;
                        $id_client = $quotation->id_client;
                        $coin_mov = $quotation->coin;
                        }
                        if (isset($quotation_delivery)) {
                        $detail->header_description .= ' Nota de Entrega: '.$quotation_delivery->number_delivery_note;
                        $id_client = $quotation_delivery->id_client;
                        $coin_mov = $quotation_delivery->coin;
                        }

                        if(isset($id_client)) {
                            $client = Client::on(Auth::user()->database_name) // buscar cliente de factura
                            ->where('id','=',$id_client)
                            ->get()->first();

                            if(!empty($client)) {
                            $detail->header_description .= $client->name;
                            }
                       }


                       // $detail->header_description .= '. '.$coin_mov;

                        //descripcon Anticipo Compra



                } else { // sin anticipo




                        if (isset($anticipo->id_client)) {

                            $client = Client::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_client)
                            ->get()->first();
                                 if (isset($client)) {
                                 $detail->header_description .= '. '.$client->name;
                                 }
                        }

                        if (isset($anticipo->id_provider)) {

                            $proveedor = Provider::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_provider)
                            ->get()->first();

                                 if (isset($proveedor)) {
                                 $detail->header_description .= '. '.$proveedor->razon_social;
                                 }
                        }

                        //$detail->header_description .= '. '.$anticipo->coin;
                   }


                }

                if(isset($expense)){


                    $detail->header_description .= ' Factura: '.$expense->invoice;

                    $proveedor = Provider::on(Auth::user()->database_name) // buscar factura
                    ->where('id','=',$expense->id_provider)
                    ->get()->first();

                         if (isset($proveedor)) {
                         $detail->header_description .= '. '.$proveedor->razon_social;
                         }

                }


            }

        }



       ///////////FIN MODIFI DONA PAULA/////////////////////////////////////////////////////////////


        $pdf = $pdf->loadView('admin.reports.journal_book',compact('company','detailvouchers'
                                ,'datenow','date_begin','date_end'));
        return $pdf->stream();
    }




    public function print_diary_book_detail(Request $request)
    {


        $id_account = request('id_account');
        $coin = request('coin');

        $date_begin = request('date_begin');
        $date_end = request('date_end');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');

        $pdf = App::make('dompdf.wrapper');

        $company = Company::on(Auth::user()->database_name)->find(1);

        $period = Carbon::parse($date_begin)->format('Y');

        $mesdia = Carbon::parse($date_begin)->format('m-d');

        $account = Account::on(Auth::user()->database_name)->find($id_account);

                   //consulta normal Bs.
                   $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                   ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                   ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                   ->whereRaw(
                       "(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
                      [$date_begin, $date_end])
                   ->whereIn('header_vouchers.id', function($query) use ($id_account){
                       $query->select('id_header_voucher')
                       ->from('detail_vouchers')
                       ->where('id_account',$id_account);
                   })
                   ->whereIn('detail_vouchers.status', ['F','C'])
                   ->select('detail_vouchers.*','header_vouchers.*'
                   ,'accounts.description as account_description'
                   ,'header_vouchers.id as id_header'
                   ,'accounts.balance_previus as balance_previous'
                   ,'header_vouchers.description as header_description')
                   ->orderBy('header_vouchers.date','asc')
                   ->orderBy('header_vouchers.id','asc')->get();

        if($coin != "bolivares"){


                if($account->period == $period ){

                  if($mesdia == '01-01') {

                      $detailvouchers_saldo_debe = 0;
                      $detailvouchers_saldo_haber = 0;

                  } else {
                      //busca los saldos previos de la cuenta
                      $total_debe =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                  ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                  ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                  ->where('header_vouchers.date','<' ,$date_begin)
                                  ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
                                  ->where('accounts.id',$id_account)
                                  ->whereIn('detail_vouchers.status', ['F','C'])
                                  ->select(DB::connection(Auth::user()->database_name)->raw('SUM(detail_vouchers.debe/detail_vouchers.tasa) as debe'))->first();


                      $total_haber =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                  ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                  ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                  ->where('header_vouchers.date','<' ,$date_begin)
                                  ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
                                  ->where('accounts.id',$id_account)
                                  ->whereIn('detail_vouchers.status', ['F','C'])
                                  ->select(DB::connection(Auth::user()->database_name)->raw('SUM(detail_vouchers.haber/detail_vouchers.tasa) as haber'))->first();

                                  $detailvouchers_saldo_debe = number_format($total_debe->debe,2,'.','');
                                  $detailvouchers_saldo_haber = number_format($total_haber->haber,2,'.','');


                  }



              } else {

                        //busca los saldos previos de la cuenta
                        $total_debe =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                    ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                    ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                    ->where('header_vouchers.date','<' ,$date_begin)
                                    ->where('accounts.id',$id_account)
                                    ->whereIn('detail_vouchers.status', ['F','C'])
                                    ->select(DB::connection(Auth::user()->database_name)->raw('SUM(detail_vouchers.debe/detail_vouchers.tasa) as debe'))->first();



                        $total_haber =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                    ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                    ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                    ->where('header_vouchers.date','<' ,$date_begin)
                                    ->where('accounts.id',$id_account)
                                    ->whereIn('detail_vouchers.status', ['F','C'])
                                    ->select(DB::connection(Auth::user()->database_name)->raw('SUM(detail_vouchers.haber/detail_vouchers.tasa) as haber'))->first();


                                    $detailvouchers_saldo_debe = number_format($total_debe->debe,2,'.','');
                                    $detailvouchers_saldo_haber = number_format($total_haber->haber,2,'.','');


              }

        }else{ // bolivares-----------------------------------------------


            if($account->period == $period ){

                if($mesdia == '01-01') {

                    $detailvouchers_saldo_debe = 0;
                    $detailvouchers_saldo_haber = 0;

                } else {
                    //busca los saldos previos de la cuenta
                    $detailvouchers_saldo_debe =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                ->where('header_vouchers.date','<' ,$date_begin)
                                ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
                                ->where('accounts.id',$id_account)
                                ->whereIn('detail_vouchers.status', ['F','C'])
                                ->sum('detail_vouchers.debe');


                    $detailvouchers_saldo_haber =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                                ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                                ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                                ->where('header_vouchers.date','<' ,$date_begin)
                                ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
                                ->where('accounts.id',$id_account)
                                ->whereIn('detail_vouchers.status', ['F','C'])
                                ->sum('detail_vouchers.haber');

                                $detailvouchers_saldo_debe = number_format($detailvouchers_saldo_debe,2,'.','');
                                $detailvouchers_saldo_haber = number_format($detailvouchers_saldo_haber,2,'.','');
                }



            } else {

            //busca los saldos previos de la cuenta
            $detailvouchers_saldo_debe =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                        ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                        ->where('header_vouchers.date','<' ,$date_begin)
                        ->where('accounts.id',$id_account)
                        ->whereIn('detail_vouchers.status', ['F','C'])
                        ->sum('detail_vouchers.debe');


            $detailvouchers_saldo_haber =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                        ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                        ->where('header_vouchers.date','<' ,$date_begin)
                        ->where('accounts.id',$id_account)
                        ->whereIn('detail_vouchers.status', ['F','C'])
                        ->sum('detail_vouchers.haber');


                        $detailvouchers_saldo_debe = number_format($detailvouchers_saldo_debe,2,'.','');
                        $detailvouchers_saldo_haber = number_format($detailvouchers_saldo_haber,2,'.','');


            }


        }


        $date_begin = Carbon::parse($date_begin)->format('d-m-Y');

        $date_end = Carbon::parse($date_end)->format('d-m-Y');

        $account_calculate = new AccountCalculationController();

        $account_historial = $account_calculate->calculateBalance($account,$date_begin);



        if(empty($account_historial->rate) || ($account_historial->rate == 0)){
            $account_historial->rate = 1;
        }


       /* if($coin != "bolivares"){ //saldos Anteriores del historial
        $account_historial->balance_previous = $account_historial->balance_previous / $account_historial->rate;
        } else {
        $account_historial->balance_previous = $account_historial->balance_previous;
        } */



        $primer_movimiento = true;
        $saldo = 0;
        $saldo_anterior =0;
        $counterpart = "";


        foreach($detailvouchers as $detail){

            //$detailvouchers->account_counterpart = '';

            $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
            ->where('id','=',$detail->id_invoice)
            ->where('date_billing','!=',null)
            ->get()->first();


            $anticipo = Anticipo::on(Auth::user()->database_name) // buscar anticipo
                ->where('id','=',$detail->id_anticipo)
                ->get()->first();

            if($detail->reference == null){

                if ($detail->id_expense != null) {

                    $referencia = ExpensePayment::on(Auth::user()->database_name) // buscar referencia
                    ->where('id_expense','=',$detail->id_expense)->get();

                    if(count($referencia) > 1){

                        $detail->reference = '';
                        $count = 0;
                        foreach ($referencia as $refe) {

                                if ($count >= 1){

                                    $detail->reference .= ' / ';

                                    $detail->reference .= $refe->reference;

                                } else {

                                    $detail->reference .= $refe->reference;
                                }
                               $count++;
                        }

                   } else {

                    $referenciab = ExpensePayment::on(Auth::user()->database_name) // buscar referencia
                    ->where('id_expense','=',$detail->id_expense)
                    ->select('reference')
                    ->first();

                        if(!empty($referenciab)){
                            $detail->reference = $referenciab->reference;
                        }else{
                            $detail->reference = '';
                        }
                   }


                }
            }




            if (isset($quotation)) {

                $detail->header_description .= ' Factura: '.$quotation->number_invoice;
                $client = Client::on(Auth::user()->database_name) // buscar factura
                ->where('id','=',$quotation->id_client)
                ->get()->first();

                $detail->header_description .= '. '.$client->name.'. '.$quotation->coin;

                $referenciab = QuotationPayment::on(Auth::user()->database_name) // buscar referencia
                ->where('id_quotation','=',$quotation->id)
                ->first();

                if($referenciab != null ){

                         $detail->reference = $referenciab->reference;

                }





            } else {


                if (isset($anticipo)) {
                    $id_client = '';
                    $coin_mov = '';
                   if ($anticipo->id_quotation != null){ //con anticipo


                        $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','!=',null)
                        ->get()->first();

                        $quotation_delivery = Quotation::on(Auth::user()->database_name) // buscar Nota de entrega
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','=',null)
                        ->where('number_invoice','=',null)
                        ->get()->first();



                        if (isset($quotation)) { // descriocion  Anticipo factura
                        $detail->header_description .= ' Factura: '.$quotation->number_invoice;
                        $id_client = $quotation->id_client;
                        $coin_mov = $quotation->coin;
                        }
                        if (isset($quotation_delivery)) {
                        $detail->header_description .= ' Nota de Entrega: '.$quotation_delivery->number_delivery_note;
                        $id_client = $quotation_delivery->id_client;
                        $coin_mov = $quotation_delivery->coin;
                        }

                        if(isset($id_client)) {
                            $client = Client::on(Auth::user()->database_name) // buscar cliente de factura
                            ->where('id','=',$id_client)
                            ->get()->first();

                            if(!empty($client)) {
                            $detail->header_description .= $client->name;
                            }
                       }


                        $detail->header_description .= '. '.$coin_mov;

                        //descripcon Anticipo Compra



                } else { // sin anticipo




                        if (isset($anticipo->id_client)) {

                            $client = Client::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_client)
                            ->get()->first();
                                 if (isset($client)) {
                                 $detail->header_description .= '. '.$client->name;
                                 }
                        }

                        if (isset($anticipo->id_provider)) {

                            $proveedor = Provider::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_provider)
                            ->get()->first();
                                 if (isset($proveedor)) {
                                 $detail->header_description .= '. '.$proveedor->razon_social;
                                 }
                        }

                        $detail->header_description .= '. '.$anticipo->coin;
                   }


                }

            }



                if($coin != "bolivares"){

                    if((isset($detail->debe)) && ($detail->debe != 0) && ($detail->debe != 0.00)){
                    $detail->debe = $detail->debe / ($detail->tasa ?? 1);
                    }

                    if((isset($detail->haber)) && ($detail->haber != 0)  && ($detail->haber != 0.00)){
                    $detail->haber = $detail->haber / ($detail->tasa ?? 1);
                    }

                    $saldo_anterior = $account->balance_previus / ($account->rate ?? 1);

                } else {

                    $saldo_anterior = $account->balance_previus;
                }

                $saldo_anterior = number_format($saldo_anterior,2,'.','');

                if($account->period != $period){
                    $saldo_anterior = 0;
                }


                $detail->balance_previus = $saldo_anterior;
                $amount_voucher = 0;
                $account_contrapartida = '';



                if($detail->id_account == $id_account){

                    if($primer_movimiento){


                            $detail->saldo = $saldo_anterior + ($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0) + $detail->debe - $detail->haber;
                            $saldo += $detail->saldo;



                        $primer_movimiento = false;

                    }else{



                            $detail->saldo = $detail->debe - $detail->haber + $saldo;

                            $saldo = $detail->saldo;
                    }

                   /* if($counterpart == ""){
                        $last_detail = $detail;
                    }else{
                        $detail->account_counterpart = $counterpart;
                    }*/

                    $detail->account_counterpart = '';

                }else{
                   /*if(isset($last_detail)){
                        $last_detail->account_counterpart = $detail->account_description;

                    }else{
                        $counterpart = $detail->account_description;
                    }*/

                   // $account = Account::on(Auth::user()->database_name)->find($detail->id_account);

                   $detail->account_counterpart = '';

                }

                    $amount_voucher = $detail->debe + $detail->haber;


                    $account_contrapartida_id = DetailVoucher::on(Auth::user()->database_name) // buscar factura
                    ->where('id_header_voucher','=',$detail->id_header)
                    ->where('id_account','<>',$detail->id_account)
                    ->get()->first();

                    if(!empty($account_contrapartida_id)) {
                    $account_contrapartida = Account::on(Auth::user()->database_name)->find($account_contrapartida_id->id_account);
                    }

                    if(empty($account_contrapartida)) {
                        $description_contrapartida = $account->description;
                    } else{
                        $description_contrapartida = $account_contrapartida->description;
                    }


                    if($coin != "bolivares"){
                    $detail->account_counterpart = $description_contrapartida.' - Tasa: '.number_format($detail->tasa,2,',','').' Bs.';
                    } else {
                        $detail->account_counterpart = $description_contrapartida;
                    }
        }

        //voltea los movimientos para mostrarlos del mas actual al mas antiguo
        $detailvouchers = array_reverse($detailvouchers->toArray());


                $saldo_inicial = $saldo_anterior + ($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0);

                //$saldo_inicial = number_format(($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0),2,'.','');


        $pdf = $pdf->loadView('admin.reports.diary_book_detail',compact('coin','company','detailvouchers'
                                ,'datenow','date_begin','date_end','account','saldo_anterior'
                                ,'detailvouchers_saldo_debe','detailvouchers_saldo_haber','saldo','id_account','saldo_inicial'));
        return $pdf->stream();
    }



}
