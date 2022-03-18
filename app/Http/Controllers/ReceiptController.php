<?php

namespace App\Http\Controllers;
use App;
use App\Account;
use App\Anticipo;
use App\Client;
use App\Vendor;
use App\Company;
use App\Branch;
use App\Product;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Http\Controllers\Validations\FacturaValidationController;
use App\Inventory;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use App\Transport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use App\Multipayment;

class ReceiptController extends Controller
{
    public $userAccess;
    public $modulo = 'Cotizacion';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }
 
    
 
    public function index()
    {
        if($this->userAccess->validate_user_access($this->modulo)){

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
           
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                                            ->where('date_billing','<>',null)
                                            ->get();
            
    
            return view('admin.receipt.index',compact('quotations','datenow'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function createreceipt($type = null) // crando recibo
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        return view('admin.receipt.createreceipt',compact('datenow','transports','type'));
    }

    public function createreceiptclient($id_client,$type = null) // seleccionar cliente
    {
        $client = null;

            
        if(isset($id_client)){
            $client = Client::on(Auth::user()->database_name)->find($id_client);
        }
        if(isset($client)){

        /* $vendors     = Vendor::on(Auth::user()->database_name)->get();*/

            $transports     = Transport::on(Auth::user()->database_name)->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    

            
            return view('admin.receipt.createreceipt',compact('client','datenow','transports','type'));

        }else{
            return redirect('/receipt')->withDanger('El Cliente no existe');
        } 
    }





    public function createreceiptclients($id_client = null,$type = null) // generando recibo clientes
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();
        $services = Product::on(Auth::user()->database_name)
        ->where('type','=','SERVICIO')
        ->orderBy('description','desc')->get();

        if ($id_client != null) {
            $client =  Client::on(Auth::user()->database_name)->find($id_client);
            $invoices_to_pay = Quotation::on(Auth::user()->database_name)->whereIn('status',['P'])->where('id_client',$id_client)->get();
        
        } else {
            $client = null;
            $invoices_to_pay = null;
        }


        return view('admin.receipt.createreceiptclients',compact('datenow','transports','type','client','invoices_to_pay','branches','services'));
    }



    public function selectclient($type = null)
    {
        $clients     = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectclient',compact('clients','type'));
    }
    
    public function selectclientfactura($type = null)
    {
        $clients     = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectclientfactura',compact('clients','type'));
    }
    



    public function create($id_quotation,$coin,$type = null) // creando recibo de cobro agregar items
    {
        
        if($this->userAccess->validate_user_access($this->modulo)){
            $quotation = null;
                
            if(isset($id_quotation)){
                $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
            }

            if(isset($quotation) && ($quotation->status == 1)){
                //$inventories_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                ->where('quotation_products.id_quotation',$id_quotation)
                                ->whereIn('quotation_products.status',['1','C'])
                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as quotation_products_id','products.code_comercial as code','quotation_products.discount as discount',
                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva')
                                ->get(); 
            
                
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');  

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();

                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    //esto es para que siempre se pueda guardar la tasa en la base de datos
                    $bcv_quotation_product = $global->search_bcv();
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv_quotation_product = $company->rate;
                    $bcv = $company->rate;

                }
               
                if(($coin == 'bolivares') ){
                    
                    $coin = 'bolivares';
                }else{
                    //$bcv = null;

                    $coin = 'dolares';
                }
                
        
                return view('admin.receipt.create',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','type','company'));
            }else{
                return redirect('/receipt')->withDanger('No es posible ver esta cotizacion');
            } 
            
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

    }




public function store(Request $request) // Guardar recibo solo
    {
    
        $data = request()->validate([
            
        
            'id_client'         =>'required',
            'id_transport'         =>'required',
            'id_user'         =>'required',
            'date_quotation'         =>'required',
        
        ]);

        $id_client = request('id_client');
        $id_vendor = request('id_vendor');

     
        if($id_client != '-1'){
            
                $var = new Quotation();
                $var->setConnection(Auth::user()->database_name);

                $validateFactura = new FacturaValidationController($var);

                $var->id_client = $id_client;
                $var->id_vendor = $id_vendor;

                $id_transport = request('id_transport');

                $type = request('type');

                if(empty($type)){
                    $type = '';
                }else if($type == 'factura'){
                    $var->date_billing = request('date_quotation');
                    $var = $validateFactura->validateNumberInvoice();
                }

               

                if($id_transport != '-1'){
                    $var->id_transport = request('id_transport');
                }
                
                $var->id_user = request('id_user');
                $var->serie = request('serie');
                $var->date_quotation = request('date_quotation');
        
                $var->observation = request('observation');
                $var->note = request('note');

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();

                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv = $company->rate;
                }

                $var->bcv = bcdiv($bcv, '1', 2);

                $var->coin = 'bolivares';
        
                $var->status =  1;
            
                $var->save();


              /*  $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation","Creó Cotización");
              */
                return redirect('receipt/register/'.$var->id.'/bolivares/'.$type);

            
        }else{
            return redirect('/receipt/registerreceipt')->withDanger('Debe Buscar un Cliente');
        } 

        
    }



    public function storeclients(Request $request) // Generar recibo de Clientes
    {
    
        $data = request()->validate([
            
        
            'id_client'         =>'required',
            'id_invoice'        =>'required',
            'service'           =>'required'
  
        
        ]);

        $id_client = request('id_client');
        $id_invoice = request('id_invoice');
        $id_cost_center = request('id_cost_center');
        $id_service = request('service');

        $clients = Client::on(Auth::user()->database_name)
        ->where('id_cost_center','=',$id_cost_center)->get();

        //Buscar Factura original
        $quotations = Quotation::on(Auth::user()->database_name)
        ->orderBy('number_invoice' ,'desc')
        ->where('date_billing','<>',null)
        ->where('id','=',$id_invoice)
        ->select('quotations.*')
        ->get();


        //dd($quotations[0]['number_invoice']);

      /*  $quotations[0]['number_invoice'];
        $quotations[0]['number_delivery_note'];
        $quotations[0]['number_order'];
        $quotations[0]['id_client'];
        $quotations[0]['id_vendor'];
        $quotations[0]['id_transport'];
        $quotations[0]['id_user'];
        $quotations[0]['serie'];
        $quotations[0]['date_quotation'];
        $quotations[0]['date_billing'];
        $quotations[0]['date_delivery_note'];
        $quotations[0]['date_order'];
        $quotations[0]['anticipo'];
        $quotations[0]['iva_percentage'];
        $quotations[0]['observation'];
        $quotations[0]['note'];
        $quotations[0]['credit_days'];
        $quotations[0]['coin'];
        $quotations[0]['bcv'];
        $quotations[0]['retencion_iva'];
        $quotations[0]['retencion_islr'];
        $quotations[0]['base_imponible'];
        $quotations[0]['amount_exento'];
        $quotations[0]['amount'];
        $quotations[0]['amount_iva'];
        $quotations[0]['amount_with_iva'];
        $quotations[0]['status'];
        $quotations[0]['created_at'];
        $quotations[0]['updated_at'];*/
 
        
        if(!empty($quotations) & $id_client != '-1'){  
               
            
            foreach ($clients as $client) {

                $global = new GlobalController();
                
                $var = new Quotation(); //inicio factrua cabecera /////////////////////////

                $var->setConnection(Auth::user()->database_name);

                $validateFactura = new FacturaValidationController($var);

                $var->id_client = $client->id;
                //$var->id_vendor = $id_vendor;
                $id_transport = $quotations[0]['id_transport'];
                $type = 'factura';
                $var->date_billing = $quotations[0]['date_billing'];
                $var = $validateFactura->validateNumberInvoice();
                $var->id_transport = $quotations[0]['id_transport'];
                $var->number_invoice = $quotations[0]['number_invoice'];
                $var->id_user = $quotations[0]['id_user'];
                $var->serie = $quotations[0]['serie'];
                $var->date_quotation = $quotations[0]['date_quotation'];
                $var->observation = $quotations[0]['observation'];
                $var->note = 'cabecera 2';
                $var->bcv = $quotations[0]['bcv'];
                $var->coin = 'bolivares';

                
                /*$quotations[0]['base_imponible'];
                $quotations[0]['amount_exento'];
                $quotations[0]['amount'];
                $quotations[0]['amount_iva'];
                $quotations[0]['amount_with_iva'];
                */
                $var->status = $quotations[0]['status'];
            
                $var->save();
                
                $id_quotation = DB::connection(Auth::user()->database_name)
                ->table('quotations')
                ->where('number_invoice','=',$quotations[0]['number_invoice'])
                ->select('id')
                ->get()->last(); 

              /*  $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation","Creó Cotización");
              */
             
                // Guardar detalle de factura//////////////////////////////

                $quotation = new QuotationProduct(); //inicio factrua cabecera /////////////////////////

                $quotation->setConnection(Auth::user()->database_name);
                /*
                id_quotation 
                $quotation->id_inventory 
                $quotation->amount
                discount
                $quotation->price
                $quotation->rate
                retiene_iva
                retiene_islr
                status
                id_inventory_histories
                created_at
                updated_at*/
                $quotation->id_quotation = $id_quotation->id;
                $quotation->id_inventory = $id_service;
                $quotation->amount = 1;

                $montofactura = $quotations[0]['amount_with_iva'];
                $alicuota_cliente = $client->aliquot;;

                $quotation->price = ($montofactura*$alicuota_cliente)/100;
                $quotation->discount = 0;
                $quotation->retiene_iva = 0;
                $quotation->retiene_islr = 0;
                $quotation->rate = $quotations[0]['bcv'];
                $quotation->status = 'C';
                $quotation->id_inventory_histories = 0;
                $quotation->save();

            }
//////////////////////////////


            return redirect('receipt');

            
        }else{
             return redirect('/receipt/registerreceiptclients/'.$type)->withDanger('Debe Buscar un Cliente');
        } 

        
    }


    public function createreceiptfacturado($id_quotation,$coin,$reverso = null)
    {
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Quotation::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
                                 
         }
 
         if(isset($quotation)){
                // $product_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $payment_quotations = QuotationPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
     
             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');    

             if(isset($coin)){
                if($coin == 'bolivares'){
                   $bcv = null;
                }else{
                    $bcv = $quotation->bcv;
                    $quotation->anticipo = $quotation->anticipo;
                }
            }else{
               $bcv = null;
            }
             
             return view('admin.receipt.createreceiptfacturado',compact('quotation','payment_quotations', 'datenow','bcv','coin','reverso'));
            }else{
             return redirect('/receipt')->withDanger('La Recibo no existe');
         } 
         
    }



    public function storeproduct(Request $request)
    {
        
        $data = request()->validate([
            
        
            'id_quotation'         =>'required',
            'id_inventory'         =>'required',
            'amount'         =>'required',
            'discount'         =>'required',
        
        
        ]);

        
        $var = new QuotationProduct();
        $var->setConnection(Auth::user()->database_name);

        $var->id_quotation = request('id_quotation');
        
        $var->id_inventory = request('id_inventory');

        $islr = request('islr');
        if($islr == null){
            $var->retiene_islr = false;
        }else{
            $var->retiene_islr = true;
        }

        $exento = request('exento');
        if($exento == null){
            $var->retiene_iva = false;
        }else{
            $var->retiene_iva = true;
        }

        $coin = request('coin');

        $quotation = Quotation::on(Auth::user()->database_name)->find($var->id_quotation);

        $var->rate = $quotation->bcv;

        if($var->id_inventory == -1){
            return redirect('quotations/register/'.$var->id_quotation.'')->withDanger('No se encontro el producto!');
           
        }

        $amount = request('amount');
        $cost = str_replace(',', '.', str_replace('.', '',request('cost')));

        $global = new GlobalController();

        $value_return = $global->check_product($quotation->id,$var->id_inventory,$amount);

       
        if($value_return != 'exito'){
                return redirect('quotations/registerproduct/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger($value_return);
        }

        

        if($coin == 'dolares'){
            $cost_sin_formato = ($cost) * $var->rate;
        }else{
            $cost_sin_formato = $cost;
        }

        $var->price = $cost_sin_formato;
        

        $var->amount = $amount;

        $var->discount = request('discount');

        if(($var->discount < 0) || ($var->discount > 100)){
            return redirect('quotations/register/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger('El descuento debe estar entre 0% y 100%!');
        }
        
        $var->status =  1;
    
        $var->save();

        if(isset($quotation->date_delivery_note) || isset($quotation->date_billing)){
            $this->recalculateQuotation($quotation->id);
        }

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"quotation_product","Registró un Producto");

        $type_quotation = request('type_quotation');

        if(empty($type_quotation)){
            $type_quotation = '';
        }


        return redirect('quotations/register/'.$var->id_quotation.'/'.$coin.'/'.$type_quotation)->withSuccess('Producto agregado Exitosamente!');
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
 


    function imprimirFactura($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Quotation::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                                     
             }else{
                return redirect('/receipt')->withDanger('No llega el numero del recibo de cobro');
                } 
     
             if(isset($quotation)){

                $payment_quotations = QuotationPayment::on(Auth::user()->database_name)
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
                    ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                    ->where('quotation_products.id_quotation',$quotation->id)
                    ->where('quotation_products.status','C')
                    ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation'
                    ,'quotation_products.retiene_islr as retiene_islr_quotation')
                    ->get(); 


                 $client = Client::on(Auth::user()->database_name) // buscar cliente
                ->where('id','=',$quotation->id_client)
                ->get();

        
                //Buscar Factura original
                $quotationsorigin = Quotation::on(Auth::user()->database_name) // buscar facura original
                ->orderBy('id' ,'asc') 
                ->where('date_billing','<>',null)
                ->where('number_invoice','=',$quotation->number_invoice)
                ->select('quotations.*')
                ->get();

                $inventories_quotationso = DB::connection(Auth::user()->database_name)->table('products')
                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                ->where('quotation_products.id_quotation',$quotationsorigin[0]['id'])
                ->where('quotation_products.status','C')
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
                
               // $lineas_cabecera = $company->format_header_line;

                 $pdf = $pdf->loadView('pdf.receipt',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','quotationsorigin','inventories_quotationso','client'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/receipt')->withDanger('La recibo de cobro no existe');
             } 
             
        

        
    }



}
