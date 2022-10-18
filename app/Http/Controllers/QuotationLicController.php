<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\DetailVoucher;
use App\Exports\ProductsExport;
use App\Inventory;
use App\Product;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\UserAccess;
use App\Quotation;
use App\QuotationPayment;
use App\QuotationProduct;
use App\Transport;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Driver;


class QuotationLicController extends Controller
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
            $quotations = Quotation::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
            ->where('date_billing','=',null)
            ->where('date_delivery_note','=',null)
            ->get();

        /*  $company = Company::on(Auth::user()->database_name)->find(1);

            $clients = Client::on(Auth::user()->database_name)->orderBy('name','asc')->get();

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');*/

            //return view('admin.quotationslic.index',compact('quotations','company','coin','clients','datenow'));
            return view('admin.quotationslic.index',compact('quotations'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
   
    }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function createquotation()
    {

        $date           = Carbon::now();
        $datenow        = $date->format('Y-m-d');

        $transports = Transport::on(Auth::user()->database_name)->get();
        $drivers = Driver::on(Auth::user()->database_name)->get();
        $company = Company::on(Auth::user()->database_name)->find(1);
       /* $last_number = Quotation::on(Auth::user()->database_name)
        ->where('number_invoice','<>',NULL)->orderBy('number_invoice','desc')->first();

        //Asigno un numero incrementando en 1
        if(empty($quotation->number_invoice)){
            if(isset($last_number)){
                $quotation->number_invoice = $last_number->number_invoice + 1;
            }else{
                $quotation->number_invoice = 1;
            }
        } */


        return view('admin.quotationslic.createquotation',compact('datenow','transports','drivers','company'));
    }

    public function createquotationclient($id_client)
    {
        $client = null;
        if(isset($id_client)){
            $client = Client::on(Auth::user()->database_name)->find($id_client);
        }
        if(isset($client)){

        /* $vendors     = Vendor::on(Auth::user()->database_name)->get();*/

            $transports     = Transport::on(Auth::user()->database_name)->get();
            $drivers = Driver::on(Auth::user()->database_name)->get();
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
            $company = Company::on(Auth::user()->database_name)->find(1);

            return view('admin.quotationslic.createquotation',compact('client','datenow','transports','drivers','company'));
        }else{
            return redirect('/quotationslic')->withDanger('El Cliente no existe');
        }
    }

    public function createquotationvendor($id_client,$id_vendor)
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
                $drivers        = Driver::on(Auth::user()->database_name)->get();
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');
                $company = Company::on(Auth::user()->database_name)->find(1);

                return view('admin.quotationslic.createquotation',compact('client','vendor','datenow','transports','drivers','company'));
            }else{
                return redirect('/quotationslic')->withDanger('El Vendedor no existe');
            }
        }else{
            return redirect('/quotationslic')->withDanger('El Cliente no existe');
        }
    }

    public function create($id_quotation,$coin)
    {
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
                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                                ->get(); 
                $inventories_quotationss = DB::connection(Auth::user()->database_name)->table('products')
                                ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
                    ->where('quotation_products.id_quotation',$quotation->id)
                    ->whereIn('quotation_products.status',['1','C'])
                    ->select('products.*','quotation_products.price as price','quotation_products.rate as rate','quotation_products.id as quotation_products_id','products.code_comercial as code','quotation_products.discount as discount',
                    'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                    ->get(); 


                $date = Carbon::now();
                $datenow = $date->format('Y-m-d');

                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController;  
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

                $company = Company::find(1);
                $tax_1   = $company->tax_1;
                $tax_3   = $company->iba_percibido_porc;

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

                $total= 0;
                $base_imponible= 0;
                $price_cost_total= 0;

                //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
                $total_retiene_iva     = 0;
                $retiene_iva           = 0;
                $total_retiene_islr    = 0;
                $total_retiene         = 0;
                $total_iva             = 0;
                $total_base_impo_pcb   = 0;
                $total_iva_pcb         = 0;
                $total_venta           = 0;
                $base_imponible_pcb    = $tax_3;
                $iva                   = $tax_1;
                $rate                  = $quotation->bcv;

                foreach($inventories_quotationss as $vars){

                    //Se calcula restandole el porcentaje de descuento (discount)
                    $percentage = (($vars->price * $vars->amount_quotation) * $vars->discount)/100;
                    $total += number_format(($vars->price * $vars->amount_quotation) - $percentage,2,".","");


                    if( $vars->retiene_iva_quotation == 1 ){
                        $total_retiene         = 0;
                        $total_base_impo_pcb   = 0;
                        $total_iva_pcb         = 0;
                        $total_iva             = 0;
                        $total_venta        += $vars->price * $vars->amount_quotation ;

                    }else{

                        $total_retiene         +=  number_format(($vars->price * $vars->amount_quotation) - $percentage,2,".","");
                        $total_iva             =  number_format($total_retiene * ($iva / 100),2,".","") ;
                        $total_base_impo_pcb   =  $total_retiene *($base_imponible_pcb /100) ;
                        $total_iva_pcb         =  $total_base_impo_pcb * ($iva /100);
                        $total_venta           =    $total_retiene + $total_iva + $total_iva_pcb;
                    }

                }
                return view('admin.quotationslic.create',compact('quotation','inventories_quotations','datenow','bcv','coin','bcv_quotation_product','total','rate','total_retiene','total_iva','total_base_impo_pcb','total_iva_pcb','total_venta','iva'));
            }else{
                return redirect('/quotationslic')->withDanger('No es posible ver esta cotizacion');
            }
    }



    public function createproduct($id_quotation,$coin,$id_inventory)
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
                                'quotation_products.amount as amount_quotation','quotation_products.retiene_iva as retiene_iva_quotation')
                                ->get(); 

                if(isset($id_inventory)){
                    $inventory = Product::on(Auth::user()->database_name)->find($id_inventory);
                }
                if(isset($inventory)){

                    $date = Carbon::now();
                    $datenow = $date->format('Y-m-d');
                   
                    $global = new GlobalController;

                    /*Revisa si la tasa de la empresa es automatica o fija*/
                    $company = Company::on(Auth::user()->database_name)->find(1);
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

                    return view('admin.quotationslic.create',compact('bcv_quotation_product','quotation','inventories_quotations','inventory','bcv','datenow','coin'));

                }else{
                    return redirect('/quotationslic')->withDanger('El Producto no existe');
                }
        }else{
            return redirect('/quotationslic')->withDanger('La cotizacion no existe');
        }

    }

    public function selectproduct($id_quotation,$coin,$type)
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

            return view('admin.quotationslic.selectservice',compact('type','services','id_quotation','coin','bcv','bcv_quotation_product'));
        }

        return view('admin.quotationslic.selectinventary',compact('type','inventories','id_quotation','coin','bcv','bcv_quotation_product'));
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

        $pdf = $pdf->loadView('admin.quotationslic.pdfQuotations',compact('company','quotations'
        ,'datenow','date_begin','date_end'));

        return $pdf->stream();
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

            return view('admin.quotationslic.create',compact('clients','vendors','datenow','transports','vendor'));
    }

    public function selectvendor($id_client)
    {
            if($id_client != -1){

                $vendors     = vendor::on(Auth::user()->database_name)->get();



                return view('admin.quotationslic.selectvendor',compact('vendors','id_client'));

            }else{
                return redirect('/quotationslic/registerquotation')->withDanger('Seleccione un Cliente primero');
            }


    }

    public function selectclient()
    {
        $clients     = Client::on(Auth::user()->database_name)->get();

        return view('admin.quotationslic.selectclient',compact('clients'));
    }


    /**
        * Store a newly created resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\Response
        */
    public function store(Request $request)
    {
        $id_client = request('id_client');
        $id_vendor = request('id_vendor',Null);

        if($id_vendor == '-1'){

            $id_vendor = 1;
        } else {
            $id_vendor = request('id_vendor');  
        }


        if($id_client != '-1'){

                $var = new Quotation();
                $var->setConnection(Auth::user()->database_name);
                $var->id_client = $id_client;
                $var->id_vendor = $id_vendor;
                $id_transport = request('id_transport');
                if($id_transport != '-1'){
                    $var->id_transport = request('id_transport');
                }
                $var->id_user               = request('id_user');
                $var->id_client             = $id_client;
                $var->id_vendor             = $id_vendor;
                $var->id_transport          = request('Transporte');
                $var->serie                 = request('serie');
                $var->date_quotation        = request('date_quotation');
                $var->observation           = request('observation');
                $var->note                  = request('note');
                $var->id_driver             = request('Conductor');
                $var->date_expiration       = request('Fecha_Vencimiento');
                $var->licence               = request('Licencia');
                $var->destiny               = request('Direccion_Destino');
                $var->delivery              = request('Direccion_Entrega');
                $var->iva_percibido         = request('Iva_Percibido');
                $var->serie                 = request('serie');
                $company = Company::on(Auth::user()->database_name)->find(1);
                $global = new GlobalController;
                //Si la taza es automatica
                if($company->tiporate_id == 1){
                    $bcv = $global->search_bcv();
                }else{
                    //si la tasa es fija
                    $bcv = $company->rate;
                }
                $var->bcv = $bcv;
                $var->coin = 'bolivares';
                $var->status =  1;
                $var->save();
                return redirect('quotationslic/register/'.$var->id.'/bolivares');
        }else{
            return redirect('/quotationslic/registerquotation')->withDanger('Debe Buscar un Cliente');
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
            $var->excento = 0;
        }else{
            $var->retiene_iva = true;
            $var->excento = 1;
        }

        $coin = request('coin');

        $quotation = Quotation::on(Auth::user()->database_name)->find($var->id_quotation);

        $var->rate = $quotation->bcv;

        if($var->id_inventory == -1){
            return redirect('quotationslic/register/'.$var->id_quotation.'')->withDanger('No se encontro el producto!');
        }

        $amount = request('amount');
        $cost = str_replace(',', '.', str_replace('.', '',request('cost')));


        $value_return = $this->check_amount($quotation->id,$var->id_inventory,$amount);

        if($value_return != 'exito'){
                return redirect('quotationslic/registerproduct/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger('La cantidad de este producto excede a la cantidad puesta en inventario!');
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
            return redirect('quotationslic/register/'.$var->id_quotation.'/'.$coin.'/'.$var->id_inventory.'')->withDanger('El descuento debe estar entre 0% y 100%!');
        }

        $var->status =  1;

        $var->save();

        return redirect('quotationslic/register/'.$var->id_quotation.'/'.$coin.'')->withSuccess('Producto agregado Exitosamente!');
    }
    /**
        * Display the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

    public function check_amount($id_quotation,$id_inventory,$amount_new)
    {
        $inventories_quotations = DB::connection(Auth::user()->database_name)
                ->table('products')
                ->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->where('inventories.id',$id_inventory)
                ->select('products.*')
                ->first();

        //si es un servicio no se chequea que posea inventario

        if(isset($inventories_quotations) && ($inventories_quotations->type == "MERCANCIA")){

            $global = new GlobalController;

            $sum_amount =  $global->consul_prod_invt($id_inventory);

            $total_in_quotation = $sum_amount + $amount_new;

            $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);

            if($inventory->amount >= $total_in_quotation){
                return "exito";
            }else{
                return "no_hay_cantidad_suficiente";
            }
        }else{
            return "exito";
        }




    }


    public function show($id)
    {
        //
    }

    /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
    public function edit($id)
    {
        $quotation = Quotation::on(Auth::user()->database_name)->find($id);

        return view('admin.quotationslic.edit',compact('quotation'));

    }
    public function editquotationproduct($id,$coin = null)
    {
            $quotation_product = QuotationProduct::on(Auth::user()->database_name)->find($id);

            if(isset($quotation_product)){

                $global = new GlobalController;
                
                $inventory= Product::on(Auth::user()->database_name)->find($quotation_product->id_inventory);
               
                foreach ($inventory as $inventorie) {
                    $inventory->amount = $global->consul_prod_invt($quotation_product->id_inventory);        
                }

                $company = Company::on(Auth::user()->database_name)->find(1);
                
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



                return view('admin.quotationslic.edit_product',compact('rate','coin','quotation_product','inventory','bcv'));
            }else{
                return redirect('/quotationslic')->withDanger('No se Encontro el Producto!');
            }



    }


    /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
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
        $var->subsegment_id= request('sub_segment_id');
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

        return redirect('/quotationslic')->withSuccess('Actualizacion Exitosa!');
        }





        public function updatequotationproduct(Request $request, $id)
        {

            
            $data = request()->validate([

                'amount'         =>'required',
                'discount'         =>'required',

            ]);



            $var = QuotationProduct::on(Auth::user()->database_name)->findOrFail($id);

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


            $value_return = $this->check_amount($var->id_quotation,$var->id_inventory,$var->amount);


            $islr = request('islr');
            if($islr == null){
                $var->retiene_islr = false;
            }else{
                $var->retiene_islr = true;
            }

            $exento = request('exento');

            
            if($exento == null){
                $var->excento = 0; 
            }else{
                $var->excento = 1;        
            }

            if($exento == null){
                $var->retiene_iva = false;
            }else{
                $var->retiene_iva = true;
            }

            if($value_return != 'exito'){
                return redirect('quotationslic/quotationproduct/'.$var->id.'/'.$coin.'/edit')->withDanger('La cantidad de este producto excede a la cantidad puesta en inventario!');
           }


            $var->save();

            return redirect('/quotationslic/register/'.$var->id_quotation.'/'.$coin.'')->withSuccess('Actualizacion Exitosa de Detalle!');

        }


        public function refreshrate($id_quotation,$coin,$rate)
        {
            $sin_formato_rate = str_replace(',', '.',$rate);


            QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$id_quotation)
                                    ->update(['rate' => $sin_formato_rate]);


            Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)
                                    ->update(['bcv' => $sin_formato_rate]);



            return redirect('/quotationslic/register/'.$id_quotation.'/'.$coin.'')->withSuccess('Actualizacion de Tasa Exitosa!');

        }


        public function guardarcambios($id_quotation,$coin,$observation,$note,$serie2)
        {
           
              if ($observation == '-1'){
                $observation = '';
              }
              if ($note == '-1'){
                $note = '';
              }
              if ($serie2 == '-1'){
                $serie2 = '';
              }

 
            Quotation::on(Auth::user()->database_name)->where('id',$id_quotation)
                                    ->update(['observation' => $observation,'note' => $note,'serie_note' => $serie2]);


            return redirect('/quotationslic/register/'.$id_quotation.'/'.$coin.'')->withSuccess('Datos Guardados!');
            
        }



    /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

    public function deleteProduct(Request $request)
    {
        
        /*$quotation_product = QuotationProduct::on(Auth::user()->database_name)->find(request('id_quotation_product_modal')); 

        if(isset($quotation_product) && $quotation_product->status == "C"){
            QuotationProduct::on(Auth::user()->database_name)
                ->join('inventories','inventories.id','quotation_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('quotation_products.id',$quotation_product->id)
                ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount'), 'quotation_products.status' => 'X']);

                $this-> discountAmountsForEliminationProduct($quotation_product);
        }else{
            
            $quotation_product->status = 'X'; 
            $quotation_product->save(); 
        } */
        $product = QuotationProduct::on(Auth::user()->database_name)->find(request('id_quotation_product_modal'));
        $product->delete();

        return redirect('/quotationslic/register/'.request('id_quotation_modal').'/'.request('coin_modal').'')->withDanger('Eliminacion exitosa del producto!!');
        
    }
  


    public function deleteQuotation(Request $request)
    {

        $quotation = Quotation::on(Auth::user()->database_name)->find(request('id_quotation_modal'));

        QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$quotation->id)->delete();

        $quotation->delete();

        return redirect('/quotationslic')->withDanger('Eliminacion exitosa del factura!!');

    }

    public function reversar_quotation($id_quotation)
    {

        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);

        if($quotation != 'X'){
            $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$id_quotation)
            ->update(['status' => 'X']);

             /*DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->join('multipayment_expenses', 'multipayment_expenses.id_header','=','header_vouchers.id')
            ->where('multipayment_expenses.id_expense','=',$id_quotation)
            ->update(['detail_vouchers.status' => 'X']);*/

            QuotationProduct::on(Auth::user()->database_name)
                            ->join('inventories','inventories.id','quotation_products.id_inventory')
                            ->where('id_quotation',$quotation->id)
                            ->update(['inventories.amount' => DB::raw('inventories.amount+quotation_products.amount') , 'quotation_products.status' => 'X']);

            QuotationPayment::on(Auth::user()->database_name)
                            ->where('id_quotation',$quotation->id)
                            ->update(['status' => 'X']);



            $quotation->status = 'X';
            $quotation->save();
        }


        return redirect('invoices')->withSuccess('Reverso de Factura Exitosa!');

    }


    public function listinventory(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{

                $respuesta = Product::on(Auth::user()->database_name)->select('id')->where('code_comercial',$var)->where('status',1)->get();
                return response()->json($respuesta,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }

    }




}
