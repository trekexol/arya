<?php

namespace App\Http\Controllers;
use App;
use App\Account;
use App\Anticipo;
use App\Client;
use App\Condominiums;
use App\Owners;
use App\Vendor;
use App\Company;
use App\Branch;
use App\Product;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\Historial\HistorialQuotationController;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Http\Controllers\Validations\ReceiptValidationController;
use App\Inventory;
use App\Receipts;
use App\ReceiptPayment;
use App\ReceiptProduct;
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

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
           
            $quotations = Receipts::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                                            ->where('date_billing','<>',null)
                                            ->where('type','=','F')
                                            ->get();
            
    
            return view('admin.receipt.index',compact('quotations','datenow'));

    }


 
    public function indexr($id_quotation = null,$check = null)
    {
      
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
            if (isset($id_quotation)){
                $verified = Receipts::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['verified' => $check]);

            }
            
            if (Auth::user()->role_id  == '11'){

                $email = Auth::user()->email;
                $id_owner = Owners::on(Auth::user()->database_name)->where('email','=',$email)->get()->first();

                $quotations = Receipts::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                                                ->where('date_billing','<>',null)
                                                ->where('receipts.id_client','=',$id_owner->id)
                                                ->where('type','=','R')
                                                ->get();
             }else{
    
                $quotations = Receipts::on(Auth::user()->database_name)
                ->orderBy('id' ,'desc')
                ->where('type','=','R')
                ->get(); 
             }
             

            return view('admin.receipt.indexr',compact('quotations','datenow'));

    }

    public function index_pen_verif($id_quotation = null,$check = null)
    {
      
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
            if (isset($id_quotation)){
                $verified = Receipts::on(Auth::user()->database_name)->where('id',$id_quotation)->update(['verified' => $check]);

            }
            
            if (Auth::user()->role_id  == '11'){

                $email = Auth::user()->email;
                $id_owner = Owners::on(Auth::user()->database_name)->where('email','=',$email)->get()->first();

                $quotations = Receipts::on(Auth::user()->database_name)->orderBy('number_invoice' ,'desc')
                                                ->where('date_billing','<>',null)
                                                ->where('receipts.id_client','=',$id_owner->id)
                                                ->where('type','=','R')
                                                ->get();
             }else{
    
                $quotations = Receipts::on(Auth::user()->database_name)
                ->orderBy('id' ,'desc')
                ->where('type','=','R')
                ->where('status','=','C')
                ->where('verified','=','0')
                ->get(); 
             }
             

            return view('admin.receipt.indexpenverif',compact('quotations','datenow'));

    }

    public function createreceipt($type = null) // crando recibo
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        return view('admin.receipt.createreceipt',compact('datenow','transports','type'));
    }
    

    public function createreceiptunique($id_client = null,$type = null,$datereceipt = null,$id_ownerreceipt=null) // seleccionar cliente condominio
    {
        $client = null;    
        $transports = Transport::on(Auth::user()->database_name)->get();
        $date = Carbon::now();  

        if (isset($datereceipt)) {
 
          $datenow = $datereceipt;
        } else {
           $datenow = $date->format('Y-m-d');          
        }
        if(isset($id_client)){

            $client = Condominiums::on(Auth::user()->database_name)->find($id_client);
        }
        
        if(isset($id_ownerreceipt)){

            $owners = Owners::on(Auth::user()->database_name)->find($id_ownerreceipt);
        } else {
            $owners = null;
        }


            return view('admin.receipt.createreceiptunique',compact('datenow','client','transports','type','owners'));      
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

    public function createreceiptcondominiums($id_client,$type = null) // seleccionar cliente condominio
    {
        $client = null;

            
        if(isset($id_client)){
            $client = Condominiums::on(Auth::user()->database_name)->find($id_client);
        }
        if(isset($client)){

    
            $transports     = Transport::on(Auth::user()->database_name)->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    

            
            return view('admin.receipt.createreceipt',compact('client','datenow','transports','type'));

        }else{
            return redirect('/receipt')->withDanger('El Condominio no existe');
        } 
    }


    public function createreceiptowners($id_client,$type = null) // seleccionar cliente propietario
    {
        $client = null;

            
        if(isset($id_client)){
            $client = Owners::on(Auth::user()->database_name)->find($id_client);
        }
        if(isset($client)){

        /* $vendors     = Vendor::on(Auth::user()->database_name)->get();*/

            $transports     = Transport::on(Auth::user()->database_name)->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    

            
            return view('admin.receipt.createreceipts',compact('client','datenow','transports','type'));

        }else{
            return redirect('receipt/receiptr')->withDanger('El Propietario no existe');
        } 
    }





    public function createreceiptclients($id_client = null,$type = null) // generando recibos pantalla crear
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();
        $services = Product::on(Auth::user()->database_name)
        ->where('type','=','SERVICIO')->get();

        if ($id_client != null) {
            $client =  Condominiums::on(Auth::user()->database_name)->find($id_client);
            $invoices_to_pay = Receipts::on(Auth::user()->database_name)->whereIn('status',['P'])->where('type','F')->where('id_client',$id_client)->get();
        
        } else {
            $client = null;
            $invoices_to_pay = null;
        }


        return view('admin.receipt.createreceiptclients',compact('datenow','transports','type','client','invoices_to_pay','branches','services'));
    }


    public function createreceiptclientsunique($id_owner = null,$type = null) // generando recibos pantalla crear
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();
        $services = Product::on(Auth::user()->database_name)
        ->where('type','=','SERVICIO')->get();

        $owner = Owners::on(Auth::user()->database_name)->find($id_owner);

        return view('admin.receipt.createreceiptclientsunique',compact('datenow','transports','type','branches','services','owner'));
    }



    public function envioreceiptclients($id_client = null,$type = null) // generando recibos pantalla crear
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();
        $services = Product::on(Auth::user()->database_name)
        ->where('type','=','SERVICIO')->get();

        if ($id_client != null) {
            $client =  Condominiums::on(Auth::user()->database_name)->find($id_client);
            $invoices_to_pay = Receipts::on(Auth::user()->database_name)->whereIn('status',['P'])->where('type','F')->where('id_client',$id_client)->get();
        
        } else {
            $client = null;
            $invoices_to_pay = null;
        }


        return view('admin.receipt.envioreceiptclients',compact('datenow','transports','type','client','invoices_to_pay','branches','services'));
    }



    public function selectclient($type = null) // clientes
    {
        $clients     = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectclient',compact('clients','type'));
    }


    public function selectcondominiums($type = null) // clientes condominios
    {
        $clients     = Condominiums::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectcondominiums',compact('clients','type'));
    }
    public function selectcondominiumsunique($type = null) // clientes condominios
    {
        $clients     = Condominiums::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectcondominiumsunique',compact('clients','type'));
    }

    public function selectcondominiumsreceipt($type = null) // clientes condominios
    {
        $clients     = Condominiums::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectcondominiumsreceipt',compact('clients','type'));
    }

    public function selectownersreceipt($type = null) // clientes propietarios
    {
        $clients     = Owners::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectownersreceipt',compact('clients','type'));
    }
    
    public function selectownersreceiptresumen($type = null) // clientes propietarios
    {
        $clients     = Owners::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectownersreceiptresumen',compact('clients','type'));
    }

    public function selectownersreceiptunique($client,$type,$datenow,$owner) //,$type = null,$datenow = null,$owners = null clientes propietario unique
    {

        $owners = Owners::on(Auth::user()->database_name)->orderBy('name','asc')->get();

        return view('admin.receipt.selectownersreceiptunique',compact('client','type','datenow','owners'));
         
    }
    
    public function selectclientfactura($type = null) // clientes a factura ??
    {
        $clients     = Condominiums::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectclientfactura',compact('clients','type'));
    }

    
    public function selectclientemail($type = null) // clientes a factura ??
    {
        $clients     = Condominiums::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.receipt.selectclientemail',compact('clients','type'));
    }
    


    public function create($id_quotation,$coin,$type = null) // crando factura de gasto
    {
        
        if($this->userAccess->validate_user_access($this->modulo)){
            $quotation = null;
                
            if(isset($id_quotation)){
                $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
            }

            if(isset($quotation) && ($quotation->status == 1)){
                //$inventories_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                ->where('receipt_products.id_quotation',$id_quotation)
                                ->whereIn('receipt_products.status',['1','C'])
                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id as quotation_products_id','products.code_comercial as code','receipt_products.discount as discount',
                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva','receipt_products.description as description_det')
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

                foreach ($inventories_quotations as $var) {
    
                    if($var->description_det != null) {
        
                        $var->description = $var->description_det; 
                    }
                    
                }
                
        
                return view('admin.receipt.create',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','type','company'));
            }else{
                return redirect('/receipt')->withDanger('No es posible ver esta cotizacion');
            } 
            
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

    }

    public function createunique($id_quotation = null,$coin = null,$type = null) // crando factura de gasto
    {

        $client = '';
        
        if($this->userAccess->validate_user_access($this->modulo)){
            $quotation = null;
                
            if(isset($id_quotation)){
                $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
           
                $client = Owners::on(Auth::user()->database_name) // buscar propietario
                ->where('id','=',$quotation->id_client)
                ->select('owners.*')
                ->get()->first();
            }

            if(isset($quotation) && ($quotation->status == 1)){
                //$inventories_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                ->where('receipt_products.id_quotation',$id_quotation)
                                ->whereIn('receipt_products.status',['1','C'])
                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id as quotation_products_id','products.code_comercial as code','receipt_products.discount as discount',
                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva','receipt_products.description as description_det')
                                ->get(); 
          

                foreach ($inventories_quotations as $var) {
    
                    if($var->description_det != null) {
        
                        $var->description = $var->description_det; 
                    }
                    
                }
                
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
                
        
                return view('admin.receipt.createunique',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','type','company','client'));
            }else{
                return redirect('/receipt')->withDanger('No es posible ver este Recibo');
            } 
            
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

    }


public function store(Request $request) // Empezar a Crear Factura 
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
            
                $var = new Receipts();
                $var->setConnection(Auth::user()->database_name);

                $validateFactura = new ReceiptValidationController($var);

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
                $var->type =  'F';
            
                $var->save();


              /*  $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation","Creó Cotización");
              */
                return redirect('receipt/register/'.$var->id.'/bolivares/'.$type);

            
        }else{
            return redirect('/receipt/registerreceipt')->withDanger('Debe Buscar un Condominio');
        } 

        
    }

    public function storeunique(Request $request) // Empezar a Crear Factura 
    {
       

        $data = request()->validate([
            
        
            'id_client'         =>'required',
            'id_transport'         =>'required',
            'id_user'         =>'required',
            'date_quotation'         =>'required',
        
        ]);

        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $id_owner = request('id_owner');

     
        if($id_owner != '-1'){
            
                $var = new Receipts();
                $var->setConnection(Auth::user()->database_name);
                $type = 'R';
                //$validateFactura = new ReceiptValidationController($var);

                $var->id_client = $id_owner;
                $id_transport = request('id_transport');

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
                $var->type =  $type;
            
                $var->save();


              /*  $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation","Creó Cotización");
              */
                return redirect('receipt/registerunique/'.$var->id.'/bolivares/'.$type);

            
        }else{
            return redirect('/receipt/registerreceipt')->withDanger('Debe Agregar un Propietario');
        } 

        
    }

   


    public function createreceiptfacturado($id_quotation,$coin,$reverso = null)
    {
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
                                 
         }
 
         if ($quotation->type == 'F'){
            $client = Condominiums::on(Auth::user()->database_name) // buscar condominio
            ->where('id','=',$quotation->id_client)
            ->select('condominiums.*')
            ->get()->first();
            
         } else {
            $client = Owners::on(Auth::user()->database_name) // buscar propietario
            ->where('id','=',$quotation->id_client)
            ->select('owners.*')
            ->get()->first();
         }
        
        
         if(isset($quotation)){
                $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
     
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
             
             return view('admin.receipt.createreceiptfacturado',compact('quotation','payment_quotations', 'datenow','bcv','coin','reverso','client'));
        
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

        
        $var = new ReceiptProduct();
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

        $quotation = Receipts::on(Auth::user()->database_name)->find($var->id_quotation);

        $var->rate = $quotation->bcv;

        if($var->id_inventory == -1){
            return redirect('receipt/register/'.$var->id_quotation.'')->withDanger('No se encontro el producto!');
           
        }

        $amount = request('amount');
        $cost = str_replace(',', '.', str_replace('.', '',request('cost')));

        $global = new GlobalController();

        $value_return = $global->check_product($quotation->id,$var->id_inventory,$amount);

       
        if($value_return != 'exito'){
                return redirect('receipt/registerproduct/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger($value_return);
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
            return redirect('receipt/register/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger('El descuento debe estar entre 0% y 100%!');
        }
        
        $var->status =  1;
    
        $var->save();

        if(isset($quotation->date_delivery_note) || isset($quotation->date_billing)){
            $this->recalculateQuotation($quotation->id);
        }
        /*
        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"receipt_product","Registró un Producto");
        */
        $type_quotation = request('type_quotation');

        if(empty($type_quotation)){
            $type_quotation = '';
        }


        return redirect('receipt/register/'.$var->id_quotation.'/'.$coin.'/'.$type_quotation)->withSuccess('Producto agregado Exitosamente!');
    }

    public function storeproductunique(Request $request)
    {
        
        $data = request()->validate([
            
        
            'id_quotation'         =>'required',
            'id_inventory'         =>'required',
            'amount'         =>'required',
            'discount'         =>'required',
        
        
        ]);

        
        $var = new ReceiptProduct();
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

        $quotation = Receipts::on(Auth::user()->database_name)->find($var->id_quotation);

        $var->rate = $quotation->bcv;

        if($var->id_inventory == -1){
            return redirect('receipt/registerunique/'.$var->id_quotation.'')->withDanger('No se encontro el producto!');
           
        }

        $amount = request('amount');
        $cost = str_replace(',', '.', str_replace('.', '',request('cost')));

        $global = new GlobalController();

        $value_return = $global->check_product($quotation->id,$var->id_inventory,$amount);

       
        if($value_return != 'exito'){
                return redirect('receipt/registerproductunique/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger($value_return);
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
            return redirect('receipt/registerunique/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger('El descuento debe estar entre 0% y 100%!');
        }
        
        $var->status =  1;
    
        $var->save();

        if(isset($quotation->date_delivery_note) || isset($quotation->date_billing)){
            $this->recalculateQuotation($quotation->id);
        }
        /*
        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"receipt_product","Registró un Producto");
        */
        $type_quotation = request('type_quotation');

        if(empty($type_quotation)){
            $type_quotation = '';
        }

        return redirect('receipt/registerunique/'.$var->id_quotation.'/'.$coin.'/'.$type_quotation)->withSuccess('Producto agregado Exitosamente!');
    }
   
    public function multipayment(Request $request)
    {
        $quotation = null;

        //Recorre el request y almacena los valores despues del segundo valor que le llegue, asi guarda los id de las facturas a procesar
        $array = $request->all();
        $count = 0;
        $facturas_a_procesar = [];

        

        $total_facturas = new Receipts();
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
            return redirect('receipt')->withDanger('Debe seleccionar facturar para Pagar!');
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

                $var = new ReceiptPayment();
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

                $var2 = new ReceiptPayment();
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

                    $var3 = new ReceiptPayment();
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

                    $var4 = new ReceiptPayment();
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

                $var5 = new ReceiptPayment();
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

                $var6 = new ReceiptPayment();
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

                $var7 = new ReceiptPayment();
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
            
            $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);

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
            
            $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($factura);
            
            $payment = $global->add_payment($quotation,$id_account,3,$quotation->amount_with_iva,$bcv);

            $this->register_multipayment($factura,$header_voucher,$payment,$user_id);
        }
    }
    
    
    
    public function procesar_quotation($id_quotation,$total_pay)
    {
        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);
        
        /*descontamos el inventario, si existe la fecha de nota de entrega, significa que ya hemos descontado del inventario, por ende no descontamos de nuevo*/
        if(!isset($quotation->date_delivery_note) && !isset($quotation->date_order)){
            $retorno = $this->discount_inventory($quotation->id);

            if($retorno != "exito"){
                return redirect('invoices');
            }
        }

        //Aqui pasa los quotation_products a status C de Cobrado
        DB::connection(Auth::user()->database_name)->table('receipt_products')
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

    public function add_movement($bcv,$id_header,$id_account,$id_user,$debe,$haber,$id_quotation = null)
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
                                ->join('receipt_products', 'receipt_products.id_inventory','=','inventories.id')
                                ->join('products', 'products.id','=','inventories.product_id')
                                ->where('receipt_products.id_quotation','=',$id_quotation)
                                ->where(function ($query){
                                    $query->where('products.type','MERCANCIA')
                                        ->orWhere('products.type','COMBO');
                                })
                                ->where('receipt_products.amount','<','inventories.amount')
                                ->select('inventories.code as code','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id_quotation as id_quotation','receipt_products.discount as discount',
                                'receipt_products.amount as amount_quotation')
                                ->first(); 
    
        if(isset($no_hay_cantidad_suficiente)){
            return redirect('receipt/facturar/'.$id_quotation.'/bolivares')->withDanger('En el Inventario de Codigo: '.$no_hay_cantidad_suficiente->code.' no hay Cantidad suficiente!');
        }

        /*Luego, descuenta del Inventario*/
        $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
        ->join('receipt_products', 'inventories.id', '=', 'receipt_products.id_inventory')
        ->where('receipt_products.id_quotation',$id_quotation)
        ->where(function ($query){
            $query->where('products.type','MERCANCIA')
                ->orWhere('products.type','COMBO');
        })
        ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id as id_quotation','receipt_products.discount as discount',
        'receipt_products.amount as amount_quotation')
        ->get(); 

            foreach($inventories_quotations as $inventories_quotation){

                $quotation_product = ReceiptProduct::on(Auth::user()->database_name)->findOrFail($inventories_quotation->id_quotation);

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
            $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation)){
                                                           
            $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

            


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
                                                           ->join('receipt_products', 'inventories.id', '=', 'receipt_products.id_inventory')
                                                           ->where('receipt_products.id_quotation',$quotation->id)
                                                           ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                           'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                           ,'receipt_products.retiene_islr as retiene_islr_quotation')
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
            $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

           
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
 

    function imprimirecibo($id_quotation,$coin = null) /// recibo de condominio
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                                     
             }else{
                return redirect('/receipt/receipt')->withDanger('No llega el numero del recibo de condominio');
                } 
     
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


                 $client = owners::on(Auth::user()->database_name) // buscar propietario
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
                ,'receipt_products.retiene_islr as retiene_islr_quotation','receipt_products.description as description_det')
                ->get();

                

                foreach ($inventories_quotationso as $varo) {
    
                    if($varo->description_det != null) {
        
                        $varo->description = $varo->description_det; 
                    }
                    
                }              
                
                //Buscar recibos que debe
                $quotationp = Receipts::on(Auth::user()->database_name) // buscar recibo original
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
                        ->where('receipt_products.status','!=','X')
                        ->select('products.*','receipt_products.id_quotation as id_quotation','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                        'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                        ,'receipt_products.retiene_islr as retiene_islr_quotation', )
                        ->get();

                    }


                    if(!isset($inventories_quotationsp)){
                       /*   foreach ($inventories_quotationsp as $varp) {
                          $quotationpn = Receipts::on(Auth::user()->database_name) // buscar facura original
                            ->orderBy('id' ,'asc') 
                            ->where('date_billing','<>',null)
                            ->where('type','=','F')
                            ->where('status','=','P')
                            ->where('receipts.id_client','=',$client)
                            ->select('number_delivery_note','date_billing')
                            ->get()->first();
                            
                             if (isset($quotationpn)) {
                             $varp->number_delivery_note = $quotationpn->number_delivery_note;
                             $varp->date_billing = $quotationpn->date_billing;
                             } else {
                                $varp->number_delivery_note = NULL;
                                $varp->date_billing = NULL;                               
                             }
                        }*/
                   // } else {
                        $inventories_quotationsp = 0;
                    }


                } else {
                      
                    $inventories_quotationsp = 0;


                }



                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                $pdf = $pdf->loadView('pdf.receipt',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','quotationsorigin','inventories_quotationso','client','quotationp','inventories_quotationsp'));
                return $pdf->stream();
         
                }else{
                 return redirect('/receipt/receipt')->withDanger('La recibo de condominio no existe');
             } 
             
        

        
    }
    function imprimirecibounique($id_quotation,$coin = null) /// recibo de condominio
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                                     
             }else{
                return redirect('/receipt/receiptunique')->withDanger('No llega el numero del recibo de condominio');
                } 
     
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
                $quotationsorigin = '';

                $inventories_quotationso = '';
                
                
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
                            ->where('type','=','F')
                            ->where('status','=','P')
                            ->where('receipts.id_client','=',$client)
                            ->select('number_delivery_note','date_billing')
                            ->get()->first();
                            
                             if (isset($quotationpn)) {
                             $varp->number_delivery_note = $quotationpn->number_delivery_note;
                             $varp->date_billing = $quotationpn->date_billing;
                             } else {
                                $varp->number_delivery_note = NULL;
                                $varp->date_billing = NULL;                               
                             }
                        }
                    } else {
                        $inventories_quotationsp = 0;
                    }


                } else {
                      
                    $inventories_quotationsp = 0;


                }



                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                $pdf = $pdf->loadView('pdf.receiptunique',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','quotationsorigin','inventories_quotationso','client','quotationp','inventories_quotationsp'));
                return $pdf->stream();
         
                }else{
                 return redirect('/receipt/receiptunique')->withDanger('La recibo de condominio no existe');
             } 
             
        

        
    }

    public function storefacturacredit(Request $request)
    {
        
        $id_quotation = request('id_quotation');

        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);
        $quotation->coin = request('coin');
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

        $sin_formato_grand_total = str_replace(',', '.', str_replace('.', '', request('grand_total')));
        

        $total_mercancia = request('total_mercancia_credit');
        $total_servicios = request('total_servicios_credit');

        if($quotation->coin != 'bolivares'){
            $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
            $sin_formato_amount_with_iva = $sin_formato_amount_with_iva * $bcv;
            $sin_formato_base_imponible = $sin_formato_base_imponible * $bcv;
            $sin_formato_amount = $sin_formato_amount * $bcv;
       
            $total_retiene_iva = $total_retiene_iva * $bcv;
            $total_retiene_islr = $total_retiene_islr * $bcv;
            $anticipo = $anticipo * $bcv;

            $sin_formato_grand_total = $sin_formato_grand_total * $bcv;
        }

       
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        $quotation->date_billing = request('date-begin-form');

        $quotation->retencion_iva =  $total_retiene_iva;
        $quotation->retencion_islr =  $total_retiene_islr;
        $quotation->anticipo =  $anticipo;

        $quotation->base_imponible = $sin_formato_base_imponible;
        $quotation->amount_exento =  $amount_exento;
        $quotation->amount =  $sin_formato_amount;
        $quotation->amount_iva =  $sin_formato_amount_iva;
        $quotation->amount_with_iva = $sin_formato_grand_total;
        
        $credit = request('credit');
        
        $user_id = request('user_id');

        $quotation->iva_percentage = request('iva');

        $quotation->credit_days = $credit;

        //P de por pagar
        $quotation->status = 'P';

        $last_number = Receipts::on(Auth::user()->database_name)
        ->where('number_invoice','<>',NULL)->where('type','=','F')->orderBy('number_invoice','desc')->first();
 
        //Asigno un numero incrementando en 1
        if(empty($quotation->number_invoice)){
            if(isset($last_number)){
                $quotation->number_invoice = $last_number->number_invoice + 1;
            }else{
                $quotation->number_invoice = 1;
            }
        }

        $quotation->save();

        $date_payment = request('date-payment');

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Ventas de Bienes o servicios.";
        $header_voucher->date = $date_payment;
            
        $header_voucher->status =  "P";
    
        $header_voucher->save();

        DB::connection(Auth::user()->database_name)->table('receipt_products')
                ->where('id_quotation', '=', $quotation->id)
                ->where('status', '!=', 'X')
                ->update(['status' => 'C']);

                if(!isset($quotation->number_delivery_note)){
                    $quotation->number_delivery_note = 0;    
                } else {

                    if(empty($quotation->number_delivery_note) || $quotation->number_delivery_note == null) {
                        $quotation->number_delivery_note = 0;
                    }
                }
             
        $global = new GlobalController; 
        
        $quotation_products = DB::connection(Auth::user()->database_name)->table('receipt_products')
        ->where('id_quotation', '=', $quotation->id)->get(); // Conteo de Productos para incluiro en el historial de inventario

        foreach($quotation_products as $det_products){ // guardado historial de inventario
            
        $global->transaction_inv('venta',$det_products->id_inventory,'venta_n',$det_products->amount,$det_products->price,$quotation->date_billing,1,1,$quotation->number_delivery_note,$det_products->id_inventory_histories,$det_products->id,$quotation->id);
        
        }  
       
        /*Busqueda de Cuentas*/

        //Cuentas por Cobrar Clientes

        $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();  
    
        if(isset($account_cuentas_por_cobrar)){
            $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,$sin_formato_grand_total,0,$quotation->id);
        }          


        if($total_mercancia != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_mercancia,$quotation->id);
            }
        }
        
        if($total_servicios != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_servicios,$quotation->id);
            }
        }

        //Debito Fiscal IVA por Pagar

        $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();
        
        if($sin_formato_amount_iva != 0){
           
            if(isset($account_debito_iva_fiscal)){
                $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$user_id,0,$sin_formato_amount_iva,$quotation->id);
            }
        }

       
        
        /*$historial_quotation = new HistorialQuotationController();
        $historial_quotation->registerAction($quotation,"quotation","Cotizactión convertida a Factura a Crédito");
        */  
       
       
    
        return redirect('receipt/facturado/'.$quotation->id.'/'.$quotation->coin.'')->withSuccess('Relación de Gasto Guardada con Exito!');
    }
    
    public function storefacturacreditunique(Request $request)
    {
        
        
        $id_quotation = request('id_quotation');

        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);
        $quotation->coin = request('coin');
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

        $sin_formato_grand_total = str_replace(',', '.', str_replace('.', '', request('grand_total')));
        

        $total_mercancia = request('total_mercancia_credit');
        $total_servicios = request('total_servicios_credit');

        if($quotation->coin != 'bolivares'){
            $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
            $sin_formato_amount_with_iva = $sin_formato_amount_with_iva * $bcv;
            $sin_formato_base_imponible = $sin_formato_base_imponible * $bcv;
            $sin_formato_amount = $sin_formato_amount * $bcv;
       
            $total_retiene_iva = $total_retiene_iva * $bcv;
            $total_retiene_islr = $total_retiene_islr * $bcv;
            $anticipo = $anticipo * $bcv;

            $sin_formato_grand_total = $sin_formato_grand_total * $bcv;
        }

       
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        $quotation->date_billing = request('date-begin-form');

        $quotation->retencion_iva =  $total_retiene_iva;
        $quotation->retencion_islr =  $total_retiene_islr;
        $quotation->anticipo =  $anticipo;

        $quotation->base_imponible = $sin_formato_base_imponible;
        $quotation->amount_exento =  $amount_exento;
        $quotation->amount =  $sin_formato_amount;
        $quotation->amount_iva =  $sin_formato_amount_iva;
        $quotation->amount_with_iva = $sin_formato_grand_total;
        
        $credit = request('credit');
        
        $user_id = request('user_id');

        $quotation->iva_percentage = request('iva');

        $quotation->credit_days = $credit;

        //P de por pagar
        $quotation->status = 'P';

       
        $last_number = Receipts::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->where('type','=','R')->orderBy('number_delivery_note','desc')->first();
  
        //Asigno un numero incrementando en 1
        if(empty($quotation->number_delivery_note)){
            if(isset($last_number)){
                $quotation->number_delivery_note = $last_number->number_delivery_note + 1;
            }else{
                $quotation->number_delivery_note = 1;
            }
        }


        $quotation->save();

        $date_payment = request('date-payment');

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Ventas de Bienes o servicios.";
        $header_voucher->date = $date_payment;
            
        $header_voucher->status =  "P";
    
        $header_voucher->save();

        DB::connection(Auth::user()->database_name)->table('receipt_products')
                ->where('id_quotation', '=', $quotation->id)
                ->where('status', '!=', 'X')
                ->update(['status' => 'C']);

                if(!isset($quotation->number_delivery_note)){
                    $quotation->number_delivery_note = 0;    
                } else {

                    if(empty($quotation->number_delivery_note) || $quotation->number_delivery_note == null) {
                        $quotation->number_delivery_note = 0;
                    }
                }
             
        $global = new GlobalController; 
        
        $quotation_products = DB::connection(Auth::user()->database_name)->table('receipt_products')
        ->where('id_quotation', '=', $quotation->id)->get(); // Conteo de Productos para incluiro en el historial de inventario

        foreach($quotation_products as $det_products){ // guardado historial de inventario
            
        $global->transaction_inv('venta',$det_products->id_inventory,'venta_n',$det_products->amount,$det_products->price,$quotation->date_billing,1,1,$quotation->number_delivery_note,$det_products->id_inventory_histories,$det_products->id,$quotation->id);
        
        }  
       
        //Busqueda de Cuentas

        //Cuentas por Cobrar Clientes

        $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();  
    
        if(isset($account_cuentas_por_cobrar)){
            $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,$sin_formato_grand_total,0,$quotation->id);
        }          


        if($total_mercancia != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_mercancia,$quotation->id);
            }
        }
        
        if($total_servicios != 0){
            $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

            if(isset($account_subsegmento)){
                $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_servicios,$quotation->id);
            }
        }

        //Debito Fiscal IVA por Pagar

        $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();
        
        if($sin_formato_amount_iva != 0){
           
            if(isset($account_debito_iva_fiscal)){
                $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$user_id,0,$sin_formato_amount_iva,$quotation->id);
            }
        }
    
        return redirect('receipt/facturadounique/'.$quotation->id.'/'.$quotation->coin.'')->withSuccess('Recibo Guardado con Exito!');
        
    }



    public function storefactura(Request $request)
    {
        
        
        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail(request('id_quotation'));

        $quotation_status = $quotation->status;

       

        if($quotation->status == 'C' ){
            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Esta Operación fue procesada!');
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

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }
        
        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($sin_formato_total_pay > 0){
            $payment_type = request('payment_type');
            if($come_pay >= 1){

                /*-------------PAGO NUMERO 1----------------------*/

                $var = new ReceiptPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');
        
                if(isset($amount_pay)){
                    
                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 1!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
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
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }
        
                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){
        
                                $var->id_account = $account_efectivo;
        
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }
        
                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
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
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }
        
                    
                }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }   
            $payment_type2 = request('payment_type2');
            if($come_pay >= 2){

                /*-------------PAGO NUMERO 2----------------------*/

                $var2 = new ReceiptPayment();
                $var2->setConnection(Auth::user()->database_name);

                $amount_pay2 = request('amount_pay2');

                if(isset($amount_pay2)){
                    
                    $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 2!');
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
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
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
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
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
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }

                
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                }
                /*--------------------------------------------*/
            } 
            $payment_type3 = request('payment_type3');   
            if($come_pay >= 3){

                    /*-------------PAGO NUMERO 3----------------------*/

                    $var3 = new ReceiptPayment();
                    $var3->setConnection(Auth::user()->database_name);

                    $amount_pay3 = request('amount_pay3');

                    if(isset($amount_pay3)){
                        
                        $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                    }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 3!');
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
                                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
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
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }

                        
                    }else{
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type4 = request('payment_type4');
            if($come_pay >= 4){

                    /*-------------PAGO NUMERO 4----------------------*/

                    $var4 = new ReceiptPayment();
                    $var4->setConnection(Auth::user()->database_name);

                    $amount_pay4 = request('amount_pay4');

                    if(isset($amount_pay4)){
                        
                        $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                    }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 4!');
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
                                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
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
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }

                        
                    }else{
                            return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            } 
            $payment_type5 = request('payment_type5');
            if($come_pay >= 5){

                /*-------------PAGO NUMERO 5----------------------*/

                $var5 = new ReceiptPayment();
                $var5->setConnection(Auth::user()->database_name);

                $amount_pay5 = request('amount_pay5');

                if(isset($amount_pay5)){
                    
                    $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 5!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
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
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
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
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }

                    
                }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 
            $payment_type6 = request('payment_type6');
            if($come_pay >= 6){

                /*-------------PAGO NUMERO 6----------------------*/

                $var6 = new ReceiptPayment();
                $var6->setConnection(Auth::user()->database_name);

                $amount_pay6 = request('amount_pay6');

                if(isset($amount_pay6)){
                    
                    $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 6!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
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
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
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
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }

                    
                }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 
            $payment_type7 = request('payment_type7');
            if($come_pay >= 7){

                /*-------------PAGO NUMERO 7----------------------*/

                $var7 = new ReceiptPayment();
                $var7->setConnection(Auth::user()->database_name);

                $amount_pay7 = request('amount_pay7');

                if(isset($amount_pay7)){
                    
                    $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                }else{
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 7!');
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
                                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
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
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
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
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }

                    
                }else{
                        return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 

        }
        
        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if(($total_pay == $sin_formato_total_pay) || ($sin_formato_total_pay <= 0))
        {
            $global = new GlobalController();

            $comboController = new ComboController();

            if(empty($quotation->date_billing) && empty($quotation->date_delivery_note) && empty($quotation->date_order)){

                //$value_return_combo = $comboController->validate_combo_discount($quotation->id);
                $value_return_all = $global->check_all_products_after_facturar($quotation->id);

                if($value_return_all != "exito"){
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($value_return_all);
                }

                $retorno = $global->discount_inventory($quotation->id);
            
                if($retorno != "exito"){
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($retorno);
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
            
                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation_payment","Registro de Pago");
                      */  
                //LE PONEMOS STATUS C, DE COBRADO
                $quotation->status = "C";
            }
            
            if($validate_boolean2 == true){
                $var2->created_at = $date_payment;
                $var2->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var2,"quotation_payment","Registro de Pago");
                 */
                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$quotation->id,$user_id,$var2->amount,0);
                
            }
            
            if($validate_boolean3 == true){
                $var3->created_at = $date_payment;
                $var3->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var3,"quotation_payment","Registro de Pago");
                */
                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$quotation->id,$user_id,$var3->amount,0);
            
                
            }
            if($validate_boolean4 == true){
                $var4->created_at = $date_payment;
                $var4->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var4,"quotation_payment","Registro de Pago");
                */
                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$quotation->id,$user_id,$var4->amount,0);
            
            }
            if($validate_boolean5 == true){
                $var5->created_at = $date_payment;
                $var5->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var5,"quotation_payment","Registro de Pago");
                */
                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$quotation->id,$user_id,$var5->amount,0);
             
            }
            if($validate_boolean6 == true){
                $var6->created_at = $date_payment;
                $var6->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var6,"quotation_payment","Registro de Pago");
                 */
                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$quotation->id,$user_id,$var6->amount,0);
            
            }
            if($validate_boolean7 == true){
                $var7->created_at = $date_payment;
                $var7->save();

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var7,"quotation_payment","Registro de Pago");
                */
                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$quotation->id,$user_id,$var7->amount,0);
            
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
    
            }


            /*Anticipos*/
            
            if(isset($anticipo) && ($anticipo != 0)){
                $account_anticipo_cliente = Account::on(Auth::user()->database_name)->where('code_one',2)
                                                        ->where('code_two',3)
                                                        ->where('code_three',1)
                                                        ->where('code_four',1)
                                                        ->where('code_five',2)->first(); 
                //Si el total a pagar es negativo, quiere decir que los anticipos sobrepasan al monto total de la factura
                if($sin_formato_total_pay < 0){
                    $this->check_anticipo($quotation,$sin_formato_grandtotal);
                    $quotation->anticipo =  $sin_formato_grandtotal;
                    $quotation->status = "C";
                   
                }else{
                    $quotation->anticipo =  $anticipo;
                    $global->associate_anticipos_quotation($quotation);
                    $quotation->status = "C";
                }
                
                if(isset($account_anticipo_cliente)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id,$quotation->anticipo,0,$quotation->id);
                    $global->add_payment($quotation,$account_anticipo_cliente->id,3,$quotation->anticipo,$bcv);
                }
             }else{
                 $quotation->anticipo = 0;
             }
            /*---------- */

            if($retencion_iva !=0){
                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)
                                                        ->where('code_three',4)->where('code_four',1)->where('code_five',2)->first();  
            
                if(isset($account_iva_retenido)){
                    $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$user_id,$retencion_iva,0,$quotation->id);
                }
            }

            
            if($retencion_islr !=0){
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',4)
                                                ->where('code_four',1)->where('code_five',4)->first();  

                if(isset($account_islr_pagago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$user_id,$retencion_islr,0,$quotation->id);
                }
            }
            

         
            
            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first(); 
            
            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,0,$sin_formato_grandtotal,$quotation->id);
            }
            
            
            if(($quotation_status != 'C') && ($quotation_status != 'P')){

                if(empty($quotation->number_invoice))
                {   //Me busco el ultimo numero en notas de entrega
                    $last_number = Receipts::on(Auth::user()->database_name)->where('number_invoice','<>',NULL)->orderBy('number_invoice','desc')->first();

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
                ->where('id_quotation', '=', $quotation->id)->get();
        
                foreach($quotation_products as $det_products){
    
                $global->transaction_inv('venta',$det_products->id_inventory,'venta',$det_products->amount,$det_products->price,$date,1,1,0,$det_products->id_inventory_histories,$det_products->id,$quotation->id);
        
                }  
        

            }

            /*Modifica la factura*/
 
            $quotation->date_billing = request('date-begin-form2');
            $quotation->base_imponible = $base_imponible;
            $quotation->amount_exento =  $amount_exento;
            $quotation->amount =  $sin_formato_amount;
            $quotation->amount_iva =  $sin_formato_amount_iva;
            $quotation->amount_with_iva = $sin_formato_grandtotal;
            $quotation->iva_percentage = $iva_percentage;
            $quotation->retencion_iva = $retencion_iva;
            $quotation->retencion_islr = $retencion_islr;
            $quotation->status = "C";
            
            $quotation->save();

            /*---------------------- */

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   

            if(($quotation_status != 'C') && ($quotation_status != 'P')){

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);

                $header_voucher->description = "Ventas de Bienes o servicios.";
                $header_voucher->date = $date_payment;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();

                /*Busqueda de Cuentas*/

                //Cuentas por Cobrar Clientes

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();  
            
                

                dd($sin_formato_grandtotal);
                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,$sin_formato_grandtotal,0,$quotation->id);
                }

                //Ingresos por SubSegmento de Bienes

                if($total_mercancia != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_mercancia,$quotation->id);
                    }
                }
                
                if($total_servicios != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_servicios,$quotation->id);
                    }
                }

                //Debito Fiscal IVA por Pagar

                $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();
                
                if($base_imponible != 0){
                    $total_iva = ($base_imponible * $iva_percentage)/100;

                    if(isset($account_cuentas_por_cobrar)){
                        $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$user_id,0,$total_iva,$quotation->id);
                    }
                }
                
                //Mercancia para la Venta
               /* $validation_factura = new FacturaValidationController($quotation);

                $return_validation_factura = $validation_factura->validate_movement_mercancia();*/

                  /*
                if(empty($quotation->date_delivery_note)){
                    if($price_cost_total != 0){
                      
                        //BUSCA EL TOTAL DEL COSTO DE MERCANCIA POR PRODUCTO
                        $facturaCalculation = new FacturaCalculationController($quotation);

                        $accounts_for_movements = $facturaCalculation->calculateTotalForAccount($quotation->id);
                         
                        $account_costo_mercancia = Account::on(Auth::user()->database_name)->where('description', 'like', 'Costo de Mercancía')->first();


                        foreach($accounts_for_movements as $movement){

                            $movement->total = $movement->total * $quotation->bcv;

                            if(isset($account_cuentas_por_cobrar)){
                                $this->add_movement($bcv,$header_voucher->id,$movement->id_account,$quotation->id,$user_id,0,$movement->total);
                            }

                            //Costo de Mercancia
                            if(isset($account_cuentas_por_cobrar)){
                                $this->add_movement($bcv,$header_voucher->id,$account_costo_mercancia->id,$quotation->id,$user_id,$movement->total,0);
                            }
                        }
                      
                        

                        
                    }
                } */
                /*----------- */
            }

            
            $global = new GlobalController;                                                
        
            //Aqui pasa los quotation_products a status C de Cobrado
           /* DB::connection(Auth::user()->database_name)->table('quotation_products')
                                                        ->where('id_quotation', '=', $quotation->id)
                                                        ->update(['status' => 'C']);*/
    
            $global->procesar_anticipos($quotation,$sin_formato_total_pay);
            
            /*------------------------------------------------- */

           /* $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($quotation,"quotation","Registro de Factura Realizada");
            */
          
          
             return redirect('receipt/facturado/'.$quotation->id.'/'.$coin.'')->withSuccess('Factura Guardada con Exito!');

           
        }else{
            return redirect('receipt/facturar/'.$quotation->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }

        
        }
        
    }

    public function storefacturaunique(Request $request)
    {

        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail(request('id_quotation'));

        $quotation_status = $quotation->status;

       

        if($quotation->status == 'C' ){
            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Esta Operación fue procesada!');
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

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }
        
        //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
        if($sin_formato_total_pay > 0){
            $payment_type = request('payment_type');
            if($come_pay >= 1){

                /*-------------PAGO NUMERO 1----------------------*/

                $var = new ReceiptPayment();
                $var->setConnection(Auth::user()->database_name);

                $amount_pay = request('amount_pay');
        
                if(isset($amount_pay)){
                    
                    $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 1!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                }
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
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
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                            }
                        }
        
                        if($payment_type == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo != 0)){
        
                                $var->id_account = $account_efectivo;
        
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                            }
                        }
        
                        if($payment_type == 9 || $payment_type == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta != 0)){
                                $var->id_account = $account_punto_de_venta;
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
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
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                    }
        
                    
                }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            }   
            $payment_type2 = request('payment_type2');
            if($come_pay >= 2){

                /*-------------PAGO NUMERO 2----------------------*/

                $var2 = new ReceiptPayment();
                $var2->setConnection(Auth::user()->database_name);

                $amount_pay2 = request('amount_pay2');

                if(isset($amount_pay2)){
                    
                    $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 2!');
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
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                            }
                        }else{
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
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
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 6){
                        //DIAS DE CREDITO
                        if(($account_efectivo2 != 0)){

                            $var2->id_account = $account_efectivo2;

                        }else{
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                        }
                    }

                    if($payment_type2 == 9 || $payment_type2 == 10){
                            //CUENTAS PUNTO DE VENTA
                        if(($account_punto_de_venta2 != 0)){
                            $var2->id_account = $account_punto_de_venta2;
                        }else{
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
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
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                }

                
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                }
                /*--------------------------------------------*/
            } 
            $payment_type3 = request('payment_type3');   
            if($come_pay >= 3){

                    /*-------------PAGO NUMERO 3----------------------*/

                    $var3 = new ReceiptPayment();
                    $var3->setConnection(Auth::user()->database_name);

                    $amount_pay3 = request('amount_pay3');

                    if(isset($amount_pay3)){
                        
                        $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                    }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 3!');
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
                                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                    }
                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo3 != 0)){

                                    $var3->id_account = $account_efectivo3;

                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                }
                            }

                            if($payment_type3 == 9 || $payment_type3 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta3 != 0)){
                                    $var3->id_account = $account_punto_de_venta3;
                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
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
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                        }

                        
                    }else{
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            }
            $payment_type4 = request('payment_type4');
            if($come_pay >= 4){

                    /*-------------PAGO NUMERO 4----------------------*/

                    $var4 = new ReceiptPayment();
                    $var4->setConnection(Auth::user()->database_name);

                    $amount_pay4 = request('amount_pay4');

                    if(isset($amount_pay4)){
                        
                        $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                    }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 4!');
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
                                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                    }
                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo4 != 0)){

                                    $var4->id_account = $account_efectivo4;

                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                }
                            }

                            if($payment_type4 == 9 || $payment_type4 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta4 != 0)){
                                    $var4->id_account = $account_punto_de_venta4;
                                }else{
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
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
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                        }

                        
                    }else{
                            return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
            } 
            $payment_type5 = request('payment_type5');
            if($come_pay >= 5){

                /*-------------PAGO NUMERO 5----------------------*/

                $var5 = new ReceiptPayment();
                $var5->setConnection(Auth::user()->database_name);

                $amount_pay5 = request('amount_pay5');

                if(isset($amount_pay5)){
                    
                    $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 5!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                }
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
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
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo5 != 0)){

                                $var5->id_account = $account_efectivo5;

                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                            }
                        }

                        if($payment_type5 == 9 || $payment_type5 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta5 != 0)){
                                $var5->id_account = $account_punto_de_venta5;
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
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
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                    }

                    
                }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 
            $payment_type6 = request('payment_type6');
            if($come_pay >= 6){

                /*-------------PAGO NUMERO 6----------------------*/

                $var6 = new ReceiptPayment();
                $var6->setConnection(Auth::user()->database_name);

                $amount_pay6 = request('amount_pay6');

                if(isset($amount_pay6)){
                    
                    $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 6!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                }
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
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
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo6 != 0)){

                                $var6->id_account = $account_efectivo6;

                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                            }
                        }

                        if($payment_type6 == 9 || $payment_type6 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta6 != 0)){
                                $var6->id_account = $account_punto_de_venta6;
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
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
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                    }

                    
                }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                    }
                /*--------------------------------------------*/
            } 
            $payment_type7 = request('payment_type7');
            if($come_pay >= 7){

               //-------------PAGO NUMERO 7----------------------

                $var7 = new ReceiptPayment();
                $var7->setConnection(Auth::user()->database_name);

                $amount_pay7 = request('amount_pay7');

                if(isset($amount_pay7)){
                    
                    $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                }else{
                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar un monto de pago 7!');
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
                                    return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                }
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
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
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo7 != 0)){

                                $var7->id_account = $account_efectivo7;

                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                            }
                        }

                        if($payment_type7 == 9 || $payment_type7 == 10){
                            //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta7 != 0)){
                                $var7->id_account = $account_punto_de_venta7;
                            }else{
                                return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
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
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                    }

                    
                }else{
                        return redirect('receipt/facturarunique/'.$quotation->id.'/'.$quotation->coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                    }

            } 

        }
        
        //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
        if(($total_pay == $sin_formato_total_pay) || ($sin_formato_total_pay <= 0))
        {
            $global = new GlobalController();

            $comboController = new ComboController();

            if(empty($quotation->date_billing) && empty($quotation->date_delivery_note) && empty($quotation->date_order)){

                //$value_return_combo = $comboController->validate_combo_discount($quotation->id);
                $value_return_all = $global->check_all_products_after_facturar($quotation->id);

                if($value_return_all != "exito"){
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($value_return_all);
                }

                $retorno = $global->discount_inventory($quotation->id);
            
                if($retorno != "exito"){
                    return redirect('receipt/facturar/'.$quotation->id.'/'.$quotation->coin.'')->withDanger($retorno);
                }
            }
        


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

                //LE PONEMOS STATUS C, DE COBRADO
                $quotation->status = "C";
            }
            
            if($validate_boolean2 == true){
                $var2->created_at = $date_payment;
                $var2->save();


                $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$quotation->id,$user_id,$var2->amount,0);
                
            }
            
            if($validate_boolean3 == true){
                $var3->created_at = $date_payment;
                $var3->save();


                $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$quotation->id,$user_id,$var3->amount,0);
            
                
            }
            if($validate_boolean4 == true){
                $var4->created_at = $date_payment;
                $var4->save();

                $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$quotation->id,$user_id,$var4->amount,0);
            
            }
            if($validate_boolean5 == true){
                $var5->created_at = $date_payment;
                $var5->save();


                $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$quotation->id,$user_id,$var5->amount,0);
             
            }
            if($validate_boolean6 == true){
                $var6->created_at = $date_payment;
                $var6->save();

                $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$quotation->id,$user_id,$var6->amount,0);
            
            }
            if($validate_boolean7 == true){
                $var7->created_at = $date_payment;
                $var7->save();


                $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$quotation->id,$user_id,$var7->amount,0);
            
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
    
            }


            //Anticipos
            
            if(isset($anticipo) && ($anticipo != 0)){
                $account_anticipo_cliente = Account::on(Auth::user()->database_name)->where('code_one',2)
                                                        ->where('code_two',3)
                                                        ->where('code_three',1)
                                                        ->where('code_four',1)
                                                        ->where('code_five',2)->first(); 
                //Si el total a pagar es negativo, quiere decir que los anticipos sobrepasan al monto total de la factura
                if($sin_formato_total_pay < 0){
                    $this->check_anticipo($quotation,$sin_formato_grandtotal);
                    $quotation->anticipo =  $sin_formato_grandtotal;
                    $quotation->status = "C";
                   
                }else{
                    $quotation->anticipo =  $anticipo;
                    $global->associate_anticipos_quotation($quotation);
                    $quotation->status = "C";
                }
                
                if(isset($account_anticipo_cliente)){
                    $this->add_movement($bcv,$header_voucher->id,$account_anticipo_cliente->id,$user_id,$quotation->anticipo,0,$quotation->id);
                    $global->add_payment($quotation,$account_anticipo_cliente->id,3,$quotation->anticipo,$bcv);
                }
             }else{
                 $quotation->anticipo = 0;
             }

            if($retencion_iva !=0){
                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)
                                                        ->where('code_three',4)->where('code_four',1)->where('code_five',2)->first();  
            
                if(isset($account_iva_retenido)){
                    $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$user_id,$retencion_iva,0,$quotation->id);
                }
            }

            
            if($retencion_islr !=0){
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',4)
                                                ->where('code_four',1)->where('code_five',4)->first();  

                if(isset($account_islr_pagago)){
                    $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$user_id,$retencion_islr,0,$quotation->id);
                }
            }
            

         
            
            //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
            $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first(); 
            
            if(isset($account_cuentas_por_cobrar)){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,0,$sin_formato_grandtotal,$quotation->id);
            }
            
            
            if(($quotation_status != 'C') && ($quotation_status != 'P')){

                if(empty($var->number_delivery_note))
                {   //Me busco el ultimo numero en notas de entrega
                    $last_number = Receipts::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->where('type','=','R')->orderBy('number_delivery_note','desc')->first();
  
                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $var->number_delivery_note = $last_number->number_delivery_note + 1;
                    }else{
                        $var->number_delivery_note = 1;
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
                ->where('id_quotation', '=', $quotation->id)->get();
        
                foreach($quotation_products as $det_products){
    
                $global->transaction_inv('venta',$det_products->id_inventory,'venta',$det_products->amount,$det_products->price,$date,1,1,0,$det_products->id_inventory_histories,$det_products->id,$quotation->id);
        
                }  
        

            }

            //Modifica la factura
 
            $quotation->date_billing = request('date-begin-form2');
            $quotation->base_imponible = $base_imponible;
            $quotation->amount_exento =  $amount_exento;
            $quotation->amount =  $sin_formato_amount;
            $quotation->amount_iva =  $sin_formato_amount_iva;
            $quotation->amount_with_iva = $sin_formato_grandtotal;
            $quotation->iva_percentage = $iva_percentage;
            $quotation->retencion_iva = $retencion_iva;
            $quotation->retencion_islr = $retencion_islr;
            $quotation->status = "C";
            
            $quotation->save();


            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   

            if(($quotation_status != 'C') && ($quotation_status != 'P')){

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);

                $header_voucher->description = "Ventas de Bienes o servicios.";
                $header_voucher->date = $date_payment;
                
            
                $header_voucher->status =  "1";
            
                $header_voucher->save();

                //Busqueda de Cuentas

                //Cuentas por Cobrar Clientes

                $account_cuentas_por_cobrar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Cobrar Clientes')->first();  
            
    
                if(isset($account_cuentas_por_cobrar)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_cobrar->id,$user_id,$sin_formato_grandtotal,0,$quotation->id);
                }

                //Ingresos por SubSegmento de Bienes

                if($total_mercancia != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Bienes')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_mercancia,$quotation->id);
                    }
                }
                
                if($total_servicios != 0){
                    $account_subsegmento = Account::on(Auth::user()->database_name)->where('description', 'like', 'Ventas por Servicios')->first();

                    if(isset($account_subsegmento)){
                        $this->add_movement($bcv,$header_voucher->id,$account_subsegmento->id,$user_id,0,$total_servicios,$quotation->id);
                    }
                }

                //Debito Fiscal IVA por Pagar

                $account_debito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'Debito Fiscal IVA por Pagar')->first();
                
                if($base_imponible != 0){
                    $total_iva = ($base_imponible * $iva_percentage)/100;

                    if(isset($account_cuentas_por_cobrar)){
                        $this->add_movement($bcv,$header_voucher->id,$account_debito_iva_fiscal->id,$user_id,0,$total_iva,$quotation->id);
                    }
                }
                
                //Mercancia para la Venta

            }

            
            $global = new GlobalController;                                                
        
            //Aqui pasa los quotation_products a status C de Cobrado
           /* DB::connection(Auth::user()->database_name)->table('quotation_products')
                                                        ->where('id_quotation', '=', $quotation->id)
                                                        ->update(['status' => 'C']);*/
    
            $global->procesar_anticipos($quotation,$sin_formato_total_pay);

          
             return redirect('receipt/facturado/'.$quotation->id.'/'.$coin.'')->withSuccess('Recibo Guardado con Exito!');

           
        }else{
            return redirect('receipt/facturar/'.$quotation->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
        }

        
        }
        
    }
    

    public function createfacturar_after($id_quotation,$coin)
    {
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
         }
 
         if(isset($quotation)){
                                                            
            $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

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
                                            ->get();
             $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->get();
             $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                                            ->where('receipt_products.id_quotation',$quotation->id)
                                                            ->whereIn('receipt_products.status',['1','C'])
                                                            ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                            'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                            ,'receipt_products.retiene_islr as retiene_islr_quotation')
                                                            ->get(); 

             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;

             //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
             $total_retiene_iva = 0;
             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;

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
                 if($var->money == 'Bs'){
                    $price_cost_total += $var->price_buy * $var->amount_quotation;
                }else{
                    $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
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
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }
             }else{
                $bcv = null;
             }
             

            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */
             $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                /*if($client->percentage_retencion_iva != 0){
                    $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
                }*/

            
                
                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */

            $is_after = false;
     
             return view('admin.receipt.createfacturar',compact('price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_iva','total_retiene_islr','is_after','client'));
         }else{
             
             return redirect('/receipt')->withDanger('La Relación de gasto no existe');
         } 
         
    }

    
    public function createfacturado($id_quotation,$coin,$reverso = null) // finalizando factura
    {
         $quotation = null;
 

         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
       
             
            
             
                if ($quotation->type == 'F') {
                    $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                } else {
                    $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

                }

         }


         if(isset($quotation)){
             
    
            // $product_quotations = ReceiptProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
     
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
             

            if ($quotation->type == 'F') {
            return view('admin.receipt.createfacturado',compact('quotation','payment_quotations', 'datenow','bcv','coin','reverso','client'));
            } else {
            return view('admin.receipt.createreceiptfacturado',compact('quotation','payment_quotations', 'datenow','bcv','coin','reverso','client'));    
            }


            }else{
             return redirect('/receipt')->withDanger('La relación de gasto no existe');
         } 
         
    }


    public function createfacturadounique($id_quotation,$coin,$reverso = null) // finalizando factura
    {
         $quotation = null;
 

         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
       
             
            
            $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

         }


        if(isset($quotation)){
             
    
            // $product_quotations = ReceiptProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
     
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
             

            return view('admin.receipt.createfacturadounique',compact('quotation','payment_quotations', 'datenow','bcv','coin','reverso','client'));    
            


        }else{
             
            return redirect('/receiptunique')->withDanger('El recibo no existe');
        
            } 
         
    }

    public function reversar_quotation(Request $request)
    { 
       
        $id_quotation = $request->id_quotation_modal;

        $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);

        $exist_multipayment = Multipayment::on(Auth::user()->database_name)
                            ->where('id_quotation',$quotation->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  
                            
        if(empty($exist_multipayment)){
            if($quotation != 'X'){

                HeaderVoucher::on(Auth::user()->database_name)
                ->join('detail_vouchers','detail_vouchers.id_header_voucher','header_vouchers.id')
                ->where('detail_vouchers.id_invoice',$id_quotation)
                ->update(['header_vouchers.status' => 'X']);

                $detail = DetailVoucher::on(Auth::user()->database_name)
                ->where('id_invoice',$id_quotation)
                ->update(['status' => 'X']);
    
                
                $global = new GlobalController();
                $global->deleteAllProducts($quotation->id);

                ReceiptPayment::on(Auth::user()->database_name)
                                ->where('id_quotation',$quotation->id)
                                ->update(['status' => 'X']);
    
                $quotation->status = 'X';
                $quotation->save();


                $quotation_products = DB::connection(Auth::user()->database_name)->table('receipt_products')
                ->where('id_quotation', '=', $id_quotation)
                ->get(); // Conteo de Productos para incluiro en el historial de inventario
                               
                foreach($quotation_products as $det_products){ // guardado historial de inventario 
                $global->transaction_inv('rev_venta',$det_products->id_inventory,'rev_venta',$det_products->amount,$det_products->price,$quotation->date_billing,1,1,0,$det_products->id_inventory_histories,$det_products->id,$quotation->id);
                }


                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($quotation->anticipo))&& ($quotation->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipo($datenow,$quotation->id_client,$account_anticipo->id,"bolivares",
                    $quotation->anticipo,$quotation->bcv,"reverso factura N°".$quotation->number_invoice);
                    
                }

                /*$historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($quotation,"quotation","Se Reversó la Factura");
                */
            }   

        }else{
            
            return redirect('/receipt/facturado/'.$quotation->id.'/bolivares/'.$exist_multipayment->id_header.'');
        }
        
        if ($quotation->type == 'F') {
             return redirect('receipt')->withSuccess('Reverso de Gasto Exitoso!');
        } else {
             return redirect('receipt/receipt')->withDanger('Reverso de Gasto Exitoso!');  
        }
    }

    public function reversar_quotation_multipayment($id_quotation,$id_header){

        
        if(isset($id_header)){
            $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);

            //aqui reversamos todo el movimiento del multipago
            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id','=',$id_header)
            ->update(['detail_vouchers.status' => 'X' , 'header_vouchers.status' => 'X']);

            //aqui se cambia el status de los pagos
            DB::connection(Auth::user()->database_name)->table('multipayments')
            ->join('receipt_payments', 'receipt_payments.id_quotation','=','multipayments.id_quotation')
            ->where('multipayments.id_header','=',$id_header)
            ->update(['receipt_payments.status' => 'X']);

            //aqui aumentamos el inventario y cambiamos el status de los productos que se reversaron
            DB::connection(Auth::user()->database_name)->table('multipayments')
                ->join('receipt_products', 'receipt_products.id_quotation','=','multipayments.id_quotation')
                ->join('inventories','inventories.id','receipt_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('multipayments.id_header','=',$id_header)
                ->update(['inventories.amount' => DB::raw('inventories.amount+receipt_products.amount') ,
                        'receipt_products.status' => 'X']);
    

            //aqui le cambiamos el status a todas las facturas a X de reversado
            Multipayment::on(Auth::user()->database_name)
            ->join('receipts', 'receipt.id','=','multipayments.id_quotation')
            ->where('id_header',$id_header)->update(['receipt.status' => 'X']);

            Multipayment::on(Auth::user()->database_name)->where('id_header',$id_header)->delete();



            /*$historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($quotation,"quotation","Se Reversó MultiFactura");
            */
            return redirect('invoices')->withSuccess('Reverso de relación Multipago Exitosa!');
        }else{
            return redirect('invoices')->withDanger('No se pudo reversar la relación');
        }
        
    }




    function imprimirFactura($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                 if ($quotation->type == 'F') {
                    $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                } else {
                    $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

                }

                                     
             }else{
                return redirect('/receipt')->withDanger('No llega el numero de la Relacion');
                } 
     
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
                                                                ,'receipt_products.retiene_islr as retiene_islr_quotation','receipt_products.description as description_det')
                                                                ->get(); 

                foreach ($inventories_quotations as $var) {
    
                    if($var->description_det != null) {
        
                        $var->description = $var->description_det; 
                    }
                    
                }

                
                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                 $pdf = $pdf->loadView('pdf.receiptfac',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','client'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/receipt')->withDanger('La Relación no existe');
             } 
             
        

        
    }


    function imprimirFactura_media($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                 if ($quotation->type == 'F') {
                    $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                } else {
                    $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

                }

                                     
             }else{
                return redirect('/receipt')->withDanger('No llega el numero de la factura');
                } 
     
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
                                                                ->where('receipt_products.status','C')
                                                                ->orwhere('receipt_products.status','1')
                                                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                                ,'receipt_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 
                 
                 if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->bcv;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);                
                
                 $pdf = $pdf->loadView('pdf.receiptfacmedia',compact('quotation','inventories_quotations','payment_quotations','bcv','company','coin','client'))->setPaper('letter','portrait');
                 return $pdf->stream();
         
                }else{
                 return redirect('/receipt')->withDanger('Relación no existe');
             } 
             
        

        
    }

    function imprimirFactura_maq($id_quotation,$coin = null)
    {
      

        $pdf = App::make('dompdf.wrapper');

        
             $quotation = null;
                 
             if(isset($id_quotation)){
                 $quotation = Receipts::on(Auth::user()->database_name)->where('date_billing', '<>', null)->find($id_quotation);
              
                 if ($quotation->type == 'F') {
                    $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                } else {
                    $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

                }
                   
             }else{
                return redirect('/receipt')->withDanger('No llega el numero de la factura');
                } 
     
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
                                                                ->where('receipt_products.status','C')
                                                                ->orwhere('receipt_products.status','1')
                                                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                                ,'receipt_products.retiene_islr as retiene_islr_quotation')
                                                                ->get(); 

                
                if($coin == 'bolivares'){
                    $bcv = null;
                    
                }else{
                    $bcv = $quotation->login;
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
               // $lineas_cabecera = $company->format_header_line;

                 $pdf = $pdf->loadView('pdf.receiptfacmaq',compact('company','quotation','inventories_quotations','payment_quotations','bcv','coin','client'));
                 return $pdf->stream();
         
                }else{
                 return redirect('/receipt')->withDanger('La Relación no existe');
             } 
    }


    public function selectproduct($id_quotation,$coin,$type,$type_quotation = null)
    {

        $services = null;

            $services = null;

            $user       =   auth()->user();
            $users_role =   $user->role_id;
     
            $global = new GlobalController();
            
            $inventories = Product::on(Auth::user()->database_name)
            ->where(function ($query){
                $query->where('type','MERCANCIA')
                    ->orWhere('type','COMBO')
                    ->orWhere('type','SERVICIO')
                    ->orWhere('type','MATERIAP');
            })
    
            ->where('products.status',1)
            ->select('products.id as id_inventory','products.*')  
            ->get();     
    
            foreach ($inventories as $inventorie) {
                
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);
    
            }
  
    

            
        $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);

        $bcv_quotation_product = $quotation->bcv;
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if(($type == 'servicios') || $inventories->isEmpty()){

            $type = 'servicios';
            $services = DB::connection(Auth::user()->database_name)->table('inventories')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->where('products.type','SERVICIO')
            ->where('products.status',1)
            ->select('products.*','inventories.id as id_inventory')
            ->orderBy('products.code_comercial','desc')
            ->get();
            
            return view('admin.receipt.selectservice',compact('type','services','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
        }
    
        return view('admin.receipt.selectinventary',compact('type','inventories','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
    }

    public function selectproductunique($id_quotation,$coin,$type,$type_quotation = null)
    {

        $services = null;

            $services = null;

            $user       =   auth()->user();
            $users_role =   $user->role_id;
     
            $global = new GlobalController();
            
            $inventories = Product::on(Auth::user()->database_name)
            ->where(function ($query){
                $query->where('type','MERCANCIA')
                    ->orWhere('type','COMBO')
                    ->orWhere('type','SERVICIO')
                    ->orWhere('type','MATERIAP');
            })
    
            ->where('products.status',1)
            ->select('products.id as id_inventory','products.*')  
            ->get();     
    
            foreach ($inventories as $inventorie) {
                
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);
    
            }
  
    

            
        $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);

        $bcv_quotation_product = $quotation->bcv;
        
        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        if(($type == 'servicios') || $inventories->isEmpty()){

            $type = 'servicios';
            $services = DB::connection(Auth::user()->database_name)->table('inventories')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->where('products.type','SERVICIO')
            ->where('products.status',1)
            ->select('products.*','inventories.id as id_inventory')
            ->orderBy('products.code_comercial','desc')
            ->get();
            
            return view('admin.receipt.selectservice',compact('type','services','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
        }
    
        return view('admin.receipt.selectinventaryunique',compact('type','inventories','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
    }

    public function selectinventaryunique($id_expense,$coin,$type)
    {
        if($type == 'mercancia' || $type == 'MERCANCIA'){
            $type = 'MERCANCIA';
        }

        if($type == 'servicio' || $type == 'SERVICIO'){
            $type = 'SERVICIO';
        }

        if($type == 'materiap' || $type == 'MATERIAP'){
            $type = 'MATERIAP';
        }



        $user       =   auth()->user();
        $users_role =   $user->role_id;
    
            $global = new GlobalController();
            $inventories = Product::on(Auth::user()->database_name)
        
            ->where(function ($query){
                $query->where('type','MERCANCIA')
                    ->orWhere('type','COMBO')
                    ->orWhere('type','MATERIAP')
                    ->orWhere('type','SERVICIO');
            })
    
    
            ->where('products.status',1)
            ->select('products.id as id_inventory','products.*')  
            ->get();     
            
            foreach ($inventories as $inventorie) {
                
                $inventorie->amount = 11;
    
            }

        
            return view('admin.receipt.selectinventaryunique',compact('type','coin','inventories','id_expense'));
    }


    public function movementsinvoice($id_invoice,$coin = null)
    {
        

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
            $quotation = Receipts::on(Auth::user()->database_name)->find($id_invoice);
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
         
        
        return view('admin.receipt.index_detail_movement',compact('detailvouchers','quotation','coin','invoices','multipayments_detail'));
    }




    public function createproduct($id_quotation,$coin,$id_inventory,$type = null)
    {
        $quotation = null;
                
        if(isset($id_quotation)){
            $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation) && ($quotation->status == 1)){
            //$product_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $product = null;
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                ->where('receipt_products.id_quotation',$id_quotation)
                                ->whereIn('receipt_products.status',['1','C'])
                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id as quotation_products_id','products.code_comercial as code','receipt_products.discount as discount',
                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva')
                                ->get(); 
                
                if(isset($id_inventory)){
                    $inventory = Product::on(Auth::user()->database_name)->find($id_inventory);
                }
                if(isset($inventory)){

                    $date = Carbon::now();
                    $datenow = $date->format('Y-m-d');    
                    
                    /*Revisa si la tasa de la empresa es automatica o fija*/
                    $company = Company::on(Auth::user()->database_name)->find(1);
                    $global = new GlobalController();
                    //Si la taza es automatica
                    if($company->tiporate_id == 1){
                        $bcv_quotation_product = $global->search_bcv();
                    }else{
                        //si la tasa es fija
                        $bcv_quotation_product = $company->rate;
                    }


                    if(($coin == 'bolivares')){
                        
                        if($company->tiporate_id == 1){
                            $bcv = $global->search_bcv();
                        }else{
                            //si la tasa es fija
                            $bcv = $company->rate;
                        }
                    }else{
                        //Cuando mi producto esta en Bolivares, pero estoy cotizando en dolares, convierto los bs a dolares
                        if($inventory->money == 'Bs'){
                            $inventory->price = $inventory->price / $quotation->bcv;
                        }
                        $bcv = null;
                    }
                   
                    return view('admin.receipt.create',compact('bcv_quotation_product','quotation','inventories_quotations','inventory','bcv','datenow','coin','type'));

                }else{
                    return redirect('/receipt')->withDanger('El Producto no existe');
                } 
        }else{
            return redirect('/receipt')->withDanger('El Recibo no existe');
        } 

    }

    public function createproductunique($id_quotation,$coin,$id_inventory,$type = null)
    {
        $quotation = null;
                
        if(isset($id_quotation)){
            $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation) && ($quotation->status == 1)){
            //$product_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $product = null;
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                ->where('receipt_products.id_quotation',$id_quotation)
                                ->whereIn('receipt_products.status',['1','C'])
                                ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.id as quotation_products_id','products.code_comercial as code','receipt_products.discount as discount',
                                'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva')
                                ->get(); 
                
                if(isset($id_inventory)){
                    $inventory = Product::on(Auth::user()->database_name)->find($id_inventory);
                }
                if(isset($inventory)){

                    $date = Carbon::now();
                    $datenow = $date->format('Y-m-d');    
                    
                    /*Revisa si la tasa de la empresa es automatica o fija*/
                    $company = Company::on(Auth::user()->database_name)->find(1);
                    $global = new GlobalController();
                    //Si la taza es automatica
                    if($company->tiporate_id == 1){
                        $bcv_quotation_product = $global->search_bcv();
                    }else{
                        //si la tasa es fija
                        $bcv_quotation_product = $company->rate;
                    }


                    if(($coin == 'bolivares')){
                        
                        if($company->tiporate_id == 1){
                            $bcv = $global->search_bcv();
                        }else{
                            //si la tasa es fija
                            $bcv = $company->rate;
                        }
                    }else{
                        //Cuando mi producto esta en Bolivares, pero estoy cotizando en dolares, convierto los bs a dolares
                        if($inventory->money == 'Bs'){
                            $inventory->price = $inventory->price / $quotation->bcv;
                        }
                        $bcv = null;
                    }
                   
                    return view('admin.receipt.createunique',compact('bcv_quotation_product','quotation','inventories_quotations','inventory','bcv','datenow','coin','type'));

                }else{
                    return redirect('/receipt')->withDanger('El Producto no existe');
                } 
        }else{
            return redirect('/receipt')->withDanger('El Recibo no existe');
        } 

    }

    public function editquotationproduct($id,$coin = null)
    {
            $quotation_product = ReceiptProduct::on(Auth::user()->database_name)->find($id);
        
            if(isset($quotation_product)){

                $inventory= Product::on(Auth::user()->database_name)->find($quotation_product->id_inventory);

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();
                
                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv = $company->rate;
                }

                if(!isset($coin)){
                    $coin = 'bolivares';
                }

                if($coin == 'bolivares'){
                    $rate = null;
                }else{
                    $rate = $quotation_product->rate;
                }

                 
                
                    if($quotation_product->description != null) {
        
                        $inventory->description = $quotation_product->description; 
                    }
                    


                return view('admin.receipt.edit_product',compact('rate','coin','quotation_product','inventory','bcv'));
            }else{
                return redirect('/receipt')->withDanger('No se Encontro el Producto!');
            }
    
    
    }

    public function editquotationproductunique($id,$coin = null)
    {
            $quotation_product = ReceiptProduct::on(Auth::user()->database_name)->find($id);
        
            if(isset($quotation_product)){

                $inventory= Product::on(Auth::user()->database_name)->find($quotation_product->id_inventory);

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController();
                
                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv = $company->rate;
                }

                if(!isset($coin)){
                    $coin = 'bolivares';
                }

                if($coin == 'bolivares'){
                    $rate = null;
                }else{
                    $rate = $quotation_product->rate;
                }

                 
                
                    if($quotation_product->description != null) {
        
                        $inventory->description = $quotation_product->description; 
                    }
                    


                return view('admin.receipt.edit_productunique',compact('rate','coin','quotation_product','inventory','bcv'));
            }else{
                return redirect('/receiptr')->withDanger('No se Encontro el Producto!');
            }
    
    
    }


    public function updatequotationproduct(Request $request, $id)
    { 

           
            $data = request()->validate([
                
                'amount'         =>'required',
                'discount'         =>'required',
                'description'         =>'required'
            
            ]);

            
        
            $var = ReceiptProduct::on(Auth::user()->database_name)->findOrFail($id);

            $price_old = $var->price;
            $amount_old = $var->amount;

            $sin_formato_price = str_replace(',', '.', str_replace('.', '', request('price')));
            $sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            $coin = request('coin');
            $var->rate = $sin_formato_rate;

            if($coin == 'bolivares'){
                $var->price = $sin_formato_price;
            }else{
                $var->price = $sin_formato_price * $sin_formato_rate;
            }
        
            $var->amount = request('amount');
        
            $var->discount = request('discount');

            $var->description = request('description');
        
            $global = new GlobalController();

            $value_return = $global->check_product($var->id_quotation,$var->id_inventory,$var->amount);


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

           /* if($value_return != 'exito'){
                return redirect('receipt/quotationproduct/'.$var->id.'/'.$coin.'/edit')->withDanger('La cantidad de este producto excede a la cantidad puesta en inventario! ');
            } */

        
            $var->save();

            
            if(isset($var->quotations['date_delivery_note']) || isset($var->quotations['date_billing'])){
                $this->recalculateQuotation($var->id_quotation);
            }

            $global = new GlobalController();
            
            if(isset($var->quotations['date_delivery_note'])) {
                $quotation_products = DB::connection(Auth::user()->database_name)->table('receipt_products')
                ->where('id_quotation', '=', $var->id_quotation)
                ->where('id', '=',  $var->id)
                ->get(); // Conteo de Productos para incluiro en el historial de inventario
                foreach($quotation_products as $det_products){ // guardado historial de inventario 
                $global->transaction_inv('aju_nota',$det_products->id_inventory,'pruebaf',$det_products->amount,$det_products->price,null,1,1,0,$det_products->id_inventory_histories,$det_products->id,$var->id_quotation);
                }
            }

            /*$historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($var,"receipt_product","Actualizó el Producto: ".$var->inventories['code']."/ 
            Precio Viejo: ".number_format($price_old, 2, ',', '.')." Cantidad: ".$amount_old."/ Precio Nuevo: ".number_format($var->price, 2, ',', '.')." Cantidad: ".$var->amount);
        */
            
            return redirect('/receipt/register/'.$var->id_quotation.'/'.$coin.'')->withSuccess('Actualizacion Exitosa!');
        
    }

    public function updatequotationproductunique(Request $request, $id)
    { 

           
            $data = request()->validate([
                
                'amount'         =>'required',
                'discount'         =>'required',
                'description'         =>'required'
            
            ]);

            
        
            $var = ReceiptProduct::on(Auth::user()->database_name)->findOrFail($id);

            $price_old = $var->price;
            $amount_old = $var->amount;

            $sin_formato_price = str_replace(',', '.', str_replace('.', '', request('price')));
            $sin_formato_rate = str_replace(',', '.', str_replace('.', '', request('rate')));

            $coin = request('coin');
            $var->rate = $sin_formato_rate;

            if($coin == 'bolivares'){
                $var->price = $sin_formato_price;
            }else{
                $var->price = $sin_formato_price * $sin_formato_rate;
            }
        
            $var->amount = request('amount');
        
            $var->discount = request('discount');

            $var->description = request('description');
        
            $global = new GlobalController();

            $value_return = $global->check_product($var->id_quotation,$var->id_inventory,$var->amount);


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

            /*if($value_return != 'exito'){
                return redirect('receipt/quotationproduct/'.$var->id.'/'.$coin.'/edit')->withDanger('La cantidad de este producto excede a la cantidad puesta en inventario! ');
            } */

        
            $var->save();

            
            if(isset($var->quotations['date_delivery_note']) || isset($var->quotations['date_billing'])){
                $this->recalculateQuotation($var->id_quotation);
            }

            $global = new GlobalController();
            
            /*if(isset($var->quotations['date_delivery_note'])) {
                $quotation_products = DB::connection(Auth::user()->database_name)->table('receipt_products')
                ->where('id_quotation', '=', $var->id_quotation)
                ->where('id', '=',  $var->id)
                ->get(); // Conteo de Productos para incluiro en el historial de inventario
                foreach($quotation_products as $det_products){ // guardado historial de inventario 
                $global->transaction_inv('aju_nota',$det_products->id_inventory,'pruebaf',$det_products->amount,$det_products->price,null,1,1,0,$det_products->id_inventory_histories,$det_products->id,$var->id_quotation);
                }
            }

            $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($var,"receipt_product","Actualizó el Producto: ".$var->inventories['code']."/ 
            Precio Viejo: ".number_format($price_old, 2, ',', '.')." Cantidad: ".$amount_old."/ Precio Nuevo: ".number_format($var->price, 2, ',', '.')." Cantidad: ".$var->amount);
        */
            
            return redirect('/receipt/registerunique/'.$var->id_quotation.'/'.$coin.'')->withSuccess('Actualizacion Exitosa!');
        
    }

    public function deleteProduct(Request $request)
    {


        $quotation_product = ReceiptProduct::on(Auth::user()->database_name)->find(request('id_quotation_product_modal')); 
        
        if(isset($quotation_product) && $quotation_product->status == "C"){
            
                ReceiptProduct::on(Auth::user()->database_name)
                ->join('products','products.id','receipt_products.id_inventory')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('receipt_products.id',$quotation_product->id)
                ->update(['receipt_products.status' => 'X']);
               
                $this->recalculateQuotation($quotation_product->id_quotation);
        }else{
            
            $quotation_product->status = 'X'; 
            $quotation_product->save(); 
        }

        //$historial_quotation = new HistorialReceiptController();

       // $historial_quotation->registerAction($quotation_product,"receipt_product","Se eliminó un Producto");

     return redirect('/receipt/register/'.request('id_quotation_modal').'/'.request('coin_modal').'')->withDanger('Eliminacion exitosa!!');
        
    }

    public function recalculateQuotation($id_quotation)
    {
        $quotation = null;
                 
        if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->findOrFail($id_quotation);
        }else{
            return redirect('/receipt')->withDanger('No llega el numero de la cotizacion');
        } 
 
         if(isset($quotation)){
           
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                            ->join('receipt_products', 'inventories.id', '=', 'receipt_products.id_inventory')
                                                            ->where('receipt_products.id_quotation',$quotation->id)
                                                            ->whereIn('receipt_products.status',['1','C'])
                                                            ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                            'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                            ,'receipt_products.retiene_islr as retiene_islr_quotation')
                                                            ->get(); 

            $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva = 0;
            $retiene_iva = 0;

            $total_retiene_islr = 0;
            $retiene_islr = 0;

            foreach($inventories_quotations as $var){
                if(isset($coin) && ($coin != 'bolivares')){
                    $var->price =  bcdiv(($var->price / ($var->rate ?? 1)), '1', 2);
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

            
            }

            $rate = null;
            
            if(isset($coin) && ($coin != 'bolivares')){
                $rate = $quotation->bcv;
            }
           
            $quotation->amount = $total * ($rate ?? 1);
            $quotation->base_imponible = $base_imponible * ($rate ?? 1);
            $quotation->amount_iva = $base_imponible * $quotation->iva_percentage / 100;
            $quotation->amount_with_iva = ($quotation->amount + $quotation->amount_iva);
            
            $quotation->save();
           
        }
    }



    public function createfacturar($id_quotation,$coin)
    {
        
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
         }
 
         if(isset($quotation)){
                                                            
            $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

            
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
                                            ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                                            ->where('receipt_products.id_quotation',$quotation->id)
                                                            ->whereIn('receipt_products.status',['1','C'])
                                                            ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                            'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                            ,'receipt_products.retiene_islr as retiene_islr_quotation')
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
             

            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */

             if ($quotation->type == 'F') {
                $client = Condominiums::on(Auth::user()->database_name)->find($quotation->id_client);

                } else {
                    $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

                }
               
                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */
     
            $is_after = false;
            if(empty($quotation->credit_days)){
                $is_after = true;
            }
             return view('admin.receipt.createfacturar',compact('price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_islr','is_after'
                        ,'total_mercancia','total_servicios','client','retiene_iva'));
         }else{
             return redirect('/receipt')->withDanger('El recibo no existe');
         } 
         
    }

    public function createfacturarunique($id_quotation,$coin)
    {
        
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
         }
 
         if(isset($quotation)){
                                                            
            $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

            
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
                                            ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                                            ->where('receipt_products.id_quotation',$quotation->id)
                                                            ->whereIn('receipt_products.status',['1','C'])
                                                            ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                            'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                            ,'receipt_products.retiene_islr as retiene_islr_quotation')
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
             

            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */

                 $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client);

          
               
                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }

            /*-------------- */
     
            $is_after = false;
            if(empty($quotation->credit_days)){
                $is_after = true;
            }
             return view('admin.receipt.createfacturarunique',compact('price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_islr','is_after'
                        ,'total_mercancia','total_servicios','client','retiene_iva'));
         }else{
             return redirect('/receiptunique')->withDanger('El recibo no existe');
         } 
         
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
//RECIBOS.////////////////////////////////////////////////////////////

