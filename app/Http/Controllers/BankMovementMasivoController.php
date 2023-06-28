<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use App;
use App\Account;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\QuotationPayment;

use App\Imports\TempMovimientosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Calculations\AccountCalculationController;
use App\ExpensePayment;
use App\Client;
use App\Anticipo;

use App\Provider;
use Carbon\Carbon;
use App\Quotation;
use App\TempMovimientos;
class BankMovementMasivoController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Movimientos Bancarios Masivos');
   }

   public function index(Request $request)
   {


    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');

    $bancosmasivos   = TempMovimientos::on(Auth::user()->database_name)
    ->select('banco')
    ->where('estatus','0')
    ->groupBy('banco')
    ->orderBy('banco','asc')->get();

       return view('admin.bankmovementsmasivo.index',compact('bancosmasivos','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }


/***********CARGA MASIVA DE MOVIMIENTOS *****/


public function importmovimientos(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if($request->ajax()){
        try{


                $banco = $request->banco;
                $file = $request->file('file');
                $extension = $request->file('file')->extension();


    if(($banco == 'Bancamiga' OR $banco == 'Bancamigausd'  OR $banco == 'Chase' OR $banco == 'Banco Banesco' OR $banco == 'Banco del Tesoro') AND $extension == 'xlsx'){

    $import = new TempMovimientosImport($banco);
    Excel::import($import, $file);
    $resp['error'] = $import->estatus;
    $resp['msg'] = $import->mensaje;
      return response()->json($resp);

    }

    elseif(($banco == 'Mercantil' OR $banco == 'BOFA' OR $banco == 'Banco Banplus' OR $banco == 'Banplus Custodia') AND $extension == 'txt'){
        $import = new TempMovimientosImport($banco);
        Excel::import($import, $file);
        $resp['error'] = $import->estatus;
        $resp['msg'] = $import->mensaje;
        return response()->json($resp);
    }else{

        $resp['error'] = false;
        $resp['msg'] = 'Verifique Formato. <br> Banesco,Bancamiga,Banco del Tesoro .xlsx <br> Mercantil y Banplus .txt <br> Chase y BOFA .csv';

        return response()->json($resp);
    }

        }catch(\error $error){
            $resp['error'] = false;
	        $resp['msg'] = 'Verifique el Archivo.';

            return response()->json($resp);
        }
    }


}



public function facturasmovimientos(Request $request){

    $data = explode('/',$request->value);
    $valormovimiento = $data[0];
    $idmovimiento = $data[1];
    $fechamovimiento = $data[2];
    $bancomovimiento = $data[3];
    $tipo = $data[4];


    if($tipo == 'match'){
        $moneda = $data[5];
        $bcv = $data[6];
        $monto = $data[0] / $bcv;
        $conta = $data[7];

        $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
        ->where('date_billing','<>',null)
        ->where('number_invoice','<>',null)
        ->where('status','=','P')
        ->where('amount_with_iva','=',$monto)
        ->get();


        return View::make('admin.bankmovementsmasivo.tablafactura',compact('quotations','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo','conta'))->render();


    }elseif($tipo == 'contra'){
        $global = new GlobalController();
        $bcv = $global->search_bcv();
        $montohaber = $data[5];
        $referenciamovimiento = $data[6];
        $moneda = $data[7];
        $descripcionbanco = $data[8];
        $contrapartidas     = Account::on(Auth::user()->database_name)
        ->where('code_one', '<>',0)
        ->where('code_one', '<>',4)
        ->where('code_two', '<>',0)
        ->where('code_three', '<>',0)
        ->where('code_four', '<>',0)
        ->where('code_five', '=',0)
    ->orderBY('description','asc')->pluck('description','id')->toArray();



        return View::make('admin.bankmovementsmasivo.tablafactura',compact('bcv','contrapartidas','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo','montohaber','referenciamovimiento','moneda','descripcionbanco'))->render();

    }elseif($tipo == 'transferencia'){

        $montohaber = $data[5];
        $referenciamovimiento = $data[6];
        $moneda = $data[7];
        $descripcionbanco = $data[8];

        $account = Account::on(Auth::user()->database_name)->where('description', $bancomovimiento)->first();

        if(isset($account)){

            $counterparts     =     DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                        ->where('code_two', 1)
                                        ->where('code_three', 1)
                                        ->whereIn('code_four', [1,2])
                                        ->where('code_five','<>',0)
                                        ->orderBY('description','asc')
                                        ->get();
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $company = Company::on(Auth::user()->database_name)->find(1);
            $global = new GlobalController();
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }
        return View::make('admin.bankmovementsmasivo.tablafactura',compact('bcv','account','datenow','counterparts','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo','montohaber','referenciamovimiento','moneda','descripcionbanco'))->render();

    }



        }
        elseif($tipo == 'deposito'){

            $global = new GlobalController();
            $bcv = $global->search_bcv();

            $montohaber = $data[5];
            $referenciamovimiento = $data[6];
            $moneda = $data[7];
            $descripcionbanco = $data[8];
            $contrapartidas     = Account::on(Auth::user()->database_name)
            ->where('code_one', '<>',0)
            ->where('code_one', '<>',4)
            ->where('code_two', '<>',0)
            ->where('code_three', '<>',0)
            ->where('code_four', '<>',0)
            ->where('code_five', '=',0)
        ->orderBY('description','asc')->pluck('description','id')->toArray();



            return View::make('admin.bankmovementsmasivo.tablafactura',compact('bcv','contrapartidas','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo','montohaber','referenciamovimiento','moneda','descripcionbanco'))->render();

        }

}

public function add_movementfacturas($bcv,$id_header,$id_account,$id_invoice,$id_user,$debe,$haber){

    $detail = new DetailVoucher();
    $detail->setConnection(Auth::user()->database_name);


    $detail->id_account = $id_account;
    $detail->id_header_voucher = $id_header;
    $detail->user_id = $id_user;
    $detail->tasa = $bcv;
    $detail->id_invoice = $id_invoice;

  /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
    $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/


    $detail->debe = $debe;
    $detail->haber = $haber;


    $detail->status =  "C";

     /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */

        $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

        if($account->status != "M"){
            $account->status = "M";
            $account->save();
        }


    $detail->save();

}

public function procesarfact(Request $request){

    if($request->ajax()){
        try{

            /**********VERIFICO QUE LA FACTURA EXITE Y QUE EL MOVIMIENTO Y
             MONTOS SEAN EXACTAMENTE IGUALES CON SUS RESPECTIVOS ID *****/


            $quotations = Quotation::on(Auth::user()->database_name)
            ->join('tempmovimientos','tempmovimientos.'.$request->conta,'amount_with_iva')
            ->where('tempmovimientos.id_temp_movimientos',$request->idmovimiento)
            ->where('date_billing','<>',null)
            ->where('number_invoice','=',$request->nrofactura)
            ->where('status','=','P')
            ->where('amount_with_iva','=',$request->montoiva)
            ->where('id','=',$request->id)->first();


            if($quotations){

                $quotations->status = 'C';
                $quotations->save();

                if($request->conta == 'debe'){


                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = "Cobro Masivo Match de Bienes o servicios.";
                $header_voucher->date = $request->fechamovimiento;
                $header_voucher->status =  "1";
                $header_voucher->save();

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($request->tasa,$header_voucher->id,$account_cuentas_por_cobrar->id,$request->id,Auth::user()->id,0,$request->montoiva);
                }

                //Banco

                $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', $request->bancomovimiento)->first();

                if(isset($account_subsegmento)){
                    $this->add_movementfacturas($request->tasa,$header_voucher->id,$account_subsegmento->id,$request->id,Auth::user()->id,$request->montoiva,0);
                }

            }

                $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                ->find($request->idmovimiento,['id_temp_movimientos', 'estatus']);
                $movimientosmasivos->estatus = 1;
                $movimientosmasivos->save();

                return response()->json(true,200);

            }else{

                return response()->json(false,500);
            }



        }catch(\error $error){
            return response()->json(false,500);
        }
    }
}


/************** CONTRAPARTIDA NEW*******/

public function listcontrapartidanew(Request $request, $id_var = null){
    //validar si la peticion es asincrona
    if($request->ajax()){
        try{

            $account = Account::on(Auth::user()->database_name)->find($id_var);
            $subcontrapartidas = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$account->code_one)
                                                                ->where('code_two',$account->code_two)
                                                                ->where('code_three',$account->code_three)
                                                                ->where('code_four',$account->code_four)
                                                                ->where('code_five','<>',0)
                                                                ->orderBy('description','asc')->get();

            return response()->json($subcontrapartidas,200);


        }catch(\Throwable $th){
            return response()->json(false,500);
        }
    }

}




