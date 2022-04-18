<?php

namespace App\Http\Controllers\Mail;


use App;
use App\Anticipo;
use App\Company;
use App\Http\Controllers\Controller;
use App\Mail\ReceiptMail;
use App\Owners;
use App\ReceiptPayment;
use App\Receipts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;

class ReceiptMailController extends Controller
{
    public function sendreceipt(Request $request,$id_quotation,$coin){

        $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
        
        $pdf = $this->pdfQuotation($quotation,$coin);

        $company = Company::on(Auth::user()->database_name)->find(1);

        $email_to_send = $request->email_modal;

        $company->message_from_email = $request->message_modal;

        Mail::to($email_to_send)->send(new ReceiptMail($quotation,$pdf,$company));

        return redirect('/receipt/receipt')->withSuccess('El Recibo se ha enviado por Correo Exitosamente!');
         
    }



    public function sendreceiptmasive(Request $request){
       
        $id_quotation = $request->id_invoice;

        $quotationmasive = Receipts::on(Auth::user()->database_name)->whereIn('status',['P'])->where('type','R')->where('number_invoice',$id_quotation)->get();

        $company = Company::on(Auth::user()->database_name)->find(1);
       
        $company->message_from_email = $request->message_modal;
        
        foreach ($quotationmasive as $quotationt) {
            $quotation = '';
            $email_to_send = '';
            $owner = '';
            //$quotation = Receipts::on(Auth::user()->database_name)->whereIn('status',['P'])->where('type','R')->where('number_invoice',$quotationt->id)->get();
            $quotation = Receipts::on(Auth::user()->database_name)->find($quotationt->id);
        
            $owner = Owners::on(Auth::user()->database_name)->find($quotationt->id_client);
    
            $email_to_send = $owner->email;

            $pdf = $this->pdfQuotationmasive($quotation,$quotationt->coin);

           // dd($request->message_modal);

            Mail::to($email_to_send)->send(new ReceiptMail($quotation,$pdf,$company));
          
        }
            return redirect('/receipt/receipt')->withSuccess('El Recibo se ha enviado por Correo Exitosamente!');

    }

