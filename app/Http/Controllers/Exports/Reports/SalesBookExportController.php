<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\SalesBookExportFromView;
use App\Exports\Reports\ReporteVentasExcel;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Quotation;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Company;

class SalesBookExportController extends Controller
{
    public function exportExcel(Request $request)
    {



        $export = new SalesBookExportFromView($request);

        $export->setter($request);

        $export->view();

        return Excel::download($export, 'libro_ventas.xlsx');
    }

    function sales_books_pdf($coin,$date_begin,$date_end)
    {

        $pdf = App::make('dompdf.wrapper');


        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
        $period = $date->format('Y');

        $quotations = Quotation::on(Auth::user()->database_name)
                                    ->where('date_billing','<>',null)
                                    ->whereRaw("(DATE_FORMAT(date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(date_billing, '%Y-%m-%d') <= ?)", [$date_begin, $date_end])
                                    ->orderBy('number_invoice','asc')->get();




        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        return view('export_excel.sales_books',compact('coin','quotations','datenow','date_begin','date_end'));


    }



    public function ventasreporte(Request $request)
    {



        $export = new ReporteVentasExcel($request);

        $export->setter($request);

        $export->view();

        return Excel::download($export, 'reporteventas.xlsx');
    }



    function ventasreportepdf($coin,$date_begin,$date_end,$type,$name)
    {


        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
        $period = $date->format('Y');
        //$type = 'todo';


        if($name != 'nada'){

            if($type == 'todo') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')

                ->whereRaw(
                "(DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') <= ?)",
                [$date_begin, $date_end])
                ->orwhereRaw(
                    "(DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') <= ?)",
                    [$date_begin, $date_end])
                    //  ->where('quotations.date_delivery_note','!=',null)
                    // ->orwhere('quotations.date_billing','!=',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                    ->where('products.description','LIKE','%'.$name.'%')
                ->select('quotations.number_invoice as invoice','products.description',DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('invoice','note','products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();


                $invoices = '';
                $notes = '';

                foreach ($sales as $sale) {

                    $sales->invoices = $sale->number_delivery;
                    $sales->notes = $sale->number_delivery_note;


                }


            }
            if($type == 'notas') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
                ->whereRaw(
                    "(DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') <= ?)",
                    [$date_begin, $date_end])
                    ->where('quotations.date_delivery_note','!=',null)
                    ->where('quotations.date_billing','=',null)
                    // ->orwhere('quotations.date_billing','!=',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                    ->where('products.description','LIKE','%'.$name.'%')
                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();
            }
            if($type == 'facturas') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')

                ->whereRaw(
                "(DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') <= ?)",
                [$date_begin, $date_end])

                    ->where('quotations.date_billing','<>',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                    ->where('products.description','LIKE','%'.$name.'%')
                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();
            }


        }else{ //////////////////////////////sin busqueda/////////////////////////////////////////////////////////////////

            if($type == 'todo') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')

                ->whereRaw(
                "(DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') <= ?)",
                [$date_begin, $date_end])
                ->orwhereRaw(
                    "(DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') <= ?)",
                    [$date_begin, $date_end])
                    //  ->where('quotations.date_delivery_note','!=',null)
                    // ->orwhere('quotations.date_billing','!=',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();
            }
            if($type == 'notas') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
                ->whereRaw(
                    "(DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_delivery_note, '%Y-%m-%d') <= ?)",
                    [$date_begin, $date_end])
                    ->where('quotations.date_delivery_note','!=',null)
                    ->where('quotations.date_billing','=',null)
                    // ->orwhere('quotations.date_billing','!=',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();
            }
            if($type == 'facturas') {
                $sales = Quotation::on(Auth::user()->database_name)
                ->join('quotation_products', 'quotation_products.id_quotation', '=', 'quotations.id')
                ->join('products', 'products.id', '=', 'quotation_products.id_inventory')
                ->join('segments', 'segments.id', '=', 'products.segment_id')
                ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')

                ->whereRaw(
                "(DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotations.date_billing, '%Y-%m-%d') <= ?)",
                [$date_begin, $date_end])

                     ->where('quotations.date_billing','<>',null)
                    //->where('quotations.status','!=','X')
                    ->where('quotation_products.status','!=','X')
                ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','products.code_comercial','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
                ->groupBy('products.description','products.type','products.price','products.price_buy','products.code_comercial','products.money','segments.description','subsegments.description')
                ->orderBy('products.description','asc')->get();
            }
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



        return view('admin.reports.reporteventasexcel',compact('coin','rate','sales','datenow','date_begin','date_end','type'));


    }

}
