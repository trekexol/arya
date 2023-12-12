<?php

namespace App\Http\Controllers;

use App\Account;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\Calculations\CalculationController;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class DetailVoucherController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('create');
        $this->middleware('valimodulo:Ajustes Contables');
   }

  /* public function index()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       if($users_role == '1'){
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        }

       return view('admin.detailvouchers.index');
   }*/


   public function create(Request $request,$coin,$id_header = null,$id_account = null)
   {

    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        $header_disponible = HeaderVoucher::on(Auth::user()->database_name)->orderBy('id','desc')->first();
        $header_number = 1;

        if(isset($header_disponible)){
            $header_number = $header_disponible->id + 1;
        }

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if(empty($coin)){
            $coin = "bolivares";
        }
        elseif($coin != 'bolivares'){
            $coin = 'dolares';
        }

        $header = null;
        $detailvouchers = null;
        $account = null;
        $detailvouchers_last = null;
        $tasa_calculada = null;
        $saldo_total_bs = null;
        $saldo_total_dolares = null;

        if(isset($id_header)){
            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);

            if(isset($header) && ($header->status != 'X')){
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)
                ->join('accounts','accounts.id','id_account')
                ->where('detail_vouchers.status','!=','X')
                ->orderBy('detail_vouchers.debe','desc')
                ->orderBy('accounts.code_one','desc')
                ->orderBy('accounts.code_two','asc')
                ->orderBy('accounts.code_three','asc')
                ->orderBy('accounts.code_four','asc')
                ->orderBy('accounts.code_five','asc')
                ->select('detail_vouchers.*','accounts.code_one','accounts.code_two','accounts.code_three','accounts.code_four','accounts.code_five')
                ->get();



               // dd($detailvouchers);

                //se usa el ultimo movimiento agregado de la cabecera para tomar cual fue la tasa que se uso
                $detailvouchers_last = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->orderBy('id','desc')->first();

                $detailvouchers_first = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->orderBy('id','asc')->first();
                if(isset($id_account)){
                    $account = Account::on(Auth::user()->database_name)->find($id_account);

                    if(empty($detailvouchers_first)){
                        $calculationController = new CalculationController();

                        $account_bolivares = $calculationController->calculate_account_all($account,"bolivares");

                        $account_dolares = $calculationController->calculate_account_all($account,"dolares");

                        $saldo_total_bs = $account_bolivares->balance_previus + $account_bolivares->debe - $account_bolivares->haber;

                        $saldo_total_dolares = $account_dolares->balance_previus + $account_dolares->debe - $account_dolares->haber;

                        if($saldo_total_dolares != 0){
                            $tasa_calculada = ($saldo_total_bs / ($saldo_total_dolares ?? 1));
                        }else{
                            $tasa_calculada = null;
                        }
                    }else{
                        $tasa_calculada = $detailvouchers_first->tasa;
                    }



               }
            }else{
               return redirect('/detailvouchers/register/bolivares')->withDanger('Este movimiento fue Deshabilitado!');
            }
        }

        return view('admin.detailvouchers.create',compact('saldo_total_bs','saldo_total_dolares','tasa_calculada','detailvouchers_last','account','datenow','header_number','coin','bcv','header','detailvouchers','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }

   public function createvalidation($coin,$id_header = null,$id_account = null)
   {
    dd('v1');    
    $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
       // $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->get();
        $header_disponible = HeaderVoucher::on(Auth::user()->database_name)->orderBy('id','desc')->first();
        $header_number = 1;

        if(isset($header_disponible)){
            $header_number = $header_disponible->id + 1;
        }

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automaticaheadervouchers.store'
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }


        if(($coin == 'bolivares') ){
            $coin = 'bolivares';
        }else{
            //$bcv = null;
            $coin = 'dolares';
        }
        $header = null;
        $detailvouchers = null;
        $account = null;
        $detailvouchers_last = null;

        $details = HeaderVoucher::on(Auth::user()->database_name)
                                    ->join('detail_vouchers','detail_vouchers.id_header_voucher','header_vouchers.id')
                                    ->where('detail_vouchers.debe','<>','detail_vouchers.haber')
                                    ->where('detail_vouchers.id_header_voucher','<>',1)
                                    ->select('detail_vouchers.*')
                                    ->get();

        if(isset($id_header)){
            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();
            //se usa el ultimo movimiento agregado de la cabecera para tomar cual fue la tasa que se uso
            $detailvouchers_last = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->orderBy('id','desc')->first();
            if(isset($id_account)){
                $account = Account::on(Auth::user()->database_name)->find($id_account);
            }
        }


        return view('admin.detailvouchers.create',compact('detailvouchers_last','account','datenow','header_number','coin','bcv','header','detailvouchers'));
   }


   public function createselect($id_header)
   {
    dd('v2');
        $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);

        if(isset($header)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)->get();

            return view('admin.detailvouchers.create',compact('header','datenow','detailvouchers'));
        }else{
            return redirect('/detailvouchers/register')->withDanger('No existe el Header!');
        }

   }




   public function selectaccount($coin,$id_header,$id_detail)
   {

    dd('v3');
       if($id_header != -1){

            $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);
            $accounts = $this->calculation($coin);

            if($id_detail == 'detail'){
                $id_detail = null;
            }

            return view('admin.detailvouchers.selectaccount',compact('coin','accounts','header','id_detail'));

       }else{
        return redirect('/detailvouchers/register/'.$coin.'')->withDanger('Seleccione informacion de Cabecera!');
       }

   }

   public function selectheader()
   {
        $headervouchers = HeaderVoucher::on(Auth::user()->database_name)->where('status','LIKE','U')->get();


        return view('admin.detailvouchers.selectheadervouche',compact('headervouchers'));
   }


   public function contabilizar($coin,$id_header)
   {

        //  dd($id_header);
        $header = HeaderVoucher::on(Auth::user()->database_name)->find($id_header);

        if(isset($header)){

            $affected = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->whereIn('status',['1','N'])
            ->where('id_header_voucher', '=', $id_header)
            ->update(array('status' => 'C'));

            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$id_header)
            ->join('accounts','accounts.id','id_account')
            ->where('detail_vouchers.status','!=','X')
            ->orderBy('detail_vouchers.debe','desc')
            ->orderBy('accounts.code_one','desc')
            ->orderBy('accounts.code_two','asc')
            ->orderBy('accounts.code_three','asc')
            ->orderBy('accounts.code_four','asc')
            ->orderBy('accounts.code_five','asc')
            ->select('detail_vouchers.*','accounts.code_one','accounts.code_two','accounts.code_three','accounts.code_four','accounts.code_five')
            ->get();


             /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
             foreach($detailvouchers as $var){

                $account = Account::on(Auth::user()->database_name)->findOrFail($var->id_account);

                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }
             }

             /*----------------------------- */

            $company = Company::on(Auth::user()->database_name)->find(1);
            $global = new GlobalController();

            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            return view('admin.detailvouchers.create',compact('bcv','coin','header','datenow','detailvouchers'));


        }else{
            return redirect('/detailvouchers/register/bolivares')->withDanger('No existe el Header!');
        }

   }

   public function store(Request $request)
    {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
         $data = request()->validate([




                'id_account'     =>'required',

                'id_header_voucher'     =>'required',
                'debe'                  =>'required',
                'haber'                 =>'required',



            ]);

            $var = new DetailVoucher();
            $var->setConnection(Auth::user()->database_name);

            $coin = request('coin');

            $var->id_account = request('id_account');
            $var->id_header_voucher = request('id_header_voucher');
            $var->user_id = request('id_user');

            $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', request('debe')));
            $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', request('haber')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            if($coin == 'bolivares'){
                $var->debe = $valor_sin_formato_debe;
                $var->haber = $valor_sin_formato_haber;
                $var->tasa = $valor_sin_formato_rate;

            }else{
                $var->debe = $valor_sin_formato_debe * $valor_sin_formato_rate;
                $var->haber = $valor_sin_formato_haber * $valor_sin_formato_rate;
                $var->tasa = $valor_sin_formato_rate;

            }

            $var->status =  "N";

            $var->save();

            return redirect('/detailvouchers/register/'.$coin.'/'.$var->id_header_voucher.'')->withSuccess('Agregado el movimiento Correctamente, para procesarlo debe contabilizar!');
        }else{
            return redirect('/detailvouchers/register/bolivares')->withDanger('No Tienes Permiso');

        }
    }



   public function edit(request $request,$coin,$id,$id_account = null)
   {


    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

        $var = DetailVoucher::on(Auth::user()->database_name)->find($id);

        if(isset($id_account)){
            $var->id_account = $id_account;
        }


        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if($coin != 'bolivares'){
            $var->debe = $var->debe / $var->tasa;
            $var->haber = $var->haber / $var->tasa;
        }

        return view('admin.detailvouchers.edit',compact('var','bcv','coin','id_account'));

    }else{
        return redirect('/detailvouchers/register/bolivares')->withDanger('No Tienes Permiso');

    }

   }


   public function update(Request $request, $id = null)
    {

        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
        $data = request()->validate([

            'type'      =>'required',
            'amount'    =>'required',
            'rate'    =>'required',
            'coin'    =>'required',
            'id_account'    =>'required',


        ]);

        if(isset($id)){
            $var = DetailVoucher::on(Auth::user()->database_name)->findOrFail($id);

            $coin = request('coin');
            $type = request('type');
            $id_account = request('id_account');

            if($id_account != -1){
                $var->id_account = $id_account;
            }

            $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));
            $valor_sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            if($coin == 'bolivares'){
                if($type == 'debe'){
                    $var->debe = $valor_sin_formato_amount;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->haber = 0;
                }else{
                    $var->haber = $valor_sin_formato_amount;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->debe = 0;
                }
            }else{
                if($type == 'debe'){
                    $var->debe = $valor_sin_formato_amount * $valor_sin_formato_rate;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->haber = 0;
                }else{
                    $var->haber = $valor_sin_formato_amount * $valor_sin_formato_rate;
                    $var->tasa = $valor_sin_formato_rate;
                    $var->debe = 0;
                }
            }
            $var->save();

            $affected = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->whereIn('status',['1','N','C'])
            ->where('id_header_voucher', '=', $var->id_header_voucher)->update(array('status' => 'N'));

            $this->check_exist_movement_in_account();

            return redirect('/detailvouchers/register/'.$coin.'/'.$var->id_header_voucher.'')->withSuccess('Actualizacion Exitosa!');
        }

    }else{
            return redirect('/detailvouchers/register/bolivares')->withDanger('No Tienes Permiso');

        }

    }

    public function check_exist_movement_in_account()
    {
        $account_with_movement = Account::on(Auth::user()->database_name)->where('status','M')->get();

        foreach($account_with_movement as $var){
           $exist_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_account',$var->id)->first();

           if(!isset($exist_detail)){

                $account = Account::on(Auth::user()->database_name)->findOrFail($var->id);
                $account->status = '1';
                $account->save();

           }
        }
    }

   public function destroy($id = null)
   {

       if(isset($id)){
        $header = HeaderVoucher::on(Auth::user()->database_name)->findOrFail($id);

        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$header->id)
            ->update(['status' => 'X']);

        $header->status = "X";
        $header->save();

        return redirect('/detailvouchers/register/bolivares')->withDanger('Se deshabilitó con éxito el movimiento!');

       }else{

        return redirect('/detailvouchers/register/bolivares')->withDanger('Debe buscar un movimiento primero !!');

       }

   }

   public function check_header(Request $request){


    $coin = "bolivares";
    $date = Carbon::now();
    $datenow = $date->format('Y-m-d');
    $company = Company::on(Auth::user()->database_name)->find(1);
    $global = new GlobalController();
    //Si la taza es automatica
   /* if($company->tiporate_id == 1){

    }else{
        //si la tasa es fija
        $bcv = $company->rate;
    }*/
    $bcv = $global->search_bcv();

    $id = $request->id_detail_modal;

    $header = HeaderVoucher::on(Auth::user()->database_name)->findOrFail($id);
    $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$header->id)->first();

    $id_delete = 0;
    $type_delete = "";
    $message_delete = "";

    if(isset($header->id_anticipo)){
        $id_delete = $header->id_anticipo;
        $type_delete = "anticipo";
        $message_delete = "Este movimiento posee el anticipo Numero ".$header->id_anticipo;
    }else{
        if(isset($detail->id_invoice)){
            $id_delete = $detail->id_invoice;
            $type_delete = "factura";
            $message_delete = "Este movimiento posee la factura Numero ".$detail->id_invoice;
        }elseif(isset($detail->id_expense)){
            $id_delete = $detail->id_expense;
            $type_delete = "compra";

            if(substr($header->description, 0, 4) == "Pago"){
                $message_delete = "Este movimiento posee Los Pagos de la Compra Numero. ".$detail->id_expense;
            }else{
                $message_delete = "Este movimiento posee la compra Numero ".$detail->id_expense;
            }
            }
    }


    if($id_delete == 0){

        $this->destroy($id);
        return redirect('/detailvouchers/register/bolivares')->withDanger('Se deshabilitó con éxito el movimiento!');

    }else{
        return view('admin.detailvouchers.create',compact('datenow','coin','bcv','header','type_delete','id_delete','message_delete'));
    }



   }

   public function disable(Request $request){


        $id_header = $request->id_header_modal;
        $id_delete = $request->id_modal;
        $type_modal = $request->type_modal;

        $header = HeaderVoucher::on(Auth::user()->database_name)->findOrFail($id_header);


        if(isset($type_modal) && ($type_modal == "anticipo")){
            $anticipo = new AnticipoController();
            $anticipo->delete_anticipo_with_id($id_delete);

        }else if(isset($type_modal) && ($type_modal == "factura")){
           /* $invoice = new QuotationController();
            $invoice->reversar_quotation_with_id($id_delete);*/
            $this->destroy($id_header);
        }else if(isset($type_modal) && ($type_modal == "compra")){
            $expense = new ExpensesAndPurchaseController();

            if(substr($header->description, 0, 4) == "Pago"){
                /*/$expense_payment = new PaymentExpenseController();
                $expense_payment->deleteAllPaymentsWithId($id_delete);*/
            }else{
                $expense->reversar_expense_with_id($id_delete);
            }

            $this->destroy($id_header);
        }



        return redirect('/detailvouchers/register/bolivares')->withSuccess('Se deshabilitó con éxito el movimiento!');
   }

   public function deleteDetail(Request $request)
    {

        $header = request('header_modal');
        $id = request('id_detail_modal');

       /* $detail = DetailVoucher::on(Auth::user()->database_name)
        ->where('id',$id)
        ->where('',$header);
        $detail->status = "X";
        $detail->save();*/

        $coin = request('coin_modal');

        DetailVoucher::on(Auth::user()->database_name)->where('id',$id)->where('id_header_voucher',$header)
        ->update(['status' => 'X']);



        return redirect('/detailvouchers/register/'.$coin.'/'.$header.'')->withDanger('Eliminacion exitosa!!');

    }


   public function listheader(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{

                $respuesta = HeaderVoucher::on(Auth::user()->database_name)->select('id','description')->where('id',$var)->orderBy('description','asc')->get();
                return response()->json($respuesta,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }

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
                                     /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */

                                     if($coin == 'bolivares'){
                                        $total_debe =   0;
                                        $total_haber =  0;

                                        $total_dolar_debe =   0;

                                        $total_dolar_haber =   0;

                                                        $var->balance =  $var->balance_previus;


                                        }else{
                                            $total_debe =   0;

                                            $total_haber =   0;




                                        }
                                        $total_debe = 0;
                                        $total_haber = 0;
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
                                    $total_debe =   0;
                                    $total_haber =  0;

                                    $total_dolar_debe =   0;

                                    $total_dolar_haber =  0;

                                                    $var->balance =  $var->balance_previus;

                                    $total_balance =   0;

                                    }else{
                                        $total_debe =  0;

                                        $total_haber =   0;

                                        $total_balance =   0;

                                        /*if(($var->balance_previus != 0) && ($var->rate !=0))
                                        $var->balance =  $var->balance_previus / $var->rate;*/
                                    }
                                    $total_debe = 0;
                                    $total_haber = 0;
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

                                    $total_balance = 0;
                                    $var->balance = $total_balance;
                                }
                            }else{

                                if($coin == 'bolivares'){
                                $total_debe =  0;
                                $total_haber =  0;

                                $total_balance =   0;

                                }else{
                                        $total_debe =  0;

                                        $total_haber =   0;

                                        $total_balance =  0;

                                    }
                                    $total_debe = 0;
                                    $total_haber = 0;

                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;



                                    $total_balance = 0;
                                    $var->balance = $total_balance;


                            }
                        }else{

                            if($coin == 'bolivares'){
                                $total_debe =   0;
                                $total_haber =   0;

                                $total_balance =   0;

                                }else{
                                    $total_debe =   0;

                                    $total_haber =   0;

                                    $total_balance =  0;

                                }

                                $total_debe = 0;
                                $total_haber = 0;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;



                                $total_balance = 0;
                                $var->balance = $total_balance;
                        }
                    }else{
                        if($coin == 'bolivares'){
                            $total_debe =  0;
                            $total_haber =   0;

                            $total_balance =  0;

                            }else{
                                $total_debe =  0;

                                $total_haber =  0;

                                $total_balance =  0;

                            }
                            $total_debe = 0;
                            $total_haber = 0;
                            $var->debe = $total_debe;
                            $var->haber = $total_haber;

                            $total_balance = 0;

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

}
