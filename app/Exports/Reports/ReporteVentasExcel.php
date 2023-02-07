<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\SalesBookExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ReporteVentasExcel implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new SalesBookExportController();

        return $report->ventasreportepdf($this->request->coin,$this->request->date_begin,$this->request->date_end,$this->request->type,$this->request->name);
    }



    public function setter($request){
        $this->request = $request;
     }


}
