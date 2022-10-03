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

class ExcelController extends Controller
{

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

   /* public function export_product() // producto
    {
         $products = Product::on(Auth::user()->database_name)
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
        
        return Excel::download($export, 'guia_productos.xlsx');
    }*/
    

    public function export_product() // inventario 
    {
         $products = Product::on(Auth::user()->database_name)
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
              'exento_1_o_0','islr_1_o_0','cantidad_actual'],
              $products
        ]);
        
        return Excel::download($export, 'guia_productos.xlsx');
    }


    public function export_combo() // inventario 
    {
         $products = Product::on(Auth::user()->database_name)
         ->where('status','1')
         ->where('type','!=','COMBO')
         ->where('type','!=','SERVICIO')
         ->select('id as id_combo','id as nombre_combo','id as codigo_comercial_combo','id as precio_venta_combo','id as cantidad_producto','id as id_producto','code_comercial','description')
         ->get();

         $global = new GlobalController(); 

         $last_combo = Product::on(Auth::user()->database_name)
         ->where('status','1')
         ->select('id')
         ->get()->last();

         if (!empty($last_combo)) {
            $id_last_combo = $last_combo->id + 1; 
         } else {
            $id_last_combo = 1;
         }
              
         $cont = 0;

         foreach ($products as $product) {  // ingresar el monto de inventario al array producto por la funciuon $global->consul_prod_invt()
            /*$buscar_num = $global->consul_prod_invt($product->id);

            if($buscar_num < 0 || $buscar_num == '0' || $buscar_num == 0 || $buscar_num == '' || $buscar_num == ' ' || $buscar_num == false || $buscar_num == NULL) {
            
             $product->amount = '0.00';

            } else {
                $product->amount = $buscar_num;  
            }*/
            $product->precio_venta_combo = '0';
            $product->cantidad_producto = '0';
            $product->nombre_combo = '';
            $product->codigo_comercial_combo = '';

            if ($cont == 0) {
            $product->id_combo = $id_last_combo;  
            } else{
            $product->id_combo = '';    
            } 

            $cont++;
        }  

        
         $export = new ExpensesExport([
             ['id_combo','nombre_combo','codigo_comercial_combo','precio_venta_combo','cantidad_producto','id_producto','codigo_comercial','descripcion'],
              $products
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

    
        if(isset($file)){
            
            $rows = Excel::toArray(new ProductReadImport, $file);


            foreach ($rows[0] as $row) {


                if ($row['id'] != ''){
                    
                    $products = Product::on(Auth::user()->database_name)
                    ->select('price','price_buy','money')
                    ->find($row['id']);

                    if (!empty($products)){
                        return redirect('products')->with('danger', 'El producto con id '.$row['id'].' ya existe');
                    }


                    
                    if ($row['codigo_comercial'] == '') {
                        return redirect('products')->with('danger', 'El codigo Comercial es requerido ');
                    }
        
                   if ($row['id_segmento'] == '') {
                    return redirect('products')->with('danger', 'Id Segmento es Requerido Cree Segmento y coloque un ID, falta un producto con segmento');
                   }
    
                   if ($row['id_unidadmedida'] == '') {
                    return redirect('products')->with('danger', 'Unidad de Medida es Requerido un ID, falta un producto con unidad de medida');
                   }
    
                   if ($row['precio'] == '') {
                    return redirect('products')->with('danger', 'Columna Precio predeterminado es 0 no puede ir vacia la fila o la Columna');
                   }
                   if ($row['precio_compra'] == '') {
                    return redirect('products')->with('danger', 'Columna Precio de Compra predeterminado es 0 no puede ir vacia la fila o la Columna');
                   }
    
                   /*if ($row['exento_1_o_0'] != 0 or $row['exento_1_o_0'] != 1) {
                    return redirect('products')->with('danger', 'Columna Excento debe ser 0 o 1');
                   }
    
                   if ($row['islr_1_o_0'] != 0 or $row['islr_1_o_0'] != 0) {
                    return redirect('products')->with('danger', 'Columna islr debe ser 0 o 1');
                   }*/
    
                   if ($row['moneda_d_o_bs'] == '') {
                    return redirect('products')->with('danger', 'El tipo de moneda es D para dolares o Bs para Bolivares');
                   }
                   
                   if ($row['descripcion'] == '') {
                    return redirect('products')->with('danger', 'Columna descripcion el producto debe contener un nombre');
                   }
    
    
                   if ($row['tipo_mercancia_o_servicio'] == '') {
                    return redirect('products')->with('danger', 'Falta una fila por Tipo de Mercancia, MERCANCIA,SERVICIO,MATERIA PRIMA');
                   }


                } 

                Excel::import(new ProductImport, $file);

                return redirect('products')
                ->with('success', 'Archivo importado con Exito!');
  
            }



       }else{
            return redirect('products')->with('danger', 'Debe seleccionar un archivo');
       }


   }

   public function import_combo(Request $request) 
   {

            $file = $request->file('file');

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

                        $a_filas[] = array($row['id_combo'],$row['id_producto'],$row['cantidad_producto'],$precio_compra,$row['cantidad_producto']*$precio_compra,0);    
                        
                    }

                    for ($q=0;$q<count($a_filas);$q++) {

                        for ($k=$q+1; $k<count($a_filas);$k++) {
                            if ($a_filas[$q][0] == $a_filas[$k][0]) {
                              $a_filas[$q][5] = $a_filas[$q][4]+$a_filas[$k][4];
                              $a_filas[$k][5]=0; 
                            }
                
                        }
                    }
                     
                    for ($q=0;$q<count($a_filas);$q++) {
                        $total_precio_compra = 0;
                        
                        if ($a_filas[$q][5] != 0){
                           
                            $total_precio_compra = $a_filas[$q][5];
                            Product::on(Auth::user()->database_name)->where('id',$a_filas[$q][0])->update([ 'price_buy' => $total_precio_compra]);


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
            $rate = str_replace(',', '.', str_replace('.', '', $request->rate));
    
            $file = $request->file('file');
            
            Excel::import(new ProductImport, $file);
            Excel::import(new InventoryImport, $file);

            $movement = new MovementProductImportController();
            $movement->add_movement($subcontrapartida,$amount,$rate);
            
            return redirect('products')->with('success', 'Archivo importado con Exito!');

       }else{
            return redirect('products')->with('danger', 'Debe seleccionar una cuenta de pago');
       }
       
   }

   /*public function import_product_update_price(Request $request) //producto
   {
       
       $file = $request->file('file');
       
       Excel::import(new ProductUpdatePriceImport, $file);
       
       return redirect('products')->with('success', 'Se han actualizado los precios Correctamente');
   } */


   public function import_product_update_price(Request $request) // INVENTARIO
   {
       
       $file = $request->file('file');
       
       Excel::import(new ProductUpdatePriceImport, $file);
       
       return redirect('products')->with('success', 'Se han actualizado los precios Correctamente');
   }



}
