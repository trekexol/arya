<?php

namespace App\Http\Controllers\Exports\DailyListing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Account;
use App\Client;
use App\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\DailyListing\LedgerExportFromView;
use App\Http\Controllers\Calculations\AccountCalculationController;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LedgerExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
        
        $export = new LedgerExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Libro Diario.xlsx');
    }

    function ledger_pdf($date_begin = null,$date_end = null)
    {
      
        $pdf = App::make('dompdf.wrapper');

        $company = Company::on(Auth::user()->database_name)->find(1);
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 
       
        if(isset($date_begin)){
            $from = $date_begin;
        }
        if(isset($date_end)){
            $to = $date_end;
        }else{
            $to = $datenow;
        }

        $details = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                ->join('accounts', 'accounts.id','=','detail_vouchers.id_account')
                ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
                ->select('accounts.code_one','accounts.code_two','accounts.code_three'
                        ,'accounts.code_four','accounts.code_five','accounts.description as account_description'
                        ,'detail_vouchers.debe','detail_vouchers.haber'
                        ,'header_vouchers.description as header_description'
                        ,'header_vouchers.id as id_header'
                        ,'header_vouchers.date as date')
                ->whereRaw(
                            "(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)", 
                            [$date_begin, $date_end])
                ->whereIn('detail_vouchers.status', ['F','C'])
                ->orderBy('accounts.code_one','asc')
                ->orderBy('accounts.code_two','asc')
                ->orderBy('accounts.code_three','asc')
                ->orderBy('accounts.code_four','asc')
                ->orderBy('accounts.code_five','asc')
                ->get();

                
        return view('export_excel.daily_listing.ledger',compact('company','datenow','details','date_begin','date_end'));
     
                 
    }


   
}
