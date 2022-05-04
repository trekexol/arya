<?php

namespace App\Exports\DailyListing;


use App\Http\Controllers\Exports\DailyListing\BankMovementExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BankMovementExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new BankMovementExportController();
       
        return $report->pdfAccountBankMovement($this->request ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
