<?php

namespace App\Exports\Reports;

use App\Company;
use App\ExpensesAndPurchase;
use App\Http\Controllers\ExportExpenseController;
use App\Http\Controllers\Report2Controller;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;

class AccountReceivableExportFromView implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->$request = $request;
    }

    public function view(): View
    {
        $report = new Report2Controller();
         
        
        return $report->accounts_receivable_pdf_excel(
            $this->request->coin ?? "bolivares",$this->request->date_end,
            $this->request->typeinvoice,$this->request->type,
            $this->request->id_client ?? $this->request->id_vendor ?? null);
    }

    

    public function setter($request){
        $this->request = $request;
     }

    
}