public function storeclients(Request $request) // Generar recibo multiple de propietarios
{

    $data = request()->validate([
        
    
        'id_client'         =>'required',
        'id_invoice'        =>'required',

    
    ]);

    $id_client = request('id_client');
    $id_invoice = request('id_invoice');
    $id_cost_center = request('id_client');
    $id_service = 4;

    

    $clients = Owners::on(Auth::user()->database_name) // propietario del condominio
    ->where('id_cost_center','=',$id_cost_center)->get();

    //Buscar Factura original
    $quotations = Receipts::on(Auth::user()->database_name)
    ->orderBy('number_invoice' ,'desc')
    ->where('id','=',$id_invoice)
    ->where('type','=','F')
    ->select('receipts.*')
    ->get();


    //dd($quotations[0]['number_invoice']);


    
    if(!empty($quotations) & $id_client != '-1'){  
           
        
        foreach ($clients as $client) {

            $global = new GlobalController();
            
            $var = new Receipts(); //inicio recibo cabecera /////////////////////////

            $var->setConnection(Auth::user()->database_name);

            $last_number = Receipts::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->where('type','=','R')->orderBy('number_delivery_note','desc')->first();
  
            //Asigno un numero incrementando en 1
            if(isset($last_number)){
                $var->number_delivery_note = $last_number->number_delivery_note + 1;
            }else{
                $var->number_delivery_note = 1;
            }



            $var->id_client = $client->id;
            //$var->id_vendor = $id_vendor;
            $id_transport = $quotations[0]['id_transport'];
            $type = 'factura';
            $var->date_billing = $quotations[0]['date_billing'];
            $var->id_transport = $quotations[0]['id_transport'];
            $var->number_invoice = $quotations[0]['number_invoice'];
            $var->id_user = $quotations[0]['id_user'];
            $var->serie = $quotations[0]['serie'];
            $var->date_quotation = $quotations[0]['date_quotation'];
            $var->observation = $quotations[0]['observation'];
            $var->note = 'note recibo';
            $var->bcv = $quotations[0]['bcv'];
            $var->coin = 'bolivares';

            $var->base_imponible = 0;
            
            $montofactura = $quotations[0]['amount_with_iva'];
            $alicuota_cliente = $client->aliquot;
            $var->amount_exento = ($montofactura*$alicuota_cliente)/100;
            $var->amount_iva = 0;
            $alicuota_cliente = $client->aliquot;
            
            $var->amount_iva = 0;
            $var->amount = ($montofactura*$alicuota_cliente)/100;
            $var->amount_with_iva = ($montofactura*$alicuota_cliente)/100;

            $var->status = $quotations[0]['status'];
            $var->type = 'R';

            $var->save();
            
            $id_quotation = DB::connection(Auth::user()->database_name)
            ->table('receipts')
            ->where('number_invoice','=',$quotations[0]['number_invoice'])
            ->where('type','=','R')
            ->where('status','=',$quotations[0]['status'])
            ->select('id')
            ->get()->last(); 

          /*  $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($var,"quotation","Creó Cotización");
          */
         
            // Guardar detalle de factura//////////////////////////////

            $quotation = new ReceiptProduct(); //inicio recibo detalle /////////////////////////

            $quotation->setConnection(Auth::user()->database_name);

            $quotation->id_quotation = $id_quotation->id;
            $quotation->id_inventory = $id_service;
            $quotation->amount = 1;

            $montofactura = $quotations[0]['amount_with_iva'];
            $alicuota_cliente = $client->aliquot;

            $quotation->price = ($montofactura*$alicuota_cliente)/100;
            $quotation->discount = 0;
            $quotation->retiene_iva = 1;
            $quotation->retiene_islr = 0;
            $quotation->rate = $quotations[0]['bcv'];
            $quotation->status = 'C';
            $quotation->id_inventory_histories = 0;
            $quotation->save();

        }
//////////////////////////////


        return redirect('receipt/receipt');

        
    }else{
         return redirect('/receipt/registerreceiptclients/'.$type)->withDanger('Debe Buscar un Propietario');
    } 

    
}

