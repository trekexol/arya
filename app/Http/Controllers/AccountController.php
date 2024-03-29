<?php

namespace App\Http\Controllers;

use App\Account;
use App\Anticipo;
use App\AccountHistorial;
use App\BankMovement;
use App\Company;
use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\Http\Controllers\Calculations\CalculationController;
use App\Http\Controllers\Calculations\CalculationWithCController;
use App\Http\Controllers\Calculations\CalculationWithoutDateController;
use App\Quotation;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Plan de Cuentas');
   }

   public function index(request $request,$coin = null,$level = null,$ini = null, $fin = null)
   {
    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');


        if($coin == null){
            $coin = 'bolivares';
        }

        $total_saldo_anterior = 0;
        $total_debe = 0;
        $total_haber = 0;

        $total_saldo_anterior1 = 0;
        $total_saldo_anterior2 = 0;
        $total_saldo_anterior3 = 0;

        $accounts = $this->calculation($coin,$ini,$fin);

        if($ini == null or $fin == null){

            $ini = '2000-01-01';
            $date = Carbon::now();
            $fin = $date->format('Y-m-d');


        }else{
            $ini = $ini;
            $fin = $fin;
        }

        foreach($accounts as $account){

            if($account->level == 5){
                if(($account->code_one == 3) && ($account->code_two == 2) && ($account->code_three == 1) && ($account->code_four == 1) && ($account->code_five == 1) ){
                    if($coin == 'bolivares'){

                            $total_debe_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        a.code_five = ? AND
                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$ini,$fin]);
                        $total_haber_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        a.code_five = ? AND
                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$ini,$fin]);

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
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$ini,$fin]);

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
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$fin,$ini]);

                                        $account->balance =  $account->balance_previus;





                        }else{
                            $total_debe_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                            FROM accounts a
                            INNER JOIN detail_vouchers d
                                ON d.id_account = a.id
                            WHERE a.code_one = ? AND
                            a.code_two = ? AND
                            a.code_three = ? AND
                            a.code_four = ? AND
                            a.code_five = ? AND
                            d.status = ?
                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                            '
                            , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$ini,$fin]);

                            $total_haber_account =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                            FROM accounts a
                            INNER JOIN detail_vouchers d
                                ON d.id_account = a.id
                            WHERE a.code_one = ? AND
                            a.code_two = ? AND
                            a.code_three = ? AND
                            a.code_four = ? AND
                            a.code_five = ? AND
                            d.status = ?
                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                            '
                            , [$account->code_one,$account->code_two,$account->code_three,$account->code_four,$account->code_five,'C',$ini,$fin]);




                        }
                        $total_debe_account = $total_debe_account[0]->debe;
                        $total_haber_account = $total_haber_account[0]->haber;

                        $total_debe += $total_debe_account;
                        $total_haber += $total_haber_account;
                }else{
                    $total_debe += $account->debe;
                    $total_haber += $account->haber;
                }
            }


            if(($account->code_one == 1)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){

                $total_saldo_anterior1 += $account->balance;
            }
            if(($account->code_one == 2)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                $total_saldo_anterior2 += $account->balance;
            }
            if(($account->code_one == 3)&&($account->code_two == 0)&&($account->code_three == 0)&&($account->code_four == 0)&&($account->code_five == 0)){
                $total_saldo_anterior3 += $account->balance;
            }

        }




        $total_saldo_anterior = $total_saldo_anterior1 + $total_saldo_anterior2 + $total_saldo_anterior3;

       return view('admin.accounts.index',compact('eliminarmiddleware','actualizarmiddleware','agregarmiddleware','total_debe','total_haber','total_saldo_anterior','accounts','coin','level','ini','fin'));

   }

   public function index_previous_exercise()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       if($users_role == '1'){

        $account_historial = AccountHistorial::on(Auth::user()->database_name)->select('date_begin','date_end')->groupBy('date_begin','date_end')->get();

        }else if($users_role == '2'){
           return view('admin.index');
       }

       return view('admin.accounts.index_previous_exercises',compact('account_historial'));
   }


    public function movements($id_account,$coin = null,$period = null)
    {


        $user       =   auth()->user();
        $users_role =   $user->role_id;


        if($users_role == '1' || $users_role == '1'){


            $periods = DetailVoucher::on(Auth::user()->database_name)
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->where('detail_vouchers.id_account',$id_account)
            ->where('header_vouchers.status','1')
            ->select('header_vouchers.date')
            ->orderBy('header_vouchers.date','desc')
            ->get();


            foreach ($periods as $per) {
                $per->date = substr($per->date,0,4);
            }

            $periodselect = [];

            foreach ($periods->unique('date') as $periodss) {

                $periodselect[] = $periodss->date;
            }



            $company = Company::on(Auth::user()->database_name)->find(1);


            if (!isset($period)){
                
                if (count($periodselect) > 0) {
                    $period = $periodselect[0];
                } else {
                $period = $company->period;
                }

            } else {
            $period = $period;
            }


            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            //->join('quotations', 'quotations.id', '=', 'detail_vouchers.id_invoice')
           // ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->where('detail_vouchers.status','C')
            ->where('detail_vouchers.id_account',$id_account)
            ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
            ->select('detail_vouchers.*','header_vouchers.date as date','header_vouchers.description as description','header_vouchers.id_anticipo as id_anticipo','header_vouchers.reference as reference')
            ->orderBy('header_vouchers.date','desc')
            ->orderBy('detail_vouchers.id','desc')
            ->get();


            $detailvouchers_most = DetailVoucher::on(Auth::user()->database_name)
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            //->join('quotations', 'quotations.id', '=', 'detail_vouchers.id_invoice')
           // ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->where('detail_vouchers.status','C')
            ->where('detail_vouchers.id_account',$id_account)
            ->where('header_vouchers.date','LIKE' ,'%'.$period.'%')
            ->select('detail_vouchers.*','header_vouchers.date as date','header_vouchers.description as description','header_vouchers.id_anticipo as id_anticipo','header_vouchers.reference as reference')
            ->orderBy('header_vouchers.date','asc')
            ->orderBy('detail_vouchers.id','asc')
            ->get();

            /*$primer_movimiento = true;
            $saldo = 0;

            foreach ($detailvouchers as $var){

                if($primer_movimiento){
                    $var->saldo = $var->debe - $var->haber;

                    $saldo += $var->saldo;
                    $primer_movimiento = false;


                }else{
                    $var->saldo = ($saldo + $var->debe) - $var->haber;
                   // $saldo = $var->saldo;
                }
            }    */


            if (!empty($detailvouchers)) {
                foreach ($detailvouchers as $var) {

                    $anticipos = Anticipo::on(Auth::user()->database_name)->find($var->id_anticipo);

                    if (isset($anticipos)) {
                        $quotation_anticipo = Quotation::on(Auth::user()->database_name)->find($anticipos->id_quotation);
                        $expense_anticipo = ExpensesAndPurchase::on(Auth::user()->database_name)->find($anticipos->id_expense);


                        if (isset($quotation_anticipo)) {

                            if (isset($quotation_anticipo->date_billing)) {
                            $var->id_anticipo .= ' (Fact: '.$quotation_anticipo->number_invoice.') Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");
                            } else {
                            $var->id_anticipo .= ' (NE: '.$quotation_anticipo->number_delivery_note.') Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");
                            }

                        }


                        if (isset($expense_anticipo)) {
                            $var->id_anticipo .= ' Compra Factura: '.$expense_anticipo->invoice.' Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");
                        }

                    }
                }


            }

            $account = Account::on(Auth::user()->database_name)->find($id_account);


            if($account->period != $period) {

                $ultimo_historial = DB::connection(Auth::user()->database_name)->table('account_historials')
                ->where('id_account', $account->id)
                ->where('period',$period)
                ->get()->first();

                if (empty($ultimo_historial)) {
                    $balance_previus = 0;
                } else {
                    $balance_previus = $ultimo_historial->balance_previous;
                }

            } else {

                $balance_previus = $account->balance_previus;
            }




            if(empty($coin)){
                $coin = "bolivares";
            }

         }else if($users_role == '2'){
            return view('admin.index');
        }

        return view('admin.accounts.index_account_movement',compact('detailvouchers','account','coin','detailvouchers_most','balance_previus','periodselect','period'));
    }

    public function header_movements($id,$type,$id_account)
    {


        $user       =   auth()->user();
        $users_role =   $user->role_id;
        $detailvouchers = null;
        $var = null;
        $type = null;

        if($users_role == '1'){

            if($type == 'header_voucher'){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_header_voucher',$id)->orderBy('id','desc')->get();

                $var = null;
                $type = $detailvouchers[0]['headers']->description;
                $reference = $detailvouchers[0]['headers']->reference;
            }
            else if($type == 'invoice'){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_invoice',$id)->get();
                $var = Quotation::on(Auth::user()->database_name)->find($id);
                $type = 'Factura';
                $reference = $detailvouchers[0]['headers']->reference;
            }else{
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('status','C')->where('id_header_voucher',$id)->orderBy('id','desc')->get();

                $var = null;
                $type = $detailvouchers[0]['headers']->description;
                $reference = $detailvouchers[0]['headers']->reference;
            }

         }else if($users_role == '2'){
            return view('admin.index');
        }

        return view('admin.accounts.index_header_movement',compact('detailvouchers','type','var','id_account','reference'));
    }



   public function create(Request $request)
   {

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){


        $date = Carbon::now();
        $datenow = $date->format('Y');

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $global->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

        return view('admin.accounts.create',compact('datenow','rate'));

    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }

   }

    public function createlevel(Request $request, $id_account)
    {


        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

            $var = DB::connection(Auth::user()->database_name)->table('accounts')->where('id', $id_account)->first();

            if(isset($var)){

                    if($var->code_one != 0){

                        if($var->code_two != 0){


                            if($var->code_three != 0){


                                if($var->code_four != 0){

                                                $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                            ->where('code_two', $var->code_two)
                                                                            ->where('code_three', $var->code_three)
                                                                            ->where('code_four', $var->code_four)
                                                                            ->max('code_five');
                                                $var->code_five = $level + 1;
                                                $var->level = 5;

                                }else{

                                    $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                ->where('code_two', $var->code_two)
                                                                ->where('code_three', $var->code_three)
                                                        ->max('code_four');
                                    $var->code_four = $level + 1;
                                    $var->level = 4;

                                }
                            }else{

                                $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                                ->where('code_two', $var->code_two)
                                                        ->max('code_three');
                                $var->code_three = $level + 1;
                                $var->level = 3;

                            }
                        }else{
                            //Cuentas NIVEL 2
                        //level trae el valor de code_two mas alto
                            $level = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', $var->code_one)
                                                        ->max('code_two');

                            //luego que tenemos el valor del codigo two mas alto, le sumamos uno para crear el proximo
                            $var->code_two = $level + 1;
                            $var->level = 2;


                        }
                    }else{
                        return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                    }


                $date = Carbon::now();
                $datenow = $date->format('Y');

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();
                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $rate = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $rate = $company->rate;
                }


                return view('admin.accounts.createlevel',compact('var','datenow','rate'));

            }else{
                return redirect('/accounts/menu')->withDanger('No existe la Cuenta!');
        }


    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }

    }





   public function store(Request $request)
    {


        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

        $exist = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', request('code_one'))
                                ->where('code_two', request('code_two'))
                                ->where('code_three', request('code_three'))
                                ->where('code_four', request('code_four'))
                                ->where('period', request('period'))->first();

        if(!isset($exist)){


            $data = request()->validate([



                'period'            =>'required',
                'description'       =>'required',
                'type'              =>'required',
                'level'             =>'required',
                'balance_previus'   =>'required',

            ]);

            $var = new Account();
            $var->setConnection(Auth::user()->database_name);
            $var->code_one = request('code_one');
            $var->code_two = request('code_two');
            $var->code_three = request('code_three');
            $var->code_four = request('code_four');

            $var->period = request('period');
            $var->description = request('description');
            $var->type = request('type');
            $var->level = request('level');

            $valor_sin_formato = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));


            $var->balance_previus = $valor_sin_formato;

            $var->balance_previus =$valor_sin_formato;

            $var->rate = $valor_sin_formato_rate;

            if(request('coin') != 'BsS'){
                $var->coin = request('coin');

            }else{
                $var->coin = null;
            }


            $var->status =  "1";

            $var->save();

            return redirect('/accounts/menu')->withSuccess('Registro Exitoso!');

        }else{
            return redirect('/accounts/menu')->withDanger('La Cuenta ya existe!');
       }


    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }

    }


    public function storeNewLevel(Request $request)
    {



        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

        $exist = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', request('code_one'))
                                ->where('code_two', request('code_two'))
                                ->where('code_three', request('code_three'))
                                ->where('code_four', request('code_four'))
                                ->where('code_five', request('code_five'))
                                ->where('period', request('period'))->first();

        if(!isset($exist)){

            //dd($request);
            $data = request()->validate([



                'period'            =>'required',
                'description'       =>'required',
                'type'              =>'required',
                'level'             =>'required',


            ]);

            $var = new Account();
            $var->setConnection(Auth::user()->database_name);

            $var->code_one = request('code_one');
            $var->code_two = request('code_two');
            $var->code_three = request('code_three');
            $var->code_four = request('code_four');
            $var->code_five = request('code_five');

            $var->period = request('period');
            $var->description = request('description');
            $var->type = request('type');
            $var->level = request('level');

            $valor_sin_formato = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            $var->balance_previus =$valor_sin_formato;
            $var->rate = $valor_sin_formato_rate;

            if(request('coin') != 'BsS'){
                $var->coin = request('coin');

            }else{
                $var->coin = null;
            }

            $var->status =  "1";

            $var->save();


            return redirect('/accounts/menu')->withSuccess('Registro Exitoso!');

        }else{
            return redirect('/accounts/menu')->withDanger('La Cuenta ya existe!');
       }


    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }

    }





   public function edit(request $request,$id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

        $var = Account::on(Auth::user()->database_name)->find($id);

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $global->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

        return view('admin.accounts.edit',compact('var','rate'));

    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }
   }


   public function update(Request $request, $id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){


        $data = request()->validate([
            'description'       =>'required',
            'balance_previus'   =>'required',

        ]);

        $var = Account::on(Auth::user()->database_name)->findOrFail($id);

        $rate = request('rate');

        $sin_formato_balance_previus = str_replace(',', '.', str_replace('.', '', request('balance_previus')));
        $sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

        $var->description = request('description');
        $var->type = request('type');


        if(request('coin') != 'BsS'){
            //Dolares
            $var->coin = request('coin');
            //Guardo el monto de los dolares multiplicados por la tasa para que sea en bolivares
            //$var->balance_previus = $sin_formato_balance_previus * $sin_formato_rate;
        }else{
            //Bolivares
            $var->coin = null;
           // $var->balance_previus = $sin_formato_balance_previus;
        }

        $var->balance_previus = $sin_formato_balance_previus;

        $var->rate = $sin_formato_rate;

        $var->save();

        return redirect('/accounts/menu')->withSuccess('Actualizacion Exitosa!');


    }else{

        return redirect('/accounts/menu')->withDanger('No Tienes Permiso');
    }

    }


    public function year_end()
   {

        $calculationController = new CalculationWithCController();
        //$accounts = $this->calculation($coin);
        $accounts = $calculationController->calculate_all("bolivares");

        $date = Carbon::now();
        $datenow = $date->format('Y');
        $datenow2 = $date->format('Y-m-d');
        $last_detail_activate = DetailVoucher::on(Auth::user()->database_name)->where('status', 'C')->orderBy('id', 'desc')->first();
        $last_detail_desactivate = DetailVoucher::on(Auth::user()->database_name)->where('status', 'F')->orderBy('id', 'desc')->first();


        $first_detail_activate = DetailVoucher::on(Auth::user()->database_name)
        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
        ->where('detail_vouchers.status', 'C')->orderBy('header_vouchers.date', 'asc')->first();


        //Verifica que existan movimientos con los cuales hacer el cierre
        if(isset($last_detail_activate)){

            //Verifica que no se haga el cierre 2 veces un mismo dia, empty es para cuando no existen cierres anteriores
            if( empty($last_detail_desactivate->date_end) || ((isset($last_detail_desactivate->date_end)) && $last_detail_desactivate->date_end != $datenow2))
            {

                //dd($accounts[8]->balance_previus + $accounts[8]->debe - $accounts[8]->haber);
                foreach($accounts as $account){

                    $var = new AccountHistorial();
                    $var->setConnection(Auth::user()->database_name);

                    $var_account = Account::on(Auth::user()->database_name)->findOrFail($account->id);

                    $var->id_account =  $account->id;
                    $var->period =  $datenow;
                    $var->date_begin = $first_detail_activate->created_at->format('Y-m-d');
                    $var->date_end = $datenow2;

                    $var->balance_previous = $account->balance_previus;

                        $var->coin =  $account->coin;


                        //if($account->level == 5){
                            //$var->rate =  $this->tasa_calculada($account);
                        /*}else{
                            $var->rate =  $account->rate;
                        }*/
                        $var->rate =  $account->rate;

                        if($account->code_one <= 3){
                            $var->balance_current = $account->balance_previus + $account->debe - $account->haber;

                        }else{
                            $var->balance_current = $account->debe - $account->haber;
                        }


                        $var->debe =  $account->debe ?? 0;
                        $var->haber =  $account->haber ?? 0;
                        $var->debe_coin =  $account->dolar_debe ?? 0;
                        $var->haber_coin =  $account->dolar_haber ?? 0;

                    $var->status =  "F";

                    $var->save();
                    if($account->level == 5){

                        $var_account->balance_previus = $var->balance_current;
                        $var_account->rate = $var->rate;

                        $var_account->save();
                    }


                }

                DetailVoucher::on(Auth::user()->database_name)->where('status', 'C')
                        ->update(['status' => 'F' , 'date_end' => $datenow2]);


                return redirect('/accounts/menu')->withSuccess('Se realizo el Cierre Exitosamente!');

            }else{
                return redirect('/accounts/menu')->withDanger('No se puede realizar el cierre en un mismo dia!');
            }

        }else{
                return redirect('/accounts/menu')->withDanger('No hay movimientos para realizar un cierre!');
        }
}

