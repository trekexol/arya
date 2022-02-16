<?php

namespace App\Mail;

use App\Company;
use App\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $pdf;

    public $company;

    public function __construct(Quotation $quotation, $pdf,Company $company)
    {
        $this->quotation = $quotation;
        $this->pdf = $pdf;

        $this->company = $company;
    }

    public function build()
    {
        //return $this->view('mail.quotation');
        
        return $this->view('mail.quotation')
        ->attachData($this->pdf, 'cotizacion.pdf', ['mime' => 
        'application/pdf']);
    }

    

}
