<?php

namespace App\Http\Controllers;

use App\Account;
use App\Anticipo;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Inventory;
use App\Client;
use App\Company;
use App\Http\Controllers\Calculations\FacturaCalculationController;
use App\Http\Controllers\Historial\HistorialQuotationController;
use App\Http\Controllers\Validations\FacturaValidationController;
use App\Http\Controllers\UserAccess\UserAccessController;

use Illuminate\Http\Request;

use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use App\DebitNote;
use App\CreditNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\FacturasCour;

class FacturarController extends Controller
{


    public $userAccess;
    public $modulo = 'Facturas';


    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();

   }

    public function createfacturar($id_quotation,$coin,$type = 'Cotización')
    {
        $user       =   auth()->user();
        $company_user = $user->id_company;

         $quotation = null;

         if(isset($id_quotation)){
             $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
         }

         if(isset($quotation)){

            $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();


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


            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 2)
                                            ->where('code_five', '<>',0)
                                            ->where('description','not like', 'Punto de Venta%')
                                            ->orderBy('description','ASC')
                                            ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->orderBy('description','ASC')
                                            ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                            ->where('quotation_products.id_quotation',$quotation->id)
                                                            ->whereIn('quotation_products.status',['1','C'])
                                                            ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id_inventory as id_inventory','quotation_products.discount as discount',
                                                            'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                            ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                            ->get();

            $notasdedebito = DB::connection(Auth::user()->database_name)->table('debit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();


            $notasdecredito = DB::connection(Auth::user()->database_name)->table('credit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();


             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;


             $total_retiene_iva = 0;
             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;

             $total_mercancia= 0;
             $total_servicios= 0;
             $total_debit_notes = 0;

             foreach($inventories_quotations as $var){

                if($coin != "bolivares"){
                    $var->price = $var->price / $var->rate;
                }

                 //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;

                    if ($company_user == 26){ // 26 NORTH D CORP
                        if($var->id_inventory == 34){
                            $total -= (($var->price * $var->amount_quotation) - $percentage) * 2;

                        }
                    }
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
                        $total_mercancia += (($var->price * $var->amount_quotation) - $percentage);
                    }else{
                        $total_servicios += (($var->price * $var->amount_quotation) - $percentage);
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

             if($type == 'factura'){
             $datenow = date_format(date_create($quotation->date_quotation),"Y-m-d");
             }else{
             $datenow = $date->format('Y-m-d');
             }
             $anticipos_sum = 0;

            if ($coin == null) {    /// condicion de la moneda
                $coin = $quotation->coin;
            }

             if(isset($coin)){
                 if($coin == 'dolares'){
                    $bcv = $quotation->bcv;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($quotation);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->dolar;
                    $total_credit_notes = $notasdecredito[0]->dolar;
                 }else{

                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->bolivares;
                    $total_credit_notes = $notasdecredito[0]->bolivares;
                 }
             }else{
                $bcv = null;
                $total_debit_notes = $notasdedebito[0]->bolivares;
                $total_credit_notes = $notasdecredito[0]->bolivares;
             }

             if (count($notasdedebito) <= 0){
                $total_debit_notes = 0;
             }

             if (count($notasdecredito) <= 0){
                $total_credit_notes = 0;
             }


            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
             $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

                if($client->percentage_retencion_iva != 0){
                    $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
                } else {
                    $total_retiene_iva = 0;
                }

                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */
            $company = Company::on(Auth::user()->database_name)->find(1);
            $igtfporc = $company->IGTF_porc ?? 3;
            $impuesto = $company->tax_1 ?? 1;
            $impuesto2 = $company->tax_2 ?? 1;
            $impuesto3 = $company->tax_3 ?? 1;

            $is_after = false;
            if(empty($quotation->credit_days)){
                $is_after = true;
            }

            if (Auth::user()->company['id']  == '26'){
                $validarfact = FacturasCour::on(Auth::user()->database_name)
                ->where('id_ventas',$id_quotation)
                ->first();
                    if($validarfact){
                        $existe = true;
                    }else{
                        $existe = false;
                    }
            }else{
                $existe = false;
            }



             return view('admin.quotations.createfacturar',compact('existe','price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_iva','total_retiene_islr','is_after'
                        ,'total_mercancia','total_servicios','client','retiene_iva','type','igtfporc','total_debit_notes','total_credit_notes','impuesto','impuesto2','impuesto3'));
         }else{
             return redirect('/quotations/index')->withDanger('La cotizacion no existe');
         }

    }
    public function registerAnticipo($date_begin,$id_client,$id_account,$coin,$amount,$rate,$reference,$id_quotation = null,$header_voucher = null)
    {

        $user       =   auth()->user();
        $var = new Anticipo();
        $var->setConnection(Auth::user()->database_name);

        $var->date = $date_begin;

        $var->id_client = $id_client;

        $var->id_quotation = $id_quotation;


        $var->id_account = $id_account;
        $var->id_user =  $user->id;
        $var->coin = $coin;


        $var->amount = $amount;
        $var->rate = $rate;


        $var->reference = $reference;
        $var->status = 'C';

        $var->save();

        $updatecabecera = HeaderVoucher::on(Auth::user()->database_name)
        ->where('id',$header_voucher) //Saldar anticipo previo
        ->update(['id_anticipo' => $var->id]);

    }

    public function anticipos_bolivares_to_dolars($quotation)
    {

        $anticipos_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
        ->where('id_client',$quotation->id_client)
        ->where(function ($query) use ($quotation){
            $query->where('id_quotation',null)
                ->orWhere('id_quotation',$quotation->id);
        })
        ->where('coin','like','bolivares')
        ->get();

        $total_dolar = 0;

        if(isset($anticipos_bolivares)){
            foreach($anticipos_bolivares as $anticipo){
                $total_dolar += bcdiv(($anticipo->amount / $anticipo->rate), '1', 2);
            }
        }


        return $total_dolar;
    }

    public function createfacturar_after($id_quotation,$coin)
    {

        $user       =   auth()->user();
        $company_user = $user->id_company;

         $quotation = null;
         if(isset($id_quotation)){
             $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
         }

         if(isset($quotation)){

            $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

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

             $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 2)
                                            ->where('code_five', '<>',0)
                                            ->where('description','not like', 'Punto de Venta%')
                                            ->orderBy('description','ASC')
                                            ->get();
             $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->orderBy('description','ASC')
                                            ->get();

            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $notasdedebito = DB::connection(Auth::user()->database_name)->table('debit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();


            $notasdecredito = DB::connection(Auth::user()->database_name)->table('credit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                            ->where('quotation_products.id_quotation',$quotation->id)
                                                            ->whereIn('quotation_products.status',['1','C'])
                                                            ->select('products.*','quotation_products.price as price','quotation_products.id_inventory as id_inventory','quotation_products.rate as rate','quotation_products.discount as discount',
                                                            'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                            ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                            ->get();


             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;

             //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
             $total_retiene_iva = 0;
             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;
             $total_debit_notes = 0;
             foreach($inventories_quotations as $var){
                 //Se calcula restandole el porcentaje de descuento (discount)

                if($coin != "bolivares"){
                    $var->price = $var->price / $var->rate;
                }

                 //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;


                    $total += ($var->price * $var->amount_quotation) - $percentage;

                    if ($company_user == 26){ // 26 NORTH D CORP
                        if($var->id_inventory == 34){
                            $total -= (($var->price * $var->amount_quotation) - $percentage) * 2;

                        }
                    }

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


                //me suma todos los precios de costo de los productos
                if(($var->money == 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation;
                }else if(($var->money != 'Bs') && (($var->type == "MERCANCIA") || ($var->type == "COMBO"))){
                    $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
                }


            }

             $quotation->total_factura = $total;
             $quotation->base_imponible = $base_imponible;

             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');
             $anticipos_sum = 0;



            if ($coin == null) {    /// condicion de la moneda
                $coin = $quotation->coin;
            }


             if(isset($coin)){
                if($coin == 'dolares'){
                    $bcv = $quotation->bcv;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($quotation);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->dolar;
                    $total_credit_notes = $notasdecredito[0]->dolar;
                 }else{

                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->bolivares;
                    $total_credit_notes = $notasdecredito[0]->bolivares;
                 }
             }else{
                $bcv = null;
                $total_debit_notes = $notasdedebito[0]->bolivares;
                $total_credit_notes = $notasdecredito[0]->bolivares;
             }

             if (count($notasdedebito) <= 0){
                $total_debit_notes = 0;
             }
             if (count($notasdecredito) <= 0){
                $total_credit_notes = 0;
             }



            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
             $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

                if($client->percentage_retencion_iva != 0){
                    $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
                } else {
                    $total_retiene_iva = 0;
                }



                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                } else{
                    $total_retiene_islr =0;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                $igtfporc = $company->IGTF_porc ?? 3;

                $impuesto = $company->tax_1 ?? 1;
                $impuesto2 = $company->tax_2 ?? 1;
                $impuesto3 = $company->tax_3 ?? 1;

            /*-------------- */
            $is_after = false;

            if (Auth::user()->company['id']  == '26'){
                $validarfact = FacturasCour::on(Auth::user()->database_name)
                ->where('id_ventas',$id_quotation)
                ->first();
                    if($validarfact){
                        $existe = true;
                    }else{
                        $existe = false;
                    }
            }else{
                $existe = false;
            }


             return view('admin.quotations.createfacturar',compact('existe','price_cost_total','coin','quotation','payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta','datenow','bcv','anticipos_sum','total_retiene_iva','total_retiene_islr','is_after','client','igtfporc','total_debit_notes','total_credit_notes','impuesto','impuesto2','impuesto3'));
         }else{
             return redirect('/quotations/index')->withDanger('La cotizacion no existe');
         }

    }
    public function storefacturacredit(Request $request)
    {
        //dd($request);
        $id_quotation = request('id_quotation');

        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
        $quotation->coin = request('coin');
        $moneda = request('coin');
        $bcv = $quotation->bcv;


        //precio de costo de los productos, vienen en bolivares
        $price_cost_total = request('price_cost_total');

        $amount_exento = request('amount_exento');

        $total_retiene_iva = str_replace(',', '.', str_replace('.', '', request('iva_retencion')));
        $total_retiene_islr = str_replace(',', '.', str_replace('.', '', request('islr_retencion')));
        $anticipo = str_replace(',', '.', str_replace('.', '', request('anticipo')));


        $sin_formato_base_imponible = str_replace(',', '.', str_replace('.', '', request('base_imponible')));
        $sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('total_factura')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount')));
        $sin_formato_amount_with_iva = str_replace(',', '.', str_replace('.', '', request('total_pay')));
       /*$sin_formato_grand_total = str_replace(',', '.', str_replace('.', '', request('grand_total_form')));*/
        $sin_formato_grand_total = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $impuesto_tf = request('impuesto_tf');

        /************PARA LO DE COURIERTOOL NO TOCAR ********/
        $montocour = str_replace(',', '.', str_replace('.', '', request('total_pay')));
        /***************************************************************/



        $IGTF_input = request('IGTF_input_pre');
        $IGTF_input_check = request('IGTF_input');

        if ($IGTF_input_check == 0) {
            $IGTF_input = 0;
        }

        $iva_percibido = request('iva_percibido_form');


        $total_mercancia = request('total_mercancia_credit');
        $total_servicios = request('total_servicios_credit');

        if($moneda == 'dolares'){
            $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
            $sin_formato_amount_iva = round($sin_formato_amount_iva, 2);

            $sin_formato_base_imponible = $sin_formato_base_imponible * $bcv;
            $sin_formato_base_imponible = round($sin_formato_base_imponible, 2);

            $sin_formato_grand_total = $sin_formato_grand_total * $bcv;

            $sin_formato_amount = $sin_formato_amount * $bcv;
            $sin_formato_amount = round($sin_formato_amount, 2);

            $total_retiene_iva = $total_retiene_iva * $bcv;
            $total_retiene_islr = $total_retiene_islr * $bcv;
            $anticipo = $anticipo * $bcv;

            $total_mercancia = $total_mercancia * $bcv;
            $total_servicios = $total_servicios * $bcv;
        }


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $quotation->date_billing = request('date-begin-form');

        $quotation->retencion_iva = $total_retiene_iva;
        $quotation->retencion_islr = $total_retiene_islr;
        $quotation->anticipo = $anticipo;
        $quotation->base_imponible = $sin_formato_base_imponible;
        $quotation->amount_exento = $amount_exento;
        $quotation->amount = $sin_formato_amount;
        $quotation->amount_iva = $sin_formato_amount_iva;
        $quotation->amount_with_iva = $sin_formato_grand_total;

        $credit = request('credit');

        $user_id = request('user_id');

        $quotation->iva_percentage = request('iva');

        $quotation->credit_days = $credit;

        //P de por pagar
        $quotation->status = 'P';

        $last_number = Quotation::on(Auth::user()->database_name)
        ->where('id_branch',$quotation->id_branch)
        ->where('number_invoice','<>',NULL)
        ->orderBy('number_invoice','desc')->first();

        //Asigno un numero incrementando en 1
        if(empty($quotation->number_invoice)){
            if(isset($last_number)){
                $quotation->number_invoice = $last_number->number_invoice + 1;
            }else{
                $quotation->number_invoice = 1;
            }
        }
        $company = Company::on(Auth::user()->database_name)->find(1);
        $igtfporc = $company->IGTF_porc;

        $quotation->IGTF_amount = $IGTF_input;
        $quotation->IGTF_percentage = $igtfporc;
        $quotation->impuesto_tf;

        $quotation->save();



        /////////////////////////////**************LO DE COURIERTOOL**************/////////////////
        if($request->court != null AND  $request->tifac != null AND $request->nrofactcou != null AND Auth::user()->company['id'] == '26'){

            $factcour  = new FacturasCour();
            $factcour->setConnection(Auth::user()->database_name);
            $factcour->id_ventas = $id_quotation;
            $factcour->tipo_fac = $request->tifac;
            $factcour->tipo_movimiento = $request->court;
            $factcour->numero =  $request->nrofactcou;
            $factcour->monto =  $montocour;
            $factcour->save();

        }
    /////////////////////////////**************LO DE COURIERTOOL**************/////////////////



        $date_payment = request('date-payment');

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);

        $header_voucher->description = "Ventas de Bienes o servicios.";
        $header_voucher->date = $date_payment;


        $header_voucher->status =  "1";

        $header_voucher->save();



        DB::connection(Auth::user()->database_name)->table('quotation_products')
                ->where('id_quotation', '=', $quotation->id)
                ->where('status','!=','X')
                ->update(['status' => 'C']);

                if(!isset($quotation->number_delivery_note)){
                    $quotation->number_delivery_note = 0;
                } else {

                    if(empty($quotation->number_delivery_note) || $quotation->number_delivery_note == null) {
                        $quotation->number_delivery_note = 0;
                    }
                }

        $global = new GlobalController;

        $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
        ->where('id_quotation', '=', $quotation->id)
        ->where('status','!=','X')
        ->get(); // Conteo de Productos para incluiro en el historial de inventario

        foreach($quotation_products as $det_products){ // guardado historial de inventario

        $global->transaction_inv('venta',$det_products->id_inventory,'venta_n',$det_products->amount,$det_products->price,$quotation->date_billing,1,1,$quotation->number_delivery_note,$det_products->id_inventory_histories,$det_products->id,$quotation->id);

        }

        /*Busqueda de Cuentas*/

        //Cuentas por Cobrar Clientes

        $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();

        if(isset($account_cuentas_por_cobrar)){
            $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$quotation->id,$user_id,$sin_formato_grand_total,0);
        }

        if($total_mercancia != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$quotation->id,$user_id,0,$total_mercancia);
            }
        }

        if($total_servicios != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$quotation->id,$user_id,0,$total_servicios);
            }
        }

        //Debito Fiscal IVA por Pagar

        $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();

        if($sin_formato_amount_iva != 0){

            if(isset($account_debito_iva_fiscal)){
                $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$quotation->id,$user_id,0,$sin_formato_amount_iva);
            }
        }

        //anadir movimiento de IGTF
        if ($IGTF_input > 0){
            $account_IGTF = Account::on(Auth::user()->database_name)->where('description', 'like', '%Cuentas por Pagar IGTF%')->first();

            if(isset($account_IGTF)){

                $this->add_movement($bcv,$header_voucher->id,$account_IGTF->id,$quotation->id,$user_id,0,$IGTF_input);
            }
        }


        $validation_factura = new FacturaValidationController($quotation);

        $return_validation_factura = $validation_factura->validate_movement_mercancia();

        if($return_validation_factura == true){
            //Mercancia para la Venta
            if((isset($price_cost_total)) && ($price_cost_total != 0)){
                $account_mercancia_venta = Account::on(Auth::user()->database_name)->where('description', 'like', 'Mercancia para la Venta')->first();

                if(isset( $account_mercancia_venta)){
                    $this->add_movement($bcv,$header_voucher->id,$account_mercancia_venta->id,$quotation->id,$user_id,0,$price_cost_total);
                }

                //Costo de Mercancia

                $account_costo_mercancia = Account::on(Auth::user()->database_name)->where('description', 'like', 'Costo de Mercancia')->first();

                if(isset($account_costo_mercancia)){
                    $this->add_movement($bcv,$header_voucher->id,$account_costo_mercancia->id,$quotation->id,$user_id,$price_cost_total,0);
                }
            }
        }


        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($quotation,"quotation","Cotizactión convertida a Factura a Crédito");


        return redirect('quotations/facturado/'.$quotation->id.'/'.$quotation->coin.'')->withSuccess('Factura Guardada con Exito!');
    }


    public function storefactura(Request $request)
    {

        /************PARA LO DE COURIERTOOL NO TOCAR ********/
        $montocour = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        /***************************************************************/

        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail(request('id_quotation'));

        $quotation_status = $quotation->status;

        $company = Company::on(Auth::user()->database_name)->find(1);

        if($quotation->date_billing != null && $quotation->status == 'C' ){
            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Ya esta factura fue procesada!');
        }else{

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $user_id = request('user_id');

        /*Validar cuales son los pagos a guardar */
            $validate_boolean1 = false;
            $validate_boolean2 = false;
            $validate_boolean3 = false;
            $validate_boolean4 = false;
            $validate_boolean5 = false;
            $validate_boolean6 = false;
            $validate_boolean7 = false;

        //-----------------------

        $bcv = $quotation->bcv;

        $coin = request('coin');

        $price_cost_total = request('price_cost_total');

        $anticipo = request('anticipo_form');
        $retencion_iva = request('total_retiene_iva');
        $retencion_islr = request('total_retiene_islr');
        $impuesto_tf = request('impuesto_tf_form');
        $anticipo = request('anticipo_form');

        $sub_total = request('sub_total_form');
        $base_imponible = request('base_imponible_form');

        $amount_exento = request('amount_exento');
        $sin_formato_amount = request('sub_total_form');
        $iva_percentage = request('iva_form');
        $sin_formato_total_pay = request('total_pay_form');

        $sin_formato_grandtotal = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount_form')));

        $amount_pay = request('amount_pay');
        $IGTF_input = request('IGTF_input_pre');
        $IGTF_input_check = request('IGTF_input_store');

        $debitnote = request('debitnote_input_pre');
        $creditnote = request('creditnote_input_pre');


        if ($IGTF_input_check == 0) {
            $IGTF_input = 0;
        }

        $IGTF_porc = request('IGTF_porc');

        $total_mercancia = request('total_mercancia');
        $total_servicios = request('total_servicios');

        $date_payment = request('date-payment-form');

        $total_iva = 0;

        $IGTF_percentage = $company->IGTF_percentage ?? 3;



        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;
            $total_iva = round($total_iva, 2);
        }
        $quotation->date_billing = request('date-begin-form2');

        $IGTF_amount_check = 0;


        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($sin_formato_total_pay > 0){
            $payment_type = request('payment_type');
            if($come_pay >= 1){

                /*-------------PAGO NUMERO 1----------------------*/

                $var = new QuotationPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');

                if(isset($amount_pay)){

                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 1!');
                }


                $account_bank = request('account_bank');
                $account_efectivo = request('account_efectivo');
                $account_punto_de_venta = request('account_punto_de_venta');

                $credit_days = request('credit_days');

                $reference = request('reference');

                if($valor_sin_formato_amount_pay != 0){

                    if($payment_type != 0){

                        $var->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type == 1 || $payment_type == 11 || $payment_type == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank != 0)){
                                if(isset($reference)){

                                    $var->id_account = $account_bank;

                                    $var->reference = $reference;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                            }
                        }if($payment_type == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica%')->first();

                            $var->id_account = $account_contado->id;
                        }
                        if($payment_type == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days)){

                                $var->credit_days = $credit_days;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }

                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){

                                $var->id_account = $account_efectivo;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }

                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                            }
                        }




                            $var->payment_type = request('payment_type');
                            $var->amount = $valor_sin_formato_amount_pay;

                            if($coin == 'dolares'){
                                $var->amount = $var->amount * $bcv;
                            }

                            $var->rate = $bcv;

                            if(isset($request->IGTF)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var->amount;
                            }

                            $var->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay;

                            $validate_boolean1 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type2 = request('payment_type2');
            if($come_pay >= 2){

                /*-------------PAGO NUMERO 2----------------------*/

                $var2 = new QuotationPayment();
                $var2->setConnection(Auth::user()->database_name);

                $amount_pay2 = request('amount_pay2');

                if(isset($amount_pay2)){

                    $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 2!');
                }


                $account_bank2 = request('account_bank2');
                $account_efectivo2 = request('account_efectivo2');
                $account_punto_de_venta2 = request('account_punto_de_venta2');

                $credit_days2 = request('credit_days2');



                $reference2 = request('reference2');

                if($valor_sin_formato_amount_pay2 != 0){

                if($payment_type2 != 0){

                    $var2->id_quotation = request('id_quotation');

                    //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                    if($payment_type2 == 1 || $payment_type2 == 11 || $payment_type2 == 5 ){
                        //CUENTAS BANCARIAS
                        if(($account_bank2 != 0)){
                            if(isset($reference2)){

                                $var2->id_account = $account_bank2;

                                $var2->reference = $reference2;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                        }
                    }
                    if($payment_type2 == 2){

                        $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                        $var2->id_account = $account_contado->id;
                    }
                    if($payment_type2 == 4){
                        //DIAS DE CREDITO
                        if(isset($credit_days2)){

                            $var2->credit_days = $credit_days2;

                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                        }
                    }




                        $var2->payment_type = request('payment_type2');
                        $var2->amount = $valor_sin_formato_amount_pay2;

                        if($coin == 'dolares'){
                            $var2->amount = $var2->amount * $bcv;
                        }

                        if(isset($request->IGTF2)){
                            $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                            $IGTF_amount_check += $var2->amount;
                        }

                        $var2->rate = $bcv;

                        $var2->status =  1;

                        $total_pay += $valor_sin_formato_amount_pay2;

                        $validate_boolean2 = true;


                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }


                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                }
                /*--------------------------------------------*/
            }
            $payment_type3 = request('payment_type3');
            if($come_pay >= 3){

                    /*-------------PAGO NUMERO 3----------------------*/

                    $var3 = new QuotationPayment();
                    $var3->setConnection(Auth::user()->database_name);

                    $amount_pay3 = request('amount_pay3');

                    if(isset($amount_pay3)){

                        $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 3!');
                    }


                    $account_bank3 = request('account_bank3');
                    $account_efectivo3 = request('account_efectivo3');
                    $account_punto_de_venta3 = request('account_punto_de_venta3');

                    $credit_days3 = request('credit_days3');



                    $reference3 = request('reference3');

                    if($valor_sin_formato_amount_pay3 != 0){

                        if($payment_type3 != 0){

                            $var3->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type3 == 1 || $payment_type3 == 11 || $payment_type3 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank3 != 0)){
                                    if(isset($reference3)){

                                        $var3->id_account = $account_bank3;

                                        $var3->reference = $reference3;

                                    }else{
                                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                }
                            }
                            if($payment_type3 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var3->id_account = $account_contado->id;
                            }
                            if($payment_type3 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days3)){

                                    $var3->credit_days = $credit_days3;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                }
                            }




                                $var3->payment_type = request('payment_type3');
                                $var3->amount = $valor_sin_formato_amount_pay3;

                                if($coin == 'dolares'){
                                    $var3->amount = $var3->amount * $bcv;
                                }

                                if(isset($request->IGTF3)){
                                    $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                    $IGTF_amount_check += $var3->amount;
                                }

                                $var3->rate = $bcv;

                                $var3->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay3;

                                $validate_boolean3 = true;


                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }


                    }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type4 = request('payment_type4');
            if($come_pay >= 4){

                    /*-------------PAGO NUMERO 4----------------------*/

                    $var4 = new QuotationPayment();
                    $var4->setConnection(Auth::user()->database_name);

                    $amount_pay4 = request('amount_pay4');

                    if(isset($amount_pay4)){

                        $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 4!');
                    }


                    $account_bank4 = request('account_bank4');
                    $account_efectivo4 = request('account_efectivo4');
                    $account_punto_de_venta4 = request('account_punto_de_venta4');

                    $credit_days4 = request('credit_days4');



                    $reference4 = request('reference4');

                    if($valor_sin_formato_amount_pay4 != 0){

                        if($payment_type4 != 0){

                            $var4->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type4 == 1 || $payment_type4 == 11 || $payment_type4 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank4 != 0)){
                                    if(isset($reference4)){

                                        $var4->id_account = $account_bank4;

                                        $var4->reference = $reference4;

                                    }else{
                                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                }
                            }
                            if($payment_type4 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var4->id_account = $account_contado->id;
                            }
                            if($payment_type4 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days4)){

                                    $var4->credit_days = $credit_days4;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                }
                            }




                                $var4->payment_type = request('payment_type4');
                                $var4->amount = $valor_sin_formato_amount_pay4;

                                if($coin == 'dolares'){
                                    $var4->amount = $var4->amount * $bcv;
                                }

                                if(isset($request->IGTF4)){
                                    $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                    $IGTF_amount_check += $var4->amount;
                                }

                                $var4->rate = $bcv;

                                $var4->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay4;

                                $validate_boolean4 = true;


                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }


                    }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type5 = request('payment_type5');
            if($come_pay >= 5){

                /*-------------PAGO NUMERO 5----------------------*/

                $var5 = new QuotationPayment();
                $var5->setConnection(Auth::user()->database_name);

                $amount_pay5 = request('amount_pay5');

                if(isset($amount_pay5)){

                    $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 5!');
                }


                $account_bank5 = request('account_bank5');
                $account_efectivo5 = request('account_efectivo5');
                $account_punto_de_venta5 = request('account_punto_de_venta5');

                $credit_days5 = request('credit_days5');



                $reference5 = request('reference5');

                if($valor_sin_formato_amount_pay5 != 0){

                    if($payment_type5 != 0){

                        $var5->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type5 == 1 || $payment_type5 == 11 || $payment_type5 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank5 != 0)){
                                if(isset($reference5)){

                                    $var5->id_account = $account_bank5;

                                    $var5->reference = $reference5;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                            }
                        }
                        if($payment_type5 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var5->id_account = $account_contado->id;
                        }
                        if($payment_type5 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days5)){

                                $var5->credit_days = $credit_days5;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                            }
                        }




                            $var5->payment_type = request('payment_type5');
                            $var5->amount = $valor_sin_formato_amount_pay5;

                            if($coin == 'dolares'){
                                $var5->amount = $var5->amount * $bcv;
                            }

                            if(isset($request->IGTF5)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var5->amount;
                            }

                            $var5->rate = $bcv;

                            $var5->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay5;

                            $validate_boolean5 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type6 = request('payment_type6');
            if($come_pay >= 6){

                /*-------------PAGO NUMERO 6----------------------*/

                $var6 = new QuotationPayment();
                $var6->setConnection(Auth::user()->database_name);

                $amount_pay6 = request('amount_pay6');

                if(isset($amount_pay6)){

                    $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 6!');
                }


                $account_bank6 = request('account_bank6');
                $account_efectivo6 = request('account_efectivo6');
                $account_punto_de_venta6 = request('account_punto_de_venta6');

                $credit_days6 = request('credit_days6');



                $reference6 = request('reference6');

                if($valor_sin_formato_amount_pay6 != 0){

                    if($payment_type6 != 0){

                        $var6->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type6 == 1 || $payment_type6 == 11 || $payment_type6 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank6 != 0)){
                                if(isset($reference6)){

                                    $var6->id_account = $account_bank6;

                                    $var6->reference = $reference6;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                            }
                        }
                        if($payment_type6 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var6->id_account = $account_contado->id;
                        }
                        if($payment_type6 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days6)){

                                $var6->credit_days = $credit_days6;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                            }
                        }


                            $var6->payment_type = request('payment_type6');
                            $var6->amount = $valor_sin_formato_amount_pay6;

                            if($coin == 'dolares'){
                                $var6->amount = $var6->amount * $bcv;
                            }

                            if(isset($request->IGTF6)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var6->amount;
                            }

                            $var6->rate = $bcv;

                            $var6->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay6;

                            $validate_boolean6 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }
            $payment_type7 = request('payment_type7');
            if($come_pay >= 7){

                /*-------------PAGO NUMERO 7----------------------*/

                $var7 = new QuotationPayment();
                $var7->setConnection(Auth::user()->database_name);

                $amount_pay7 = request('amount_pay7');

                if(isset($amount_pay7)){

                    $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 7!');
                }


                $account_bank7 = request('account_bank7');
                $account_efectivo7 = request('account_efectivo7');
                $account_punto_de_venta7 = request('account_punto_de_venta7');

                $credit_days7 = request('credit_days7');



                $reference7 = request('reference7');

                if($valor_sin_formato_amount_pay7 != 0){

                    if($payment_type7 != 0){

                        $var7->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type7 == 1 || $payment_type7 == 11 || $payment_type7 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank7 != 0)){
                                if(isset($reference7)){

                                    $var7->id_account = $account_bank7;

                                    $var7->reference = $reference7;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                            }
                        }
                        if($payment_type7 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var7->id_account = $account_contado->id;
                        }
                        if($payment_type7 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days7)){

                                $var7->credit_days = $credit_days7;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                            }
                        }




                            $var7->payment_type = request('payment_type7');
                            $var7->amount = $valor_sin_formato_amount_pay7;

                            if($coin == 'dolares'){
                                $var7->amount = $var7->amount * $bcv;
                            }

                            if(isset($request->IGTF7)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var7->amount;
                            }

                            $var7->rate = $bcv;

                            $var7->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay7;

                            $validate_boolean7 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }

        }

       //validacion que verifica que el total pagado en IGTF sea igual al total a pagar de IGTF
      /*  if(isset($IGTF_amount)){
            if($IGTF_amount_check != $IGTF_amount){
                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger("El total pagado en IGTF es diferente al total a pagar de IGTF !!");
            }
        }*/

        
