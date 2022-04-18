<?php

namespace App\Mail;

use App\Company;
use App\Receipts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $pdf;

    public $company;

    public function __construct(Receipts $quotation, $pdf,Company $company)
    {
        $this->quotation = $quotation;
        $this->pdf = $pdf;

        $this->company = $company;
    }

    public function build()
    {
        //return $this->view('mail.quotation');
        
        return $this->view('mail.quotation')
        ->attachData($this->pdf, 'recibo_de_condominio.pdf', ['mime' => 
        'application/pdf'])->subject('Recibo de Condominio');
    }

    

}
