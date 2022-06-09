<?php

namespace App\Http\Controllers;

use App;
use App\Account;
use App\Anticipo;
use App\AnticipoQuotation;
use App\Branch;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\Exports\ProductsExport;
use App\HeaderVoucher;
use App\HistorialQuotation;
use App\Http\Controllers\Historial\HistorialQuotationController;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Http\Controllers\Validations\FacturaValidationController;
use App\Inventory;
use App\InventoryHistories;
use App\Multipayment;
use App\Product;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use App\Transport;
use App\UserAccess;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class QuotationController extends Controller
{
    public $userAccess;
    public $modulo = 'Cotizacion';

 
    public function __construct(){

       $this->middleware('auth');
       $this->userAccess = new UserAccessController();
      
   }

   public function index($coin = null)
   {
        
        if($this->userAccess->validate_user_access($this->modulo)){
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->where('date_order','=',null)
            ->get();

            $company = Company::on(Auth::user()->database_name)->find(1);

            $clients = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            return view('admin.quotations.index',compact('quotations','company','coin','clients','datenow'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

      
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    
    public function createquotation($type = null)
    {
        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');   
        
        $user   =   auth()->user();


        if(isset($user->id_branch)){
            $user_branch  = Branch::on(Auth::user()->database_name)->find($user->id_branch);
        }else{
            $user_branch  = null;
        }

        $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();

        return view('admin.quotations.createquotation',compact('user_branch','branches','datenow','transports','type','user'));
   
    }

    public function createquotationclient($id_client,$type = null)
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

            $user   =   auth()->user();

            if(isset($user->id_branch)){
                $user_branch  = Branch::on(Auth::user()->database_name)->find($user->id_branch);
            }else{
                $user_branch  = null;
            }
    
            $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();

            
            return view('admin.quotations.createquotation',compact('user_branch','branches','client','datenow','transports','type','user'));

        }else{
            return redirect('/quotations/index')->withDanger('El Cliente no existe');
        } 
    }

    public function createquotationvendor($id_client,$id_vendor,$type = null)
    {
        $client = null;
                
        if(isset($id_client)){
            $client = Client::on(Auth::user()->database_name)->find($id_client);
        }
        if(isset($client)){

            $vendor = null;
                
            if(isset($id_vendor)){
                $vendor = Vendor::on(Auth::user()->database_name)->find($id_vendor);
            }
            if(isset($vendor)){

                /* $vendors     = Vendor::on(Auth::user()->database_name)->get();*/

                $transports     = Transport::on(Auth::user()->database_name)->get();
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');    

                $user   =   auth()->user();

                if(isset($user->id_branch)){
                    $user_branch  = Branch::on(Auth::user()->database_name)->find($user->id_branch);
                }else{
                    $user_branch  = null;
                }
        
                $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();

                return view('admin.quotations.createquotation',compact('user_branch','branches','client','vendor','datenow','transports','type','user'));

            }else{
                return redirect('/quotations/index')->withDanger('El Vendedor no existe');
            } 

        }else{
            return redirect('/quotations/index')->withDanger('El Cliente no existe');
        } 
    }



    public function create($id_quotation,$coin,$type = null)
    {
        
        $user   =   auth()->user();

        if(isset($user->id_branch)){
            $user_branch  = Branch::on(Auth::user()->database_name)->find($user->id_branch);
        }else{
            $user_branch  = null;
        }

        $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();



        if($this->userAccess->validate_user_access($this->modulo)){
            $quotation = null;
                
            if(isset($id_quotation)){
                $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
            }

            if(isset($quotation) && $quotation->date_billing == null){
                //$inventories_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                ->where('quotation_products.id_quotation',$id_quotation)
                                ->whereIn('quotation_products.status',['1','C'])
                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as quotation_products_id','products.code_comercial as code','quotation_products.discount as discount',
                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva')
                                ->orderBy('id','desc')
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
                
        
                return view('admin.quotations.create',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','type','company','branches','user_branch'));
            }else{
                return redirect('/quotations/index')->withDanger('No es posible ver esta cotizacion fall');
            } 
            
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

    }


    public function createproduct($id_quotation,$coin,$id_inventory,$type = null)
    {
        $quotation = null;
                
        if(isset($id_quotation)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);
        }

        if(isset($quotation) && ($quotation->status == 1)){
            //$product_quotations = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->get();
                $product = null;
                $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                                ->where('quotation_products.id_quotation',$id_quotation)
                                ->whereIn('quotation_products.status',['1','C'])
                                ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as quotation_products_id','products.code_comercial as code','quotation_products.discount as discount',
                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva')
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
                   
                    return view('admin.quotations.create',compact('bcv_quotation_product','quotation','inventories_quotations','inventory','bcv','datenow','coin','type'));

                }else{
                    return redirect('/quotations/index')->withDanger('El Producto no existe');
                } 
        }else{
            return redirect('/quotations/index')->withDanger('La cotizacion no existe');
        } 

    }

    public function selectproduct($id_quotation,$coin,$type,$type_quotation = null)
    {

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
  
    

            
        $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);

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
            
            return view('admin.quotations.selectservice',compact('type','services','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
        }
    
        return view('admin.quotations.selectinventary',compact('type','inventories','id_quotation','coin','bcv','bcv_quotation_product','type_quotation'));
    }


    public function createvendor($id_product,$id_vendor)
    {
        $vendor = null;
        
        if(isset($id_vendor)){
            $vendor = vendor::on(Auth::user()->database_name)->find($id_vendor);
        }

        $clients     = Client::on(Auth::user()->database_name)->get();
    
        $vendors     = Vendor::on(Auth::user()->database_name)->get();

        $transports     = Transport::on(Auth::user()->database_name)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        return view('admin.quotations.create',compact('clients','vendors','datenow','transports','vendor'));
    }

    public function selectvendor($id_client,$type = null)
    {
        if($id_client != -1){

            $vendors     = vendor::on(Auth::user()->database_name)->get();
    
            return view('admin.quotations.selectvendor',compact('vendors','id_client','type'));

        }else{
            return redirect('/quotations/registerquotation')->withDanger('Seleccione un Cliente primero');
        }
        
    }

    public function selectclientQuotation(Request $request,$id)
    {
        $clients     = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
        $coin = $request->coin2;

        $id_quotation = $id;

        return view('admin.quotations.selectclientQuotation',compact('clients','id_quotation','coin'));
    }

    public function updateClientQuotation($id_quotation,$id_client,$coin)
    {
        $var = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        $var->id_client = $id_client;
       
        $var->save();

        return redirect('/quotations/register/'.$id_quotation.'/'.$coin.'')->withSuccess('Cliente Actualizado Con Exito !!');
       
    }


    public function selectclient($type = null)
    {
        $clients     = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        
    
        return view('admin.quotations.selectclient',compact('clients','type'));
    }

    public function store(Request $request)
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
                    /*$var->date_billing = request('date_quotation');*/
                   /* $var = $validateFactura->validateNumberInvoice();*/
                }

               

                if($id_transport != '-1'){
                    $var->id_transport = request('id_transport');
                }
                
                $var->id_user = request('id_user');
                $var->serie = request('serie');
                $var->date_quotation = request('date_quotation');
        
                $var->observation = request('observation');
                $var->note = request('note');

                $var->id_branch = request('id_branch');

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


                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($var,"quotation","Creó Cotización");


                return redirect('quotations/register/'.$var->id.'/bolivares/'.$type);

            
        }else{
            return redirect('/quotations/registerquotation')->withDanger('Debe Buscar un Cliente');
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
   
    public function edit($id)
    {
        $quotation = quotation::on(Auth::user()->database_name)->find($id);
    
        return view('admin.quotations.edit',compact('quotation'));
    
    }
    public function editquotationproduct($id,$coin = null)
    {
            $quotation_product = QuotationProduct::on(Auth::user()->database_name)->find($id);
        
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

                return view('admin.quotations.edit_product',compact('rate','coin','quotation_product','inventory','bcv'));
            }else{
                return redirect('/quotations/index')->withDanger('No se Encontro el Producto!');
            }
        
        
    
    }
    
    public function update(Request $request, $id)
    {

        $vars =  Quotation::on(Auth::user()->database_name)->find($id);

        $vars_status = $vars->status;
        $vars_exento = $vars->exento;
        $vars_islr = $vars->islr;
    
        $data = request()->validate([
            
        
            'segment_id'         =>'required',
            'sub_segment_id'         =>'required',
            'unit_of_measure_id'         =>'required',


            'type'         =>'required',
            'description'         =>'required',
        
            'price'         =>'required',
            'price_buy'         =>'required',
            'cost_average'         =>'required',

            'money'         =>'required',
        
            'special_impuesto'         =>'required',
            'status'         =>'required',
        
        ]);

        $var = Quotation::on(Auth::user()->database_name)->findOrFail($id);

        $var->segment_id = request('segment_id');
        $var->serie = request('sub_segment_id');
        $var->unit_of_measure_id = request('unit_of_measure_id');

        $var->code_comercial = request('code_comercial');
        $var->type = request('type');
        $var->description = request('description');
        
        $var->price = request('price');
        $var->price_buy = request('price_buy');
    
        $var->cost_average = request('cost_average');
        $var->photo_quotation = request('photo_quotation');

        $var->money = request('money');


        $var->special_impuesto = request('special_impuesto');

        if(request('exento') == null){
            $var->exento = "0";
        }else{
            $var->exento = "1";
        }
        if(request('islr') == null){
            $var->islr = "0";
        }else{
            $var->islr = "1";
        }
    

        if(request('status') == null){
            $var->status = $vars_status;
        }else{
            $var->status = request('status');
        }
    
        $var->save();

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"quotation","Actualizó la Cotización");


        return redirect('/quotations/index')->withSuccess('Actualizacion Exitosa!');
    }

    public function pdfQuotations(Request $request)
    {
        $date_begin = request('date_begin');
        $date_end = request('date_end');
 
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');
 
        $pdf = App::make('dompdf.wrapper');
 
        $id_client = request('id_client');
 
        $coin = request('coin');
        
        $company = Company::on(Auth::user()->database_name)->find(1);

        if(isset($id_client)){
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->where('date_order','=',null)
            ->where('id_client',$id_client)
            ->whereBetween('date_quotation', [$date_begin, $date_end])->get();
        }else{
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->where('date_order','=',null)
            ->whereBetween('date_quotation', [$date_begin, $date_end])->get();
        }

        $pdf = $pdf->loadView('admin.quotations.pdfQuotations',compact('company','quotations'
        ,'datenow','date_begin','date_end'));

        return $pdf->stream();
    }


    public function updateQuotation(Request $request, $id)
    {
      

        $var = Quotation::on(Auth::user()->database_name)->findOrFail($id);

        $var->date_quotation = request('date_quotation');
        $var->serie = request('serie');

        $var->observation = request('observation');
        $var->note = request('note');
       
    
        $var->save();

        $type = request('type_f');

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"quotation","Actualizó la Cotización");


        return redirect('/quotations/register/'.$var->id.'/'.$request->coin2.'/'.$type)->withSuccess('Actualizacion Exitosa!');
    }

        

    public function updatequotationproduct(Request $request, $id)
    { 

           
            $data = request()->validate([
                
                'amount'         =>'required',
                'discount'         =>'required',
            
            ]);

            
        
            $var = QuotationProduct::on(Auth::user()->database_name)->findOrFail($id);

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

            if($value_return != 'exito'){
                return redirect('quotations/quotationproduct/'.$var->id.'/'.$coin.'/edit')->withDanger('La cantidad de este producto excede a la cantidad puesta en inventario! ');
            }

        
            $var->save();

            
            if(isset($var->quotations['date_delivery_note']) || isset($var->quotations['date_billing'])){
                $this->recalculateQuotation($var->id_quotation);
            }

            $global = new GlobalController();
            
            if(isset($var->quotations['date_delivery_note'])) {
                $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
                ->where('id_quotation', '=', $var->id_quotation)
                ->where('id', '=',  $var->id)
                ->get(); // Conteo de Productos para incluiro en el historial de inventario
                foreach($quotation_products as $det_products){ // guardado historial de inventario 
                $global->transaction_inv('aju_nota',$det_products->id_inventory,'pruebaf',$det_products->amount,$det_products->price,null,1,1,0,$det_products->id_inventory_histories,$det_products->id,$var->id_quotation);
                }
            }

            $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($var,"quotation_product","Actualizó el Producto: ".$var->inventories['code']."/ 
            Precio Viejo: ".number_format($price_old, 2, ',', '.')." Cantidad: ".$amount_old."/ Precio Nuevo: ".number_format($var->price, 2, ',', '.')." Cantidad: ".$var->amount);
        
            
            return redirect('/quotations/register/'.$var->id_quotation.'/'.$coin.'')->withSuccess('Actualizacion Exitosa!');
        
    }


    public function refreshrate($id_quotation,$coin,$rate)
    { 
        $sin_formato_rate = str_replace(',', '.', str_replace('.', '', $rate));

        $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);

        QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$id_quotation)
                                ->update(['rate' => $sin_formato_rate]);
    

        Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)
                                ->update(['bcv' => $sin_formato_rate]);

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($quotation,"quotation","Actualizó la tasa: ".$rate." / tasa antigua: ".number_format($quotation->bcv, 2, ',', '.'));
        
        return redirect('/quotations/register/'.$id_quotation.'/'.$coin.'')->withSuccess('Actualizacion de Tasa Exitosa!');
    
    }

 
    public function deleteProduct(Request $request)
    {
        
        $quotation_product = QuotationProduct::on(Auth::user()->database_name)->find(request('id_quotation_product_modal')); 
        
        if(isset($quotation_product) && $quotation_product->status == "C"){
            
                QuotationProduct::on(Auth::user()->database_name)
                ->join('products','products.id','quotation_products.id_inventory')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('quotation_products.id',$quotation_product->id)
                ->update(['quotation_products.status' => 'X']);
               
                $this->recalculateQuotation($quotation_product->id_quotation);
        }else{
            
            $quotation_product->status = 'X'; 
            $quotation_product->save(); 
        }

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($quotation_product,"quotation_product","Se eliminó un Producto");

        return redirect('/quotations/register/'.request('id_quotation_modal').'/'.request('coin_modal').'')->withDanger('Eliminacion exitosa!!');
        
    }

    public function recalculateQuotation($id_quotation)
    {
        $quotation = null;
                 
        if(isset($id_quotation)){
             $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
        }else{
            return redirect('/quotations/index')->withDanger('No llega el numero de la cotizacion');
        } 
 
         if(isset($quotation)){
           
            $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                            ->join('quotation_products', 'inventories.id', '=', 'quotation_products.id_inventory')
                                                            ->where('quotation_products.id_quotation',$quotation->id)
                                                            ->whereIn('quotation_products.status',['1','C'])
                                                            ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.discount as discount',
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

    public function deleteQuotation(Request $request)
    {
        
        $quotation = Quotation::on(Auth::user()->database_name)->find(request('id_quotation_modal')); 

        $global = new GlobalController();
        $global->deleteAllProducts($quotation->id);

        //Anticipo::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->delete();

        $quotation->delete(); 

        $historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($quotation,"quotation","Se eliminó la cotización");

        return redirect('/quotations/index')->withDanger('Eliminacion exitosa!!');
        
    }

    public function reversar_quotation(Request $request)
    { 
       
        $id_quotation = $request->id_quotation_modal;

        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

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

                QuotationPayment::on(Auth::user()->database_name)
                                ->where('id_quotation',$quotation->id)
                                ->update(['status' => 'X']);
    
                $quotation->status = 'X';
                $quotation->save();


                $quotation_products = DB::connection(Auth::user()->database_name)->table('quotation_products')
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

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($quotation,"quotation","Se Reversó la Factura");
            }

        }else{
            
            return redirect('/quotations/facturado/'.$quotation->id.'/bolivares/'.$exist_multipayment->id_header.'');
        }
       
        return redirect('invoices')->withSuccess('Reverso de Factura Exitosa!');

    }

    public function reversar_quotation_multipayment($id_quotation,$id_header){

        
        if(isset($id_header)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);

            //aqui reversamos todo el movimiento del multipago
            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id','=',$id_header)
            ->update(['detail_vouchers.status' => 'X' , 'header_vouchers.status' => 'X']);

            //aqui se cambia el status de los pagos
            DB::connection(Auth::user()->database_name)->table('multipayments')
            ->join('quotation_payments', 'quotation_payments.id_quotation','=','multipayments.id_quotation')
            ->where('multipayments.id_header','=',$id_header)
            ->update(['quotation_payments.status' => 'X']);

            //aqui aumentamos el inventario y cambiamos el status de los productos que se reversaron
            DB::connection(Auth::user()->database_name)->table('multipayments')
                ->join('quotation_products', 'quotation_products.id_quotation','=','multipayments.id_quotation')
                ->join('inventories','inventories.id','quotation_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('multipayments.id_header','=',$id_header)
                ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount') ,
                        'quotation_products.status' => 'X']);
    

            //aqui le cambiamos el status a todas las facturas a X de reversado
            Multipayment::on(Auth::user()->database_name)
            ->join('quotations', 'quotations.id','=','multipayments.id_quotation')
            ->where('id_header',$id_header)->update(['quotations.status' => 'X']);

            Multipayment::on(Auth::user()->database_name)->where('id_header',$id_header)->delete();



            $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($quotation,"quotation","Se Reversó MultiFactura");

            return redirect('invoices')->withSuccess('Reverso de Facturas Multipago Exitosa!');
        }else{
            return redirect('invoices')->withDanger('No se pudo reversar las facturas');
        }
        
    }

    public function reversar_quotation_multipayment_with_id($id_quotation,$id_header){

        
        if(isset($id_header)){
            $quotation = Quotation::on(Auth::user()->database_name)->find($id_quotation);

            //aqui reversamos todo el movimiento del multipago
            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id','=',$id_header)
            ->update(['detail_vouchers.status' => 'X' , 'header_vouchers.status' => 'X']);

            //aqui se cambia el status de los pagos
            DB::connection(Auth::user()->database_name)->table('multipayments')
            ->join('quotation_payments', 'quotation_payments.id_quotation','=','multipayments.id_quotation')
            ->where('multipayments.id_header','=',$id_header)
            ->update(['quotation_payments.status' => 'X']);

            //aqui aumentamos el inventario y cambiamos el status de los productos que se reversaron
            DB::connection(Auth::user()->database_name)->table('multipayments')
                ->join('quotation_products', 'quotation_products.id_quotation','=','multipayments.id_quotation')
                ->join('inventories','inventories.id','quotation_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('multipayments.id_header','=',$id_header)
                ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount') ,
                        'quotation_products.status' => 'X']);
    

            //aqui le cambiamos el status a todas las facturas a X de reversado
            Multipayment::on(Auth::user()->database_name)
            ->join('quotations', 'quotations.id','=','multipayments.id_quotation')
            ->where('id_header',$id_header)->update(['quotations.status' => 'X']);

            Multipayment::on(Auth::user()->database_name)->where('id_header',$id_header)->delete();



            $historial_quotation = new HistorialQuotationController();

            $historial_quotation->registerAction($quotation,"quotation","Se Reversó MultiFactura");
        }
        
    }

    public function reversar_quotation_with_id($id_invoice)
    { 
       
        $id_quotation = $id_invoice;

        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        $exist_multipayment = Multipayment::on(Auth::user()->database_name)
                            ->where('id_quotation',$quotation->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  
                            
        if(empty($exist_multipayment)){
            if($quotation != 'X'){
                $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$id_quotation)
                ->update(['status' => 'X']);
    
                
                $global = new GlobalController();
                $global->deleteAllProducts($quotation->id);

                QuotationPayment::on(Auth::user()->database_name)
                                ->where('id_quotation',$quotation->id)
                                ->update(['status' => 'X']);
    
                $quotation->status = 'X';
                $quotation->save();



                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($quotation->anticipo))&& ($quotation->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipo($datenow,$quotation->id_client,$account_anticipo->id,"bolivares",
                    $quotation->anticipo,$quotation->bcv,"reverso factura N°".$quotation->number_invoice);
                    
                }

                $historial_quotation = new HistorialQuotationController();

                $historial_quotation->registerAction($quotation,"quotation","Se Reversó la Factura");
            }
        }else{
            $this->reversar_quotation_multipayment_with_id($id_quotation,$exist_multipayment->id_header);
        }
       
      
    }

    public function listinventory(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                $respuesta = Product::on(Auth::user()->database_name)->where('code_comercial',$var)->where('status',1)->get();
                return response()->json($respuesta,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }




}