public function tasa_calculada($account){

    $calculationController = new CalculationWithCController();

    $account_dolares = $calculationController->calculate_account_all($account,"dolares");

    $account_bolivares = $calculationController->calculate_account_all($account,"bolivares");



    if($account->code_one <= 3){
        $saldo_total_bs = $account_bolivares->balance_previus + $account_bolivares->debe - $account_bolivares->haber;

        $saldo_total_dolares = $account_dolares->balance_previus + $account_dolares->debe - $account_dolares->haber;
    }else{
        $saldo_total_bs = $account_bolivares->debe - $account_bolivares->haber;

        $saldo_total_dolares = $account_dolares->debe - $account_dolares->haber;
    }


    if($saldo_total_dolares != 0){
        $tasa_calculada = ($saldo_total_bs / ($saldo_total_dolares ?? 1));
    }else{
        $tasa_calculada = 0;
    }

   /*if($account->code_one == 1 && $account->code_two == 1 &&$account->code_three == 1 &&$account->code_four == 1 &&$account->code_five == 5){
        dd($account_dolares);
    }*/

    return $tasa_calculada;
}

public function calculation($coin,$ini,$fin)
{

    $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                     ->orderBy('code_two', 'asc')
                     ->orderBy('code_three', 'asc')
                     ->orderBy('code_four', 'asc')
                     ->orderBy('code_five', 'asc')
                     ->get();

    if($ini == null or $fin == null){

        $ini = '1980-01-01';
        $date = Carbon::now();
        $fin = $date->format('Y-m-d');


    }else{
        $ini = $ini;
        $fin = $fin;
    }



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
                                ($var->code_four == 1) && ($var->code_five == 1)){

                                    $var = $this->calculation_superavit($var,4,$coin,$ini,$fin);

                                }else
                                {
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */
                                    if($coin == 'bolivares')
                                    {
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
                                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);


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
                                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);

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
                                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);

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
                                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);

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
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);

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
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C',$ini,$fin]);

                                        if(($var->balance_previus != 0) && ($var->rate !=0)){
                                            $var->balance_previus =  $var->balance_previus / $var->rate;
                                        }


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


                                }

                            }else{

                                //Calculo de superavit
                                if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) &&
                                ($var->code_four == 1) ){
                                    $var = $this->calculation_superavit($var,4,$coin,$ini,$fin);
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
                                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

                                    $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

                                    $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

                                                    $var->balance =  $var->balance_previus;


                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);


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
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                    FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C',$ini,$fin]);

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
                            }
                        }else{
                            //Calculo de superavit
                            if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1)){
                                $var = $this->calculation_superavit($var,4,$coin,$ini,$fin);
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
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND

                                                d.status = ?
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);

                             $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND
                                                d.status = ?
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);

                                }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);

                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND

                                        d.status = ?
                                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            d.status = ?
                                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,'C',$ini,$fin]);



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
                        //Calculo de superavit
                        if(($var->code_one == 3) && ($var->code_two == 2)){
                            $var = $this->calculation_superavit($var,4,$coin,$ini,$fin);
                        }else{

                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,'C',$ini,$fin]);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,'C',$ini,$fin]);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                                '
                                                , [$var->code_one,$var->code_two,'C',$ini,$fin]);

                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                    '
                                    , [$var->code_one,$var->code_two,'C',$ini,$fin]);

                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                    '
                                    , [$var->code_one,$var->code_two,'C',$ini,$fin]);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                    '
                                    , [$var->code_one,$var->code_two,'C',$ini,$fin]);

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
                    //Calcular patrimonio con las cuentas mayores o iguales a 3.0.0.0.0
                    if($var->code_one == 3){
                        $var = $this->calculation_capital($var,$coin,$ini,$fin);

                    }else{
                        if($coin == 'bolivares'){
                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                            '
                                            , [$var->code_one,'C',$ini,$fin]);
                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                            '
                                            , [$var->code_one,'C',$ini,$fin]);

                            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            d.status = ?
                                            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                            '
                                            , [$var->code_one,'C',$ini,$fin]);

                            }else{
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                FROM accounts a
                                INNER JOIN detail_vouchers d
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                '
                                , [$var->code_one,'C',$ini,$fin]);

                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                FROM accounts a
                                INNER JOIN detail_vouchers d
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                '
                                , [$var->code_one,'C',$ini,$fin]);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d
                                    ON d.id_account = a.id
                                WHERE a.code_one = ? AND
                                d.status = ?
                                AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                                '
                                , [$var->code_one,'C',$ini,$fin]);

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