public function storeownersunique(Request $request) // Crear recibo de propietario individual
{

    $data = request()->validate([
        
    
        'id_client'         =>'required',
        'id_invoice'        =>'required',

    
    ]);

    //$id_cost_center = request('id_client');
    $id_owner = request('id_owner');
    $id_service = 4;

    

    $clients = Owners::on(Auth::user()->database_name) // propietario del condominio
    ->where('id_cost_center','=',$id_cost_center)
    ->where('id','=',$id_owner)
    ->get();

    //Buscar Factura original
    $quotations = Receipts::on(Auth::user()->database_name)
    ->orderBy('number_invoice' ,'desc')
    ->where('id','=',$id_invoice)
    ->where('type','=','F')
    ->select('receipts.*')
    ->get();


    //dd($quotations[0]['number_invoice']);


    
    if(!empty($quotations) & $id_client != '-1'){  
           
        
        foreach ($clients as $client) {

            $global = new GlobalController();
            
            $var = new Receipts(); //inicio recibo cabecera /////////////////////////

            $var->setConnection(Auth::user()->database_name);

            $last_number = Receipts::on(Auth::user()->database_name)->where('number_delivery_note','<>',NULL)->where('type','=','R')->orderBy('number_delivery_note','desc')->first();
  
            //Asigno un numero incrementando en 1
            if(isset($last_number)){
                $var->number_delivery_note = $last_number->number_delivery_note + 1;
            }else{
                $var->number_delivery_note = 1;
            }



            $var->id_client = $client->id;
            //$var->id_vendor = $id_vendor;
            $id_transport = $quotations[0]['id_transport'];
            $type = 'factura';
            $var->date_billing = $quotations[0]['date_billing'];
            $var->id_transport = $quotations[0]['id_transport'];
            $var->number_invoice = $quotations[0]['number_invoice'];
            $var->id_user = $quotations[0]['id_user'];
            $var->serie = $quotations[0]['serie'];
            $var->date_quotation = $quotations[0]['date_quotation'];
            $var->observation = $quotations[0]['observation'];
            $var->note = 'note recibo';
            $var->bcv = $quotations[0]['bcv'];
            $var->coin = 'bolivares';

            $var->base_imponible = 0;
            
            $montofactura = $quotations[0]['amount_with_iva'];
            $alicuota_cliente = $client->aliquot;
            $var->amount_exento = ($montofactura*$alicuota_cliente)/100;
            $var->amount_iva = 0;
            $alicuota_cliente = $client->aliquot;
            
            $var->amount_iva = 0;
            $var->amount = ($montofactura*$alicuota_cliente)/100;
            $var->amount_with_iva = ($montofactura*$alicuota_cliente)/100;

            $var->status = $quotations[0]['status'];
            $var->type = 'R';

            $var->save();
            
            $id_quotation = DB::connection(Auth::user()->database_name)
            ->table('receipts')
            ->where('number_invoice','=',$quotations[0]['number_invoice'])
            ->where('type','=','R')
            ->where('status','=',$quotations[0]['status'])
            ->select('id')
            ->get()->last(); 

          /*  $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($var,"quotation","Creó Cotización");
          */
         
            // Guardar detalle de factura//////////////////////////////

            $quotation = new ReceiptProduct(); //inicio recibo detalle /////////////////////////

            $quotation->setConnection(Auth::user()->database_name);

            $quotation->id_quotation = $id_quotation->id;
            $quotation->id_inventory = $id_service;
            $quotation->amount = 1;

            $montofactura = $quotations[0]['amount_with_iva'];
            $alicuota_cliente = $client->aliquot;

            $quotation->price = ($montofactura*$alicuota_cliente)/100;
            $quotation->discount = 0;
            $quotation->retiene_iva = 1;
            $quotation->retiene_islr = 0;
            $quotation->rate = $quotations[0]['bcv'];
            $quotation->status = 'C';
            $quotation->id_inventory_histories = 0;
            $quotation->save();

        }

        return redirect('receipt/receipt');

        
    }else{
         return redirect('/receipt/registerreceiptclients/'.$type)->withDanger('Debe Buscar un Propietario');
    } 

    
}


