<?php

namespace App\Http\Controllers;

use App\Account;
use App\Http\Controllers\Calculations\CalculationController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($coin = null)
    {
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $global = new GlobalController();
        $bcv_actual = $global->search_bcv();

        if($users_role == 1){

            if($user->id_company == 1) { // acceso especifico a do7a paula

                return redirect('/quotations/index/bolivares');
            }


            if(empty($coin)){
                $coin = 'bolivares';
            }

            $accounts = $this->calculation($coin ?? 'bolivares');
            $account_activo = 0;
            $account_pasivo = 0;
            $account_patrimonio = 0;
            $account_ingresos = 0;
            $account_costos = 0;
            $account_gastos = 0;

            $account_cuentas_por_pagar = 0;
            $account_cuentas_por_cobrar = 0;
            $account_prestamos = 0;



            foreach($accounts as $account){

                if(($account->code_one == 1)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_activo = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 2)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_pasivo = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 3)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_patrimonio = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 4)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_ingresos = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 5)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_costos = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 6)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_gastos = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 2)&&($account->code_two == 1)&&($account->code_three == 1)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_cuentas_por_pagar = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 1)&&($account->code_two == 1)&&($account->code_three == 2)&&($account->code_four == 1)&&($account->code_five == 0)){
                    $account_cuentas_por_cobrar = $account->balance + $account->debe - $account->haber;
                }
                if(($account->code_one == 2)&&($account->code_two == 2)&&($account->code_three == 1)&&($account->code_four == 0)&&($account->code_five == 0)){
                    $account_prestamos = $account->balance + $account->debe - $account->haber;
                }


            }

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            /*Calculos para el Barchart */
                $calculate = new CalculationController();

                $totals = collect(new Account());
                $totals_per_month = [];
                $mes = 0;
                $result = new Account();

                $accountActivo = Account::on(Auth::user()->database_name)->where('code_one',1)
                ->where('code_two', 0)
                ->where('code_three', 0)
                ->where('code_four', 0)
                ->where('code_five', 0)
                ->first();


                $totals->push($calculate->calculate_account($accountActivo,$coin,$date->startOfYear()->format('Y-m-d'),$date->endOfMonth()->format('Y-m-d')));
                $totals_per_month[$mes] = ($totals[$mes]->debe ?? 0) - ($totals[$mes]->haber ?? 0);
                $mes += 1;

                while($mes <= 12){

                    $result = $calculate->calculate_account($accountActivo,$coin,$date->addDay(1)->format('Y-m-d'),$date->addDay(1)->endOfMonth()->format('Y-m-d'));
                    $totals->push($result);

                    $totals_per_month[$mes] = ($totals[$mes]->debe ?? 0) - ($totals[$mes]->haber ?? 0);
                    $mes += 1;
                }
            /*------------------------------------- */
            $date = Carbon::now();
            /*Calculos para el PieChart*/
                $totalIngresoPieChart = 0;
                $totalCostoPieChart = 0;
                $totalGastoPieChart = 0;
                $accountIngresos = Account::on(Auth::user()->database_name)->where('code_one',4)
                    ->where('code_two', 0)
                    ->where('code_three', 0)
                    ->where('code_four', 0)
                    ->where('code_five', 0)
                    ->first();
                $accountCostos= Account::on(Auth::user()->database_name)->where('code_one',5)
                    ->where('code_two', 0)
                    ->where('code_three', 0)
                    ->where('code_four', 0)
                    ->where('code_five', 0)
                    ->first();
                $accountGastos= Account::on(Auth::user()->database_name)->where('code_one',6)
                    ->where('code_two', 0)
                    ->where('code_three', 0)
                    ->where('code_four', 0)
                    ->where('code_five', 0)
                    ->first();

                $IngresoPieChart = $calculate->calculate_account($accountIngresos,$coin,$date->startOfYear()->format("Y-m-d"),$date->endOfYear()->format("Y-m-d"));

                $totalIngresoPieChart = abs($IngresoPieChart->debe + $IngresoPieChart->balance_previus - $IngresoPieChart->haber);

                $CostoPieChart = $calculate->calculate_account($accountCostos,$coin,$date->startOfYear()->format("Y-m-d"),$date->endOfYear()->format("Y-m-d"));

                $totalCostoPieChart = abs($CostoPieChart->debe + $CostoPieChart->balance_previus - $CostoPieChart->haber);

                $GastoPieChart = $calculate->calculate_account($accountGastos,$coin,$date->startOfYear()->format("Y-m-d"),$date->endOfYear()->format("Y-m-d"));

                $totalGastoPieChart = abs($GastoPieChart->debe + $GastoPieChart->balance_previus - $GastoPieChart->haber);

                /*------------------------------ */
            $accountsbanks = Account::on(Auth::user()->database_name)->where('code_one', 1)
            ->where('code_two', 1)
            ->where('code_three', 1)
            ->where('code_four', 2)
            ->where('level', 5)
            ->orWhere('description','like','%caja%')
            ->get();

            $iniciayear = $date->startOfYear()->format("Y-m-d");
            $finyear = $date->endOfYear()->format("Y-m-d");
            $soloyear = $date->startOfYear()->format("Y");

            foreach($accountsbanks as $accountsbank){


                if($coin == "bolivares"){
                      /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */
           $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
           ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
           ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
           ->where('accounts.id', $accountsbank->id)

           ->where('detail_vouchers.status','C')
           ->whereIn('header_vouchers.status',['C',1])
           ->whereRaw(
           "(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
           [$date->startOfYear()->format("Y-m-d"),$date->endOfYear()->format("Y-m-d")])->sum('debe');





           $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
           ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
           ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
           ->where('accounts.id', $accountsbank->id)
           ->where('detail_vouchers.status','C')
           ->whereIn('header_vouchers.status',['C',1])
           ->whereRaw(
           "(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",
           [$date->startOfYear()->format("Y-m-d"),$date->endOfYear()->format("Y-m-d")])
           ->sum('haber');

           $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
           ->where('accounts.id', $accountsbank->id)
           ->where('accounts.period', $date->startOfYear()->format("Y"))
           ->sum('balance_previus');

            $montobank = $total_balance +  $total_debe - $total_haber;
           $accountsbank->saldobanks = $montobank;


                }


        if($coin == "dolares"){


         $total_debe =   DB::connection(Auth::user()->database_name)->select("SELECT SUM(b.debe/b.tasa) AS debe
         FROM accounts a, detail_vouchers b, header_vouchers c
         WHERE a.id =  $accountsbank->id
         AND a.id = b.id_account
         AND c.id = b.id_header_voucher
         AND b.status = 'C'
         AND c.status IN ('C','1')
         AND c.date >= '$iniciayear'
         AND c.date <= '$finyear'");


         $total_haber = DB::connection(Auth::user()->database_name)->select("SELECT SUM(b.haber/b.tasa) AS haber
         FROM accounts a, detail_vouchers b, header_vouchers c
         WHERE a.id =  $accountsbank->id
         AND a.id = b.id_account
         AND c.id = b.id_header_voucher
         AND b.status = 'C'
         AND c.status IN ('C','1')
         AND c.date >= '$iniciayear'
         AND c.date <= '$finyear'");



        $total_balance = DB::connection(Auth::user()->database_name)->select("SELECT SUM(a.balance_previus/a.rate) AS balance_previus
        FROM accounts a
        WHERE a.id =  $accountsbank->id
        AND a.period = '$soloyear'");


            if($total_debe[0]->debe == null){
                $total_debe = 0;
            }else{
                $total_debe = $total_debe[0]->debe;
            }

            if($total_haber[0]->haber == null){
                $total_haber = 0;
            }else{
                $total_haber = $total_haber[0]->haber;
            }

            if($total_balance[0]->balance_previus == null){
                $total_balance = 0;
            }else{
                $total_balance = $total_balance[0]->balance_previus;
            }

        $montobank = $total_balance +  $total_debe - $total_haber;
         $accountsbank->saldobanks = $montobank;


              }

        }
            /********************************************* */

            return view('admin.index',compact('account_activo','account_pasivo','account_patrimonio','account_ingresos'
            ,'account_costos','account_gastos','account_cuentas_por_pagar','account_cuentas_por_cobrar','account_prestamos'

            ,'date','totals_per_month','totalIngresoPieChart','totalCostoPieChart','totalGastoPieChart','coin','accountsbanks'));


        }else{

            return view('admin.page_home');
        }
    }



    public function calculation_old($coin)
    {

        $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                         ->orderBy('code_two', 'asc')
                         ->orderBy('code_three', 'asc')
                         ->orderBy('code_four', 'asc')
                         ->orderBy('code_five', 'asc')
                         ->get();


        if(isset($accounts)) {

            foreach ($accounts as $var)
            {
                if($var->code_one != 0)
                {
                    if($var->code_two != 0)
                    {
                        if($var->code_three != 0)
                        {
                            if($var->code_four != 0)
                            {
                                if($var->code_five != 0)
                                {
                                     /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */

                                     if($coin == 'bolivares'){
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                        $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                        $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                                        $var->balance =  $var->balance_previus;


                                        }else{
                                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);




                                        }
                                        $total_debe = $total_debe[0]->debe;
                                        $total_haber = $total_haber[0]->haber;
                                        if(isset($total_dolar_debe[0]->dolar)){
                                            $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                            $var->dolar_debe = $total_dolar_debe;
                                        }
                                        if(isset($total_dolar_haber[0]->dolar)){
                                            $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                            $var->dolar_haber = $total_dolar_haber;
                                        }

                                        $var->debe = $total_debe;
                                        $var->haber = $total_haber;

                                        if(($var->balance_previus != 0) && ($var->rate !=0)){
                                            $var->balance =  $var->balance_previus;
                                        }

                                }else{

                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */

                                    if($coin == 'bolivares'){
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                                    $var->balance =  $var->balance_previus;

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                    }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                        /*if(($var->balance_previus != 0) && ($var->rate !=0))
                                        $var->balance =  $var->balance_previus / $var->rate;*/
                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                    if(isset($total_dolar_debe[0]->dolar)){
                                        $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                        $var->dolar_debe = $total_dolar_debe;
                                    }
                                    if(isset($total_dolar_haber[0]->dolar)){
                                        $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                        $var->dolar_haber = $total_dolar_haber;
                                    }

                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                }
                            }else{

                                if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND

                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND

                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?  AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;

                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;



                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;


                            }
                        }else{

                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);

                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);

                                }

                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;



                                $total_balance = $total_balance[0]->balance;
                                $var->balance = $total_balance;
                        }
                    }else{
                        if($coin == 'bolivares'){
                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,'C']);
                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,'C']);

                            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ?
                                            '
                                            , [$var->code_one]);

                            }else{
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                FROM accounts a
                                INNER JOIN detail_vouchers d
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                '
                                , [$var->code_one,'C']);

                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                FROM accounts a
                                INNER JOIN detail_vouchers d
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                '
                                , [$var->code_one,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ?
                                            '
                                            , [$var->code_one]);

                            }
                            $total_debe = $total_debe[0]->debe;
                            $total_haber = $total_haber[0]->haber;
                            $var->debe = $total_debe;
                            $var->haber = $total_haber;

                            $total_balance = $total_balance[0]->balance;

                            $var->balance = $total_balance;

                    }
                }else{
                    return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                }
            }

        }else{
            return redirect('/accounts/menu')->withDanger('No hay Cuentas');
        }



         return $accounts;
    }

    public function calculation($coin)
    {

        $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                         ->orderBy('code_two', 'asc')
                         ->orderBy('code_three', 'asc')
                         ->orderBy('code_four', 'asc')
                         ->orderBy('code_five', 'asc')
                         ->get();


        if(isset($accounts)) {

            foreach ($accounts as $var)
            {
                if($var->code_one != 0)
                {
                    if($var->code_two != 0)
                    {
                        if($var->code_three != 0)
                        {
                            if($var->code_four != 0)
                            {
                                if($var->code_five != 0)
                                {
                                    //Calculo de superavit
                                    if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) &&
                                    ($var->code_four == 1) && ($var->code_five == 1) ){
                                        $var = $this->calculation_superavit($var,4,$coin);
                                    }else{
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */
                                     if($coin == 'bolivares'){
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                        $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                        $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                                        $var->balance =  $var->balance_previus;


                                        }else{
                                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);

                                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);




                                        }
                                        $total_debe = $total_debe[0]->debe;
                                        $total_haber = $total_haber[0]->haber;
                                        if(isset($total_dolar_debe[0]->dolar)){
                                            $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                            $var->dolar_debe = $total_dolar_debe;
                                        }
                                        if(isset($total_dolar_haber[0]->dolar)){
                                            $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                            $var->dolar_haber = $total_dolar_haber;
                                        }

                                        $var->debe = $total_debe;
                                        $var->haber = $total_haber;

                                        if(($var->balance_previus != 0) && ($var->rate !=0)){
                                            $var->balance =  $var->balance_previus;
                                        }
                                    }

                                }else{

                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */

                                    if($coin == 'bolivares'){
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                                    $var->balance =  $var->balance_previus;

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                    }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                        /*if(($var->balance_previus != 0) && ($var->rate !=0))
                                        $var->balance =  $var->balance_previus / $var->rate;*/
                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                    if(isset($total_dolar_debe[0]->dolar)){
                                        $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                        $var->dolar_debe = $total_dolar_debe;
                                    }
                                    if(isset($total_dolar_haber[0]->dolar)){
                                        $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                        $var->dolar_haber = $total_dolar_haber;
                                    }

                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                }
                            }else{

                                if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND

                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND

                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?  AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;

                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;



                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;


                            }
                        }else{

                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);

                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);

                                }

                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;



                                $total_balance = $total_balance[0]->balance;
                                $var->balance = $total_balance;
                        }
                    }else{
                        //Calcular patrimonio con las cuentas mayores o iguales a 3.0.0.0.0
                        if($var->code_one == 3){
                            $var = $this->calculation_capital($var,$coin);

                        }else{
                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                FROM accounts a
                                                WHERE a.code_one = ?
                                                '
                                                , [$var->code_one]);

                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,'C']);

                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,'C']);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                FROM accounts a
                                                WHERE a.code_one = ?
                                                '
                                                , [$var->code_one]);

                                }
                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;

                                $total_balance = $total_balance[0]->balance;

                                $var->balance = $total_balance;
                        }
                    }
                }else{
                    return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                }
            }

        }else{
            return redirect('/accounts/menu')->withDanger('No hay Cuentas');
        }



         return $accounts;
    }

    public function calculation_capital($var,$coin)
    {
        if($coin == 'bolivares'){
            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                            FROM accounts a
                            INNER JOIN detail_vouchers d
                                ON d.id_account = a.id
                            WHERE a.code_one >= ? AND
                            d.status = ?
                            '
                            , [$var->code_one,'C']);
            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                            FROM accounts a
                            INNER JOIN detail_vouchers d
                                ON d.id_account = a.id
                            WHERE a.code_one >= ? AND
                            d.status = ?
                            '
                            , [$var->code_one,'C']);

            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                            FROM accounts a
                            WHERE a.code_one = ?
                            '
                            , [$var->code_one]);

            }else{
                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                FROM accounts a
                INNER JOIN detail_vouchers d
                    ON d.id_account = a.id
                WHERE a.code_one >= ? AND
                d.status = ?
                '
                , [$var->code_one,'C']);

                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                FROM accounts a
                INNER JOIN detail_vouchers d
                    ON d.id_account = a.id
                WHERE a.code_one >= ? AND
                d.status = ?
                '
                , [$var->code_one,'C']);

                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                            FROM accounts a
                            WHERE a.code_one = ?
                            '
                            , [$var->code_one]);

            }
            $total_debe = $total_debe[0]->debe;
            $total_haber = $total_haber[0]->haber;
            $var->debe = $total_debe;
            $var->haber = $total_haber;

            $total_balance = $total_balance[0]->balance;

            $var->balance = $total_balance;

            return $var;
    }


    public function calculation_superavit($var,$code,$coin)
   {
    if($coin == 'bolivares'){
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);


        }else{
            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
            FROM accounts a
            INNER JOIN detail_vouchers d
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            '
            , [$code,'C']);

            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
            FROM accounts a
            INNER JOIN detail_vouchers d
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            '
            , [$code,'C']);


        }
        $total_debe = $total_debe[0]->debe;
        $total_haber = $total_haber[0]->haber;
        $var->debe = $total_debe;
        $var->haber = $total_haber;

        //$total_balance = $total_balance[0]->balance;

        //$var->balance = $total_balance;

        return $var;

   }


}
