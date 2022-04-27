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
use App\Exports\DailyListing\JournalBookExportFromView;
use App\Http\Controllers\Calculations\AccountCalculationController;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class JournalBookExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
        
        $export = new JournalBookExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Libro Diario.xlsx');
    }

    public function print_journalbook(Request $request)
    {

        
        $date_begin = request('date_begin');
        $date_end = request('date_end');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');

        $pdf = App::make('dompdf.wrapper');

        $id_account = request('id_account');

        
        $company = Company::on(Auth::user()->database_name)->find(1);

        if(isset($id_account)){
           
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('header_vouchers')
            ->join('detail_vouchers', 'detail_vouchers.id_header_voucher', '=', 'header_vouchers.id')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->whereIn('header_vouchers.id', function($query) use ($id_account){
                $query->select('id_header_voucher')
                ->from('detail_vouchers')
                ->where('id_account',$id_account);
            })
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')->get();
        }else{
           
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')->get();
        }
        
        $date_begin = Carbon::parse($date_begin)->format('d-m-Y');

        $date_end = Carbon::parse($date_end)->format('d-m-Y');

      
        return view('export_excel.daily_listing.journal_book',compact('company','detailvouchers'
        ,'datenow','date_begin','date_end'));
     
    }


   
}