public function createfacturar_aftereceipt($id_quotation,$coin) // cobrando recibo de condominio
    {
         $quotation = null;
             
         if(isset($id_quotation)){
             $quotation = Receipts::on(Auth::user()->database_name)->find($id_quotation);
         }
         
         
         $client = Owners::on(Auth::user()->database_name)->find($quotation->id_client); // buscar propietario

   
         if(isset($quotation)){
                                                            
            $payment_quotations = ReceiptPayment::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();

            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_client',$client->id)
                                        ->where(function ($query) use ($quotation){
                                            $query->where('id_quotation',null)
                                                ->orWhere('id_quotation',$quotation->id);
                                        })
                                        ->where('coin','like','bolivares')
                                        ->sum('amount');

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_client',$client->id)
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
                                            ->get();
             $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                                            ->where('code_two', 1)
                                            ->where('code_three', 1)
                                            ->where('code_four', 1)
                                            ->where('code_five', '<>',0)
                                            ->get();
             $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                                            ->get();

            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('receipt_products', 'products.id', '=', 'receipt_products.id_inventory')
                                                            ->where('receipt_products.id_quotation',$quotation->id)
                                                            ->whereIn('receipt_products.status',['1','C'])
                                                            ->select('products.*','receipt_products.price as price','receipt_products.rate as rate','receipt_products.discount as discount',
                                                            'receipt_products.amount as amount_quotation','receipt_products.retiene_iva as retiene_iva_quotation'
                                                            ,'receipt_products.retiene_islr as retiene_islr_quotation')
                                                            ->get(); 

             $total= 0;
             $base_imponible= 0;
             $price_cost_total= 0;

             //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
             $total_retiene_iva = 0;
             $retiene_iva = 0;

             $total_retiene_islr = 0;
             $retiene_islr = 0;

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
                 if($var->money == 'Bs'){
                    $price_cost_total += $var->price_buy * $var->amount_quotation;
                }else{
                    $price_cost_total += $var->price_buy * $var->amount_quotation * $quotation->bcv;
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
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $quotation->bcv;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares; 
                 }
             }else{
                $bcv = null;
             }
             

            /*Aqui revisamos el porcentaje de retencion de iva que tiene el cliente, para aplicarlo a productos que retengan iva */

                /*if($client->percentage_retencion_iva != 0){
                    $total_retiene_iva = ($retiene_iva * $client->percentage_retencion_iva) /100;
                }

                if($client->percentage_retencion_islr != 0){
                    $total_retiene_islr = ($retiene_islr * $client->percentage_retencion_islr) /100;
                }*/

            /*-------------- */

            $is_after = false;
    
             return view('admin.receipt.createfacturar',compact('price_cost_total','coin','quotation'
                        ,'payment_quotations', 'accounts_bank', 'accounts_efectivo', 'accounts_punto_de_venta'
                        ,'datenow','bcv','anticipos_sum','total_retiene_iva','total_retiene_islr','is_after','client'));
         }else{
             
             return redirect('receipt/receipt')->withDanger('El Recibo no existe');
         } 
         
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



    public function index_accounts_receivable($typeperson,$id_client_or_vendor = null)
    {        

        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $vendor = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id_client_or_vendor)){
                    $client    = Condominiums::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }else if (isset($typeperson) && $typeperson == 'Vendedor'){
                if(isset($id_client_or_vendor)){
                    $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }
            
            return view('admin.receipt.index_accounts_receivable',compact('client','datenow','typeperson','vendor'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo');
        }
    }


    public function store_accounts_receivable(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $typeinvoice = request('typeinvoice');
        $coin = request('coin');
        $client = null;
        $vendor = null;
        $typeperson = 'ninguno';

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Condominiums::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
                $id_client_or_vendor = $id_client;
            }
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $typeperson = 'Vendedor';
                $id_client_or_vendor = $vendor;
            }
        }

        return view('admin.receipt.index_accounts_receivable',compact('coin','typeinvoice','date_end','client','vendor','typeperson'));
    }

    function accounts_receivable_pdf($coin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }
        
        $period = $date->format('Y'); 
        

        if(isset($typeperson) && ($typeperson == 'Cliente')){
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_delivery_note','<>',null)
                    ->where('receipts.date_billing',null)
                    
                    ->where('receipts.id_client',$id_client_or_vendor)
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_billing','<>',null)
                    ->where('receipts.id_client',$id_client_or_vendor)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                         ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                        ->whereIn('receipts.status',['P'])
                                        ->where('receipts.type',['F'])
                                        ->where('receipts.amount','<>',null)
                                        ->where('receipts.date_quotation','<=',$date_consult)
                                        ->where('receipts.id_client',$id_client_or_vendor)
                                        
                                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                                        ->orderBy('receipts.date_quotation','desc')
                                        ->get();
                }
            }else{
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_delivery_note','<>',null)
                    ->where('receipts.date_billing',null)
                    ->where('receipts.id_client',$id_client_or_vendor)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_billing','<>',null)
                    ->where('receipts.id_client',$id_client_or_vendor)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                         ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                        ->whereIn('receipts.status',['P'])
                                        ->where('receipts.type',['F'])
                                        ->where('receipts.amount','<>',null)
                                        ->where('receipts.date_quotation','<=',$date_consult)
                                        ->where('receipts.id_client',$id_client_or_vendor)
                                        
                                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                                        ->orderBy('receipts.date_quotation','desc')
                                        ->get();
                }
            }
        }else if(isset($typeperson) && $typeperson == 'Vendedor'){
            

        }else{
            
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_delivery_note','<>',null)
                    ->where('receipts.date_billing',null)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                     ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_billing','<>',null)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_billing','desc')
                    ->get();
                }else
                {
                   
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                        ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                        ->whereIn('receipts.status',['P'])
                                        ->where('receipts.type',['F'])
                                        ->where('receipts.amount','<>',null)
                                        ->where('receipts.date_quotation','<=',$date_consult)
                                        
                                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                                        ->orderBy('receipts.date_quotation','desc')
                                        ->get();

                    
                }
            }else{
                
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                    ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_delivery_note','<>',null)
                    ->where('receipts.date_billing',null)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                    ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['P'])
                    ->where('receipts.type',['F'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.date_billing','<>',null)
                    
                    ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                        ->leftjoin('condominiums', 'condominiums.id','=','receipts.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                        ->whereIn('receipts.status',['P'])
                                        ->where('receipts.type',['F'])
                                        ->where('receipts.amount','<>',null)
                                        ->where('receipts.date_quotation','<=',$date_consult)
                                        
                                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                        ->groupBy('receipts.date_billing','condominiums.type_code','condominiums.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','condominiums.name','receipts.amount','receipts.amount_with_iva')
                                        ->orderBy('receipts.date_quotation','desc')
                                        ->get();
                }
            }
        }
        
        $pdf = $pdf->loadView('admin.receipt.accounts_receivable',compact('coin','quotations','datenow','date_end'));
        return $pdf->stream();
                 
    }


    ///RECIBOS DE CONDOMINIO PDF//////////////////////////////////////////////////////////////

    public function index_accounts_receivable_receipt($typeperson,$id_client_or_vendor = null)
    {        

        //$userAccess = new UserAccessController();

        //if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $vendor = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id_client_or_vendor)){
                    $client    = Owners::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }else if (isset($typeperson) && $typeperson == 'Vendedor'){
                if(isset($id_client_or_vendor)){
                    $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }
            
            return view('admin.receipt.index_accounts_receivable_receipt',compact('client','datenow','typeperson','vendor'));

    }


    public function store_accounts_receivable_receipt(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $typeinvoice = request('typeinvoice');
        $coin = request('coin');
        $client = null;
        $vendor = null;
        $typeperson = 'ninguno';

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Owners::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
                $id_client_or_vendor = $id_client;
            }
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $typeperson = 'Vendedor';
                $id_client_or_vendor = $vendor;
            }
        }

        return view('admin.receipt.index_accounts_receivable_receipt',compact('coin','typeinvoice','date_end','client','vendor','typeperson'));
    }

    function accounts_receivable_pdf_receipt($coin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }
        
        $period = $date->format('Y'); 
        
        if (Auth::user()->role_id  == '11'){ //////////////////////////propietario/////////////////////////////////////
            $email = Auth::user()->email;
            $id_owner = Owners::on(Auth::user()->database_name)->where('email','=',$email)->get()->first();
       
            if(isset($typeperson) && ($typeperson == 'Cliente')){
                if(isset($coin) && $coin == 'bolivares'){
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->where('receipts.id_client',$id_client_or_vendor)
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                             ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.id_client','=',$id_owner->id)
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            ->where('receipts.id_client',$id_client_or_vendor)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }else{
                    //PARA CUANDO EL REPORTE ESTE EN DOLARES
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                             ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.id_client','=',$id_owner->id)
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            ->where('receipts.id_client',$id_client_or_vendor)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }
            }else if(isset($typeperson) && $typeperson == 'Vendedor'){
                
    
            }else{
                
                if(isset($coin) && $coin == 'bolivares'){
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                       
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                            ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.id_client','=',$id_owner->id)
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
    
                        
                    }
                }else{
                    
                    //PARA CUANDO EL REPORTE ESTE EN DOLARES
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                        ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                        ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.id_client','=',$id_owner->id)
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                            ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.id_client','=',$id_owner->id)
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }
            }
       
        } else { //////////////////////////normal/////////////////////////////////////

            if(isset($typeperson) && ($typeperson == 'Cliente')){
                if(isset($coin) && $coin == 'bolivares'){
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->where('receipts.id_client',$id_client_or_vendor)
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','condominiums.name as name_client','condominiums.type_code','condominiums.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                             ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            ->where('receipts.id_client',$id_client_or_vendor)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }else{
                    //PARA CUANDO EL REPORTE ESTE EN DOLARES
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        ->where('receipts.id_client',$id_client_or_vendor)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                             ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            ->where('receipts.id_client',$id_client_or_vendor)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }
            }else if(isset($typeperson) && $typeperson == 'Vendedor'){
                
    
            }else{
                
                if(isset($coin) && $coin == 'bolivares'){
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                         ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                       
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                            ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
    
                        
                    }
                }else{
                    
                    //PARA CUANDO EL REPORTE ESTE EN DOLARES
                    if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                        ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_delivery_note','<>',null)
                        ->where('receipts.date_billing',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_delivery_note','desc')
                        ->get();
                    }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                        ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                        ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                        ->whereIn('receipts.status',['P'])
                        ->where('receipts.type',['R'])
                        ->where('receipts.amount','<>',null)
                        ->where('receipts.date_quotation','<=',$date_consult)
                        ->where('receipts.date_billing','<>',null)
                        
                        ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                        ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                        ->orderBy('receipts.date_billing','desc')
                        ->get();
                    }else
                    {
                        $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                            ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                            ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                            ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                            ->whereIn('receipts.status',['P'])
                                            ->where('receipts.type',['R'])
                                            ->where('receipts.amount','<>',null)
                                            ->where('receipts.date_quotation','<=',$date_consult)
                                            
                                            ->select('receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                            ->groupBy('receipts.date_billing','receipts.observation','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                            ->orderBy('receipts.date_quotation','desc')
                                            ->get();
                    }
                }
            }

            
        }   // fin del else propietario         
       
        
        $pdf = $pdf->loadView('admin.receipt.accounts_receivable_receipt',compact('coin','quotations','datenow','date_end'));
        return $pdf->stream();
                 
    }


