<?php

namespace App\Http\Controllers;

use App\QuotationProduct;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{

    public function index()
    {
        $quotation_products = QuotationProduct::on(Auth::user()->database_name)
        ->join('products','products.id','quotation_products.id_inventory')
        ->where('id_quotation','3893')

        ->get();





    foreach($quotation_products as  $quotation_pro){


        $affected_debe = DB::connection(Auth::user()->database_name)
        ->table('detail_vouchers')
        ->where('id_invoice', '=', $quotation_pro->id_quotation)
        ->where('id_account','302')
        ->update(array('debe' => ($quotation_pro->price_buy*$quotation_pro->rate) * $quotation_pro->amount));
        
    }

        
     dd('hecho');

    
        return view('admin.developer.index',compact('quotation_products'));
      
    }

    

}
