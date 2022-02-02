<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\SalesBookExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class SalesBookExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new SalesBookExportController();
        
        return $report->sales_books_pdf($this->request->coin,$this->request->date_begin,$this->request->date_end);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
