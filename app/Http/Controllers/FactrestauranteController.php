<?php

namespace App\Http\Controllers;

use App\Account;
use App\Mesas;
use App\Vendor;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Inventory;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Multipayment;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\View;
use App\Product;

class FactrestauranteController extends Controller
{


    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Facturas');
    }

    public function index(request $request)
    {
        $user       =   auth()->user();
        $company_user = $user->id_company;

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $cantidadmesas = Mesas::on(Auth::user()->database_name)->orderBy('numero' ,'ASC')->get(); //Cantidad de mesas

            $mesas = Mesas::on(Auth::user()->database_name)->where('estatus','1')->get(); //Cantidad de mesas disponibles


            $quotations = Quotation::on(Auth::user()->database_name)
            ->where('status','O')
            ->get();

            foreach($quotations as $q){

                $valimesa = Mesas::on(Auth::user()->database_name)
                ->where('id_quotations',$q->id)
                ->where('estatus','0')->first(); //Cantidad de mesas disponibles

                if($valimesa){
                    $q->mesa = $valimesa->numero;
                }else{
                    Quotation::on(Auth::user()->database_name)
                    ->where('status','O')
                    ->where('id',$q->id)
                    ->delete();

                }


            }

            $agregarmiddleware = $request->get('agregarmiddleware');
            $actualizarmiddleware = $request->get('actualizarmiddleware');
            $eliminarmiddleware = $request->get('eliminarmiddleware');
            $namemodulomiddleware = $request->get('namemodulomiddleware');



            return view('admin.restaurante.index',compact('cantidadmesas','mesas','quotations','datenow','agregarmiddleware','actualizarmiddleware','eliminarmiddleware','namemodulomiddleware'));

    }


    public function pedidos(request $request)
    {
        $user       =   auth()->user();
        $company_user = $user->id_company;

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $namemodulomiddleware = $request->get('namemodulomiddleware');

        $cantidadmesas = Mesas::on(Auth::user()->database_name)->orderBy('numero' ,'ASC')->get(); //Cantidad de mesas


            return view('admin.restaurante.pedidos',compact('cantidadmesas','agregarmiddleware','actualizarmiddleware','eliminarmiddleware','namemodulomiddleware'));

    }



public function procesarmesas(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if($request->ajax()){
        try{

            $cantidadmesas = Mesas::on(Auth::user()->database_name)->orderBy('numero' ,'ASC')->get(); //Cantidad de mesas
            $cant = count($cantidadmesas);

            if($request->cantidad > 0){

                for ($i=0; $i < $request->cantidad; $i++) {
                    $cant++;
                    $var = new Mesas();
                    $var->setConnection(Auth::user()->database_name);
                    $var->numero = $cant;
                    $var->estatus  = 1;
                   $var->save();


                }

            $resp['error'] = True;
            $resp['msg'] = 'Mesas Agregadas con Exito';

        }elseif($request->cantidad < 0){
            $data = explode('-',$request->cantidad);
            $cantidad = $data[1];

            for ($i=0; $i < $cantidad; $i++) {

                Mesas::on(Auth::user()->database_name)
            ->where('numero',$cant)
            ->delete();

            $cant--;
            }



            $resp['error'] = True;
            $resp['msg'] = 'Mesas Eliminadas con Exito';

        }else{

                $resp['error'] = false;
                $resp['msg'] = 'Verifique Formato debe ser .xlsx';



            }


        }catch(\error $error){
            $resp['error'] = false;
	        $resp['msg'] = 'Verifique el Archivo.';
        }

        return response()->json($resp);
    }


}





