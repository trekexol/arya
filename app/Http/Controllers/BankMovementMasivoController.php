<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use App\Account;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Imports\TempMovimientosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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


    $import = new TempMovimientosImport($banco);



    if(($banco == 'Bancamiga' OR $banco == 'Banco Banesco') AND $extension == 'xlsx'){

    Excel::import($import, $file);
    $resp['error'] = $import->estatus;
    $resp['msg'] = $import->mensaje;
      return response()->json($resp);

    }

    elseif(($banco == 'Mercantil' OR $banco == 'Chase' OR $banco == 'BOFA' OR $banco == 'Banco Banplus' OR $banco == 'Banplus Custodia') AND $extension == 'txt'){
        Excel::import($import, $file);
        $resp['error'] = $import->estatus;
        $resp['msg'] = $import->mensaje;
        return response()->json($resp);
    }else{

        $resp['error'] = false;
        $resp['msg'] = 'Verifique Formato. <br> Banesco y Bancamiga .xlsx <br> Mercantil y Banplus .txt <br> Chase y BOFA .csv ';

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
        $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
        ->where('date_billing','<>',null)
        ->where('number_invoice','<>',null)
        ->where('status','=','P')
        ->where('amount_with_iva','=',$data[0])
        ->where('coin', $moneda)
        ->get();


        return View::make('admin.bankmovementsmasivo.tablafactura',compact('quotations','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo'))->render();


    }elseif($tipo == 'contra'){

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



        return View::make('admin.bankmovementsmasivo.tablafactura',compact('contrapartidas','valormovimiento','idmovimiento','fechamovimiento','bancomovimiento','tipo','montohaber','referenciamovimiento','moneda','descripcionbanco'))->render();

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
            ->join('tempmovimientos','tempmovimientos.debe','amount_with_iva')
            ->where('tempmovimientos.id_temp_movimientos',$request->idmovimiento)
            ->where('date_billing','<>',null)
            ->where('tempmovimientos.moneda','coin')
            ->where('number_invoice','=',$request->nrofactura)
            ->where('status','=','P')
            ->where('amount_with_iva','=',$request->montoiva)
            ->where('id','=',$request->id)->first();



            if($quotations){

                $quotations->status = 'C';
                $quotations->save();


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

            $global = new GlobalController();
            $bcv = $global->search_bcv();

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


                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $request->valordebe){

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

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,0,$request->valordebe);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);


                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,$montocontra,0);

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


                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $request->valorhaber){

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

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,$request->valorhaber,0);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);


                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,0,$montocontra);

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
                    $rate = str_replace(',', '.', str_replace('.', '', request('rate')));
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
        ->select(DB::raw("SUBSTR(fecha,1,7) as fecha"))
        ->where("estatus","0")
        ->where("banco",$request->bancos)
        ->groupBy(DB::raw("SUBSTR(fecha,1,7)"))
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


                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $request->valordebe){

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

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', $request->banco)->first();

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,0,$request->valordebe);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);


                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,$montocontra,0);

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


                if($validarcontra == TRUE){

                    $resp['error'] = false;
                    $resp['msg'] = 'Debe Seleccionar una Contrapartida';

                    return response()->json($resp);

                }elseif($montodelacontra != $request->valorhaber){

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
                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movementfacturas($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,null,Auth::user()->id,$request->valorhaber,0);
                }

                foreach ($request->input('valorcontra', []) as $i => $valorcontra) {

                   $montocontra =  $request->input('montocontra.' . $i);


                 $this->add_movementfacturas($bcv,$header_voucher->id,$valorcontra,null,Auth::user()->id,0,$montocontra);

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


}
