<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\PurchasesBookExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class PurchasesBookExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new PurchasesBookExportController();
        
        return $report->purchases_books_pdf($this->request->coin,$this->request->date_begin,$this->request->date_end);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
