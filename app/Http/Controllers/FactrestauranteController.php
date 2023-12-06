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
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

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

                $cantinv = $global->consul_prod_invt($segmento->id);

                $cantidadstop = QuotationProduct::on(Auth::user()->database_name)
                ->where('status','O')
                ->where('id_inventory',$segmento->id)
                ->sum("amount");

                $segmento->amount = $cantinv - $cantidadstop;


            }

           $segmentos = $segmentos->unique('segment_id');


        foreach ($inventories as $inventorie) {


            $cantinv = $global->consul_prod_invt($inventorie->id_inventory);

            $cantidadstop = QuotationProduct::on(Auth::user()->database_name)
            ->where('status','O')
            ->where('id_inventory',$inventorie->id_inventory)
            ->sum("amount");

            $inventorie->amount = $cantinv - $cantidadstop;

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


        $cantinv = $global->consul_prod_invt($invente->id_inventory);

        $cantidadstop = QuotationProduct::on(Auth::user()->database_name)
        ->where('status','O')
        ->where('id_inventory',$invente->id_inventory)
        ->sum("amount");

        $invente->amount = $cantinv - $cantidadstop;

    }

    $segmentos2 = Product::on(Auth::user()->database_name)
    ->whereIN('type',['MERCANCIA','COMBO'])
    ->where('status',1)
    ->select('segment_id','id')
    ->GroupBY('segment_id','id')
    ->get();
    foreach ($segmentos2 as $segmento) {


        $cantinv = $global->consul_prod_invt($segmento->id);

        $cantidadstop = QuotationProduct::on(Auth::user()->database_name)
        ->where('status','O')
        ->where('id_inventory',$segmento->id)
        ->sum("amount");

        $segmento->amount = $cantinv - $cantidadstop;

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


        return View::make('admin.restaurante.facturar',compact('quotations','data'))->render();


}


public function cliente(Request $request){

    $data = $request->value;


    $clientes = Client::on(Auth::user()->database_name)->where('status',1)->orderBy('id' ,'DESC')->get();


        return View::make('admin.restaurante.cliente',compact('data','clientes'))->render();


}


