<?php

namespace App\Http\Controllers;

use App\Account;
use App\Client;
use App\Exports\ExpensesExport;
use App\Http\Controllers\Movement\MovementProductImportController;
use App\Imports\AccountImport;
use App\Imports\ClientImport;
use App\Imports\ExpensesImport;
use App\Imports\InventoryImport;
use App\Imports\ProductImport;
use App\Imports\ComboImport;
use App\Imports\ProductReadImport;
use App\Imports\ProductUpdatePriceImport;
use App\Imports\ProviderImport;
use App\Inventory;
use App\Product;
use App\Company;
use App\Provider;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\UserAccess;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\DetailVoucher;
use App\HeaderVoucher;

class ExcelController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valimodulo:Inventario')->only('import_inventary');

       }

    public function export_account()
    {
         $accounts = Account::on(Auth::user()->database_name)
         ->select('id','code_one','code_two','code_three',
         'code_four','code_five','period','description','type','level',
         'balance_previus','rate','coin')
         ->get();

         $export = new ExpensesExport([
             ['id','code_one','code_two','code_three',
             'code_four','code_five','period','description','type','level',
             'balance_previus','rate','coin'],
              $accounts
        ]);

        return Excel::download($export, 'plantilla_cuentas.xlsx');
    }

    public function export_provider()
    {
         $providers = Provider::on(Auth::user()->database_name)
         ->select('id','code_provider','razon_social','direction',
         'city','country','phone1','phone2','has_credit','days_credit',
         'amount_max_credit','porc_retencion_iva','porc_retencion_islr',
         'balance')
         ->get();

         $export = new ExpensesExport([
             ['id','code_provider','razon_social','direction',
             'city','country','phone1','phone2','has_credit','days_credit',
             'amount_max_credit','porc_retencion_iva','porc_retencion_islr',
             'balance'],
              $providers
        ]);

        return Excel::download($export, 'plantilla_proveedores.xlsx');
    }

    public function export_client()
    {
         $clients = Client::on(Auth::user()->database_name)
         ->select('id','id_vendor','id_user','type_code','name','cedula_rif'
         ,'direction','city','country','phone1','phone2','days_credit','amount_max_credit','percentage_retencion_iva',
         'percentage_retencion_islr')
         ->get();

         $export = new ExpensesExport([
             ['id','id_vendor','id_user','type_code','name','cedula_rif'
              ,'direction','city','country','phone1','phone2','days_credit','amount_max_credit','percentage_retencion_iva',
              'percentage_retencion_islr'],
              $clients
        ]);

        return Excel::download($export, 'plantilla_clientes.xlsx');
    }

    public function export_inventary() // inventario
    {
         $products = Product::on(Auth::user()->database_name)
         ->where('type','!=','COMBO')
         ->where('status','1')
         ->select('id','segment_id','subsegment_id','twosubsegment_id','threesubsegment_id','unit_of_measure_id',
         'code_comercial','type','description','price','price_buy','money',
         'exento','islr','special_impuesto as amount')
         ->get();

         $global = new GlobalController();

         foreach ($products as $product) {  // ingresar el monto de inventario al array producto por la funciuon $global->consul_prod_invt()

            $buscar_num = $global->consul_prod_invt($product->id);

            if($buscar_num < 0 || $buscar_num == '0' || $buscar_num == 0 || $buscar_num == '' || $buscar_num == ' ' || $buscar_num == false || $buscar_num == NULL) {

             $product->amount = '0.00';

            } else {
                $product->amount = $buscar_num;
            }

         }


         $export = new ExpensesExport([
             ['id','id_segmento','id_subsegmento','id_twosubsegment','id_threesubsegment','id_unidadmedida'
              ,'codigo_comercial','tipo_mercancia_o_servicio','descripcion','precio','precio_compra','moneda_d_o_bs',
              'exento_1_o_0','islr_1_o_0','Cantidad_Actual'],
              $products
        ]);

        return Excel::download($export, 'guia_inventario.xlsx');
    }


    public function export_product() // inventario
    {
         $products = Product::on(Auth::user()->database_name)
         ->where('type','!=','COMBO')
         ->where('status','1')
         ->select('id','segment_id','subsegment_id','twosubsegment_id','threesubsegment_id','unit_of_measure_id',
         'code_comercial','type','description','price','price_buy','money','exento','islr')
         ->get();

         $global = new GlobalController();


         $export = new ExpensesExport([
             ['id','id_segmento','id_subsegmento','id_twosubsegment','id_threesubsegment','id_unidadmedida'
              ,'codigo_comercial','tipo_mercancia_o_servicio','descripcion','precio','precio_compra','moneda_d_o_bs',
              'exento_1_o_0','islr_1_o_0'],
              $products
        ]);

         return Excel::download($export, 'guia_productos.xlsx');
    }


    public function export_combo() // inventario
    {
        $sql = 'WITH a AS (SELECT a.id_combo,a.id_product ,a.amount_per_product,b.code_comercial,b.description,price
        FROM combo_products a, products b
        WHERE a.id_combo = b.id),
        b as (SELECT a.id_combo,a.id_product ,b.code_comercial,b.description
        FROM combo_products a, products b
        WHERE a.id_product = b.id)

           SELECT a.id_combo,a.code_comercial as codigo_comercial_combo, a.description as nombre_combo, a.price as precio_venta_combo, a.amount_per_product as cantidad_producto, b.id_product as id_producto, b.code_comercial,b.description
           FROM a a, b b
             WHERE a.id_combo = b.id_combo
             AND a.id_product = b.id_product';

        $products = DB::connection(Auth::user()->database_name)->select($sql);

        foreach($products as $products){
            $datoscombos[] = ['id_combo' => $products->id_combo, 'nombre_combo' => $products->nombre_combo, 'codigo_comercial_combo' => $products->codigo_comercial_combo, 'precio_venta_combo' => $products->precio_venta_combo, 'cantidad_producto' => $products->cantidad_producto, 'id_producto' => $products->id_producto, 'codigo_comercial' => $products->code_comercial, 'descripcion' => $products->description];
        }

         $export = new ExpensesExport([
             ['id_combo','nombre_combo','codigo_comercial_combo','precio_venta_combo','cantidad_producto','id_producto','codigo_comercial','descripcion'],
             $datoscombos
        ]);

        return Excel::download($export, 'guia_combos.xlsx');
    }



    public function export($id)
   {

       $export = new ExpensesExport([
            ['id_compra', 'id_inventario', 'id_cuenta','id_sucursal','descripcion','exento','islr','cantidad','precio','tasa'],
            [$id]
       ]);

       return Excel::download($export, 'plantilla_compras.xlsx');
   }

   public function export_guide_account()
   {
        $account_inventory = Account::on(Auth::user()->database_name)->select('id','description')
                                ->where('code_one',1)
                                ->where('code_two', 1)
                                ->where('code_three', 3)
                                ->where('code_four',1)
                                ->where('code_five', '<>',0)
                                ->get();
        $account_costo = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',5)
                                ->where('code_two', '<>',0)
                                ->where('code_three', '<>',0)
                                ->where('code_four', '<>',0)
                                ->where('code_five', '<>',0)->get();

        $export = new ExpensesExport([
            ['id_cuenta','Cuenta'],
            $account_inventory,
            $account_costo
       ]);

       return Excel::download($export, 'guia_cuentas.xlsx');
   }

   public function export_guide_inventory()
   {
        $account_inventory = Inventory::on(Auth::user()->database_name)
                                ->join('products','products.id','inventories.product_id')
                                ->select('inventories.id','products.description')
                                ->orderBy('products.description','asc')
                                ->get();


        $export = new ExpensesExport([
            ['id_inventario','Nombre'],
            $account_inventory
       ]);

       return Excel::download($export, 'guia_inventario.xlsx');
   }

   public function import_account(Request $request)
   {
       $file = $request->file('file');

       Excel::import(new AccountImport, $file);

       return redirect('accounts/menu')->with('success', 'Archivo importado con Exito!');
   }



   public function import_provider(Request $request)
   {
       $file = $request->file('file');

       Excel::import(new ProviderImport, $file);

       return redirect('providers')->with('success', 'Archivo importado con Exito!');
   }

   public function import_client(Request $request)
   {
       $file = $request->file('file');

       Excel::import(new ClientImport, $file);

       return redirect('clients')->with('success', 'Archivo importado con Exito!');
   }



   public function import(Request $request)
   {
       $file = $request->file('file');
       $id_expense = request('id_expense');
       $coin = request('coin_hidde');

       Excel::import(new ExpensesImport, $file);

       return redirect('expensesandpurchases/register/'.$id_expense.'/'.$coin.'')->with('success', 'Archivo importado con Exito!');
   }

   public function import_product(Request $request)
   {
        $total_amount_for_import = 0;
        $file = $request->file('file');
        $cont = 1;
        $msj = '';

        if (!isset($file)){
            return redirect('products/index/todos')->with('danger', 'Para importar productos debe seleccionar un Archivo tipo excel.. El archivo es la plantilla previamente descargada del sistema en el botón Opciones');
        }


        if(isset($file)){

            $rows = Excel::toArray(new ProductReadImport, $file);

            $concatena = '';

            foreach ($rows[0] as $row) {


                if ($row['id'] != ''){

                    $products = Product::on(Auth::user()->database_name)
                    ->select('price','price_buy','money')
                    ->find($row['id']);

                    if (!empty($products)){
                        $msj = '';
                        return redirect('products/index')->with('danger', 'El producto con id '.$row['id'].' ya existe. Fila: '.$cont);
                    }

                    if ($row['id'] <= 0) {
                        return redirect('products/index')->with('danger', 'El valor del id debe ser mayor a cero. Fila: '.$cont);
                    }

                    if ($row['codigo_comercial'] == '') {
                        return redirect('products/index')->with('danger', 'El codigo Comercial es requerido. Fila: '.$cont);
                    }

                   if ($row['id_segmento'] == '') {
                    return redirect('products/index')->with('danger', 'Id Segmento es requerido cree un Segmento en el módulo de Administración y coloque el ID, falta el segmento. Fila: '.$cont);
                   }

                   if (is_numeric($row['id_segmento']) == false) {
                    return redirect('products/index')->with('danger', 'Id Segmento debe ser numero. Fila: '.$cont);
                   }

                   if ($row['id_unidadmedida'] == '') {
                    return redirect('products/index')->with('danger', 'Unidad de Medida es Requerido un ID, falta un producto con unidad de medida. Fila: '.$cont);
                   }

                   if (is_null($row['precio'])) {
                    return redirect('products/index')->with('danger', 'Columna Precio predeterminado es 0 no puede ir vacia la fila o la Columna. Fila: '.$cont);
                   }
                   if (is_null($row['precio_compra'])) {
                    return redirect('products/index')->with('danger', 'Columna Precio de Compra predeterminado es 0 no puede ir vacia la fila o la Columna. Fila: '.$cont);
                   }

                   /*if ($row['exento_1_o_0'] != 0 or $row['exento_1_o_0'] != 1) {
                    return redirect('products/index')->with('danger', 'Columna Excento debe ser 0 o 1. Fila: '.$cont);
                   }

                   if ($row['islr_1_o_0'] != 0 or $row['islr_1_o_0'] != 0) {
                    return redirect('products/index')->with('danger', 'Columna islr debe ser 0 o 1. Fila: '.$cont);
                   }*/

                   if ($row['moneda_d_o_bs'] == '') {
                    return redirect('products/index')->with('danger', 'El tipo de moneda es D para dolares o Bs para Bolivares. Fila: '.$cont);
                   }

                   if ($row['descripcion'] == '') {
                    return redirect('products/index')->with('danger', 'Columna descripcion el producto debe contener un nombre. Fila: '.$cont);
                   }


                   if ($row['tipo_mercancia_o_servicio'] == '') {
                    return redirect('products/index')->with('danger', 'Falta una fila por Tipo de Mercancia, MERCANCIA,SERVICIO,MATERIA PRIMA. Fila: '.$cont);
                   }

                }
                //$concatena .=  ' - '.$row['id'];
                $cont++;
            }

           Excel::import(new ProductImport, $file);

           return redirect('products/index')
            ->with('success', 'Archivo importado con Exito!'.$concatena);



       }else{
            return redirect('products/index')->with('danger', 'Debe seleccionar un archivo');
       }


   }



   public function import_inventary(Request $request)
   {

    $user       =   auth()->user();

        $file = $request->file('file');

        if (!isset($file)){
            return redirect('inventories/index')->with('danger', 'Para importar debe seleccionar un Archivo tipo excel.. El archivo es la plantilla previamente descargada del sistema en el botón Opciones');
        }

        $sistemas = UserAccess::on("logins")
        ->join('modulos','modulos.id','id_modulo')
        ->where('id_user',$user->id)
        ->Where('modulos.estatus','1')
        ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
        ->select('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
        ->groupby('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
        ->get();

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $namemodulomiddleware = $request->get('namemodulomiddleware');

        $rows = Excel::toArray(new ProductReadImport, $file);

        $total_amount_for_import = 0;
        $total_amount_for_import_materiap = 0;
        $cantidad_actual = 0;
        
        foreach ($rows[0] as $row) {


            if ($row['tipo_mercancia_o_servicio'] == 'MERCANCIA'){ 
                $total_amount_for_import += $row['precio_compra'] * $row['cantidad_actual'];
            }
            if ($row['tipo_mercancia_o_servicio'] == 'MATERIAP'){ 
                $total_amount_for_import_materiap += $row['precio_compra'] * $row['cantidad_actual'];
            }
                 
        }

        $products = Product::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->where('status',1)->get();

        $contrapartidas     = Account::on(Auth::user()->database_name)
        ->orWhere('description', 'LIKE','Bancos')
        ->orWhere('description', 'LIKE','Caja')
        ->orWhere('description', 'LIKE','Cuentas por Pagar Comerciales')
        ->orWhere('description', 'LIKE','Capital Social Suscrito y Pagado')
        ->orWhere('description', 'LIKE','Capital Social Suscripto y No Pagado')
        ->orderBY('description','asc')->pluck('description','id')->toArray();

        $global = new GlobalController();

        $bcv = $global->search_bcv();

        return view('admin.inventories.index',compact('namemodulomiddleware','eliminarmiddleware','actualizarmiddleware','agregarmiddleware','sistemas','products','total_amount_for_import','contrapartidas','bcv','total_amount_for_import_materiap'))->with(compact('file'));

    }




    public function import_inventary_cantidad(Request $request)
    {

        $user       =   auth()->user();
        $file = $request->file('file');
        $tipo = $request->tipo;
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $contrapartida = $request->subcontrapartida2;

         if (!isset($file)){
             return redirect('inventories/index')->with('danger', 'Para importar debe seleccionar un Archivo tipo excel.. El archivo es la plantilla previamente descargada del sistema en el botón Opciones');
         }

         if($tipo != 'AI' AND $tipo != 'DI'){ //VERIFICO QUE SE SELECCIONE UNA OPCION CORRECTA
            return redirect('inventories/index')->with('danger', 'Seleccione un Tipo de Acción');

         }

         $rows = Excel::toArray(new ProductReadImport, $file);

         $global = new GlobalController();

         $bcv = $global->search_bcv();

         foreach ($rows[0] as $row) {

            if($row['tipo_mercancia_o_servicio'] == 'MERCANCIA' OR $row['tipo_mercancia_o_servicio'] == 'MATERIAP'){



                $products = Product::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->where('status',1)->where('id',$row['id'])->first();

                if($products != null){
                   $id_account = $products->id_account;
                }else{
                    $account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('description','LIKE','%Materia Prima%')->first();
                    $id_account = $account_gastos_ajuste_inventario->id;
                }

                if($row['moneda_d_o_bs'] == 'Bs'){
                    $total = $row['cantidad_actual'] * $row['precio'];
                }else{
                    $total = $row['cantidad_actual'] * $row['precio'] * $bcv;
                }

                if($tipo == 'AI'){
                    $global->transaction_inv('entrada',$row['id'],'Salida de Inventario',$row['cantidad_actual'],$row['precio'],$datenow,1,1,0,0,0,0,0);

                    $header_voucher  = new HeaderVoucher();
                    $header_voucher->setConnection(Auth::user()->database_name);
                    $header_voucher->description = "Incremento de Inventario";
                    $header_voucher->date = $datenow;
                    $header_voucher->status =  "1";
                    $header_voucher->save();

                    $this->add_movementinvt($bcv,$header_voucher->id,$id_account,$user->id,$total,0);

                    $account_counterpart = Account::on(Auth::user()->database_name)->find($contrapartida);

                    $this->add_movementinvt($bcv,$header_voucher->id,$account_counterpart->id,$user->id,0,$total);

                }elseif($tipo == 'DI'){

                $global->transaction_inv('salida',$row['id'],'Salida de Inventario',$row['cantidad_actual'],$row['precio'],$datenow,1,1,0,0,0,0,0);


                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = "Disminucion de Inventario";
                $header_voucher->date = $datenow;
                $header_voucher->status =  "1";
                $header_voucher->save();


                $this->add_movementinvt($bcv,$header_voucher->id,$id_account,$user->id,0,$total);
                $account_gastos_ajuste_inventario = Account::on(Auth::user()->database_name)->where('description','LIKE','%Gastos de ajuste de inventario%')->first();
                $this->add_movementinvt($row['id'],$header_voucher->id,$account_gastos_ajuste_inventario->id,$user->id,$total,0);


                }

                if ($row['precio_compra'] > 0) {
                    $precio_compra = $row['precio_compra'];
                    Product::on(Auth::user()->database_name)->where('id',$row['id'])->update(['price_buy' => $precio_compra]);
                }
                if ($row['precio'] > 0) {
                    $precio = $row['precio'];
                    Product::on(Auth::user()->database_name)->where('id',$row['id'])->update(['price' => $precio]);
                }

            }

         }

         return redirect('inventories/index')->with('success', 'Se actualizo la cantidad correctamente');



     }



   public function import_combo(Request $request)
   {

    $user       =   auth()->user();

            $file = $request->file('file');

            if (!isset($file)){
                return redirect('combos')->with('danger', 'Para importar Combos debe seleccionar un Archivo tipo excel.. El archivo es la plantilla previamente descargada del sistema en el botón Opciones');
            }


                if (isset($file)) {

                    $costo_calculado = '';

                    Excel::import(new ComboImport, $file);

                    $rows = Excel::toArray(new ProductReadImport, $file);

                    foreach ($rows[0] as $row) {

                        if ($row['id_producto'] != ''){
                            $products = Product::on(Auth::user()->database_name)
                            ->select('price','price_buy','money')
                            ->find($row['id_producto']);

                            $precio_compra = $products->price_buy;

                        } else {
                            $precio_compra = 0;
                        }

                        $a_filas[] = array($row['id_combo'],$row['id_producto'],$row['cantidad_producto'],$precio_compra,$row['cantidad_producto']*$precio_compra,0,0,$row['codigo_comercial_combo']);

                    }



                    for ($q=0;$q<count($a_filas);$q++) {

                        for ($k=$q+1; $k<count($a_filas);$k++) {
                            if ($a_filas[$q][0] == $a_filas[$k][0]) {
                              $a_filas[$q][4] = $a_filas[$q][4]+$a_filas[$k][4];
                              $a_filas[$k][0]=0;
                            }

                        }
                    }


                    for ($q=0;$q<count($a_filas);$q++) {
                        $total_precio_compra = 0;

                        if ($a_filas[$q][0] != 0){

                            $total_precio_compra = $a_filas[$q][4];
                            Product::on(Auth::user()->database_name)->where('id',$a_filas[$q][0])->update([ 'price_buy' => $total_precio_compra]);


                            $inv = Inventory::on(Auth::user()->database_name)
                            ->where('product_id',$a_filas[$q][0])->get();


                            if($inv->count() == 0){

                                $inventory = new Inventory();
                                $inventory->setConnection(Auth::user()->database_name);

                                $inventory->product_id = $a_filas[$q][0];
                                $inventory->id_user = $user->id;
                                $inventory->code = $a_filas[$q][7];
                                $inventory->amount = 0;
                                $inventory->status = 1;

                                $inventory->save();

                            }




                        }
                    }



                    return redirect('combos')->with('success', 'Archivo importado con Exito!');

                } else {

                     return redirect('combos')->with('success', 'Subir el Archivo Excel!');;
                }



   }

   public function import_product_procesar(Request $request)
   {


       if(isset($request->Subcontrapartida)){

            $subcontrapartida = $request->Subcontrapartida;
            $amount = $request->amount;
            $amountp = $request->amountp;
            $rate = str_replace(',', '.', str_replace('.', '', $request->rate));

            $file = $request->file('file');

            $coin = request('coin');

            Excel::import(new InventoryImport, $file);

            $movement = new MovementProductImportController();
            $movement->add_movement($subcontrapartida,$amount,$amountp,$rate,$coin);


            return redirect('inventories/index')->with('success', 'Archivo importado con Exito!');

       }else{
            return redirect('inventories/index')->with('danger', 'Debe seleccionar una cuenta de pago');
       }

   }



   public function import_product_update_price(Request $request) // INVENTARIO
   {

       $file = $request->file('file');

       Excel::import(new ProductUpdatePriceImport, $file);

       return redirect('products/index/todos')->with('success', 'Se han actualizado los precios Correctamente');
   }






   public function add_movementinvt($tasa,$id_header,$id_account,$id_user,$debe,$haber){

    $detail = new DetailVoucher();
    $detail->setConnection(Auth::user()->database_name);

    $detail->id_account = $id_account;
    $detail->id_header_voucher = $id_header;
    $detail->user_id = $id_user;
    $detail->tasa = $tasa;

  /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
    $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/


    $detail->debe = $debe;
    $detail->haber = $haber;

    $detail->status =  "C";

    $detail->save();

     /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */

     $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

     if($account->status != "M"){
         $account->status = "M";
         $account->save();
     }


}



}
