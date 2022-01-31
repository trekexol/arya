<?php

namespace App\Http\Controllers\Reports;

use App;
use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GlobalController;
use App\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShoppingController extends Controller
{
    public function index_shopping()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.shopping.index_shopping',compact('datebeginyear','datenow'));
      
    }

    public function store_shopping(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $name = request('name');
        $coin = request('coin');
        
        return view('admin.reports.shopping.index_shopping',compact('name','coin','date_begin','date_end'));
    }

    function shopping_pdf($coin,$date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 

        if(isset($name)){
            $shoppings = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('expenses_details', 'expenses_details.id_inventory', '=', 'inventories.id')
            ->leftjoin('segments', 'segments.id', '=', 'products.segment_id')
            ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
            ->where('expenses_details.status','C')
            ->where('expenses_details.description','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(expenses_details.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(expenses_details.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.amount) as amount_shopping'), DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.price*expenses_details.amount) as price_shopping'), DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.price*expenses_details.amount/expenses_details.rate) as price_shopping_dolar'),'products.type','products.price as price','inventories.code','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
            ->groupBy('products.description','products.type','products.price','inventories.code','products.money','segments.description','subsegments.description')
            ->orderBy('products.description','asc')->get();
           
        }else{
            $shoppings = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('expenses_details', 'expenses_details.id_inventory', '=', 'inventories.id')
            ->leftjoin('segments', 'segments.id', '=', 'products.segment_id')
            ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
            ->where('expenses_details.status','C')
            ->whereRaw(
                "(DATE_FORMAT(expenses_details.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(expenses_details.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.amount) as amount_shopping'), DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.price*expenses_details.amount) as price_shopping'), DB::connection(Auth::user()->database_name)->raw('SUM(expenses_details.price*expenses_details.amount/expenses_details.rate) as price_shopping_dolar'),'products.type','products.price as price','inventories.code','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
            ->groupBy('products.description','products.type','products.price','inventories.code','products.money','segments.description','subsegments.description')
            ->orderBy('products.description','asc')->get();
           
        }
        
        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $global->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

       
        $pdf = $pdf->loadView('admin.reports.shopping.shopping',compact('coin','rate','shoppings','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }
}
