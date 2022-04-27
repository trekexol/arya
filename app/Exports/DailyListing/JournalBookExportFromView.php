<?php

namespace App\Exports\DailyListing;


use App\Http\Controllers\Exports\DailyListing\JournalBookExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class JournalBookExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new JournalBookExportController();
       
        return $report->print_journalbook($this->request ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
