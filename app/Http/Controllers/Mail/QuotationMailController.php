<?php

namespace App\Http\Controllers\Mail;


use App;
use App\Company;
use App\Http\Controllers\Controller;
use App\Mail\QuotationMail;
use App\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;

class QuotationMailController extends Controller
{
    public function sendQuotation(Request $request,$id_quotation,$coin){

        $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        
        $pdf = $this->pdfQuotation($quotation,$coin);

        $company = Company::on(Auth::user()->database_name)->find(1);

        $email_to_send = $request->email_modal;

        $company->message_from_email = $request->message_modal;

        Mail::to($email_to_send)->send(new QuotationMail($quotation,$pdf,$company));

        return redirect('/quotations/register/'.$quotation->id.'/'.$coin)->withSuccess('La cotizacion se ha enviado por Correo Exitosamente!');

    }

    public function pdfQuotation($quotation,$coin){

        $pdf = App::make('dompdf.wrapper');

            
        if(isset($quotation)){

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                        ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                        ->where('quotation_products.id_quotation',$quotation->id)
                                                        ->where('quotation_products.status','1')
                                                        ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                        'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                        ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                        ->get(); 

            
            if($coin == 'bolivares'){
                $bcv = null;
                
            }else{
                $bcv = $quotation->bcv;
            }

            $company = Company::on(Auth::user()->database_name)->find(1);
           
            $pdf = $pdf->loadView('pdf.quotation',compact('company','quotation','inventories_quotations','bcv','coin'));
          
            return $pdf->output();
        }
    }
}
