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

    public function pdfQuotation($quotation,$coin)
    {  
    
        $pdf = App::make('dompdf.wrapper');

        
        $quotation = null;
            
        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        
                                
        }else{
            return redirect('/quotations')->withDanger('No se encontro la cotizacion');
        } 

        if(isset($quotation)){
            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
            ->where('id_client',$quotation->id_client)
            ->where(function ($query) use ($quotation){
                $query->where('id_quotation',null)
                    ->orWhere('id_quotation',$quotation->id);
            })
            ->where('coin','like','bolivares')
            ->sum('amount');


            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                ->where('id_client',$quotation->id_client)
                                ->where(function ($query) use ($quotation){
                                    $query->where('id_quotation',null)
                                        ->orWhere('id_quotation',$quotation->id);
                                })
                                ->where('coin','not like','bolivares')
                                ->select( DB::raw('SUM(anticipos.amount/anticipos.rate) As dolar'))
                                ->get();



            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo[0]->dolar)){
            $anticipos_sum_dolares = $total_dolar_anticipo[0]->dolar;
            }

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                        ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                        ->where('quotation_products.id_quotation',$quotation->id)
                                                        ->where('quotation_products.status','1')
                                                        ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                                                        'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                        ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                        ->get(); 
            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            $retiene_iva = 0;

            $total_retiene_islr = 0;
            $retiene_islr = 0;

            $total_mercancia= 0;
            $total_servicios= 0;

            foreach($inventories_quotations as $var){

                if($coin != "bolivares"){
                    $var->price = bcdiv($var->price / $var->rate, '1', 2);
                }

                //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;
                //----------------------------- 

                if($var->retiene_iva_quotation == 0){

                    $base_imponible += ($var->price * $var->amount_quotation) - $percentage; 

                }else{
                    $retiene_iva += ($var->price * $var->amount_quotation) - $percentage; 
                }

                if($var->retiene_islr_quotation == 1){

                    $retiene_islr += ($var->price * $var->amount_quotation) - $percentage; 

                }

                //me suma todos los precios de costo de los productos
                if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation;
                }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
                }

                if($coin != "bolivares"){
                    if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                        $total_mercancia += (($var->price * $var->amount_quotation) - $percentage) * $quotation->bcv;
                    }else{
                        $total_servicios += (($var->price * $var->amount_quotation) - $percentage) * $quotation->bcv;
                    }
                }else{
                    if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                        $total_mercancia += ($var->price * $var->amount_quotation) - $percentage;
                    }else{
                        $total_servicios += ($var->price * $var->amount_quotation) - $percentage;
                    }
                }
            }
            
            $quotation->total_factura = $total;
            $quotation->base_imponible = $base_imponible;
            
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
            $anticipos_sum = 0;
            if(isset($coin)){
                if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                }else{
                    $bcv = $quotation->bcv;
                    //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando 
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($quotation);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                }
            }else{
                $bcv = null;
            }
            
                                            
            
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
