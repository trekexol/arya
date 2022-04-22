<?php

namespace App\Exports\Reports;

use App\Http\Controllers\Exports\Reports\AccountReceivableExportController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AccountReceivableExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new AccountReceivableExportController();
         
        
        return $report->accounts_receivable_pdf_excel(
            $this->request->coin_form ?? "bolivares",$this->request->date_end,
            $this->request->typeinvoice,$this->request->type,
            $this->request->id_client ?? $this->request->id_vendor ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
