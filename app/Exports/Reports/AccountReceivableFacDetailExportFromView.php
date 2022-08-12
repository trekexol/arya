<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\AccountReceivableFacDetailExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AccountReceivableFacDetailExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        
        $report = new AccountReceivableFacDetailExportController();
        
        return $report->accounts_receivable_fac_det_pdf(
            $this->request->coin_form ?? "bolivares",$this->request->date_end,$this->request->typeinvoice
            ,$this->request->type,$this->request->id_client ?? $this->request->id_vendor,$this->request->date_begin);
    
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
