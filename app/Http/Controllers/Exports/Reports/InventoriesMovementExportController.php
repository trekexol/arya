<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\InventoriesMovementExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\InventoryHistories;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class InventoriesMovementExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
      
        $export = new InventoriesMovementExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'historial_inventario.xlsx');
    }

    public function movements_pdf($coin,$date_frist,$date_end,$type,$id_inventory,$id_account) 
   {
    
        $pdf = App::make('dompdf.wrapper');

        $global = new GlobalController();

        
        
        if($date_frist == 'todo'){
            $date_frist = $global->data_first_month_day();
            }

        if($date_end == 'todo'){
            $date_end =  $global->data_last_month_day();
        } 
                
        if ($type == 'todo') {
                $cond = '!=';
                $type = '';
            
            } else {
                $cond = '=';
                
            }


            if($id_inventory == 'todos') {
                $cond2 = '!=';
                $id_inventory = 'r';
            
            } else {
                $cond2 = '=';
                
            }

        
            if($id_account == 'todas') {
                $cond3 = '!=';
                $id_account = 'r';
            
            } else {
                $cond3 = '=';
                
            }

        $inventories = InventoryHistories::on(Auth::user()->database_name)
        ->join('inventories','inventories.id','inventory_histories.id_product')     
        ->join('products','products.id','inventories.product_id')
        ->where('inventory_histories.date','>=',$date_frist)
        ->where('inventory_histories.date','<=',$date_end)
        ->where('inventory_histories.type',$cond,$type)
        ->where('inventory_histories.id_product',$cond2,$id_inventory)
        ->where('products.id_account',$cond3,$id_account)
        //->where('inventory_histories.status','A')
        //->select('inventory_histories.id as id_inventory','inventory_histories.amount_real as amount_real','products.id as id','products.code_comercial as code_comercial','products.description as description','products.price as price','products.photo_product as photo_product')       
        ->orderBy('inventory_histories.id' ,'ASC')
        ->select('inventory_histories.*','products.id as id_product_pro','products.code_comercial as code_comercial','products.description as description')  
        ->get();     
        //$coin,$date_frist,$date_end,$type,$id_inventory
       
        foreach ($inventories as $inventorie) {
            
            $invoice = DB::connection(Auth::user()->database_name)
            ->table('quotations')
            ->where('id','=',$inventorie->id_quotation)
            ->select('number_invoice')
            ->get()->last(); 

            $note = DB::connection(Auth::user()->database_name)
            ->table('quotations')
            ->where('id','=',$inventorie->id_quotation)
            ->select('number_delivery_note')
            ->get()->last(); 


            $branch = DB::connection(Auth::user()->database_name)
            ->table('branches')
            ->where('id','=',$inventorie->id_branch)
            ->select('description')
            ->get()->last();         

            if (!empty($invoice)) {

            $inventorie->invoice = $invoice->number_invoice;

            } else {


                if ($inventorie->id_expense == 0) {
                    
                    if ($inventorie->type == 'venta' || $inventorie->type == 'rev_venta'){ 
                    $inventorie->invoice = $inventorie->id_quotation;  
                    } else {
                    $inventorie->invoice = '';       
                    }
                } else {

                    $inventorie->invoice = '';   
                }
            
            }
            
            if (!empty($note)) {
            $inventorie->note = $note->number_delivery_note; 
            } else {
                if ($inventorie->id_expense == 0) {
                    
                    if ($inventorie->type == 'nota' || $inventorie->type == 'rev_nota' || $inventorie->type == 'aju_nota'){ 
                    $inventorie->note =  $inventorie->id_quotation;  
                    } else {
                    $inventorie->note = '';       
                    }
                } else {

                    $inventorie->note= '';   
                }
            }
            if (!empty($branch)) {
            $inventorie->branch = $branch->description;
            } else {
                $inventorie->branch = '';
            }
    }
  

    return view('export_excel.movements',compact('coin','inventories'));
            

   
   }
}