public function procesarcontrapartidanew(Request $request){

    $resp = array();
    $resp['error'] = false;
    $resp['msg'] = '';

    if($request->ajax()){
        try{


            $bcv = $request->rate;

            if($request->valorhaber == 0){
                ///BANCO POR DEBE
                $montodelacontra = 0;
                $validarcontra = FALSE;
                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                    $montocontra =  $request->input('montocontra.' . $i);
                    if($valorcontra == 0){

                        $validarcontra = TRUE;

                     }else{

                        $montodelacontra = $montocontra + $montodelacontra;

                     }


                 }
                 $valordebe = (string) $request->valordebe;
                 $montodelacontra = (string) $montodelacontra;

                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $valordebe){

                    $resp['error'] = false;
                    $resp['msg'] = 'El Total de las contrapartidas debe ser igual al monto del movimiento bancario';
                    return response()->json($resp);
                }else{

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = $request->descripcionbanco;
                $header_voucher->reference = $request->referenciabanco;
                $header_voucher->date = $request->fechamovimiento;
                $header_voucher->status =  "1";
                $header_voucher->save();

                $global = new GlobalController();

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', $request->banco)->first();


                    /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $request->valordebe * $bcv;
                }else{
                    $amount = $request->valordebe;
                }
                /*** */

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,0,$amount);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);

                         /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $montocontra * $bcv;
                }else{
                    $amount = $montocontra;
                }
                /*** */

                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,$amount,0);

                }

                $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                                ->where('id_temp_movimientos',$request->idmovimiento)
                                ->update(['estatus' => '1']);

                $resp['error'] = True;
                $resp['msg'] = 'Movimiento Consolidado Exitosamente';

                return response()->json($resp);


                }


            }elseif($request->valordebe == 0){

                $montodelacontra = 0;
                $validarcontra = FALSE;
                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                    $montocontra =  $request->input('montocontra.' . $i);
                    if($valorcontra == 0){

                        $validarcontra = TRUE;

                     }else{

                        $montodelacontra = $montocontra + $montodelacontra;

                     }


                 }

                 $valorhaber = (string) $request->valorhaber;
                 $montodelacontra = (string) $montodelacontra;


                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $valorhaber){

                    $resp['error'] = false;
                    $resp['msg'] = 'El Total de las contrapartidas debe ser igual al monto del movimiento bancario';

                    return response()->json($resp);
                }else{

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = $request->descripcionbanco;
                $header_voucher->reference = $request->referenciabanco;
                $header_voucher->date = $request->fechamovimiento;
                $header_voucher->status =  "1";
                $header_voucher->save();

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', $request->banco)->first();

                          /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $request->valorhaber * $bcv;
                }else{
                    $amount = $request->valorhaber;
                }
                /*** */


                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,$amount,0);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);

                                 /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $montocontra * $bcv;
                }else{
                    $amount = $montocontra;
                }
                /*** */

                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,0,$amount);

                }


                $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                                ->where('id_temp_movimientos',$request->idmovimiento)
                                ->update(['estatus' => '1']);

                $resp['error'] = True;
                $resp['msg'] = 'Movimiento Consolidado Exitosamente';

                return response()->json($resp);


                }





            }else{
                $resp['error'] = False;
                $resp['msg'] = 'Error El Movimiento no Tiene Valor';

                return response()->json($resp);

            }







        }catch(\error $error){
            return response()->json(false,500);
        }
    }
}




