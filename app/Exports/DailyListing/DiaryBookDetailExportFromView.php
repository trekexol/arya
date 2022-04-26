<?php

namespace App\Exports\DailyListing;


use App\Http\Controllers\Exports\DailyListing\DiaryBookDetailExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class DiaryBookDetailExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new DiaryBookDetailExportController();

        return $report->print_diary_book_detail($this->request);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
