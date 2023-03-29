<?php

namespace App\Http\Controllers;

use App\Account;
use App\Anticipo;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Inventory;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use App\Multipayment;

class InvoiceController extends Controller
{
   

    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Facturas');
      
       
    }
 
    public function index(request $request)
    {
    
       ///////////////API COURIERTOOL TRAER FACTURAS PARA GUARDAR////////////////////////////////
	
            curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => "https://www.couriertool.com/apiinvoice.php",
                //CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_RETURNTRANSFER => 1
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            
            $respuesta = json_decode($response, true);
            

                                foreach($respuesta as $item) { 
                                  
                                    ///Guardando cabecera
                                    $var = new Quotation();
                                    $var->setConnection(Auth::user()->database_name);
                    
                                    $var->id = $item['id'];
                                    $var->number_invoice = $item['number_invoice'];
                                    $var->number_delivery_note = $item['number_delivery_note'];
                                    $var->number_order = $item['number_order'];
                                    $var->number_pedido = $item['number_pedido'];
                                    $var->id_branch = $item['id_branch'];




                                    
			if ($agregar_cliente == 'true'){
	
					$a_clientes = array(
						"id" => 'AUTO',
						"id_user" => 217,
						"id_cost_center" => 1,
						"type_code" => $letra,
						"name" => $nombre_completo,
						"cedula_rif" => $cedula,
						"direction" => $direccion_completa,
						"city" => $dir_ciudad,
						"country" => $dir_pais,
						"phone1" => $tlf_hab,
						"phone2" => $telefono2,
						"email" => $email,
						"personcontact" => $p_contacto,
						"status" => 1,
						"coin" => 0
					);
					
					$id_cliente = DBinsert($connH,"clients",$a_clientes); // registrando cliente en arya
			} 
	
                                    $var->id_client = $item['id_client'];
                                    
                                    $var->id_vendor = $item['id_vendor'];
                                    $var->id_transport = $item['id_transport'];
                                    $var->id_user = $item['id_user'];
                                    $var->serie = $item['serie'];
                                    $var->date_quotation = $item['date_quotation'];
                                    $var->date_billing = $item['date_billing'];
                                    $var->date_delivery_note = $item['date_delivery_note'];
                                    $var->date_order = $item['date_order'];
                                    $var->anticipo = $item['anticipo'];
                                    $var->iva_percentage = $item['iva_percentage'];
                                    $var->observation = $item['observation'];
                                    $var->note = $item['note'];
                                    $var->credit_days = $item['credit_days'];
                                    $var->coin = $item['coin'];
                                    $var->bcv = $item['bcv'];
                                    $var->retencion_iva = $item['retencion_iva'];
                                    $var->retencion_islr = $item['retencion_islr'];
                                    $var->IGTF_percentage = $item['IGTF_percentage'];
                                    $var->IGTF_amount = $item['IGTF_amount'];
                                    $var->base_imponible = $item['base_imponible'];
                                    $var->amount_exento = $item['amount_exento'];
                                    $var->amount = $item['amount'];
                                    $var->amount_iva = $item['amount_iva'];
                                    $var->amount_with_iva = $item['amount_with_iva'];
                                    $var->status = $item['status'];
                                    $var->date_saldate = $item['date_saldate'];
                                    $var->id_driver = $item['id_driver'];
                                    $var->delivery = $item['delivery'];
                                    $var->licence = $item['licence'];
                                    $var->destiny = $item['destiny'];
                                    $var->serie_note = $item['serie_note'];
                                    $var->serie_note_credit = $item['serie_note_credit'];
                                    $var->base_imponible_pcb = $item['base_imponible_pcb'];
                                    $var->iva_percibido = $item['iva_percibido'];
                                    $var->person_note_delivery = $item['person_note_delivery'];
                                    $var->ci_person_note_delivery = $item['ci_person_note_delivery'];
                                    $var->date_expiration = $item['date_expiration'];
                                    $var->save();           
                                }

                                curl_setopt_array($ch2 = curl_init(), array(
                                    CURLOPT_URL => "https://www.couriertool.com/apiinvoicecab.php",
                                    //CURLOPT_POSTFIELDS => http_build_query($params),
                                    CURLOPT_RETURNTRANSFER => 1
                                ));
                    
                                $response2 = curl_exec($ch2);
                                curl_close($ch2);
                                
                                $respuesta2 = json_decode($response2, true);


                                foreach($respuesta2 as $item2) { 

                                    $detail = new DetailVoucher();
                                    $detail->setConnection(Auth::user()->database_name);
                                    $detail->id = $item2['id'];
                                    $detail->id_quotation = $item2['id_quotation'];
                                    $detail->id_inventory = $item2['id_inventory'];
                                    $detail->amount = $item2['amount'];
                                    $detail->discount = $item2['discount'];
                                    $detail->price = $item2['price'];
                                    $detail->rate = $item2['rate'];
                                    $detail->excento = $item2['excento'];
                                    $detail->retiene_iva = $item2['retiene_iva'];
                                    $detail->retiene_islr = $item2['retiene_islr'];
                                    $detail->status = $item2['status'];
                                    $detail->id_inventory_histories = $item2['id_inventory_histories'];
                                    $detail->save();
                                }
            
       ///////////////FIN API COURIERTOOL TRAER FACTURAS PARA GUARDAR////////////////////////////////

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
           
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                                            ->where('date_billing','<>',null)
                                            ->get();


            $agregarmiddleware = $request->get('agregarmiddleware');
            $actualizarmiddleware = $request->get('actualizarmiddleware');
            $eliminarmiddleware = $request->get('eliminarmiddleware');
            $namemodulomiddleware = $request->get('namemodulomiddleware');
        

            if(isset($id_quotation)) {
                $quotationsupd = Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['number_pedido' => $number_pedido]);

            }
    
    
            return view('admin.invoices.index',compact('quotations','datenow','agregarmiddleware','actualizarmiddleware','eliminarmiddleware','namemodulomiddleware')); 

    }

    public function movementsinvoice($id_invoice,$coin = null)
    {
        

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_invoice);
            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
                                            ->where('id_invoice',$id_invoice)
                                            ->where('status','C')
                                            ->get();

            $multipayments_detail = null;
            $invoices = null;

            //Buscamos a la factura para luego buscar atraves del header a la otras facturas
            $multipayment = Multipayment::on(Auth::user()->database_name)->where('id_quotation',$id_invoice)->first();
            if(isset($multipayment)){
            $invoices = Multipayment::on(Auth::user()->database_name)->where('id_header',$multipayment->id_header)->where('id_payment',$multipayment->id_payment)->get();
            $multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)->get();
            }

            if(!isset($coin)){
                $coin = 'bolivares';
            }
         
        
        return view('admin.invoices.index_detail_movement',compact('detailvouchers','quotation','coin','invoices','multipayments_detail'));
    }



    public function multipayment(Request $request)
    {
        $quotation = null;

        //Recorre el request y almacena los valores despues del segundo valor que le llegue, asi guarda los id de las facturas a procesar
        $array = $request->all();
        $count = 0;
        $facturas_a_procesar = [];

        

        $total_facturas = new Quotation;
        $total_facturas->setConnection(Auth::user()->database_name);

        foreach ($array as $key => $item) 
        {
            if($count >= 2){
                array_push($facturas_a_procesar, $item);
                $quotation = $this->calcularfactura($item);

                if((empty($id_client_old)) || ($id_client_old == $quotation->id_client))
                {
                    //El id del cliente se guarda una sola vez, al igual que la suma de los anticipos de ese mismo cliente
                    if(empty($id_client_old)){
                        $id_client_old = $quotation->id_client;

                        //Aqui sacamos los totales de los anticipos del cliente
                        $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                ->where('id_client',$quotation->id_client)
                                                ->where('id_quotation',null)
                                                ->where('coin','like','bolivares')
                                                ->sum('amount');

                        $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                ->where('id_client',$quotation->id_client)
                                                ->where('id_quotation',null)
                                                ->where('coin','not like','bolivares')
                                                ->select( DB::raw('SUM(anticipos.amount/anticipos.rate) As dolar'))
                                                ->get();
    
                        
                        $anticipos_sum_dolares = 0;
                        if(isset($total_dolar_anticipo[0]->dolar)){
                            $anticipos_sum_dolares += $total_dolar_anticipo[0]->dolar;
                        }

                        
                    }

                        //aqui sacamos los totales de los anticipos por factura
                        $anticipos_sum_bolivares += Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                    ->where('id_client',$quotation->id_client)
                                                    ->where('id_quotation',$quotation->id)
                                                    ->where('coin','like','bolivares')
                                                    ->sum('amount');

                        $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                    ->where('id_client',$quotation->id_client)
                                                    ->where('id_quotation',$quotation->id)
                                                    ->where('coin','not like','bolivares')
                                                    ->select( DB::raw('SUM(anticipos.amount/anticipos.rate) As dolar'))
                                                    ->get();
    
                        
                        
                        if(isset($total_dolar_anticipo[0]->dolar)){
                            $anticipos_sum_dolares += $total_dolar_anticipo[0]->dolar;
                        }
                        

                    $total = $quotation->amount + $quotation->amount_iva - $quotation->retencion_islr - $quotation->retencion_iva;
                    
                    
                    $total_facturas->retencion_iva += $quotation->retencion_iva;
                    $total_facturas->retencion_islr += $quotation->retencion_islr;
                    $total_facturas->base_imponible += $quotation->base_imponible;
                    $total_facturas->amount += $quotation->amount;
                    $total_facturas->amount_iva += $quotation->amount_iva;
                    $total_facturas->amount_with_iva += $total;
                    $total_facturas->total_factura += $quotation->total_factura;
                    $total_facturas->price_cost_total += $quotation->price_cost_total;
                    $total_facturas->total_mercancia += $quotation->total_mercancia;
                    $total_facturas->total_servicios += $quotation->total_servicios;
                }else{
                    return redirect('invoices')->withDanger('Solo se pueden Pagar Facturas de un mismo Cliente!');
                }
            
            }
            
            $count ++;
        }
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            //esto es para que siempre se pueda guardar la tasa en la base de datos
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }
        

        
        $total_facturas->anticipo = $anticipos_sum_bolivares + ($anticipos_sum_dolares * $bcv);

        $total_facturas->amount_with_iva -= $total_facturas->anticipo;
         
        if(empty($facturas_a_procesar)){
            return redirect('invoices')->withDanger('Debe seleccionar facturar para Pagar!');
       }
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->where('code_one', 1)
                                    ->where('code_two', 1)
                                    ->where('code_three', 1)
                                    ->where('code_four', 2)
                                    ->where('code_five', '<>',0)
                                    ->where('description','not like', 'Punto de Venta%')
                                    ->get();
        $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->where('code_one', 1)
                                    ->where('code_two', 1)
                                    ->where('code_three', 1)
                                    ->where('code_four', 1)
                                    ->where('code_five', '<>',0)
                                    ->get();
        $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->where('description','LIKE', 'Punto de Venta%')
                                    ->get();

        //dd($total_facturas);
        
        return view('admin.invoices.createmultifacturar',compact('total_facturas',
                 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                ,'datenow','facturas_a_procesar'));
         
         
    }

    public function storemultipayment(Request $request)
    {
        
        /*Validar cuales son los pagos a guardar */
        $validate_boolean1 = false;
        $validate_boolean2 = false;
        $validate_boolean3 = false;
        $validate_boolean4 = false;
        $validate_boolean5 = false;
        $validate_boolean6 = false;
        $validate_boolean7 = false;
        //-----------------------

        $total_retiene_iva = str_replace(',', '.', str_replace('.', '', request('iva_retencion')));
        $total_retiene_islr = str_replace(',', '.', str_replace('.', '', request('islr_retencion')));
        $anticipo = str_replace(',', '.', str_replace('.', '', request('anticipo')));
        

        $base_imponible = str_replace(',', '.', str_replace('.', '', request('base_imponible')));
        $amount = str_replace(',', '.', str_replace('.', '', request('total_factura')));
        $amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount')));
        $amount_with_iva = str_replace(',', '.', str_replace('.', '', request('total_pay')));

        $grand_total = str_replace(',', '.', str_replace('.', '', request('grand_total')));


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
      
        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Ventas de Bienes o servicios.";
        $header_voucher->date = $datenow;
        
    
        $header_voucher->status =  "1";
    
        $header_voucher->save();

        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $user_id = request('user_id');
        $payment_type = request('payment_type');

        $coin = 'bolivares';

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();
        
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($amount_with_iva > 0){
            if($come_pay >= 1){

                /*-------------PAGO NUMERO 1----------------------*/

                $var = new QuotationPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');
        
                if(isset($amount_pay)){
                    
                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 1!');
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
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                            }
                        }
                        if($payment_type == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days)){
        
                                $var->credit_days = $credit_days;
        
                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }
        
                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){
        
                                $var->id_account = $account_efectivo;
        
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }
        
                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                            }
                        }
        
                            
                    
        
                            $var->payment_type = request('payment_type');
                            $var->amount = $valor_sin_formato_amount_pay;
                            
                            if($coin == 'dolares'){
                                $var->amount = $var->amount * $bcv;
                            }

                            $var->rate = $bcv;
                            
                            $var->status =  1;
                        
                            $total_pay += $valor_sin_formato_amount_pay;
        
                            $validate_boolean1 = true;
        
                        
                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }
        
                    
                }else{
                        return redirect('invoices')->withDanger('El pago debe ser distinto de Cero!');
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
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 2!');
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
                                return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                        }
                    }
                    if($payment_type2 == 4){
                        //DIAS DE CREDITO
                        if(isset($credit_days2)){

                            $var2->credit_days = $credit_days2;

                        }else{
                            return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                        }
                    }

                        
                

                        $var2->payment_type = request('payment_type2');
                        $var2->amount = $valor_sin_formato_amount_pay2;
                        
                        if($coin == 'dolares'){
                            $var2->amount = $var2->amount * $bcv;
                        }
                        $var2->rate = $bcv;
                        
                        $var2->status =  1;
                    
                        $total_pay += $valor_sin_formato_amount_pay2;

                        $validate_boolean2 = true;

                    
                }else{
                    return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }

                
                }else{
                    return redirect('invoices')->withDanger('El pago 2 debe ser distinto de Cero!');
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
                        return redirect('invoices')->withDanger('Debe ingresar un monto de pago 3!');
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
                                        return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                }
                            }
                            if($payment_type3 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days3)){

                                    $var3->credit_days = $credit_days3;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                }
                            }

                        
                        

                                $var3->payment_type = request('payment_type3');
                                $var3->amount = $valor_sin_formato_amount_pay3;
                                
                                if($coin == 'dolares'){
                                    $var3->amount = $var3->amount * $bcv;
                                }
                                $var3->rate = $bcv;
                                
                                $var3->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay3;

                                $validate_boolean3 = true;

                            
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }

                        
                    }else{
                            return redirect('invoices')->withDanger('El pago 3 debe ser distinto de Cero!');
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
                        return redirect('invoices')->withDanger('Debe ingresar un monto de pago 4!');
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
                                        return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                }
                            }
                            if($payment_type4 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days4)){

                                    $var4->credit_days = $credit_days4;

                                }else{
                                    return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                }
                            }

                        
                        

                                $var4->payment_type = request('payment_type4');
                                $var4->amount = $valor_sin_formato_amount_pay4;
                                
                                if($coin == 'dolares'){
                                    $var4->amount = $var4->amount * $bcv;
                                }
                                $var4->rate = $bcv;
                                
                                $var4->status =  1;
                            
                                $total_pay += $valor_sin_formato_amount_pay4;

                                $validate_boolean4 = true;

                            
                        }else{
                            return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }

                        
                    }else{
                            return redirect('invoices')->withDanger('El pago 4 debe ser distinto de Cero!');
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
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 5!');
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
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                            }
                        }
                        if($payment_type5 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days5)){

                                $var5->credit_days = $credit_days5;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                            }
                        }

                    
                    

                            $var5->payment_type = request('payment_type5');
                            $var5->amount = $valor_sin_formato_amount_pay5;
                            
                            if($coin == 'dolares'){
                                $var5->amount = $var5->amount * $bcv;
                            }

                            $var5->rate = $bcv;
                            
                            $var5->status =  1;
                        
                            $total_pay += $valor_sin_formato_amount_pay5;

                            $validate_boolean5 = true;

                        
                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }

                    
                }else{
                        return redirect('invoices')->withDanger('El pago 5 debe ser distinto de Cero!');
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
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 6!');
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
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                            }
                        }
                        if($payment_type6 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days6)){

                                $var6->credit_days = $credit_days6;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                            }
                        }

                    
                    

                            $var6->payment_type = request('payment_type6');
                            $var6->amount = $valor_sin_formato_amount_pay6;
                            
                            if($coin == 'dolares'){
                                $var6->amount = $var6->amount * $bcv;
                            }

                            $var6->rate = $bcv;

                            $var6->status =  1;
                        
                            $total_pay += $valor_sin_formato_amount_pay6;

                            $validate_boolean6 = true;

                        
                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }

                    
                }else{
                        return redirect('invoices')->withDanger('El pago 6 debe ser distinto de Cero!');
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
                    return redirect('invoices')->withDanger('Debe ingresar un monto de pago 7!');
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
                                    return redirect('invoices')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                            }
                        }
                        if($payment_type7 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days7)){

                                $var7->credit_days = $credit_days7;

                            }else{
                                return redirect('invoices')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('invoices')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                            }
                        }

                    
                    

                            $var7->payment_type = request('payment_type7');
                            $var7->amount = $valor_sin_formato_amount_pay7;
                            
                            if($coin == 'dolares'){
                                $var7->amount = $var7->amount * $bcv;
                            }
                            $var7->rate = $bcv;
                            
                            $var7->status =  1;
                        
                            $total_pay += $valor_sin_formato_amount_pay7;

                            $validate_boolean7 = true;

                        
                    }else{
                        return redirect('invoices')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }

                    
                }else{
                        return redirect('invoices')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 
        }


        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if($total_pay == $amount_with_iva  || ($amount_with_iva <= 0)){

            $array = $request->all();
            $id_quotation = 0;
            $facturas_a_procesar = [];

            foreach ($array as $key => $item) {
                
                if(substr($key,0, 12) == 'id_quotation'){
                    array_push($facturas_a_procesar, $item);
                    $this->procesar_quotation($item,$total_pay);
                    $id_quotation = $item;
                }
                
            }
            
            $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

            $bcv = $quotation->bcv;
            $coin = $quotation->coin;

            
            $header_voucher  = new HeaderVoucher();
            $header_voucher->setConnection(Auth::user()->database_name);


            $header_voucher->description = "MultiCobro de Bienes o servicios.";
            $header_voucher->date = $datenow;
            
        
            $header_voucher->status =  "1";
        
            $header_voucher->save();
            
            if($validate_boolean1 == true){
                $var->id_quotation = $id_quotation;
                $var->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var->id,$user_id);
                }


                $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$user_id,$var->amount,0);
                

                //LE PONEMOS STATUS C, DE COBRADO
                $quotation->status = "C";
            }
            
            if($validate_boolean2 == true){
                $var2->id_quotation = $id_quotation;
                $var2->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var2->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$user_id,$var2->amount,0);
                
            }
            
            if($validate_boolean3 == true){
                $var3->id_quotation = $id_quotation;
                $var3->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var3->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$user_id,$var3->amount,0);
            
                
            }
            if($validate_boolean4 == true){
                $var4->id_quotation = $id_quotation;
                $var4->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var4->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$user_id,$var4->amount,0);
            
            }
            if($validate_boolean5 == true){
                $var5->id_quotation = $id_quotation;
                $var5->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var5->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$user_id,$var5->amount,0);
             
            }
            if($validate_boolean6 == true){
                $var6->id_quotation = $id_quotation;
                $var6->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var6->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$user_id,$var6->amount,0);
            
            }
            if($validate_boolean7 == true){
                $var7->id_quotation = $id_quotation;
                $var7->save();

                foreach($facturas_a_procesar as $key => $id_factura){
                    $this->register_multipayment($id_factura,$header_voucher->id,$var7->id,$user_id);
                }

                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$user_id,$var7->amount,0);
            
            }
            

            if($coin != 'bolivares'){
                $anticipo =  $anticipo * $bcv;
                $total_retiene_iva = $total_retiene_iva * $bcv;
                $total_retiene_islr = $total_retiene_islr * $bcv;
              
                $amount_iva = $amount_iva * $bcv;
                $base_imponible = $base_imponible * $bcv;
                $amount = $amount * $bcv;
                $total_pay = $total_pay * $bcv;

                $grand_total = $grand_total * $bcv;
    
            }
            
            
             /*Anticipos*/
             if(isset($anticipo) && ($anticipo != 0)){
                $account_anticipo_cliente = Account::on(Auth::user()->database_name)->where('code_one',2)
                ->where('code_two',3)
                ->where('code_three',1)
                ->where('code_four',1)
                ->where('code_five',2)->first(); 

                
                
                //Si el total a pagar es negativo, quiere decir que los anticipos sobrepasan al monto total de la factura
                if($amount_with_iva  < 0){
                    $global = new GlobalController;     
                    $global->check_anticipo_multipayment($quotation,$facturas_a_procesar,$grand_total);
                    $quotation->anticipo =  $grand_total;
                    $quotation->status = "C";
                    $this->add_movement_anticipo_total($facturas_a_procesar,$bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id);
                }else{
                    $quotation->anticipo = $anticipo;
                }
                if(isset($account_anticipo_cliente)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id,$quotation->anticipo,0);
                }                           
                
             }
            /*---------- */

            if($total_retiene_iva !=0){
                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)
                                                        ->where('code_three',4)->where('code_four',1)->where('code_five',2)->first();  
            
                if(isset($account_iva_retenido)){
                    $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$user_id,$total_retiene_iva,0);
                }
            }

            
            if($total_retiene_islr !=0){
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',4)
                                                ->where('code_four',1)->where('code_five',4)->first();  

                if(isset($account_islr_pagago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$user_id,$total_retiene_islr,0);
                }
            }
            

         
            
            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first(); 
            
            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,0,$grand_total);
            }
            
            
            $global = new GlobalController;                                                
            $global->procesar_anticipos($quotation,$amount_with_iva);
            
            return redirect('invoices')->withSuccess('Facturas Guardadas con Exito!');
   
        }else{
            return redirect('invoices')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }
    }


    public function add_movement_anticipo_total($facturas_a_procesar,$bcv,$header_voucher,$id_account,$user_id)
    {
        $global = new GlobalController;   

        foreach($facturas_a_procesar as $factura){
            
            $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($factura);
            
            $payment = $global->add_payment($quotation,$id_account,3,$quotation->amount_with_iva,$bcv);

            $this->register_multipayment($factura,$header_voucher,$payment,$user_id);
        }
    }
    
    
    
    public function procesar_quotation($id_quotation,$total_pay)
    {
        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
        
        /*descontamos el inventario, si existe la fecha de nota de entrega, significa que ya hemos descontado del inventario, por ende no descontamos de nuevo*/
        if(!isset($quotation->date_delivery_note) && !isset($quotation->date_order)){
            $retorno = $this->discount_inventory($quotation->id);

            if($retorno != "exito"){
                return redirect('invoices');
            }
        }

        //Aqui pasa los quotation_products a status C de Cobrado
        DB::connection(Auth::user()->database_name)->table('quotation_products')
        ->where('id_quotation', '=', $quotation->id)
        ->update(['status' => 'C']);

        
        $quotation->status = 'C';
        $quotation->save();

        return true;
    }


   

    public function register_multipayment($id_quotation,$id_header,$id_payment,$id_user)
    {
        $multipayment = new Multipayment();
        $multipayment->setConnection(Auth::user()->database_name);
        $multipayment->id_quotation = $id_quotation;
        $multipayment->id_header = $id_header;
        $multipayment->id_payment = $id_payment;
        $multipayment->id_user = $id_user;

        $multipayment->save();
    }

    public function add_movement($bcv,$id_header,$id_account,$id_user,$debe,$haber)
    {

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);


        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;

      
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

    public function discount_inventory($id_quotation)
    {
        /*Primero Revisa que todos los productos tengan inventario suficiente*/
        $no_hay_cantidad_suficiente = DB::connection(Auth::user()->database_name)->table('inventories')
                                ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                                ->join('products', 'products.id','=','inventories.product_id')
                                ->where('quotation_products.id_quotation','=',$id_quotation)
                                ->where(function ($query){
                                    $query->where('products.type','MERCANCIA')
                                        ->orWhere('products.type','COMBO');
                                })
                                ->where('quotation_products.amount','<','inventories.amount')
                                ->select('inventories.code as code','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                                'quotation_products.amount as amount_quotation')
                                ->first(); 
    
        if(isset($no_hay_cantidad_suficiente)){
            return redirect('quotations/facturar/'.$id_quotation.'/bolivares')->withDanger('En el Inventario de Codigo: '.$no_hay_cantidad_suficiente->code.' no hay Cantidad suficiente!');
        }

        /*Luego, descuenta del Inventario*/
        $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
        ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
        ->where('quotation_products.id_quotation',$id_quotation)
        ->where(function ($query){
            $query->where('products.type','MERCANCIA')
                ->orWhere('products.type','COMBO');
        })
        ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as id_quotation','quotation_products.discount as discount',
        'quotation_products.amount as amount_quotation')
        ->get(); 

            foreach($inventories_quotations as $inventories_quotation){

                $quotation_product = QuotationProduct::on(Auth::user()->database_name)->findOrFail($inventories_quotation->id_quotation);

                if(isset($quotation_product)){
                $inventory = Inventory::on(Auth::user()->database_name)->findOrFail($quotation_product->id_inventory);

                    if(isset($inventory)){
                        //REVISO QUE SEA MAYOR EL MONTO DEL INVENTARIO Y LUEGO DESCUENTO
                        if($inventory->amount >= $quotation_product->amount){
                            $inventory->amount -= $quotation_product->amount;
                            $inventory->save();

                            //CAMBIAMOS EL ESTADO PARA SABER QUE ESE PRODUCTO YA SE COBRO Y SE RESTO DEL INVENTARIO
                            $quotation_product->status = 'C';  
                            $quotation_product->price = $inventories_quotation->price;
                            $quotation_product->save();
                        }else{
                            return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario de Codigo: '.$inventory->code.' no tiene Cantidad suficiente!');
                        }
                        
                    }else{
                        return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario no existe!');
                    }
                }else{
                return redirect('invoices/multipayment/'.$id_quotation.'/bolivares')->withDanger('El Inventario de la cotizacion no existe!');
                }

            }

            return "exito";

}

    
    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$user_id,$amount_debe,$amount_haber)
    {


        //Cuentas por Cobrar Clientes

            //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
            if(isset($id_account)){
                $this->add_movement($bcv,$header_voucher,$id_account,$user_id,$amount_debe,0);
            
            }//SIN DETERMINAR
            else if($payment_type == 7){
                        //------------------Sin Determinar
                $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Ingresos No Identificados')->first(); 
        
                if(isset($account_sin_determinar)){
                    $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$user_id,$amount_debe,0);
                }
            }//PAGO DE CONTADO
            else if($payment_type == 2){
                
                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first(); 
        
                if(isset($account_contado)){
                    $this->add_movement($bcv,$header_voucher,$account_contado->id,$user_id,$amount_debe,0);
                }
            }//CONTRA ANTICIPO
            else if($payment_type == 3){
                        //--------------
                $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first(); 
        
                if(isset($account_contra_anticipo)){
                    $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$user_id,$amount_debe,0);
                }
            } 
           

    }

    public function calcularfactura($id_quotation)
    {

        $coin = 'bolivares';
        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation)){
                                                           
            $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

            


            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                           ->where('code_two', 1)
                                           ->where('code_three', 1)
                                           ->where('code_four', 2)
                                           ->where('code_five', '<>',0)
                                           ->where('description','not like', 'Punto de Venta%')
                                           ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                           ->where('code_two', 1)
                                           ->where('code_three', 1)
                                           ->where('code_four', 1)
                                           ->where('code_five', '<>',0)
                                           ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                           ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                           ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                           ->where('quotation_products.id_quotation',$quotation->id)
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

               if(($var->type == "MERCANCIA") || ($var->type == "COMBO")){
                   $total_mercancia += ($var->price * $var->amount_quotation) - $percentage;
               }else{
                   $total_servicios += ($var->price * $var->amount_quotation) - $percentage;
               }
            }

            $quotation->total_factura = $total;
            $quotation->base_imponible = $base_imponible;
           
           /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
            $client = Client::on(Auth::user()->database_name)->find($quotation->id_client);

           
           if($client->percentage_retencion_islr != 0){
               $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
           }

           /*-------------- */
    
           
           $quotation->price_cost_total = $price_cost_total;
           $quotation->total_retiene_islr = $total_retiene_islr;
           $quotation->total_mercancia = $total_mercancia;
           $quotation->total_servicios = $total_servicios;

           return $quotation;
         
        }
    }
 



}
