<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\VendorCommissionExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class VendorCommissionExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new VendorCommissionExportController();
        
        return $report->pdf($this->request->coin,$this->request->date_begin,$this->request->date_end,$this->request->typeinvoice,$this->request->typeperson,$this->request->id_client_or_vendor = null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