public function pedidosmesas(Request $request){

    $data = explode('/',$request->value);
    $mesa = $data[0];
    $tipo = $data[1];

    $company = Company::on(Auth::user()->database_name)->find(1);

    if($tipo == 'agregar'){

        $global = new GlobalController();

            $inventories = Product::on(Auth::user()->database_name)
            ->whereIN('type',['MERCANCIA','COMBO'])
            ->where('status',1)
            ->select('id as id_inventory','products.*')
            ->get();


          $segmentos = Product::on(Auth::user()->database_name)
            ->whereIN('type',['MERCANCIA','COMBO'])
            ->where('status',1)
            ->select('segment_id','id')
            ->GroupBY('segment_id','id')
            ->get();
            foreach ($segmentos as $segmento) {

                $segmento->amount = $global->consul_prod_invt($segmento->id);

            }

            $segmentos = $segmentos->unique('segment_id');


        foreach ($inventories as $inventorie) {

            $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);

        }

        return View::make('admin.restaurante.pedidomesa',compact('segmentos','tipo','inventories','mesa','company'))->render();


    }elseif($tipo == 'editar'){
        $global = new GlobalController();
        /******PARA VER PEDIDO ACTUAL EN LA MESA ******/

        $valimesa = Mesas::on(Auth::user()->database_name)
        ->where('numero',$mesa)
        ->where('estatus','0')->first();


        $quotations = QuotationProduct::on(Auth::user()->database_name)
        ->where('status','O')
        ->where('id_quotation',$valimesa->id_quotations)
        ->get();

        foreach($quotations as $q){

            $inventories = Product::on(Auth::user()->database_name)
            ->whereIN('type',['MERCANCIA','COMBO'])
            ->where('status',1)
            ->where('id',$q->id_inventory)
            ->first();

            $q->nombreproducto = $inventories->description;

        }
        /************************************************** */

        /****MOSTRAR TODOS LOS PRODUCTOS */


        $inven = Product::on(Auth::user()->database_name)
        ->whereIN('type',['MERCANCIA','COMBO'])
        ->where('status',1)
        ->select('id as id_inventory','products.*')
        ->get();


    foreach ($inven as $invente) {

        $invente->amount = $global->consul_prod_invt($invente->id_inventory);

    }

    $segmentos2 = Product::on(Auth::user()->database_name)
    ->whereIN('type',['MERCANCIA','COMBO'])
    ->where('status',1)
    ->select('segment_id','id')
    ->GroupBY('segment_id','id')
    ->get();
    foreach ($segmentos2 as $segmento) {

        $segmento->amount = $global->consul_prod_invt($segmento->id);

    }

    $segmentos2 = $segmentos2->unique('segment_id');


        /********************* */



        return View::make('admin.restaurante.pedidomesa',compact('segmentos2','tipo','quotations','mesa','inven','company'))->render();

    }


}




public function facturar(Request $request){

    $data = $request->value;

    /******PARA VER PEDIDO ACTUAL EN LA MESA ******/


    $quotations = QuotationProduct::on(Auth::user()->database_name)
    ->where('status','O')
    ->where('id_quotation',$data)
    ->get();

    foreach($quotations as $q){

        $inventories = Product::on(Auth::user()->database_name)
        ->whereIN('type',['MERCANCIA','COMBO'])
        ->where('status',1)
        ->where('id',$q->id_inventory)
        ->first();

        $q->nombreproducto = $inventories->description;

    }
    /************************************************** */




        return View::make('admin.restaurante.facturar',compact('quotations'))->render();





}


public function carrito(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if(array_sum($request->input('cantidad', [])) > 0){

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

    if($request->ajax()){
        try{

            $client = Client::on(Auth::user()->database_name)->where('cedula_rif','generico')->first();

            if(is_null($client)){
                $var = new Client();
                $var->setConnection(Auth::user()->database_name);
                $var->id_user = Auth::user()->id;
                $var->type_code  = "V-";
                $var->name  = "generico";
                $var->cedula_rif  = "generico";
                $var->direction  = "sin direccion";
                $var->city  = "sin direccion";
                $var->country  = "Venezuela";
                $var->phone1  = "0412000000";
                $var->days_credit  = "0";
                $var->status  = "1";
                $var->save();

                $idcliente = $var->id;
            }else{
                $idcliente = $client->id;
            }


            $vendedor = Vendor::on(Auth::user()->database_name)->where('user_id',Auth::user()->id)->first();

            if(is_null($vendedor)){
                $var = new Vendor();
                $var->setConnection(Auth::user()->database_name);
                $var->user_id = Auth::user()->id;
                $var->code  = "01";
                $var->cedula_rif  = "generico";
                $var->name  = Auth::user()->name;
                $var->surname  = Auth::user()->name;
                $var->email  = Auth::user()->email;
                $var->phone  = "0412000000";
                $var->comision  = "5.00";
                $var->status  = "1";
                $var->save();

                $idvendedor = $var->id;
            }else{
                $idvendedor = $vendedor->id;
            }


        /****CREO FACTURA */
        $fac = new Quotation();
        $fac->setConnection(Auth::user()->database_name);
        $fac->id_client = $idcliente;
        $fac->id_vendor  = $idvendedor;
        $fac->id_user = Auth::user()->id;
        $fac->id_branch  = 1;
        $fac->date_quotation  = $datenow;
        $fac->iva_percentage  = 16;
        $fac->coin  = 'bolivares';
        $fac->bcv  = $bcv;
        $fac->status  = "O";
        $fac->save();

        /****** *****/

    $baseimponible = 0;
    foreach ($request->input('id', []) as $i => $id) {
        $cantidad = $request->input('cantidad.' . $i);
        $precio = $request->input('precio.' . $i);

        if (is_numeric($id) && $cantidad > 0 && $precio > 0) {

            $pr = new QuotationProduct();
            $pr->setConnection(Auth::user()->database_name);
            $pr->id_quotation = $fac->id;
            $pr->id_inventory  = $id;
            $pr->amount = $cantidad;
            $pr->discount  = '0.00';
            $pr->price  = $precio;
            $pr->rate  = $bcv;
            $pr->retiene_iva  = 0;
            $pr->retiene_islr  = 0;
            $pr->status  = "O";
            $pr->save();

            $baseimponible += $cantidad * $precio;

            }
        }

        $montoiva = $baseimponible * 16 / 100;
        $total = $baseimponible + $montoiva;
        /***ACTUALIZO MONTO DE FACTURA */
        $vars =  Quotation::on(Auth::user()->database_name)->findOrFail($fac->id);
        $vars->base_imponible = $baseimponible;
        $vars->amount = $baseimponible;
        $vars->amount_iva = $montoiva;
        $vars->amount_with_iva = $total;
        $vars->save();
        /********* */


        /****ACTUALIZO MESA PARA ESTATUS OCUPADA */
       Mesas::on(Auth::user()->database_name)->where('numero',$request->mesa)->update(['id_quotations' => $fac->id,'estatus' => '0']);
        /***************** */
        $resp['error'] = true;
        $resp['msg'] = 'Pedido Realizado Con Exito';



    }catch(\error $error){
        $resp['error'] = false;
        $resp['msg'] = 'Verifique..';
    }

return response()->json($resp);

    }
}
else{
    $resp['error'] = false;
    $resp['msg'] = 'Debe Ingresar una Cantidad mayor a Cero (0).';
    return response()->json($resp);
}

}





