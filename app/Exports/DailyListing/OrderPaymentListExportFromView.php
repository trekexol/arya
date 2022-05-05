<?php

namespace App\Exports\DailyListing;


use App\Http\Controllers\Exports\DailyListing\OrderPaymentListExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class OrderPaymentListExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new OrderPaymentListExportController();
       
        return $report->pdfAccountOrdenDePago($this->request ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