////////////////////////COMPROBANTE DE VENTA///////////////////////////////
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            if($quotation_status == 1){

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);

                $header_voucher->description = "Ventas de Bienes o servicios.";
                $header_voucher->date = $date_payment;


                $header_voucher->status =  "1";

                $header_voucher->save();

                /*Busqueda de Cuentas*/

                //Cuentas por Cobrar Clientes

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();

                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$quotation->id,$user_id,$sin_formato_grandtotal,0);
                }

                //Ingresos por SubSegmento de Bienes

                if($total_mercancia != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$quotation->id,$user_id,0,$total_mercancia);
                    }
                }

                if($total_servicios != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$quotation->id,$user_id,0,$total_servicios);
                    }
                }

                //Debito Fiscal IVA por Pagar

                $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();

                if($base_imponible != 0){
                    $total_iva = ($base_imponible * $iva_percentage)/100;

                    if(isset($account_cuentas_por_cobrar)){
                        $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$quotation->id,$user_id,0,$total_iva);
                    }
                }

                //anadir movimiento de IGTF

                    if ($IGTF_input > 0){
                        $account_IGTF = Account::on(Auth::user()->database_name)->where('description', 'like', '%Cuentas por Pagar IGTF%')->first();

                        if(isset($account_IGTF)){

                            $this->add_movement($bcv,$header_voucher->id,$account_IGTF->id,$quotation->id,$user_id,0,$IGTF_input);
                        }
                    }

                  //anadir movimiento de IGTF


                //Mercancia para la Venta
                $validation_factura = new FacturaValidationController($quotation);

                $return_validation_factura = $validation_factura->validate_movement_mercancia();


                if(empty($quotation->date_delivery_note)){
                    if($price_cost_total != 0){

                        //BUSCA EL TOTAL DEL COSTO DE MERCANCIA POR PRODUCTO
                        $facturaCalculation = new FacturaCalculationController($quotation);

                        $accounts_for_movements = $facturaCalculation->calculateTotalForAccount($quotation->id);

                        $account_costo_mercancia = Account::on(Auth::user()->database_name)->where('description', 'like', 'Costo de Mercancía')->first();


                        foreach($accounts_for_movements as $movement){

                            $movement->total = $movement->total;

                            if(isset($account_cuentas_por_cobrar)){
                                $this->add_movement($bcv,$header_voucher->id,$movement->id_account,$quotation->id,$user_id,0,$movement->total);
                            }

                            //Costo de Mercancia
                            if(isset($account_cuentas_por_cobrar)){
                                $this->add_movement($bcv,$header_voucher->id,$account_costo_mercancia->id,$quotation->id,$user_id,$movement->total,0);
                            }
                        }




                    }
                }
                /*----------- */
            }