public function upcarrito(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if(array_sum($request->input('cantidad', [])) > 0){

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

    if($request->ajax()){
        try{

    $baseimponible = 0;
    foreach ($request->input('id', []) as $i => $id) {
        $cantidad = $request->input('cantidad.' . $i);
        $precio = $request->input('precio.' . $i);

        if (is_numeric($id) && $cantidad > 0) {

            $prod = QuotationProduct::on(Auth::user()->database_name)
            ->where('id_quotation',$request->idfac)
            ->where('id_inventory',$id)
            ->first();

            if ($prod) {
                // Si el producto existe, actualizamos sus datos
                    if($prod->price > 0){
                        $precio = $prod->price;
                    }else{
                        $precio = $precio;
                    }

                $nuevacantidad = $prod->amount + $cantidad;
                $prod->update([
                    'amount' => $nuevacantidad,
                    'price' => $precio,
                ]);
            } else {
                // Si el producto no existe, lo creamos
                $pr = new QuotationProduct();
                $pr->setConnection(Auth::user()->database_name);
                $pr->id_quotation = $request->idfac;
                $pr->id_inventory  = $id;
                $pr->amount = $cantidad;
                $pr->discount  = '0.00';
                $pr->price  = $precio;
                $pr->rate  = $bcv;
                $pr->retiene_iva  = 0;
                $pr->retiene_islr  = 0;
                $pr->status  = "O";
                $pr->save();
            }


            } //fin validar
        } //fin foreach

        $prodnew = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$request->idfac)->get();

        foreach($prodnew as $prodnew){
            $baseimponible += $prodnew->price * $prodnew->amount;
        }
        $montoiva = $baseimponible * 16 / 100;
        $total = $baseimponible + $montoiva;

        /***ACTUALIZO MONTO DE FACTURA */
        $vars =  Quotation::on(Auth::user()->database_name)->findOrFail($request->idfac);
        $vars->base_imponible = $baseimponible;
        $vars->amount = $baseimponible;
        $vars->amount_iva = $montoiva;
        $vars->amount_with_iva = $total;
        $vars->save();
        /********* */


        $resp['error'] = true;
        $resp['msg'] = 'Pedido Actualizado Con Exito';



    }catch(\error $error){
        $resp['error'] = false;
        $resp['msg'] = 'Verifique..';
    }

return response()->json($resp);

    }
}
else{
    $resp['error'] = false;
    $resp['msg'] = 'Debe Ingresar una Cantidad mayor a Cero (0).';
    return response()->json($resp);
}

}



    public function movementsinvoice($id_invoice,$coin = null)
    {


        $user       =   auth()->user();
        $users_role =   $user->role_id;

            $quotation = Quotation::on(Auth::user()->database_name)->find($id_invoice);
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
                                            ->where('id_invoice',$id_invoice)
                                            ->where('status','C')
                                            ->get();

            $multipayments_detail = null;
            $invoices = null;

            //Buscamos a la factura para luego buscar atraves del header a la otras facturas
            $multipayment = Multipayment::on(Auth::user()->database_name)->where('id_quotation',$id_invoice)->first();
            if(isset($multipayment)){
            $invoices = Multipayment::on(Auth::user()->database_name)->where('id_header',$multipayment->id_header)->where('id_payment',$multipayment->id_payment)->get();
            $multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)->get();
            }

            if(!isset($coin)){
                $coin = 'bolivares';
            }


        return view('admin.invoices.index_detail_movement',compact('detailvouchers','quotation','coin','invoices','multipayments_detail'));
    }


    public function storemultipayment(Request $request)
    {

        /*Validar cuales son los pagos a guardar */
        $validate_boolean1 = false;
        $validate_boolean2 = false;
        $validate_boolean3 = false;
        $validate_boolean4 = false;
        $validate_boolean5 = false;
        $validate_boolean6 = false;
        $validate_boolean7 = false;
        //-----------------------

        $total_retiene_iva = str_replace(',', '.', str_replace('.', '', request('iva_retencion')));
        $total_retiene_islr = str_replace(',', '.', str_replace('.', '', request('islr_retencion')));
        $anticipo = str_replace(',', '.', str_replace('.', '', request('anticipo')));


        $base_imponible = str_replace(',', '.', str_replace('.', '', request('base_imponible')));
        $amount = str_replace(',', '.', str_replace('.', '', request('total_factura')));
        $amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount')));
        $amount_with_iva = str_replace(',', '.', str_replace('.', '', request('total_pay')));

        $grand_total = str_replace(',', '.', str_replace('.', '', request('grand_total')));


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Ventas de Bienes o servicios.";
        $header_voucher->date = $datenow;


        $header_voucher->status =  "1";

        $header_voucher->save();

        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $user_id = request('user_id');
        $payment_type = request('payment_type');

        $coin = 'bolivares';

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($amount_with_iva > 0){
            if($come_pay >= 1){

                /*-------------PAGO NUMERO 1----------------------*/

                $var = new QuotationPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');

                if(isset($amount_pay)){

                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 1!');
                }


                $account_bank = request('account_bank');
                $account_efectivo = request('account_efectivo');
                $account_punto_de_venta = request('account_punto_de_venta');

                $credit_days = request('credit_days');

                $reference = request('reference');

                if($valor_sin_formato_amount_pay != 0){

                    if($payment_type != 0){

                        $var->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type == 1 || $payment_type == 11 || $payment_type == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank != 0)){
                                if(isset($reference)){

                                    $var->id_account = $account_bank;

                                    $var->reference = $reference;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                            }
                        }
                        if($payment_type == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days)){

                                $var->credit_days = $credit_days;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }

                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){

                                $var->id_account = $account_efectivo;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }

                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                            }
                        }




                            $var->payment_type = request('payment_type');
                            $var->amount = $valor_sin_formato_amount_pay;

                            if($coin == 'dolares'){
                                $var->amount = $var->amount * $bcv;
                            }

                            $var->rate = $bcv;

                            $var->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay;

                            $validate_boolean1 = true;


                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }


                }else{
                        return redirect('invoices')->withDanger('El pago debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type2 = request('payment_type2');
            if($come_pay >= 2){

                /*-------------PAGO NUMERO 2----------------------*/

                $var2 = new QuotationPayment();
                $var2->setConnection(Auth::user()->database_name);

                $amount_pay2 = request('amount_pay2');

                if(isset($amount_pay2)){

                    $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 2!');
                }


                $account_bank2 = request('account_bank2');
                $account_efectivo2 = request('account_efectivo2');
                $account_punto_de_venta2 = request('account_punto_de_venta2');

                $credit_days2 = request('credit_days2');



                $reference2 = request('reference2');

                if($valor_sin_formato_amount_pay2 != 0){

                if($payment_type2 != 0){

                    $var2->id_quotation = request('id_quotation');

                    //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                    if($payment_type2 == 1 || $payment_type2 == 11 || $payment_type2 == 5 ){
                        //CUENTAS BANCARIAS
                        if(($account_bank2 != 0)){
                            if(isset($reference2)){

                                $var2->id_account = $account_bank2;

                                $var2->reference = $reference2;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                        }
                    }
                    if($payment_type2 == 4){
                        //DIAS DE CREDITO
                        if(isset($credit_days2)){

                            $var2->credit_days = $credit_days2;

                        }else{
                            return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                        }
                    }




                        $var2->payment_type = request('payment_type2');
                        $var2->amount = $valor_sin_formato_amount_pay2;

                        if($coin == 'dolares'){
                            $var2->amount = $var2->amount * $bcv;
                        }
                        $var2->rate = $bcv;

                        $var2->status =  1;

                        $total_pay += $valor_sin_formato_amount_pay2;

                        $validate_boolean2 = true;


                }else{
                    return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }


                }else{
                    return redirect('invoices')->withDanger('El pago 2 debe ser distinto de Cero!');
                }
                /*--------------------------------------------*/
            }
            $payment_type3 = request('payment_type3');
            if($come_pay >= 3){

                    /*-------------PAGO NUMERO 3----------------------*/

                    $var3 = new QuotationPayment();
                    $var3->setConnection(Auth::user()->database_name);

                    $amount_pay3 = request('amount_pay3');

                    if(isset($amount_pay3)){

                        $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                    }else{
                        return redirect('invoices')->withDanger('Debe ingresar un monto de pago 3!');
                    }


                    $account_bank3 = request('account_bank3');
                    $account_efectivo3 = request('account_efectivo3');
                    $account_punto_de_venta3 = request('account_punto_de_venta3');

                    $credit_days3 = request('credit_days3');



                    $reference3 = request('reference3');

                    if($valor_sin_formato_amount_pay3 != 0){

                        if($payment_type3 != 0){

                            $var3->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type3 == 1 || $payment_type3 == 11 || $payment_type3 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank3 != 0)){
                                    if(isset($reference3)){

                                        $var3->id_account = $account_bank3;

                                        $var3->reference = $reference3;

                                    }else{
                                        return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                }
                            }
                            if($payment_type3 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days3)){

                                    $var3->credit_days = $credit_days3;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                }
                            }




                                $var3->payment_type = request('payment_type3');
                                $var3->amount = $valor_sin_formato_amount_pay3;

                                if($coin == 'dolares'){
                                    $var3->amount = $var3->amount * $bcv;
                                }
                                $var3->rate = $bcv;

                                $var3->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay3;

                                $validate_boolean3 = true;


                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }


                    }else{
                            return redirect('invoices')->withDanger('El pago 3 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type4 = request('payment_type4');
            if($come_pay >= 4){

                    /*-------------PAGO NUMERO 4----------------------*/

                    $var4 = new QuotationPayment();
                    $var4->setConnection(Auth::user()->database_name);

                    $amount_pay4 = request('amount_pay4');

                    if(isset($amount_pay4)){

                        $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                    }else{
                        return redirect('invoices')->withDanger('Debe ingresar un monto de pago 4!');
                    }


                    $account_bank4 = request('account_bank4');
                    $account_efectivo4 = request('account_efectivo4');
                    $account_punto_de_venta4 = request('account_punto_de_venta4');

                    $credit_days4 = request('credit_days4');



                    $reference4 = request('reference4');

                    if($valor_sin_formato_amount_pay4 != 0){

                        if($payment_type4 != 0){

                            $var4->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type4 == 1 || $payment_type4 == 11 || $payment_type4 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank4 != 0)){
                                    if(isset($reference4)){

                                        $var4->id_account = $account_bank4;

                                        $var4->reference = $reference4;

                                    }else{
                                        return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                }
                            }
                            if($payment_type4 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days4)){

                                    $var4->credit_days = $credit_days4;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                }
                            }




                                $var4->payment_type = request('payment_type4');
                                $var4->amount = $valor_sin_formato_amount_pay4;

                                if($coin == 'dolares'){
                                    $var4->amount = $var4->amount * $bcv;
                                }
                                $var4->rate = $bcv;

                                $var4->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay4;

                                $validate_boolean4 = true;


                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }


                    }else{
                            return redirect('invoices')->withDanger('El pago 4 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type5 = request('payment_type5');
            if($come_pay >= 5){

                /*-------------PAGO NUMERO 5----------------------*/

                $var5 = new QuotationPayment();
                $var5->setConnection(Auth::user()->database_name);

                $amount_pay5 = request('amount_pay5');

                if(isset($amount_pay5)){

                    $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 5!');
                }


                $account_bank5 = request('account_bank5');
                $account_efectivo5 = request('account_efectivo5');
                $account_punto_de_venta5 = request('account_punto_de_venta5');

                $credit_days5 = request('credit_days5');



                $reference5 = request('reference5');

                if($valor_sin_formato_amount_pay5 != 0){

                    if($payment_type5 != 0){

                        $var5->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type5 == 1 || $payment_type5 == 11 || $payment_type5 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank5 != 0)){
                                if(isset($reference5)){

                                    $var5->id_account = $account_bank5;

                                    $var5->reference = $reference5;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                            }
                        }
                        if($payment_type5 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days5)){

                                $var5->credit_days = $credit_days5;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                            }
                        }




                            $var5->payment_type = request('payment_type5');
                            $var5->amount = $valor_sin_formato_amount_pay5;

                            if($coin == 'dolares'){
                                $var5->amount = $var5->amount * $bcv;
                            }

                            $var5->rate = $bcv;

                            $var5->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay5;

                            $validate_boolean5 = true;


                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }


                }else{
                        return redirect('invoices')->withDanger('El pago 5 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type6 = request('payment_type6');
            if($come_pay >= 6){

                /*-------------PAGO NUMERO 6----------------------*/

                $var6 = new QuotationPayment();
                $var6->setConnection(Auth::user()->database_name);

                $amount_pay6 = request('amount_pay6');

                if(isset($amount_pay6)){

                    $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 6!');
                }


                $account_bank6 = request('account_bank6');
                $account_efectivo6 = request('account_efectivo6');
                $account_punto_de_venta6 = request('account_punto_de_venta6');

                $credit_days6 = request('credit_days6');



                $reference6 = request('reference6');

                if($valor_sin_formato_amount_pay6 != 0){

                    if($payment_type6 != 0){

                        $var6->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type6 == 1 || $payment_type6 == 11 || $payment_type6 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank6 != 0)){
                                if(isset($reference6)){

                                    $var6->id_account = $account_bank6;

                                    $var6->reference = $reference6;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                            }
                        }
                        if($payment_type6 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days6)){

                                $var6->credit_days = $credit_days6;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                            }
                        }




                            $var6->payment_type = request('payment_type6');
                            $var6->amount = $valor_sin_formato_amount_pay6;

                            if($coin == 'dolares'){
                                $var6->amount = $var6->amount * $bcv;
                            }

                            $var6->rate = $bcv;

                            $var6->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay6;

                            $validate_boolean6 = true;


                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }


                }else{
                        return redirect('invoices')->withDanger('El pago 6 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type7 = request('payment_type7');
            if($come_pay >= 7){

                /*-------------PAGO NUMERO 7----------------------*/

                $var7 = new QuotationPayment();
                $var7->setConnection(Auth::user()->database_name);

                $amount_pay7 = request('amount_pay7');

                if(isset($amount_pay7)){

                    $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 7!');
                }


                $account_bank7 = request('account_bank7');
                $account_efectivo7 = request('account_efectivo7');
                $account_punto_de_venta7 = request('account_punto_de_venta7');

                $credit_days7 = request('credit_days7');



                $reference7 = request('reference7');

                if($valor_sin_formato_amount_pay7 != 0){

                    if($payment_type7 != 0){

                        $var7->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type7 == 1 || $payment_type7 == 11 || $payment_type7 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank7 != 0)){
                                if(isset($reference7)){

                                    $var7->id_account = $account_bank7;

                                    $var7->reference = $reference7;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                            }
                        }
                        if($payment_type7 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days7)){

                                $var7->credit_days = $credit_days7;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                            }
                        }




                            $var7->payment_type = request('payment_type7');
                            $var7->amount = $valor_sin_formato_amount_pay7;

                            if($coin == 'dolares'){
                                $var7->amount = $var7->amount * $bcv;
                            }
                            $var7->rate = $bcv;

                            $var7->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay7;

                            $validate_boolean7 = true;


                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }


                }else{
                        return redirect('invoices')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
        }


        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if($total_pay == $amount_with_iva  || ($amount_with_iva <= 0)){

            $array = $request->all();
            $id_quotation = 0;
            $facturas_a_procesar = [];

            foreach ($array as $key => $item) {

                if(substr($key,0, 12) == 'id_quotation'){
                    array_push($facturas_a_procesar, $item);
                    $this->procesar_quotation($item,$total_pay);
                    $id_quotation = $item;
                }

            }

            $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

            $bcv = $quotation->bcv;
            $coin = $quotation->coin;


            $header_voucher  = new HeaderVoucher();
            $header_voucher->setConnection(Auth::user()->database_name);


            $header_voucher->description = "MultiCobro de Bienes o servicios.";
            $header_voucher->date = $datenow;


            $header_voucher->status =  "1";

            $header_voucher->save();

            if($validate_boolean1 == true){
                $var->id_quotation = $id_quotation;
                $var->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var->id,$user_id);
                }


                $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$user_id,$var->amount,0);


                //LE PONEMOS STATUS C, DE COBRADO
                $quotation->status = "C";
            }

            if($validate_boolean2 == true){
                $var2->id_quotation = $id_quotation;
                $var2->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var2->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$user_id,$var2->amount,0);

            }

            if($validate_boolean3 == true){
                $var3->id_quotation = $id_quotation;
                $var3->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var3->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$user_id,$var3->amount,0);


            }
            if($validate_boolean4 == true){
                $var4->id_quotation = $id_quotation;
                $var4->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var4->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$user_id,$var4->amount,0);

            }
            if($validate_boolean5 == true){
                $var5->id_quotation = $id_quotation;
                $var5->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var5->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$user_id,$var5->amount,0);

            }
            if($validate_boolean6 == true){
                $var6->id_quotation = $id_quotation;
                $var6->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var6->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$user_id,$var6->amount,0);

            }
            if($validate_boolean7 == true){
                $var7->id_quotation = $id_quotation;
                $var7->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var7->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$user_id,$var7->amount,0);

            }


            if($coin != 'bolivares'){
                $anticipo =  $anticipo * $bcv;
                $total_retiene_iva = $total_retiene_iva * $bcv;
                $total_retiene_islr = $total_retiene_islr * $bcv;

                $amount_iva = $amount_iva * $bcv;
                $base_imponible = $base_imponible * $bcv;
                $amount = $amount * $bcv;
                $total_pay = $total_pay * $bcv;

                $grand_total = $grand_total * $bcv;

            }


             /*Anticipos*/
             if(isset($anticipo) && ($anticipo != 0)){
                $account_anticipo_cliente = Account::on(Auth::user()->database_name)->where('code_one',2)
                ->where('code_two',3)
                ->where('code_three',1)
                ->where('code_four',1)
                ->where('code_five',2)->first();



                //Si el total a pagar es negativo, quiere decir que los anticipos sobrepasan al monto total de la factura
                if($amount_with_iva  < 0){
                    $global = new GlobalController;
                    $global->check_anticipo_multipayment($quotation,$facturas_a_procesar,$grand_total);
                    $quotation->anticipo =  $grand_total;
                    $quotation->status = "C";
                    $this->add_movement_anticipo_total($facturas_a_procesar,$bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id);
                }else{
                    $quotation->anticipo = $anticipo;
                }
                if(isset($account_anticipo_cliente)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id,$quotation->anticipo,0);
                }

             }
            /*---------- */

            if($total_retiene_iva !=0){
                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)
                                                        ->where('code_three',4)->where('code_four',1)->where('code_five',2)->first();

                if(isset($account_iva_retenido)){
                    $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$user_id,$total_retiene_iva,0);
                }
            }


            if($total_retiene_islr !=0){
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',4)
                                                ->where('code_four',1)->where('code_five',4)->first();

                if(isset($account_islr_pagago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$user_id,$total_retiene_islr,0);
                }
            }




            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();

            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,0,$grand_total);
            }


            $global = new GlobalController;
            $global->procesar_anticipos($quotation,$amount_with_iva);

            return redirect('invoices')->withSuccess('Facturas Guardadas con Exito!');

        }else{
            return redirect('invoices')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }
    }


    public function add_movement_anticipo_total($facturas_a_procesar,$bcv,$header_voucher,$id_account,$user_id)
    {
        $global = new GlobalController;

        foreach($facturas_a_procesar as $factura){

            $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($factura);

            $payment = $global->add_payment($quotation,$id_account,3,$quotation->amount_with_iva,$bcv);

            $this->register_multipayment($factura,$header_voucher,$payment,$user_id);
        }
    }



    public function procesar_quotation($id_quotation,$total_pay)
    {
        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        /*descontamos el inventario, si existe la fecha de nota de entrega, significa que ya hemos descontado del inventario, por ende no descontamos de nuevo*/
        if(!isset($quotation->date_delivery_note) && !isset($quotation->date_order)){
            $retorno = $this->discount_inventory($quotation->id);

            if($retorno != "exito"){
                return redirect('invoices');
            }
        }

        //Aqui pasa los quotation_products a status C de Cobrado
        DB::connection(Auth::user()->database_name)->table('quotation_products')
        ->where('id_quotation', '=', $quotation->id)
        ->update(['status' => 'C']);


        $quotation->status = 'C';
        $quotation->save();

        return true;
    }




    public function register_multipayment($id_quotation,$id_header,$id_payment,$id_user)
    {
        $multipayment = new Multipayment();
        $multipayment->setConnection(Auth::user()->database_name);
        $multipayment->id_quotation = $id_quotation;
        $multipayment->id_header = $id_header;
        $multipayment->id_payment = $id_payment;
        $multipayment->id_user = $id_user;

        $multipayment->save();
    }


    public function add_movement($bcv,$id_header,$id_account,$id_quotation = null,$id_user,$debe,$haber)
    {

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);


        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->id_invoice = $id_quotation;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;


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

    public function discount_inventory($id_quotation)
    {
        /*Primero Revisa que todos los productos tengan inventario suficiente*/
        $no_hay_cantidad_suficiente = DB::connection(Auth::user()->database_name)->table('inventories')
                                ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                                ->join('products', 'products.id','=','inventories.product_id')
                                ->where('quotation_products.id_quotation','=',$id_quotation)
                                ->where(function ($query){
                                    $query->where('products.type','MERCANCIA')
                                        ->orWhere('products.type','COMBO');
                                })
                                ->where('quotation_products.amount','<','inventories.amount')
                                ->select('inventories.code as code','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                                'quotation_products.amount as amount_quotation')
                                ->first();

        if(isset($no_hay_cantidad_suficiente)){
            return redirect('quotations/facturar/'.$id_quotation.'/bolivares')->withDanger('En el Inventario de Codigo: '.$no_hay_cantidad_suficiente->code.' no hay Cantidad suficiente!');
        }

        /*Luego, descuenta del Inventario*/
        $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
        ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
        ->where('quotation_products.id_quotation',$id_quotation)
        ->where(function ($query){
            $query->where('products.type','MERCANCIA')
                ->orWhere('products.type','COMBO');
        })
        ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as id_quotation','quotation_products.discount as discount',
        'quotation_products.amount as amount_quotation')
        ->get();

            foreach($inventories_quotations as $inventories_quotation){

                $quotation_product = QuotationProduct::on(Auth::user()->database_name)->findOrFail($inventories_quotation->id_quotation);

                if(isset($quotation_product)){
                $inventory = Inventory::on(Auth::user()->database_name)->findOrFail($quotation_product->id_inventory);

                    if(isset($inventory)){
                        //REVISO QUE SEA MAYOR EL MONTO DEL INVENTARIO Y LUEGO DESCUENTO
                        if($inventory->amount >= $quotation_product->amount){
                            $inventory->amount -= $quotation_product->amount;
                            $inventory->save();

                            //CAMBIAMOS EL ESTADO PARA SABER QUE ESE PRODUCTO YA SE COBRO Y SE RESTO DEL INVENTARIO
                            $quotation_product->status = 'C';
                            $quotation_product->price = $inventories_quotation->price;
                            $quotation_product->save();
                        }else{
                            return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario de Codigo: '.$inventory->code.' no tiene Cantidad suficiente!');
                        }

                    }else{
                        return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario no existe!');
                    }
                }else{
                return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario de la cotizacion no existe!');
                }

            }

            return "exito";

}


    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$user_id,$amount_debe,$amount_haber)
    {


        //Cuentas por Cobrar Clientes

            //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
            if(isset($id_account)){
                $this->add_movement($bcv,$header_voucher,$id_account,$user_id,$amount_debe,0);

            }//SIN DETERMINAR
            else if($payment_type == 7){
                        //------------------Sin Determinar
                $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Ingresos No Identificados')->first();

                if(isset($account_sin_determinar)){
                    $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$user_id,$amount_debe,0);
                }
            }//PAGO DE CONTADO
            else if($payment_type == 2){

                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                if(isset($account_contado)){
                    $this->add_movement($bcv,$header_voucher,$account_contado->id,$user_id,$amount_debe,0);
                }
            }//CONTRA ANTICIPO
            else if($payment_type == 3){
                        //--------------
                $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first();

                if(isset($account_contra_anticipo)){
                    $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$user_id,$amount_debe,0);
                }
            }


    }

    public function calcularfactura($id_quotation)
    {

        $coin = 'bolivares';
        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation)){

            $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();




            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                           ->where('code_two', 1)
                                           ->where('code_three', 1)
                                           ->where('code_four', 2)
                                           ->where('code_five', '<>',0)
                                           ->where('description','not like', 'Punto de Venta%')
                                           ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                           ->where('code_two', 1)
                                           ->where('code_three', 1)
                                           ->where('code_four', 1)
                                           ->where('code_five', '<>',0)
                                           ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                           ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                           ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                           ->where('quotation_products.id_quotation',$quotation->id)
                                                           ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                           'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                           ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                           ->get();

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;


            $retiene_iva = 0;

            $total_retiene_islr = 0;
            $retiene_islr = 0;

            $total_mercancia= 0;
            $total_servicios= 0;

            foreach($inventories_quotations as $var){
                //Se calcula restandole el porcentaje de descuento (discount)
                   $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                   $total += ($var->price * $var->amount_quotation) - $percentage;
               //-----------------------------

               if($var->retiene_iva_quotation == 0){

                   $base_imponible += ($var->price * $var->amount_quotation) - $percentage;

               }else{
                   $retiene_iva += ($var->price * $var->amount_quotation) - $percentage;
               }

               if($var->retiene_islr_quotation == 1){

                   $retiene_islr += ($var->price * $var->amount_quotation) - $percentage;

               }

               //me suma todos los precios de costo de los productos
                if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                   $price_cost_total += $var->price_buy * $var->amount_quotation;
               }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                   $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
               }

               if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                   $total_mercancia += ($var->price * $var->amount_quotation) - $percentage;
               }else{
                   $total_servicios += ($var->price * $var->amount_quotation) - $percentage;
               }
            }

            $quotation->total_factura = $total;
            $quotation->base_imponible = $base_imponible;

           /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
            $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);


           if($client->percentage_retencion_islr != 0){
               $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
           }

           /*-------------- */


           $quotation->price_cost_total = $price_cost_total;
           $quotation->total_retiene_islr = $total_retiene_islr;
           $quotation->total_mercancia = $total_mercancia;
           $quotation->total_servicios = $total_servicios;

           return $quotation;

        }
    }




}
