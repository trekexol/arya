<?php

namespace App\Http\Controllers\Exports\Reports;

use App\Exports\Reports\AccountReceivableExportFromView;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AccountReceivableExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
       
         $export = new AccountReceivableExportFromView($request);

         $export->setter($request);
 
         $export->view();       
         
         return Excel::download($export, 'cuentas_por_cobrar.xlsx');
    }
}
