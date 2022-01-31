<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\BalanceGeneralExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class BalanceGeneralExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new BalanceGeneralExportController();
        
        return $report->balance_pdf($this->request->coin ?? "bolivares",$this->request->date_begin,$this->request->date_end,
                                        $this->request->level ?? null);
    }
   
    public function setter($request){
        $this->request = $request;
     }

    
}