public function guardartransferencia(Request $request)
{
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $resp = array();
        $resp['error'] = false;
        $resp['msg'] = '';

        if($request->ajax()){
            try{
                $account = request('id_account');
                $contrapartida = request('id_counterpart');
                $coin = request('coin');
                $desde = explode('/',request('iddesde'));

                if($desde[1] != $contrapartida){

                    //$amount = str_replace(',', '.', str_replace('.', '', request('amount')));
                    $rate = request('rate');
                    $amount = request('amount');
                    if($coin != 'bolivares'){
                        $amount = $amount * $rate;
                    }


                        $header = new HeaderVoucher();
                        $header->setConnection(Auth::user()->database_name);

                        $header->reference = request('reference');
                        $header->description = "Transferencia " . request('descripcionbanco');
                        $header->date = request('date');
                        $header->status =  "1";

                        $header->save();


                        $movement = new DetailVoucher();
                        $movement->setConnection(Auth::user()->database_name);

                        $movement->id_header_voucher = $header->id;
                        $movement->id_account = $desde[1];
                        $movement->user_id = request('user_id');
                        $movement->debe = 0;
                        $movement->haber = $amount;
                        $movement->tasa = $rate;
                        $movement->status = "C";

                        $movement->save();

                        $movement_counterpart = new DetailVoucher();
                        $movement_counterpart->setConnection(Auth::user()->database_name);

                        $movement_counterpart->id_header_voucher = $header->id;
                        $movement_counterpart->id_account = $contrapartida;
                        $movement_counterpart->user_id = request('user_id');
                        $movement_counterpart->debe = $amount;
                        $movement_counterpart->haber = 0;
                        $movement_counterpart->tasa = $rate;
                        $movement_counterpart->status = "C";

                        $movement_counterpart->save();


                        $verification = Account::on(Auth::user()->database_name)->findOrFail($account);

                        if($verification->status != "M"){
                            $verification->status = "M";
                            $verification->save();
                        }

                        $verification2 = Account::on(Auth::user()->database_name)->findOrFail($contrapartida);

                        if($verification2->status != "M"){
                            $verification2->status = "M";
                            $verification2->save();
                        }

                        $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                ->find($request->idmovimiento,['id_temp_movimientos', 'estatus']);
                $movimientosmasivos->estatus = 1;
                $movimientosmasivos->save();


                $user = new TempMovimientos();
                                $user->setConnection(Auth::user()->database_name);
                                $user->banco        = $desde[0];
                                $user->referencia_bancaria     = request('reference');
                                $user->descripcion       = "Transferencia " . request('descripcionbanco');
                                $user->fecha    = request('date');
                                $user->haber     = $amount;
                                $user->debe   = 0;
                                $user->moneda      = $coin;
                                $user->estatus      = 1;
                                $user->save();



                        $resp['error'] = True;
                        $resp['msg'] = 'Transferencia Exitosa';

                        return response()->json($resp);




                }else{
                    $resp['error'] = False;
                    $resp['msg'] = 'No se puede realizar una transferencia a la misma cuenta';

                    return response()->json($resp);
                 }

            }catch(\error $error){
                return response()->json(false,500);
            }
        }







}else{
    return redirect('/bankmovements'.request('id_account').'')->withDanger('No Tiene Permiso!');
}
}

