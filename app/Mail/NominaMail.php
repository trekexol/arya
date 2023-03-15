<?php

namespace App\Mail;

use App\Company;
use App\Nomina;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class NominaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nomina;
    public $pdf;

    public $company;

    public function __construct( $pdf,Company $company)
    {

        $this->pdf = $pdf;

        $this->company = $company;
    }

    public function build()
    {
        //return $this->view('mail.quotation');

        return $this->view('mail.quotation')
        ->attachData($this->pdf, 'recibopago.pdf', ['mime' =>
        'application/pdf']);
    }



}
