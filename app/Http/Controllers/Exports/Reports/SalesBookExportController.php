<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\SalesBookExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Quotation;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

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
                                    ->where('status','C')
                                    ->whereRaw(
                                        "(DATE_FORMAT(date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(date_billing, '%Y-%m-%d') <= ?)", 
                                        [$date_begin, $date_end])
                                    ->orderBy('date_billing','desc')->get();

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        return view('export_excel.sales_books',compact('coin','quotations','datenow','date_begin','date_end'));
          
       
    }
}
