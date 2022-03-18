<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UserAccess\UserAccessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public $userAccess;
    public $modulo = 'Venta';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }
 
 
    public function index()
    {
        if($this->userAccess->validate_user_access($this->modulo)){
            $user       =   auth()->user();
            $users_role =   $user->role_id;
        
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('inventories', 'products.id', '=', 'inventories.product_id')
                                ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                ->join('quotations', 'quotations.id', '=', 'quotation_products.id_quotation')
                                ->where('quotation_products.status','C')
                                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'),'products.type','products.price as price','inventories.code','products.money as money')
                                ->groupBy('products.description','products.type','products.price','inventories.code','products.money')
                                ->get(); 
            $bcv = null;

    
            return view('admin.sales.index',compact('inventories_quotations','bcv'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }



 
}