public function listarfecha(Request $request){

    if($request->ajax()){
        try{

            $agregarmiddleware = $request->get('agregarmiddleware');
            $actualizarmiddleware = $request->get('actualizarmiddleware');
            $eliminarmiddleware = $request->get('eliminarmiddleware');
                  /********MOVIMIENTOS MASIVOS ********/
        $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
        ->select(DB::raw("SUBSTR(fecha,1,7) as fecha,moneda"))
        ->where("estatus","0")
        ->where("banco",$request->bancos)
        ->groupBy(DB::raw("SUBSTR(fecha,1,7),moneda"))
        ->orderBy('fecha','asc')->get();



    return response()->json(View::make('admin.bankmovementsmasivo.listarfecha',compact('movimientosmasivos','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'))->render());



        }catch(\Throwable $th){
            return response()->json(false,500);
        }
    }


}


public function listardatos(Request $request){

    if($request->ajax()){
        try{
            $agregarmiddleware = $request->get('agregarmiddleware');
            $actualizarmiddleware = $request->get('actualizarmiddleware');
            $eliminarmiddleware = $request->get('eliminarmiddleware');
                  /********MOVIMIENTOS MASIVOS ********/
        $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
        ->where('estatus','0')
        ->where('banco',$request->bancos)
        ->where(DB::raw("SUBSTR(fecha,1,7)"), $request->fechabancos)
        ->orderBy('fecha','asc')->get();

        $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                    ->where('date_billing','<>',null)
                    ->where('number_invoice','<>',null)
                    ->where('status','=','P')
                    ->get();




    return response()->json(View::make('admin.bankmovementsmasivo.listardatos',compact('movimientosmasivos','quotations','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'))->render());



        }catch(\Throwable $th){
            return response()->json(false,500);
        }
    }


}