public function metodos(Request $request){

    $monto = $request->monto;
    $numero = $request->numero;
    $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
    ->where('code_two', 1)
    ->where('code_three', 1)
    ->where('code_four', 2)
    ->where('code_five', '<>',0)
    ->where('description','not like', 'Punto de Venta%')
    ->orderBy('description','ASC')
    ->get();

    $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
    ->where('code_two', 1)
    ->where('code_three', 1)
    ->where('code_four', 1)
    ->where('code_five', '<>',0)
    ->orderBy('description','ASC')
    ->get();

        return View::make('admin.restaurante.metodos',compact('accounts_bank','accounts_efectivo','monto','numero'))->render();


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



public function upcarritonew(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

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

            $datos = explode("/",$request->value);
            $id = $datos[0];
            $tipo = $datos[1];

            $baseimponible = 0;

            $prod = QuotationProduct::on(Auth::user()->database_name)
            ->where('id',$id)
            ->first();

            if ($prod) {
                // Si el producto existe, actualizamos sus datos
                    if($prod->price > 0){
                        $precio = $prod->price;
                    }else{
                        $precio = 0;
                    }

                    if($tipo == 'ADD'){

                        $nuevacantidad = $prod->amount + 1;

                    }elseif($tipo == 'ELI'){

                        $nuevacantidad = $prod->amount - 1;
                    }

                    if($nuevacantidad == 0){
                        QuotationProduct::on(Auth::user()->database_name)
                        ->where('id',$id)
                        ->delete();
                    }else{

                        $prod->update([
                            'amount' => $nuevacantidad,
                            'price' => $precio,
                        ]);
                    }


            }




        $prodnew = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$prod->id_quotation)->get();

        foreach($prodnew as $prodnew){
            $baseimponible += $prodnew->price * $prodnew->amount;
        }
        $montoiva = $baseimponible * 16 / 100;
        $total = $baseimponible + $montoiva;

        /***ACTUALIZO MONTO DE FACTURA */
        $vars =  Quotation::on(Auth::user()->database_name)->findOrFail($prod->id_quotation);
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


public function cambiocliente(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';



    if($request->ajax()){
        try{

            $idclientenew = $request->value;
            $idfactura = $request->idfac;

            $prod = Quotation::on(Auth::user()->database_name)
            ->where('id',$idfactura)
            ->first();

            if ($prod) {
                        $prod->update([
                            'id_client' => $idclientenew,
                        ]);
            }


        $resp['error'] = true;
        $resp['msg'] = 'Cliente Actualizado Con Exito';


    }catch(\error $error){
        $resp['error'] = false;
        $resp['msg'] = 'Verifique..';
    }

return response()->json($resp);

    }


}





public function facturarpedido(Request $request){

    $resp = array();
	$resp['error'] = false;
	$resp['msg'] = '';

    if($request->ajax()){
        try{
            if(array_sum($request->input('monto', [])) >  $request->montoculto){

                $resp['error'] = false;
                $resp['msg'] = 'El Monto Ingresado No puede Superar al Monto de la factura';

            }elseif(array_sum($request->input('monto', [])) == 0){

                $resp['error'] = false;
                $resp['msg'] = 'Ingrese Monto..';

            }elseif(array_sum($request->input('monto', [])) ==  $request->montoculto){

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();

                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv = $company->rate;
                }

                $request->idfactura; //id factura

                foreach ($request->input('tipopago', []) as $i => $tipopago) {


                    $var = new QuotationPayment();
                    $var->setConnection(Auth::user()->database_name);

                    /****** VALORES DE LOS INPUTS */
                    $banco = $request->input('banco.' . $i);
                    $caja = $request->input('caja.' . $i);
                    $referencia = $request->input('referencia.' . $i);
                    $monto = $request->input('monto.' . $i);

                    if($tipopago == 1 || $tipopago == 11 || $tipopago == 5 AND $monto > 0 AND $banco > 0){

                        $var->id_account = $banco;
                        $var->reference = $referencia;
                        $var->amount = $monto;
                        $var->id_quotation = $request->idfactura;
                        $var->payment_type = $tipopago;
                        $var->rate = $bcv;
                        $var->status = 1;
                        $var->save();

                    }elseif($tipopago == 2 AND $monto > 0){

                        $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica%')->first();
                        $var->id_account = $account_contado->id;
                        $var->amount = $monto;
                        $var->id_quotation = $request->idfactura;
                        $var->payment_type = $tipopago;
                        $var->rate = $bcv;
                        $var->status = 1;
                        $var->save();

                    }elseif($tipopago == 6 AND $monto > 0 AND $caja > 0){

                        $var->id_account = $caja;
                        $var->reference = $referencia;
                        $var->amount = $monto;
                        $var->id_quotation = $request->idfactura;
                        $var->payment_type = $tipopago;
                        $var->rate = $bcv;
                        $var->status = 1;
                        $var->save();

                    }

                    elseif($tipopago == 9 || $tipopago == 10 AND $monto > 0){

                        $accounts_punto_de_venta = Account::on(Auth::user()->database_name)->where('description','LIKE', 'Punto de Venta%')->first();
                        $var->id_account = $accounts_punto_de_venta->id;
                        $var->amount = $monto;
                        $var->id_quotation = $request->idfactura;
                        $var->payment_type = $tipopago;
                        $var->rate = $bcv;
                        $var->status = 1;
                        $var->save();

                    }else{

                        $resp['error'] = true;
                        $resp['msg'] = 'Verifique Monto y Metodo de pagos';
                    }




                }


                $resp['error'] = false;
                $resp['msg'] = 'proceder con el pago';

            }else{





            $resp['error'] = false;
            $resp['msg'] = 'Monto';

            }




    }catch(\error $error){
        $resp['error'] = false;
        $resp['msg'] = 'Verifique..';
    }

return response()->json($resp);

    }


}

}
