<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\AnticipoExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AnticipoExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new AnticipoExportController();

      
        return $report->payment_pdf($this->request->coin ?? "bolivares",$this->request->date_begin,
        $this->request->date_end,$this->request->type ?? 'Todo',
         $this->request->id_provider ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