public function procesardeposito(Request $request){

    $resp = array();
    $resp['error'] = false;
    $resp['msg'] = '';

    if($request->ajax()){
        try{

            if($request->valorhaber == 0){
                ///BANCO POR DEBE
                $montodelacontra = 0;
                $validarcontra = FALSE;
                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                    $montocontra =  $request->input('montocontra.' . $i);
                    if($valorcontra == 0){

                        $validarcontra = TRUE;

                     }else{

                        $montodelacontra = $montocontra + $montodelacontra;

                     }


                 }


                 $valordebe = (string) $request->valordebe;
                 $montodelacontra = (string) $montodelacontra;

                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra !=  $valordebe){

                    $resp['error'] = false;
                    $resp['msg'] = 'El Total de las contrapartidas debe ser igual al monto del movimiento bancario';

                    return response()->json($resp);
                }else{
                    $descripcion = "Deposito ".$request->descripcionbanco;

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = $descripcion;
                $header_voucher->reference = $request->referenciabanco;
                $header_voucher->date = $request->fechamovimiento;
                $header_voucher->status =  "1";
                $header_voucher->save();

                $bcv = $request->tasa;

                  /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $request->valordebe * $bcv;
                   }else{
                    $amount = $request->valordebe;
                   }
                  /*** */

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', $request->banco)->first();

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,0,$amount);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);

                     /*** */
                if($request->moneda != 'bolivares'){
                    $amount = $montocontra * $bcv;
                   }else{
                    $amount = $montocontra;
                   }
                  /*** */

                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,$amount,0);

                }

                $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                                ->find($request->idmovimiento,['id_temp_movimientos', 'estatus']);

                $movimientosmasivos->estatus = 1;
                $movimientosmasivos->save();

                $resp['error'] = True;
                $resp['msg'] = 'Movimiento Consolidado Exitosamente';

                return response()->json($resp);


                }


            }elseif($request->valordebe == 0){

                $montodelacontra = 0;
                $validarcontra = FALSE;
                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                    $montocontra =  $request->input('montocontra.' . $i);
                    if($valorcontra == 0){

                        $validarcontra = TRUE;

                     }else{

                        $montodelacontra = $montocontra + $montodelacontra;

                     }


                 }

                 $valorhaber = (string) $request->valorhaber;
                 $montodelacontra = (string) $montodelacontra;

                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $valorhaber){

                    $resp['error'] = false;
                    $resp['msg'] = 'El Total de las contrapartidas debe ser igual al monto del movimiento bancario';

                    return response()->json($resp);
                }else{
                    $descripcion = "Deposito ".$request->descripcionbanco;

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = $descripcion;
                $header_voucher->reference = $request->referenciabanco;
                $header_voucher->date = $request->fechamovimiento;
                $header_voucher->status =  "1";
                $header_voucher->save();

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', $request->banco)->first();

                $bcv = $request->tasa;

                           /*** */
                           if($request->moneda != 'bolivares'){
                            $amount = $request->valorhaber * $bcv;
                           }else{
                            $amount = $request->valorhaber;
                           }
                          /*** */

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,$amount,0);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);

                          /*** */
                          if($request->moneda != 'bolivares'){
                            $amount = $montocontra * $bcv;
                           }else{
                            $amount = $montocontra;

                           }
                          /*** */

                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,0,$amount);

                }

                $movimientosmasivos   = TempMovimientos::on(Auth::user()->database_name)
                ->find($request->idmovimiento,['id_temp_movimientos', 'estatus']);
                $movimientosmasivos->estatus = 1;
                $movimientosmasivos->save();

                $resp['error'] = True;
                $resp['msg'] = 'Movimiento Consolidado Exitosamente';

                return response()->json($resp);


                }





            }else{
                $resp['error'] = False;
                $resp['msg'] = 'Error El Movimiento no Tiene Valor';

                return response()->json($resp);

            }







        }catch(\error $error){
            return response()->json(false,500);
        }
    }
}




public function eliminarmovimiento(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if($request->ajax()){
        try{


          TempMovimientos::on(Auth::user()->database_name)
            ->where('id_temp_movimientos',$request->idmov)
            ->delete();


        $resp['error'] = true;
        $resp['msg'] = 'Eliminado Con Exito';

        return response()->json($resp);



        }catch(\error $error){
            $resp['error'] = false;
	        $resp['msg'] = $error;

            return response()->json($resp);
        }
    }




}




public function pdflibro(Request $request)
{


    $fecha = request('fechabancos')."-01";
    $fechafin = request('fechabancos')."-31";


    $bancos = request('bancos');
    $coin = request('coin');

    $date_begin = $fecha;
    $date_end = $fechafin;

    $date = Carbon::now();
    $datenow = $date->format('d-m-Y');

    $pdf = App::make('dompdf.wrapper');

    $company = Company::on(Auth::user()->database_name)->find(1);

    $period = Carbon::parse($date_begin)->format('Y');

    $mesdia = Carbon::parse($date_begin)->format('m-d');

    $account = Account::on(Auth::user()->database_name)->where('description',$bancos)->first();
    $id_account = $account->id;

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


   /* if($coin != "bolivares"){
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

            $detail->header_description .= ' FAC: '.$quotation->number_invoice;
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
                    $detail->header_description .= ' FAC: '.$quotation->number_invoice;
                    $id_client = $quotation->id_client;
                    $coin_mov = $quotation->coin;
                    }
                    if (isset($quotation_delivery)) {
                    $detail->header_description .= ' NE: '.$quotation_delivery->number_delivery_note;
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

                if((isset($detail->debe)) && ($detail->debe != 0)){
                $detail->debe = $detail->debe / ($detail->tasa ?? 1);
                }

                if((isset($detail->haber)) && ($detail->haber != 0)){
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
