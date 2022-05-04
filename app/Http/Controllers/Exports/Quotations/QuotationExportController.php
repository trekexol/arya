<?php

namespace App\Http\Controllers\Exports\Quotations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Quotations\QuotationExportFromView;
use App\Quotation;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class QuotationExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
        
        $export = new QuotationExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Cotizaciones.xlsx');
    }

    public function pdfQuotation(Request $request)
    {
        $date_begin = request('date_begin');
        $date_end = request('date_end');
 
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
 
        $pdf = App::make('dompdf.wrapper');
 
        $id_client = request('id_client');
 
        $coin = request('coin');
        
        $company = Company::on(Auth::user()->database_name)->find(1);

        if(isset($id_client)){
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->where('date_order','=',null)
            ->where('id_client',$id_client)
            ->whereBetween('date_quotation', [$date_begin, $date_end])->get();
        }else{
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->where('date_order','=',null)
            ->whereBetween('date_quotation', [$date_begin, $date_end])->get();
        }

        return view('export_excel.quotations.quotation',compact('company','quotations'
        ,'datenow','date_begin','date_end'));
     
    }


   
}
