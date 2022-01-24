<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\DebtsToPayExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class DebtsToPayExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new DebtsToPayExportController();
         
        
        return $report->debtstopay_pdf(
            $this->request->coin ?? "bolivares",$this->request->date_end,
            $this->request->typeinvoice,$this->request->type,
            $this->request->id_client ?? $this->request->id_vendor ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
