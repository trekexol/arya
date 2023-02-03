<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Client;
use App\ExpensesAndPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Reports\PurchasesBookExportFromView;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Quotation;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PurchasesBookExportController extends Controller
{
    public function exportExcel(Request $request)
    {



        $export = new PurchasesBookExportFromView($request);

        $export->setter($request);

        $export->view();

        return Excel::download($export, 'libro_compras.xlsx');
    }

    function purchases_books_pdf($coin,$date_begin,$date_end)
    {

        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
        $period = $date->format('Y');
        $a_total = array();
        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)
                                    ->where('amount','<>',null)
                                    ->whereRaw(
                                        "(DATE_FORMAT(date, '%Y-%m-%d') >= ? AND DATE_FORMAT(date, '%Y-%m-%d') <= ?)",
                                        [$date_begin, $date_end])
                                    ->orderBy('date','desc')->get();


            foreach ($expenses as $expense) {
                /*$total_exentoG = ExpensesDetail::on(Auth::user()->database_name)
                ->where('id_expense',$expense->id)
                ->where('exento','1')
                ->orderBy('id_expense','asc')
                ->get(); */
                //->select(DB::connection(Auth::user()->database_name)->raw('price*amount as totalG,id_expense as id_expense'))->get();
                $total_exentoG = 0;
                $total_exentoG = DB::connection(Auth::user()->database_name)->table('expenses_details')
                ->where('id_expense',$expense->id)
                ->where('exento','1')
                //>sum('price * amount as suma')
                //->select('price','amount','id_expense')->get();
                ->select(DB::connection(Auth::user()->database_name)->raw('SUM(price*amount) as total'))->get();
                //->select('price','amount','id_expense')->get();
                //$a_total[] = array(bcdiv($total_exentoG[0]->total,'1',2),$expense->id);


                $a_total[] = [bcdiv($total_exentoG[0]->total,'1',2),$expense->id];


            }

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        return view('export_excel.purchases_books',compact('coin','expenses','datenow','date_begin','date_end','a_total'));


    }
}
