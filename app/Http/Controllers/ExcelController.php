<?php

namespace App\Http\Controllers;

use App\Account;
use App\Client;
use App\Exports\ExpensesExport;
use App\Imports\AccountImport;
use App\Imports\ClientImport;
use App\Imports\ExpensesImport;
use App\Imports\ProductImport;
use App\Imports\ProductReadImport;
use App\Imports\ProductUpdatePriceImport;
use App\Imports\ProviderImport;
use App\Inventory;
use App\Product;
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

    public function export_product() 
    {
         $products = Product::on(Auth::user()->database_name)
         ->join('inventories','inventories.product_id','=','products.id')
         ->select('products.id','segment_id','subsegment_id','twosubsegment_id','threesubsegment_id','unit_of_measure_id',
         'code_comercial','type','description','price','price_buy','cost_average','photo_product','money',
         'exento','islr','inventories.amount')
         ->get();

        
         $export = new ExpensesExport([
             ['id','id_segmento','id_subsegmento','id_twosubsegment','id_threesubsegment','id_unidadmedida'
              ,'codigo_comercial','tipo_mercancia_o_servicio','descripcion','precio','precio_compra','costo_promedio','foto','moneda_d_o_bs',
              'exento_1_o_0','islr_1_o_0','Cantidad en Inventario'],
              $products
        ]);
        
        return Excel::download($export, 'guia_productos.xlsx');
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
        $file = $request->file('file');

        $rows = Excel::toArray(new ProductReadImport, $file);
      
        $total_amount_for_import = 0;
       
        foreach ($rows[0] as $row) {
            $total_amount_for_import += $row['precio_compra'] * $row['cantidad_en_inventario'];
        }

        $products = Product::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->where('status',1)->get();

        $contrapartidas     = Account::on(Auth::user()->database_name)
        ->orWhere('description', 'LIKE','Bancos')
        ->orWhere('description', 'LIKE','Caja')
        ->orWhere('description', 'LIKE','Cuentas por Pagar Comerciales')
        ->orWhere('description', 'LIKE','Capital Social Suscrito y Pagado')
        ->orWhere('description', 'LIKE','Capital Social Suscripto y No Pagado')
        ->orderBY('description','asc')->pluck('description','id')->toArray();

        
        return view('admin.products.index',compact('products','total_amount_for_import','contrapartidas'))->with(compact('file'));
   }

   public function import_product_procesar(Request $request) 
   {
       dd($request);
       
       $file = $request->file('file');
       
       Excel::import(new ProductImport, $file);
       
       return redirect('products')->with('success', 'Archivo importado con Exito!');
   }

   public function import_product_update_price(Request $request) 
   {
       
       $file = $request->file('file');
       
       Excel::import(new ProductUpdatePriceImport, $file);
       
       return redirect('products')->with('success', 'Se han actualizado los precios Correctamente');
   }

}