public function calculation_capital($var,$coin,$ini,$fin)
{
    if($coin == 'bolivares'){
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                        '
                        , [$var->code_one,'C',$ini,$fin]);
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                        '
                        , [$var->code_one,'C',$ini,$fin]);

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                        '
                        , [$var->code_one,'C',$ini,$fin]);

        }else{
            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
            FROM accounts a
            INNER JOIN detail_vouchers d
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
            '
            , [$var->code_one,'C',$ini,$fin]);

            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
            FROM accounts a
            INNER JOIN detail_vouchers d
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
            '
            , [$var->code_one,'C',$ini,$fin]);

            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                        FROM accounts a
                        INNER JOIN detail_vouchers d
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
            '
            , [$var->code_one,'C',$ini,$fin]);

        }
        $total_debe = $total_debe[0]->debe;
        $total_haber = $total_haber[0]->haber;
        $var->debe = $total_debe;
        $var->haber = $total_haber;

        $total_balance = $total_balance[0]->balance;

        $var->balance = $total_balance;

        return $var;
}

public function calculation_superavit($var,$code,$coin,$ini,$fin)
{
    //Primero el suma todos los movimientos del 4 para arriba, y luego suma los movimientos que se le hayan hecho al superavit
 if($coin == 'bolivares'){
     $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                     FROM accounts a
                     INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one >= ? AND
                     d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$code,'C',$ini,$fin]);
                     
     $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                     FROM accounts a
                     INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one >= ? AND
                     d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$code,'C',$ini,$fin]);
    $total_debe2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                     FROM accounts a
                     INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one = 3 AND a.code_two = 2 AND a.code_three = 1 AND a.code_four = 1 AND a.code_five = 1 AND
                     d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$code,'C',$ini,$fin]);
     $total_haber2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                     FROM accounts a
                     INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one = 3 AND a.code_two = 2 AND a.code_three = 1 AND a.code_four = 1 AND a.code_five = 1 AND
                     d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$code,'C',$ini,$fin]);

    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                     FROM accounts a
                     INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one = ?
                     AND a.code_two = ?
                     AND d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$var->code_one,$var->code_two,'C',$ini,$fin]);


     }else{
         $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
         FROM accounts a
         INNER JOIN detail_vouchers d
             ON d.id_account = a.id
         WHERE a.code_one >= ? AND
         d.status = ?
         AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
         '
         , [$code,'C',$ini,$fin]);

         $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
         FROM accounts a
         INNER JOIN detail_vouchers d
             ON d.id_account = a.id
         WHERE a.code_one >= ? AND
         d.status = ?
         AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
         '
         , [$code,'C',$ini,$fin]);

         $total_debe2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
         FROM accounts a
         INNER JOIN detail_vouchers d
             ON d.id_account = a.id
         WHERE a.code_one = 3 AND a.code_two = 2 AND a.code_three = 1 AND a.code_four = 1 AND a.code_five = 1 AND
         d.status = ?
         AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
         '
         , [$code,'C',$ini,$fin]);

         $total_haber2 =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
         FROM accounts a
         INNER JOIN detail_vouchers d
             ON d.id_account = a.id
         WHERE a.code_one = 3 AND a.code_two = 2 AND a.code_three = 1 AND a.code_four = 1 AND a.code_five = 1 AND
         d.status = ?
         AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
         '
         , [$code,'C',$ini,$fin]);

         $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        INNER JOIN detail_vouchers d
                         ON d.id_account = a.id
                     WHERE a.code_one = ?
                     AND a.code_two = ?
                     AND d.status = ?
                     AND SUBSTR(d.created_at,1,10) BETWEEN ? AND ?
                     '
                     , [$var->code_one,$var->code_two,'C',$ini,$fin]);

     }

     $total_debe = $total_debe[0]->debe + $total_debe2[0]->debe;
     $total_haber = $total_haber[0]->haber + $total_haber2[0]->haber;
     $var->debe = $total_debe;
     $var->haber = $total_haber;

     $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;

     $var->balance = $var->balance_previus;

     return $var;

}

public function calculation_superavit_level4($var,$code,$coin)
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

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                        FROM accounts a
                                        WHERE a.code_one = ? AND
                                        a.code_two = ?  AND
                                        a.code_three = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

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

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        WHERE a.code_one = ? AND
        a.code_two = ?  AND
        a.code_three = ? AND
        a.code_four = ?
        '
        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);


    }

    $total_debe = $total_debe[0]->debe;
    $total_haber = $total_haber[0]->haber;
    $var->debe = $total_debe;
    $var->haber = $total_haber;

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

public function calculation_superavit_level3($var,$code,$coin)
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

        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
        FROM accounts a
        WHERE a.code_one = ? AND
        a.code_two = ?  AND
        a.code_three = ?
        '
        , [$var->code_one,$var->code_two,$var->code_three]);


    }

    $total_debe = $total_debe[0]->debe;
    $total_haber = $total_haber[0]->haber;
    $var->debe = $total_debe;
    $var->haber = $total_haber;

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

public function calculation_superavit_level2($var,$code,$coin)
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

    //$total_balance = $total_balance[0]->balance;
    $total_balance = $total_balance[0]->balance;
    $var->balance_previus = $total_balance;
    $var->balance = $var->balance_previus;

    return $var;

}

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
       //
   }
}