    function asignar_payment_type($type){
      
        if($type == 1){
            return "Cheque";
        }
        if($type == 2){
            return "Contado";
        }
        if($type == 3){
            return "Contra Anticipo";
        }
        if($type == 4){
            return "Crédito";
        }
        if($type == 5){
            return "Depósito Bancario";
        }
        if($type == 6){
            return "Efectivo";
        }
        if($type == 7){
            return "Indeterminado";
        }
        if($type == 8){
            return "Tarjeta Coorporativa";
        }
        if($type == 9){
            return "Tarjeta de Crédito";
        }
        if($type == 10){
            return "Tarjeta de Débito";
        }
        if($type == 11){
            return "Transferencia";
        }
    }

    
    public function pdfQuotation($quotation,$coin)
    {  

        $pdf = App::make('dompdf.wrapper');

     
             if(isset($quotation)){

                $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)
                                            ->where('id_quotation',$quotation->id)
                                            ->where('status',1)
                                            ->get();

                foreach($payment_quotations as $var){
                    $var->payment_type = $this->asignar_payment_type($var->payment_type);
                    if($coin == 'dolares'){
                        $var->amount = $var->amount / $var->rate;
                    }
                }


                 $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products') 
                    ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                    ->where('receipt_products.id_quotation',$quotation->id)
                    ->where('receipt_products.status','=','C')
                    ->orwhere('receipt_products.status','=','1')
                    ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                    'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                    ,'receipt_products.retiene_islr as retiene_islr_quotation')
                    ->get(); 


                 $client = owners::on(Auth::user()->database_name) // buscar cliente
                ->where('id','=',$quotation->id_client)
                ->select('owners.*')
                ->get()->first();

                //Buscar Factura original
                $quotationsorigin = Receipts::on(Auth::user()->database_name) // buscar facura original
                ->orderBy('id' ,'asc') 
                ->where('date_billing','<>',null)
                ->where('type','=','F')
                ->where('number_invoice','=',$quotation->number_invoice)
                ->select('receipts.*')
                ->get();

                $inventories_quotationso = DB::connection(Auth::user()->database_name)->table('products') // producto factura original
                ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                ->where('receipt_products.id_quotation',$quotationsorigin[0]['id'])
                ->where('receipt_products.status','=','C')
                ->orwhere('receipt_products.status','=','1')
                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                ,'receipt_products.retiene_islr as retiene_islr_quotation')
                ->get();
                
                
                //Buscar recibos que debe
                $quotationp = Receipts::on(Auth::user()->database_name) // buscar facura original
                ->orderBy('id' ,'asc') 
                ->where('date_billing','<>',null)
                ->where('type','=','R')
                ->where('status','=','P')
                ->where('id','!=',$quotation->id)
                ->where('receipts.id_client','=',$client->id)
                ->select('receipts.*')
                ->get();
               
               
                if (isset($quotationp)) {
                
                    foreach ($quotationp as $quotationtpp) {

                        $inventories_quotationsp = DB::connection(Auth::user()->database_name)->table('products') // productos recibo pendiente
                        ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                        ->where('receipt_products.id_quotation',$quotationtpp->id)
                        ->where('receipt_products.status','=','C')
                        ->orwhere('receipt_products.status','=','1')
                        ->select('products.*','receipt_products.id_quotation as id_quotation','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                        'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                        ,'receipt_products.retiene_islr as retiene_islr_quotation', )
                        ->get();

                    }


                    if(isset($inventories_quotationsp)){
                        foreach ($inventories_quotationsp as $varp) {
                            $quotationpn = Receipts::on(Auth::user()->database_name) // buscar facura original
                            ->orderBy('id' ,'asc') 
                            ->where('date_billing','<>',null)
                            ->where('type','=','R')
                            ->where('status','=','P')
                            ->where('receipts.id_client','=',$client)
                            ->select('number_delivery_note','date_billing')
                            ->get()->first();
                            $varp->number_delivery_note = $quotationpn->number_delivery_note;
                            $varp->date_billing = $quotationpn->date_billing;
                        }
                    } else {
                        $inventories_quotationsp = null;
                    }


                } else {
                      
                    $inventories_quotationsp = null;


                }


                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                $pdf = $pdf->loadView('pdf.receipt',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','quotationsorigin','inventories_quotationso','client','quotationp','inventories_quotationsp'));

                return $pdf->output();

                }else{

                 return redirect('/receipt/receipt')->withDanger('La recibo de condominio no existe');
             } 
             
        

        
    }

 
    
    public function pdfQuotationmasive($quotation,$coin)
    {  
    
        $pdf = App::make('dompdf.wrapper');

        /*if(isset($id_quotation)){
            $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
                                
        }else{
           return redirect('/receipt/receipt')->withDanger('No llega el numero del recibo de condominio');
        } */

        if(isset($quotation)){

           $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)
                                       ->where('id_quotation',$quotation->id)
                                       ->where('status',1)
                                       ->get();

           foreach($payment_quotations as $var){
               $var->payment_type = $this->asignar_payment_type($var->payment_type);
               if($coin == 'dolares'){
                   $var->amount = $var->amount / $var->rate;
               }
           }


            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
               ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
               ->where('receipt_products.id_quotation',$quotation->id)
               ->where('receipt_products.status','=','C')
               ->orwhere('receipt_products.status','=','1')
               ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
               'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
               ,'receipt_products.retiene_islr as retiene_islr_quotation')
               ->get(); 


            $client = owners::on(Auth::user()->database_name) // buscar cliente
           ->where('id','=',$quotation->id_client)
           ->select('owners.*')
           ->get()->first();

           //Buscar Factura original
           $quotationsorigin = Receipts::on(Auth::user()->database_name) // buscar facura original
           ->orderBy('id' ,'asc') 
           ->where('date_billing','<>',null)
           ->where('type','=','F')
           ->where('number_invoice','=',$quotation->number_invoice)
           ->select('receipts.*')
           ->get();

           $inventories_quotationso = DB::connection(Auth::user()->database_name)->table('products')
           ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
           ->where('receipt_products.id_quotation',$quotation->id)
           ->where('receipt_products.status','=','C')
           ->orwhere('receipt_products.status','=','1')
           ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
           'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
           ,'receipt_products.retiene_islr as retiene_islr_quotation')
           ->get();
           
           
            //Buscar recibos que debe
            $quotationp = Receipts::on(Auth::user()->database_name) // buscar facura original
            ->orderBy('id' ,'asc') 
            ->where('date_billing','<>',null)
            ->where('type','=','R')
            ->where('status','=','P')
            ->where('id','!=',$quotation->id)
            ->where('receipts.id_client','=',$client->id)
            ->select('receipts.*')
            ->get();


            if (isset($quotationp)) {
 
                foreach ($quotationp as $quotationtpp) {

                    $inventories_quotationsp = DB::connection(Auth::user()->database_name)->table('products') // productos recibo pendiente
                    ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                    ->where('receipt_products.id_quotation',$quotationtpp->id)
                    ->where('receipt_products.status','=','C')
                    ->orwhere('receipt_products.status','=','1')
                    ->select('products.*','receipt_products.id_quotation as id_quotation','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                    'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                    ,'receipt_products.retiene_islr as retiene_islr_quotation', )
                    ->get();

                }


                if(isset($inventories_quotationsp)){
                    foreach ($inventories_quotationsp as $varp) {
                        $quotationpn = Receipts::on(Auth::user()->database_name) // buscar facura original
                        ->orderBy('id' ,'asc') 
                        ->where('date_billing','<>',null)
                        ->where('type','=','R')
                        ->where('status','=','P')
                        ->where('receipts.id_client','=',$client)
                        ->select('number_delivery_note','date_billing')
                        ->get()->first();
                        $varp->number_delivery_note = $quotationpn->number_delivery_note;
                        $varp->date_billing = $quotationpn->date_billing;
                    }
                } else {
                    $inventories_quotationsp = null;
                }


            } else {
                
                $inventories_quotationsp = null;


            }


           if($coin == 'bolivares'){
               $bcv = null;
               
           }else{
               $bcv = $quotation->bcv;
           }

           $company = Company::on(Auth::user()->database_name)->find(1);
           
          // $lineas_cabecera = $company->format_header_line;

           $pdf = $pdf->loadView('pdf.receipt',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','quotationsorigin','inventories_quotationso','client','quotationp','inventories_quotationsp'));

           return $pdf->output();
        }
 
    }

}
