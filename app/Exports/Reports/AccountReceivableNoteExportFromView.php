<?php

namespace App\Exports\Reports;


use App\Http\Controllers\Exports\Reports\AccountReceivableNoteExportController;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AccountReceivableNoteExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new AccountReceivableNoteExportController();
        
        return $report->accounts_receivable_note_pdf(
            $this->request->coin ?? "bolivares",$this->request->date_end,$this->request->typeinvoice
            ,$this->request->type,$this->request->id_client ?? $this->request->id_vendor,$this->request->date_begin);
    
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
