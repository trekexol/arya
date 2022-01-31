<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\IngresosEgresosExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class IngresosEgresosExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new IngresosEgresosExportController();
        
        return $report->balance_ingresos_pdf($this->request->coin ?? "bolivares",$this->request->date_begin,$this->request->date_end,
                                        $this->request->level ?? null);
                                        
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