/**************************** */

  ///RECIBOS DE CONDOMINIO RESUMEN PDF//////////////////////////////////////////////////////////////

  public function index_accounts_receivable_receipt_resumen($typeperson,$id_client_or_vendor = null)
  {        

      //$userAccess = new UserAccessController();

      //if($userAccess->validate_user_access($this->modulo)){
          $date = Carbon::now();
          $datenow = $date->format('Y-m-d');   
          $client = null; 
          $vendor = null; 


          if(isset($typeperson) && $typeperson == 'Cliente'){
              if(isset($id_client_or_vendor)){
                  $client    = Owners::on(Auth::user()->database_name)->find($id_client_or_vendor);
              }
          }else if (isset($typeperson) && $typeperson == 'Vendedor'){
              if(isset($id_client_or_vendor)){
                  $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
              }
          }
          
          return view('admin.receipt.index_accounts_receivable_receipt_resumen',compact('client','datenow','typeperson','vendor'));

  }


  public function store_accounts_receivable_receipt_resumen(Request $request)
  {
      
      $date_end = request('date_end');
      $type = request('type');
      $id_client = request('id_client');
      $id_vendor = request('id_vendor');
      $typeinvoice = request('typeinvoice');
      $coin = request('coin');
      $client = null;
      $vendor = null;
      $typeperson = 'ninguno';

      if($type != 'todo'){
          if(isset($id_client)){
              $client    = Owners::on(Auth::user()->database_name)->find($id_client);
              $typeperson = 'Cliente';
              $id_client_or_vendor = $id_client;
          }
          if(isset($id_vendor)){
              $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
              $typeperson = 'Vendedor';
              $id_client_or_vendor = $vendor;
          }
      }

      return view('admin.receipt.index_accounts_receivable_receipt_resumen',compact('coin','typeinvoice','date_end','client','vendor','typeperson'));
  }

  function accounts_receivable_pdf_receipt_resumen($coin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
  {
      
      $pdf = App::make('dompdf.wrapper');
      $quotations = null;
      
      $date = Carbon::now();
      $datenow = $date->format('d-m-Y'); 
      if(empty($date_end)){
          $date_end = $datenow;

          $date_consult = $date->format('Y-m-d'); 
      }else{
          $date_end = Carbon::parse($date_end)->format('d-m-Y');

          $date_consult = Carbon::parse($date_end)->format('Y-m-d');
      }
      
      $period = $date->format('Y'); 
      
        if (Auth::user()->role_id  == '11'){ //////////////////////////propietario/////////////////////////////////////
          $email = Auth::user()->email;
          $id_owner = Owners::on(Auth::user()->database_name)->where('email','=',$email)->get()->first();
     
          if(isset($typeperson) && ($typeperson == 'Cliente')){
              if(isset($coin) && $coin == 'bolivares'){
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                       ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.id_client','=',$id_owner->id)
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->where('receipts.date_delivery_note','<>',null)
                      ->where('receipts.date_billing',null)
                      ->where('receipts.id_client',$id_client_or_vendor)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')
                      ->get();
                  }else {
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                           ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.id_client','=',$id_owner->id)
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->where('receipts.id_client',$id_client_or_vendor)  
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }else{
                  //PARA CUANDO EL REPORTE ESTE EN DOLARES
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                       ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.id_client','=',$id_owner->id)
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->where('receipts.date_delivery_note','<>',null)
                      ->where('receipts.date_billing',null)
                      ->where('receipts.id_client',$id_client_or_vendor)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')

                      ->get();
                  }else {
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                           ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.id_client','=',$id_owner->id)
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->where('receipts.id_client',$id_client_or_vendor)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }
          }else if(isset($typeperson) && $typeperson == 'Vendedor'){
              
  
          }else{
              
              if(isset($coin) && $coin == 'bolivares'){
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                       ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.id_client','=',$id_owner->id)
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->where('receipts.date_delivery_note','<>',null)
                      ->where('receipts.date_billing',null)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')
                      ->get();
                  }else {
                     
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                          ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.id_client','=',$id_owner->id)
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
  
                      
                  }
              }else{
                  
                  //PARA CUANDO EL REPORTE ESTE EN DOLARES
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                      ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.id_client','=',$id_owner->id)
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->where('receipts.date_delivery_note','<>',null)
                      ->where('receipts.date_billing',null)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')
                      ->get();
                  }else {
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                          ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.id_client','=',$id_owner->id)
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }
          }
        /////////////////////////////////////fin propietario///////////////////////////////////////////
      } else { //////////////////////////Administrador//////////////////////////////////////////////////

          if(isset($typeperson) && ($typeperson == 'Cliente')){
              if(isset($coin) && $coin == 'bolivares'){
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){

                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                    ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['C'])
                    ->where('receipts.type',['R'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->where('receipts.id_client',$id_client_or_vendor)
                    ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_quotation','desc')
                    ->get();

                  }else {
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                           ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->where('receipts.id_client',$id_client_or_vendor)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }else{
                  //PARA CUANDO EL REPORTE ESTE EN DOLARES
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                       ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->where('receipts.date_delivery_note','<>',null)
                      ->where('receipts.date_billing',null)
                      ->where('receipts.id_client',$id_client_or_vendor)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')
                      ->get();
                  }else {
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                           ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->where('receipts.id_client',$id_client_or_vendor)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }
          }else if(isset($typeperson) && $typeperson == 'Vendedor'){
              
  
          }else{
              
              if(isset($coin) && $coin == 'bolivares'){
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                      
                    $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                    ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                    ->whereIn('receipts.status',['C'])
                    ->where('receipts.type',['R'])
                    ->where('receipts.amount','<>',null)
                    ->where('receipts.date_quotation','<=',$date_consult)
                    ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                    ->orderBy('receipts.date_quotation','desc')
                    ->get();

                  }else {
                     
                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                          ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
  
                      
                  }
              }else{
                  
                  //PARA CUANDO EL REPORTE ESTE EN DOLARES
                  if(isset($typeinvoice) && ($typeinvoice == 'notas')){

                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                      ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                      ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                      ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                      ->whereIn('receipts.status',['C'])
                      ->where('receipts.type',['R'])
                      ->where('receipts.amount','<>',null)
                      ->where('receipts.date_quotation','<=',$date_consult)
                      ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                      ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                      ->orderBy('receipts.date_quotation','desc')
                      ->get();

                  }else{

                      $quotations = DB::connection(Auth::user()->database_name)->table('receipts')
                                          ->leftjoin('owners', 'owners.id','=','receipts.id_client')
                                          ->leftjoin('vendors', 'vendors.id','=','receipts.id_vendor')
                                          ->leftjoin('anticipos', 'anticipos.id_quotation','=','receipts.id')
                                          ->whereIn('receipts.status',['P'])
                                          ->where('receipts.type',['R'])
                                          ->where('receipts.amount','<>',null)
                                          ->where('receipts.date_quotation','<=',$date_consult)
                                          ->select('receipts.verified','receipts.status','receipts.date_billing','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name as name_vendor','receipts.observation','owners.direction','owners.name as name_client','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.amount','receipts.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                          ->groupBy('receipts.verified','receipts.status','receipts.date_billing','receipts.observation','owners.personcontact','owners.direction','owners.type_code','receipts.observation','owners.direction','owners.cedula_rif','receipts.date_delivery_note','receipts.retencion_islr','receipts.retencion_iva','receipts.bcv','receipts.number_invoice','receipts.number_delivery_note','receipts.date_quotation','receipts.id','receipts.serie','vendors.name','receipts.observation','owners.direction','owners.name','receipts.amount','receipts.amount_with_iva')
                                          ->orderBy('receipts.date_quotation','desc')
                                          ->get();
                  }
              }
          }

          
      }   // fin del else propietario         
     
      
      $pdf = $pdf->loadView('admin.receipt.accounts_receivable_receipt_resumen',compact('coin','quotations','datenow','date_end'));
      return $pdf->stream();
               
  }

}
