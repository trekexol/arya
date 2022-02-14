<?php

namespace App\Http\Controllers\Calculations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class FacturaCalculationController extends Controller
{
    //CALCULA EL TOTAL POR CUENTA QUE SE LE ASIGNO A UN PRODUCTO
    public function calculateTotalForAccount($id_quotation){
    
        $total = DB::connection(Auth::user()->database_name)->table('products')
                ->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$id_quotation)
                ->whereIn('quotation_products.status',['1','C'])
                ->select('products.id_account',DB::connection(Auth::user()->database_name)->raw('SUM(products.price_buy * quotation_products.amount) as total'))
                ->groupBy('products.id_account')
                ->get();
                
        
        return $total; 

    }
}