///////////////////////////////FIN COMPROBENTE DE VENTA////////////////////////////////////////////



        $sin_formato_total_pay = floatval($sin_formato_total_pay);
        $epsilon = 0.00001;

        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if (abs($total_pay - $sin_formato_total_pay) < $epsilon  || ($sin_formato_total_pay <= 0)) {

            $global = new GlobalController();

            $comboController = new ComboController();

            if(empty($quotation->date_billing) && empty($quotation->date_delivery_note) && empty($quotation->date_order)){

                //$value_return_combo = $comboController->validate_combo_discount($quotation->id);
                $value_return_all = $global->check_all_products_after_facturar($quotation->id);

                if($value_return_all != "exito"){
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($value_return_all);
                }

                $retorno = $global->discount_inventory($quotation->id);

                if($retorno != "exito"){
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($retorno);
                }
            }

            /*---------------- */

            $header_voucher  = new HeaderVoucher();
            $header_voucher->setConnection(Auth::user()->database_name);
            $header_voucher->description = "Cobro de Bienes o servicios.";
            $header_voucher->date = $date_payment;
            $header_voucher->status =  "1";
            $header_voucher->save();


            if($validate_boolean1 == true){
                $var->created_at = $date_payment;
                $var->save();

                $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$quotation->id,$user_id,$var->amount,0);

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation_payment","Registro de Pago");

                //LE PONEMOS STATUS C, DE COBRADO
                $quotation->status = "C";
            }

            if($validate_boolean2 == true){
                $var2->created_at = $date_payment;
                $var2->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var2,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$quotation->id,$user_id,$var2->amount,0);

            }

            if($validate_boolean3 == true){
                $var3->created_at = $date_payment;
                $var3->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var3,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$quotation->id,$user_id,$var3->amount,0);

            }
            if($validate_boolean4 == true){
                $var4->created_at = $date_payment;
                $var4->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var4,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$quotation->id,$user_id,$var4->amount,0);

            }
            if($validate_boolean5 == true){
                $var5->created_at = $date_payment;
                $var5->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var5,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$quotation->id,$user_id,$var5->amount,0);

            }
            if($validate_boolean6 == true){
                $var6->created_at = $date_payment;
                $var6->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var6,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$quotation->id,$user_id,$var6->amount,0);

            }
            if($validate_boolean7 == true){
                $var7->created_at = $date_payment;
                $var7->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var7,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$quotation->id,$user_id,$var7->amount,0);

            }


        if ($quotation_status == 1){

            $quotation->base_imponible = $base_imponible;
            $quotation->amount_exento =  $amount_exento;
            $quotation->amount =  $sin_formato_amount;
            $quotation->amount_iva =  $sin_formato_amount_iva;
            $quotation->amount_with_iva = $base_imponible + $amount_exento + $sin_formato_amount_iva;
            $quotation->amount_with_iva = $sin_formato_grandtotal;
            $quotation->iva_percentage = $iva_percentage;
        }
            $quotation->retencion_iva = $retencion_iva;
            $quotation->retencion_islr = $retencion_islr; 
            $quotation->impuesto_tf = $impuesto_tf;
            $quotation->IGTF_percentage = $IGTF_porc;
            $quotation->IGTF_amount = $IGTF_input;


            if($coin == 'dolares'){

                $anticipo =  $anticipo * $bcv;
                $retencion_iva = $retencion_iva * $bcv;
                $retencion_islr = $retencion_islr * $bcv;
                $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
                $base_imponible = $base_imponible * $bcv;
                $sin_formato_amount = $sin_formato_amount * $bcv;
                $sin_formato_total_pay = $sin_formato_total_pay * $bcv;
                $sin_formato_grandtotal = $sin_formato_grandtotal * $bcv;
                $sub_total = $sub_total * $bcv;
                $total_mercancia = $total_mercancia * $bcv;
                $total_servicios = $total_servicios * $bcv;
                $impuesto_tf = $impuesto_tf * $bcv;

                if ($quotation_status == 1){
                $quotation->base_imponible = $base_imponible;
                $quotation->amount_exento =  $amount_exento * $bcv;
                $quotation->amount =  $sin_formato_amount;
                $quotation->amount_iva =  $sin_formato_amount_iva;
                $quotation->amount_with_iva = ($base_imponible) + ($amount_exento * $bcv) + ($sin_formato_amount_iva);
                $quotation->amount_with_iva = $sin_formato_grandtotal;
                $quotation->iva_percentage = $iva_percentage;
                }

                $quotation->retencion_iva = $retencion_iva;
                $quotation->retencion_islr = $retencion_islr;
                $quotation->impuesto_tf = $impuesto_tf;
                $quotation->IGTF_percentage = $IGTF_porc * $bcv;
                $quotation->IGTF_amount = $IGTF_input * $bcv;
            }


            if($coin == 'dolares'){
                $sin_formato_grandtotal = (($sin_formato_grandtotal + $debitnote) - $creditnote);
            } else {
                $sin_formato_grandtotal = (($sin_formato_grandtotal + $debitnote) - $creditnote);
            }

            //incluyendo el todal de debit note en el total asiento cuanta por cobrar
            /*Anticipos*/

            if(isset($anticipo) && ($anticipo != 0)){
                $account_anticipo_cliente = Account::on(Auth::user()->database_name)->where('description','Anticipos Clientes Nacionales')->first();
                //Si el total a pagar es negativo, quiere decir que los anticipos sobrepasan al monto total de la factura
                if($sin_formato_total_pay < 0){
                    $this->check_anticipo($quotation,$sin_formato_grandtotal);
                    $quotation->anticipo =  $sin_formato_grandtotal;
                    $quotation->status = "C";

                }else{
                    $quotation->anticipo = $anticipo;
                    $global->associate_anticipos_quotation($quotation);
                    $quotation->status = "C";
                }

                if(isset($account_anticipo_cliente)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo_cliente->id,$quotation->id,$user_id,$quotation->anticipo,0);
                    $global->add_payment($quotation,$account_anticipo_cliente->id,3,$quotation->anticipo,$bcv);
                }
             }else{
                 $quotation->anticipo = 0;
             }
            /*---------- */

            if($retencion_iva > 0){
                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('description', 'like', '%IVA Retenido por Terceros%')->first();

                if(isset($account_iva_retenido)){
                    $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$quotation->id,$user_id,$retencion_iva,0);
                }
            }


            if($retencion_islr > 0){
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('description','like','%ISLR Retenido por Terceros%')->first(); 

                if(isset($account_islr_pagago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$quotation->id,$user_id,$retencion_islr,0);
                }
            }
            
            if($impuesto_tf > 0){

                
                $account_impuesto_tf = Account::on(Auth::user()->database_name)->where('description','like','%Impuesto No deducible TF%')->first(); 

                if(isset($account_impuesto_tf)){
                    $this->add_movement($bcv,$header_voucher->id,$account_impuesto_tf->id,$quotation->id,$user_id,$impuesto_tf,0);
                }
            }

            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas `
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description','like','Cuentas por Cobrar Clientes')->first();

            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$quotation->id,$user_id,0,($sin_formato_grandtotal));
            }


            if($quotation_status == 1){ //numeracion factura

                if(empty($quotation->number_invoice))
                {   //Me busco el ultimo numero en factura
                    $last_number = Quotation::on(Auth::user()->database_name)
                    ->where('id_branch',$quotation->id_branch)
                    ->where('number_invoice','<>',NULL)
                    ->orderBy('number_invoice','desc')->first();
                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $quotation->number_invoice = $last_number->number_invoice + 1;
                    }else{
                        $quotation->number_invoice = 1;
                    }
                }


                if(!isset($quotation->number_delivery_note)){
                    $quotation->number_delivery_note = 0;
                } else {

                    if(empty($quotation->number_delivery_note) || $quotation->number_delivery_note == null) {
                        $quotation->number_delivery_note = 0;
                    }
                }

                $global = new GlobalController;

                $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
                ->where('id_quotation', '=', $quotation->id)
                ->where('status','!=','X')
                ->get();

                foreach($quotation_products as $det_products){ //descontar venta de inventario

                $global->transaction_inv('venta',$det_products->id_inventory,'venta',$det_products->amount,$det_products->price,$date,1,1,0,$det_products->id_inventory_histories,$det_products->id,$quotation->id);

                }


            }

            /*Modifica la factura*/


            $quotation->status = "C";


            $quotation->save();   /// guardando factura


            $debitnote = DebitNote::on(Auth::user()->database_name) //actualizando nota de debito
            ->where('id_quotation',$quotation->id)
            ->get();

            if (!empty($debitnote)){
                DB::connection(Auth::user()->database_name)->table('debit_notes')
                ->where('id_quotation',$quotation->id)
                ->update(['status' => 'C']);
            }

            $creditnote = CreditNote::on(Auth::user()->database_name)
            ->where('id_quotation',$quotation->id)
            ->get();

            if (!empty($creditnote)){
                DB::connection(Auth::user()->database_name)->table('credit_notes')
                ->where('id_quotation', '=', $quotation->id)
                ->update(['status' => 'C']);
            }

            /*---------------------- */

            

                   /////////////////////////////**************LO DE COURIERTOOL**************/////////////////
           if($request->court != null AND  $request->tifac != null AND $request->nrofactcou != null AND Auth::user()->company['id'] == '26'){

            $factcour  = new FacturasCour();
            $factcour->setConnection(Auth::user()->database_name);
            $factcour->id_ventas = $quotation->id;
            $factcour->tipo_fac = $request->tifac;
            $factcour->tipo_movimiento = $request->court;
            $factcour->numero =  $request->nrofactcou;
            $factcour->monto =  $montocour;
            $factcour->save();

        }
    /////////////////////////////**************LO DE COURIERTOOL**************/////////////////

            $global = new GlobalController;

            //Aqui pasa los quotation_products a status C de Cobrado
            DB::connection(Auth::user()->database_name)->table('quotation_products')
                                                        ->where('id_quotation', '=', $quotation->id)
                                                        ->where('status', '!=','X')
                                                        ->update(['status' => 'C']);

            $global->procesar_anticipos($quotation,$sin_formato_total_pay);
            /*------------------------------------------------- */
            $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($quotation,"quotation","Registro de Factura Realizada");

            return redirect('quotations/facturado/'.$quotation->id.'/'.$coin.'')->withSuccess('Factura Guardada con Exito!');

        }else{
            return redirect('quotations/facturar/'.$quotation->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }


        }

    }

  /////CREAR ANTICIPO DIRECTO Y SALDAR NOTA ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function storeanticiposaldar(Request $request)
{
    $data = request()->validate([

    ]);

    //dd($request);

    $quotation = Quotation::on(Auth::user()->database_name)->find(request('id_quotation'));

    $quotation_status =  $quotation->status;

    $company = Company::on(Auth::user()->database_name)->find(1);

    $anticipo = Anticipo::on(Auth::user()->database_name)
    ->where('id_quotation',$quotation->id) //Saldar anticipo previo
    ->where('status',1)
    ->get();

    foreach ($anticipo as $variante){

        $updateanticipo = Anticipo::on(Auth::user()->database_name)
        ->where('id',$variante->id) //Saldar anticipo previo
        ->update(['status' => 'C']);
    }

    if($quotation->date_billing != null && $quotation->status == 'C' ){
        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Ya esta Nota fue procesada!');
    }else{


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $user_id = request('user_id');

        //Validar cuales son los pagos a guardar
            $validate_boolean1 = false;
            $validate_boolean2 = false;
            $validate_boolean3 = false;
            $validate_boolean4 = false;
            $validate_boolean5 = false;
            $validate_boolean6 = false;
            $validate_boolean7 = false;

        //-----------------------

        $bcv = $quotation->bcv;

        $coin = request('coin');

        $price_cost_total = request('price_cost_total');

        $anticipo = request('anticipo_form');
        $retencion_iva = request('total_retiene_iva');
        $retencion_islr = request('total_retiene_islr');
        $anticipo = request('anticipo_form');

        $sub_total = request('sub_total_form');
        $base_imponible = request('base_imponible_form');
        $amount_exento = request('amount_exento');
        $sin_formato_amount = request('sub_total_form');
        $iva_percentage = request('iva_form');
        $sin_formato_total_pay = request('total_pay_form');

        $sin_formato_grandtotal = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount_form')));


        $total_mercancia = request('total_mercancia');
        $total_servicios = request('total_servicios');

        $date_payment = request('date-payment-form');

        $total_iva = 0;

        $amount_pay = request('amount_pay');
        $IGTF_input = request('IGTF_input_pre');
        $IGTF_input_check = request('IGTF_input_store');

        if ($IGTF_input_check == 0) {
            $IGTF_input = 0;
        }

        $IGTF_porc = request('IGTF_porc');

        $IGTF_percentage = $company->IGTF_percentage ?? 3;

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }


        $IGTF_amount_check = 0;

        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($sin_formato_total_pay > 0){
            $payment_type = request('payment_type');
            if($come_pay >= 1){

                //-------------PAGO NUMERO 1----------------------

                $var = new QuotationPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');

                if(isset($amount_pay)){

                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 1!');
                }


                $account_bank = request('account_bank');
                $account_efectivo = request('account_efectivo');
                $account_punto_de_venta = request('account_punto_de_venta');

                $credit_days = request('credit_days');

                $reference = request('reference');

                if($valor_sin_formato_amount_pay != 0){

                    if($payment_type != 0){

                        $var->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type == 1 || $payment_type == 11 || $payment_type == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank != 0)){
                                if(isset($reference)){

                                    $var->id_account = $account_bank;

                                    $var->reference = $reference;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                            }
                        }if($payment_type == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica%')->first();

                            $var->id_account = $account_contado->id;
                        }
                        if($payment_type == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days)){

                                $var->credit_days = $credit_days;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }

                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){

                                $var->id_account = $account_efectivo;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }

                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                            }
                        }




                            $var->payment_type = request('payment_type');
                            $var->amount = $valor_sin_formato_amount_pay;

                            if($coin == 'dolares'){
                                $var->amount = $var->amount * $bcv;
                            }

                            $var->rate = $bcv;

                            if(isset($request->IGTF)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var->amount;
                            }

                            $var->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay;

                            $validate_boolean1 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago debe ser distinto de Cero!');
                    }
                //--------------------------------------------
            }
            $payment_type2 = request('payment_type2');
            if($come_pay >= 2){

                //-------------PAGO NUMERO 2----------------------

                $var2 = new QuotationPayment();
                $var2->setConnection(Auth::user()->database_name);

                $amount_pay2 = request('amount_pay2');

                if(isset($amount_pay2)){

                    $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 2!');
                }


                $account_bank2 = request('account_bank2');
                $account_efectivo2 = request('account_efectivo2');
                $account_punto_de_venta2 = request('account_punto_de_venta2');

                $credit_days2 = request('credit_days2');



                $reference2 = request('reference2');

                if($valor_sin_formato_amount_pay2 != 0){

                if($payment_type2 != 0){

                    $var2->id_quotation = request('id_quotation');

                    //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                    if($payment_type2 == 1 || $payment_type2 == 11 || $payment_type2 == 5 ){
                        //CUENTAS BANCARIAS
                        if(($account_bank2 != 0)){
                            if(isset($reference2)){

                                $var2->id_account = $account_bank2;

                                $var2->reference = $reference2;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                        }
                    }
                    if($payment_type2 == 2){

                        $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                        $var2->id_account = $account_contado->id;
                    }
                    if($payment_type2 == 4){
                        //DIAS DE CREDITO
                        if(isset($credit_days2)){

                            $var2->credit_days = $credit_days2;

                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                        }
                    }




                        $var2->payment_type = request('payment_type2');
                        $var2->amount = $valor_sin_formato_amount_pay2;

                        if($coin == 'dolares'){
                            $var2->amount = $var2->amount * $bcv;
                        }

                        if(isset($request->IGTF2)){
                            $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                            $IGTF_amount_check += $var2->amount;
                        }

                        $var2->rate = $bcv;

                        $var2->status =  1;

                        $total_pay += $valor_sin_formato_amount_pay2;

                        $validate_boolean2 = true;


                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }


                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                }
                //--------------------------------------------
            }
            $payment_type3 = request('payment_type3');
            if($come_pay >= 3){

                    //-------------PAGO NUMERO 3----------------------

                    $var3 = new QuotationPayment();
                    $var3->setConnection(Auth::user()->database_name);

                    $amount_pay3 = request('amount_pay3');

                    if(isset($amount_pay3)){

                        $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 3!');
                    }


                    $account_bank3 = request('account_bank3');
                    $account_efectivo3 = request('account_efectivo3');
                    $account_punto_de_venta3 = request('account_punto_de_venta3');

                    $credit_days3 = request('credit_days3');



                    $reference3 = request('reference3');

                    if($valor_sin_formato_amount_pay3 != 0){

                        if($payment_type3 != 0){

                            $var3->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type3 == 1 || $payment_type3 == 11 || $payment_type3 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank3 != 0)){
                                    if(isset($reference3)){

                                        $var3->id_account = $account_bank3;

                                        $var3->reference = $reference3;

                                    }else{
                                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                }
                            }
                            if($payment_type3 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var3->id_account = $account_contado->id;
                            }
                            if($payment_type3 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days3)){

                                    $var3->credit_days = $credit_days3;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                }
                            }




                                $var3->payment_type = request('payment_type3');
                                $var3->amount = $valor_sin_formato_amount_pay3;

                                if($coin == 'dolares'){
                                    $var3->amount = $var3->amount * $bcv;
                                }

                                if(isset($request->IGTF3)){
                                    $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                    $IGTF_amount_check += $var3->amount;
                                }

                                $var3->rate = $bcv;

                                $var3->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay3;

                                $validate_boolean3 = true;


                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }


                    }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                        }
                    //--------------------------------------------
            }
            $payment_type4 = request('payment_type4');
            if($come_pay >= 4){

                    //-------------PAGO NUMERO 4----------------------

                    $var4 = new QuotationPayment();
                    $var4->setConnection(Auth::user()->database_name);

                    $amount_pay4 = request('amount_pay4');

                    if(isset($amount_pay4)){

                        $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 4!');
                    }


                    $account_bank4 = request('account_bank4');
                    $account_efectivo4 = request('account_efectivo4');
                    $account_punto_de_venta4 = request('account_punto_de_venta4');

                    $credit_days4 = request('credit_days4');



                    $reference4 = request('reference4');

                    if($valor_sin_formato_amount_pay4 != 0){

                        if($payment_type4 != 0){

                            $var4->id_quotation = request('id_quotation');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type4 == 1 || $payment_type4 == 11 || $payment_type4 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank4 != 0)){
                                    if(isset($reference4)){

                                        $var4->id_account = $account_bank4;

                                        $var4->reference = $reference4;

                                    }else{
                                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                }
                            }
                            if($payment_type4 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var4->id_account = $account_contado->id;
                            }
                            if($payment_type4 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days4)){

                                    $var4->credit_days = $credit_days4;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                }
                            }




                                $var4->payment_type = request('payment_type4');
                                $var4->amount = $valor_sin_formato_amount_pay4;

                                if($coin == 'dolares'){
                                    $var4->amount = $var4->amount * $bcv;
                                }

                                if(isset($request->IGTF4)){
                                    $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                    $IGTF_amount_check += $var4->amount;
                                }

                                $var4->rate = $bcv;

                                $var4->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay4;

                                $validate_boolean4 = true;


                        }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }


                    }else{
                            return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                        }
                    //--------------------------------------------
            }
            $payment_type5 = request('payment_type5');
            if($come_pay >= 5){

                //-------------PAGO NUMERO 5----------------------

                $var5 = new QuotationPayment();
                $var5->setConnection(Auth::user()->database_name);

                $amount_pay5 = request('amount_pay5');

                if(isset($amount_pay5)){

                    $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 5!');
                }


                $account_bank5 = request('account_bank5');
                $account_efectivo5 = request('account_efectivo5');
                $account_punto_de_venta5 = request('account_punto_de_venta5');

                $credit_days5 = request('credit_days5');



                $reference5 = request('reference5');

                if($valor_sin_formato_amount_pay5 != 0){

                    if($payment_type5 != 0){

                        $var5->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type5 == 1 || $payment_type5 == 11 || $payment_type5 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank5 != 0)){
                                if(isset($reference5)){

                                    $var5->id_account = $account_bank5;

                                    $var5->reference = $reference5;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                            }
                        }
                        if($payment_type5 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var5->id_account = $account_contado->id;
                        }
                        if($payment_type5 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days5)){

                                $var5->credit_days = $credit_days5;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                            }
                        }




                            $var5->payment_type = request('payment_type5');
                            $var5->amount = $valor_sin_formato_amount_pay5;

                            if($coin == 'dolares'){
                                $var5->amount = $var5->amount * $bcv;
                            }

                            if(isset($request->IGTF5)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var5->amount;
                            }

                            $var5->rate = $bcv;

                            $var5->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay5;

                            $validate_boolean5 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                    }
                //--------------------------------------------
            }
            $payment_type6 = request('payment_type6');
            if($come_pay >= 6){

                //-------------PAGO NUMERO 6----------------------

                $var6 = new QuotationPayment();
                $var6->setConnection(Auth::user()->database_name);

                $amount_pay6 = request('amount_pay6');

                if(isset($amount_pay6)){

                    $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 6!');
                }


                $account_bank6 = request('account_bank6');
                $account_efectivo6 = request('account_efectivo6');
                $account_punto_de_venta6 = request('account_punto_de_venta6');

                $credit_days6 = request('credit_days6');



                $reference6 = request('reference6');

                if($valor_sin_formato_amount_pay6 != 0){

                    if($payment_type6 != 0){

                        $var6->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type6 == 1 || $payment_type6 == 11 || $payment_type6 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank6 != 0)){
                                if(isset($reference6)){

                                    $var6->id_account = $account_bank6;

                                    $var6->reference = $reference6;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                            }
                        }
                        if($payment_type6 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var6->id_account = $account_contado->id;
                        }
                        if($payment_type6 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days6)){

                                $var6->credit_days = $credit_days6;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                            }
                        }




                            $var6->payment_type = request('payment_type6');
                            $var6->amount = $valor_sin_formato_amount_pay6;

                            if($coin == 'dolares'){
                                $var6->amount = $var6->amount * $bcv;
                            }

                            if(isset($request->IGTF6)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var6->amount;
                            }

                            $var6->rate = $bcv;

                            $var6->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay6;

                            $validate_boolean6 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                    }
                //--------------------------------------------
            }
            $payment_type7 = request('payment_type7');
            if($come_pay >= 7){

               // -------------PAGO NUMERO 7----------------------

                $var7 = new QuotationPayment();
                $var7->setConnection(Auth::user()->database_name);

                $amount_pay7 = request('amount_pay7');

                if(isset($amount_pay7)){

                    $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                }else{
                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 7!');
                }


                $account_bank7 = request('account_bank7');
                $account_efectivo7 = request('account_efectivo7');
                $account_punto_de_venta7 = request('account_punto_de_venta7');

                $credit_days7 = request('credit_days7');



                $reference7 = request('reference7');

                if($valor_sin_formato_amount_pay7 != 0){

                    if($payment_type7 != 0){

                        $var7->id_quotation = request('id_quotation');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type7 == 1 || $payment_type7 == 11 || $payment_type7 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank7 != 0)){
                                if(isset($reference7)){

                                    $var7->id_account = $account_bank7;

                                    $var7->reference = $reference7;

                                }else{
                                    return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                            }
                        }
                        if($payment_type7 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var7->id_account = $account_contado->id;
                        }
                        if($payment_type7 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days7)){

                                $var7->credit_days = $credit_days7;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                            }
                        }




                            $var7->payment_type = request('payment_type7');
                            $var7->amount = $valor_sin_formato_amount_pay7;

                            if($coin == 'dolares'){
                                $var7->amount = $var7->amount * $bcv;
                            }

                            if(isset($request->IGTF7)){
                                $var->IGTF_percentage = $company->IGTF_percentage ?? 3;
                                $IGTF_amount_check += $var7->amount;
                            }

                            $var7->rate = $bcv;

                            $var7->status =  1;

                            $total_pay += $valor_sin_formato_amount_pay7;

                            $validate_boolean7 = true;


                    }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }


                }else{
                        return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }
                //--------------------------------------------
            }

        }

    //validacion que verifica que el total pagado en IGTF sea igual al total a pagar de IGTF
        if(isset($IGTF_amount)){
            if($IGTF_amount_check != $IGTF_amount){
                return redirect('quotations/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger("El total pagado en IGTF es diferente al total a pagar de IGTF !!");
            }
        }

        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if(($total_pay == $sin_formato_total_pay) || ($sin_formato_total_pay <= 0))
        {
            $global = new GlobalController();

            $comboController = new ComboController();

            $header_voucher  = new HeaderVoucher();
            $header_voucher->setConnection(Auth::user()->database_name);
            $header_voucher->description = "Anticipo a Clientes Nota ".$quotation->number_delivery_note;
            $header_voucher->date = $date_payment;
            $header_voucher->status =  "1";

            $header_voucher->save();

          //  $anticipo = Anticipo::on(Auth::user()->database_name)->where('id_quotation',$quotation->id) //Saldar anticipo previo
          //  ->where('status',1)
           // ->update(['status' => 'C']);

            if($validate_boolean1 == true){
                $var->created_at = $date_payment;
                $var->save();

                $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$quotation->id,$user_id,$var->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var->id_account,$coin,$var->amount,$bcv,$reference,$quotation->id,$header_voucher->id);

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation_payment","Registro de Pago");

            }
            if($validate_boolean2 == true){
                $var2->created_at = $date_payment;
                $var2->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var2,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$quotation->id,$user_id,$var2->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var2->id_account,$coin,$var2->amount,$bcv,$reference2,$quotation->id,$header_voucher->id);
            }
            if($validate_boolean3 == true){
                $var3->created_at = $date_payment;
                $var3->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var3,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$quotation->id,$user_id,$var3->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var3->id_account,$coin,$var3->amount,$bcv,$reference3,$quotation->id,$header_voucher->id);
            }
            if($validate_boolean4 == true){
                $var4->created_at = $date_payment;
                $var4->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var4,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$quotation->id,$user_id,$var4->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var4->id_account,$coin,$var4->amount,$bcv,$reference4,$quotation->id,$header_voucher->id);
            }
            if($validate_boolean5 == true){
                $var5->created_at = $date_payment;
                $var5->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var5,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$quotation->id,$user_id,$var5->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var5->id_account,$coin,$var5->amount,$bcv,$reference5,$quotation->id,$header_voucher->id);
            }
            if($validate_boolean6 == true){
                $var6->created_at = $date_payment;
                $var6->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var6,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$quotation->id,$user_id,$var6->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var6->id_account,$coin,$var6->amount,$bcv,$reference6,$quotation->id,$header_voucher->id);
            }
            if($validate_boolean7 == true){
                $var7->created_at = $date_payment;
                $var7->save();

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var7,"quotation_payment","Registro de Pago");

                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$quotation->id,$user_id,$var7->amount,0);
                $this->registerAnticipo($date_payment,$quotation->id_client,$var7->id_account,$coin,$var7->amount,$bcv,$reference7,$quotation->id,$header_voucher->id);
            }

            if($coin != 'bolivares'){
                $anticipo =  $anticipo * $bcv;
                $retencion_iva = $retencion_iva * $bcv;
                $retencion_islr = $retencion_islr * $bcv;

                $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
                $base_imponible = $base_imponible * $bcv;
                $sin_formato_amount = $sin_formato_amount * $bcv;
                $sin_formato_total_pay = $sin_formato_total_pay * $bcv;

                $sin_formato_grandtotal = $sin_formato_grandtotal * $bcv;

                $sub_total = $sub_total * $bcv;

                $sub_total = $sub_total * $bcv;

            }


            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('code_one',2)
                                                        ->where('code_two',3)
                                                        ->where('code_three',1)
                                                        ->where('code_four',1)
                                                        ->where('code_five',2)->first();

            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$quotation->id,$user_id,0,$sin_formato_grandtotal);
            }


            //Modifica la factura
        if ($quotation_status != "P") {

            $quotation->base_imponible = $base_imponible;
            $quotation->amount_exento =  $amount_exento;
            $quotation->amount =  $sin_formato_amount;
            $quotation->amount_iva =  $sin_formato_amount_iva;
            $quotation->amount_with_iva = $sin_formato_grandtotal;
            $quotation->iva_percentage = $iva_percentage;
        }
            $quotation->retencion_iva = $retencion_iva;
            $quotation->retencion_islr = $retencion_islr;

            $quotation->IGTF_percentage = $IGTF_porc;
            $quotation->IGTF_amount = $IGTF_input;
            $quotation->status = "C";
            $quotation->date_saldate = $date_payment;

            $quotation->save();
            $date = Carbon::now();





            return redirect('quotations/indexnotasdeentregasald')->withSuccess('Nota '.$quotation->number_delivery_note.' Saldada con Exito!');


        }else{
            return redirect('quotations/facturar/'.$quotation->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }
    }
}
/////Fin CREAR ANTICIPO DIRECTO Y SALDAR NOTA ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




    public function check_anticipo($quotation,$total_pay)
    {

            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
                                                                                    ->where(function ($query) use ($quotation){
                                                                                        $query->where('id_quotation',null)
                                                                                            ->orWhere('id_quotation',$quotation->id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $quotation->bcv;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);

                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);


                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);

                    $var->date = $quotation->date_billing;
                    $var->id_client = $quotation->id_client;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $anticipo->rate;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->id_anticipo_restante = $anticipo->id;
                    $var->save();
                    break;
                }
            }


    }


    public function procesar_anticipos($quotation,$sin_formato_grand_total)
    {

        if($sin_formato_grand_total >= 0){
            $anticipos_old = DB::connection(Auth::user()->database_name)->table('anticipos')
                                ->where('id_client', '=', $quotation->id_client)
                                ->where(function ($query) use ($quotation){
                                    $query->where('id_quotation',null)
                                        ->orWhere('id_quotation',$quotation->id);
                                })
                                ->where('status', '=', '1')->get();

            foreach($anticipos_old as $anticipo){
                DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
            }


            /*Verificamos si el cliente tiene anticipos activos */
            DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_client', '=', $quotation->id_client)
                    ->where(function ($query) use ($quotation){
                        $query->where('id_quotation',null)
                            ->orWhere('id_quotation',$quotation->id);
                    })
                    ->where('status', '=', '1')
                    ->update(['status' => 'C']);

            //los que quedaron en espera, pasan a estar activos
            DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
            ->where(function ($query) use ($quotation){
                $query->where('id_quotation',null)
                    ->orWhere('id_quotation',$quotation->id);
            })
            ->where('status', '=', 'M')
            ->update(['status' => '1']);
        }
    }


    public function add_movement($bcv,$id_header,$id_account,$id_invoice,$id_user,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);


        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        $detail->id_invoice = $id_invoice;

      /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
        $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/


        $detail->debe = $debe;
        $detail->haber = $haber;


        $detail->status =  "C";

         /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */

            $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

            if($account->status != "M"){
                $account->status = "M";
                $account->save();
            }


        $detail->save();

    }


    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$quotation_id,$user_id,$amount_debe,$amount_haber){


            //Cuentas por Cobrar Clientes

                //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
                if(isset($id_account)){
                    $this->add_movement($bcv,$header_voucher,$id_account,$quotation_id,$user_id,$amount_debe,0);

                }//SIN DETERMINAR
                else if($payment_type == 7){
                            //------------------Sin Determinar
                    $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Ingresos No Identificados')->first();

                    if(isset($account_sin_determinar)){
                        $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$quotation_id,$user_id,$amount_debe,0);
                    }
                }//PAGO DE CONTADO
                else if($payment_type == 2){

                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                    if(isset($account_contado)){
                        $this->add_movement($bcv,$header_voucher,$account_contado->id,$quotation_id,$user_id,$amount_debe,0);
                    }
                }//CONTRA ANTICIPO
                else if($payment_type == 3){
                            //--------------
                    $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first();

                    if(isset($account_contra_anticipo)){
                        $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$quotation_id,$user_id,$amount_debe,0);
                    }
                }
                //Tarjeta Corporativa
               /* else if($payment_type == 8){
                            //---------------
                    $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Tarjeta Corporativa')->first();

                    if(isset($account_contra_anticipo)){
                        $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$quotation_id,$user_id,$amount_debe,0);
                    }
                } */



    }



  public function createfacturado($id_quotation,$coin,$type = 'Cotización')
    {
        $user       =   auth()->user();
        $company_user = $user->id_company;

         $quotation = null;

         if(isset($id_quotation)){
             $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
         }

         if(isset($quotation)){

            $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();


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


            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 2)
                                            ->where('code_five', '<>',0)
                                            ->where('description','not like', 'Punto de Venta%')
                                            ->orderBy('description','ASC')
                                            ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->orderBy('description','ASC')
                                            ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                                            ->where('quotation_products.id_quotation',$quotation->id)
                                                            ->whereIn('quotation_products.status',['1','C'])
                                                            ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id_inventory as id_inventory','quotation_products.discount as discount',
                                                            'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                                                            ,'quotation_products.retiene_islr as retiene_islr_quotation')
                                                            ->get();

            $notasdedebito = DB::connection(Auth::user()->database_name)->table('debit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();


            $notasdecredito = DB::connection(Auth::user()->database_name)->table('credit_notes')
            ->where('id_quotation','=',$quotation->id)
            ->where('status','!=','X')
            ->where('status','!=','C')
            ->select( DB::raw('SUM(amount_with_iva/rate) As dolar'),DB::raw('SUM(amount_with_iva) As bolivares'))
            ->get();


             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;


             $total_retiene_iva = 0;
             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;

             $total_mercancia= 0;
             $total_servicios= 0;
             $total_debit_notes = 0;

             foreach($inventories_quotations as $var){

                if($coin != "bolivares"){
                    $var->price = $var->price / $var->rate;
                }

                 //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                    $total += ($var->price * $var->amount_quotation) - $percentage;

                    if ($company_user == 26){ // 26 NORTH D CORP
                        if($var->id_inventory == 34){
                            $total -= (($var->price * $var->amount_quotation) - $percentage) * 2;

                        }
                    }
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
                        $total_mercancia += (($var->price * $var->amount_quotation) - $percentage);
                    }else{
                        $total_servicios += (($var->price * $var->amount_quotation) - $percentage);
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

             if($type == 'factura'){
             $datenow = date_format(date_create($quotation->date_quotation),"Y-m-d");
             }else{
             $datenow = $date->format('Y-m-d');
             }
             $anticipos_sum = 0;

            if ($coin == null) {    /// condicion de la moneda
                $coin = $quotation->coin;
            }

             if(isset($coin)){
                 if($coin == 'dolares'){
                    $bcv = $quotation->bcv;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =   $this->anticipos_bolivares_to_dolars($quotation);
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->dolar;
                    $total_credit_notes = $notasdecredito[0]->dolar;
                 }else{

                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                    $total_debit_notes = $notasdedebito[0]->bolivares;
                    $total_credit_notes = $notasdecredito[0]->bolivares;
                 }
             }else{
                $bcv = null;
                $total_debit_notes = $notasdedebito[0]->bolivares;
                $total_credit_notes = $notasdecredito[0]->bolivares;
             }

             if (count($notasdedebito) <= 0){
                $total_debit_notes = 0;
             }

             if (count($notasdecredito) <= 0){
                $total_credit_notes = 0;
             }


            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
             $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

                if($client->percentage_retencion_iva != 0){
                    $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
                } else {
                    $total_retiene_iva = 0;
                }

                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */
            $company = Company::on(Auth::user()->database_name)->find(1);
            $igtfporc = $company->IGTF_porc ?? 3;
            $impuesto = $company->tax_1 ?? 1;
            $impuesto2 = $company->tax_2 ?? 1;
            $impuesto3 = $company->tax_3 ?? 1;

            $is_after = false;
            if(empty($quotation->credit_days)){
                $is_after = true;
            }

            if (Auth::user()->company['id']  == '26'){
                $validarfact = FacturasCour::on(Auth::user()->database_name)
                ->where('id_ventas',$id_quotation)
                ->first();
                    if($validarfact){
                        $existe = true;
                    }else{
                        $existe = false;
                    }
            }else{
                $existe = false;
            }



             return view('admin.quotations.createfacturado',compact('existe','price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_iva','total_retiene_islr','is_after'
                        ,'total_mercancia','total_servicios','client','retiene_iva','type','igtfporc','total_debit_notes','total_credit_notes','impuesto','impuesto2','impuesto3'));
         }else{
             return redirect('/quotations/index')->withDanger('La factura no existe');
         }

    }

}
