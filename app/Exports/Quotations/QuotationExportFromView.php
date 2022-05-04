<?php

namespace App\Exports\Quotations;


use App\Http\Controllers\Exports\Quotations\QuotationExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class QuotationExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new QuotationExportController();
       
        return $report->pdfQuotation($this->request ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
