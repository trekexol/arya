<?php

namespace App\Exports\DailyListing;


use App\Http\Controllers\Exports\DailyListing\LedgerExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class LedgerExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new LedgerExportController();

       
        return $report->ledger_pdf($this->request->date_begin ?? null,$this->request->date_end ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
