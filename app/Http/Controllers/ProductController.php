<?php

namespace App\Http\Controllers;

use App\Account;
use App\Inventory;
use App\InventoryHistories;
use Carbon\Carbon;
use App\Product;
use App\Company;
use App\Segment;
use App\Subsegment;
use App\ThreeSubsegment;
use App\TwoSubsegment;
use App\UnitOfMeasure;
use App\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class ProductController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');

   }

   public function index()
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;
       $company = Company::on(Auth::user()->database_name)->find(1);

        $products = Product::on(Auth::user()->database_name)->where('status','!=','X')->orderBy('status','DESC')->orderBy('id' ,'DESC')->get();

       return view('admin.products.index',compact('products','company'));
   }


   public function productprices($id)
   {

       $user       =   auth()->user();
       $users_role =   $user->role_id;


       $product_detail        =   Product::on(Auth::user()->database_name)->find($id);
       $products = ProductPrice::on(Auth::user()->database_name)->orderBy('id' ,'ASC')->where("id_product",$product_detail->id)->get();

       if(empty($products)){
           
        return view('admin.index');
       } else {
        return view('admin.products.productprices',compact('products','product_detail'));
       }

   }
   public function createprice($id)
   {
       $user       =   auth()->user();
       $users_role =   $user->role_id;

           $product_detail        =   Product::on(Auth::user()->database_name)->find($id);
  
       return view('admin.products.createprice',compact('product_detail'));
   }

   public function editprice($id)
   {
    $product = ProductPrice::on(Auth::user()->database_name)->find($id);
    $product_detail        =   Product::on(Auth::user()->database_name)->where('id',$product->id_product)->get()->first();

     return view('admin.products.editprice',compact('product','product_detail'));

   }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateproduct($id,Request $request)
    {
        $valor_sin_formato_price            =   trim(str_replace(',', '.', str_replace('.', '',request('Precio')))) ;
        $var = ProductPrice::on(Auth::user()->database_name)->findOrFail($id);
        $var->price = $valor_sin_formato_price;

        $var->save();
        
        $id_producto     = request('id_product');

        return \redirect()->route('products.productprices',$id_producto)->withSuccess('Actualizacion Exitosa!');

       /* //return \redirect()->route('products.productprice',$id)->withSuccess('Actualizacion Exitosa!');;*/
    }


    public function storeprice(Request $request)
    {

        $id     = request('id_product');
          $valor_sin_formato_precio =   trim(str_replace(',', '.', str_replace('.', '',request('Precio'))));

            
            $var = new ProductPrice();
            $var->setConnection(Auth::user()->database_name);
            $var->id_product        = $id;
            $var->price             = $valor_sin_formato_precio;
            $var->status            =  1;
            $var->save();

            return \redirect()->route('products.productprices',$id);
        
    }

    public function listprice(Request $request, $code_id = null){


        //validar si la peticion es asincrona
        if($request->ajax()){
            try{                
                $productprice = ProductPrice::on(Auth::user()->database_name)->select('id','price')->where('id_product',$code_id)->orderBy('price','asc')->get();
                return response()->json($productprice,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }

    }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
        $segments     = Segment::on(Auth::user()->database_name)->orderBY('description','asc')->pluck('description','id')->toArray();
      
        $subsegments  = Subsegment::on(Auth::user()->database_name)->orderBY('description','asc')->get();
     
        $unitofmeasures   = UnitOfMeasure::on(Auth::user()->database_name)->orderBY('description','asc')->get();

        $accounts = Account::on(Auth::user()->database_name)->select('id','description')
                                ->where('code_one',1)
                                ->where('code_two', 1)
                                ->where('code_three', 3)
                                ->where('code_four',1)
                                ->where('code_five', '<>',0)
                                ->get();


        return view('admin.products.create',compact('segments','subsegments','unitofmeasures','accounts'));
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
    {
        
        $data = request()->validate([
            
        
            'segment'         =>'required',
            'unit_of_measure_id'         =>'required',
            'type'         =>'required',
            'description'         =>'required',
            'price'         =>'required',
            'price_buy'         =>'required',
            'money'         =>'required'
        
        
        ]);

        //dd($request);
        //dd(Auth::on(Auth::user()->database_name);
        $var = new Product();
        $var->setConnection(Auth::user()->database_name);

        $var->segment_id = request('segment');
        $var->subsegment_id= request('Subsegment');
        $var->unit_of_measure_id = request('unit_of_measure_id');
        $var->code_comercial = request('code_comercial');
        
        $product_exist  =   Product::on(Auth::user()->database_name)->where('code_comercial',request('code_comercial'))->first();
        
        if (!empty($product_exist)) {
            
           return redirect('/products/register')->withSuccess('El Codigo Comercial '.request('code_comercial').' Ya se encuentra registrado!');  
           //return \redirect()->route('products.create');
        }

        $var->type = request('type');
        $var->description = request('description');

        $var->twosubsegment_id= request('twoSubsegment');
        $var->threesubsegment_id= request('threeSubsegment');

        $var->id_user = request('id_user');

        $valor_sin_formato_price = str_replace(',', '.', str_replace('.', '',request('price')));
        $valor_sin_formato_price_buy = str_replace(',', '.', str_replace('.', '',request('price_buy')));
        $valor_sin_formato_cost_average = str_replace(',', '.', str_replace('.', '',request('cost_average')));
        $valor_sin_formato_special_impuesto = str_replace(',', '.', str_replace('.', '',request('special_impuesto')));
        
        //Empreas licores
        $valor_sin_formato_degree           = trim(str_replace(',', '.', str_replace('.', '',request('Grado') ?? 0)));
        $valor_sin_formato_liter            = trim(str_replace(',', '.', str_replace('.', '',request('Litros') ?? 0)));
        $valor_sin_formato_capacity         = trim(str_replace(',', '.', str_replace('.', '',request('Capacidad') ?? 0)));
        //fin Empreas licores
        
        
        $var->price = $valor_sin_formato_price;
        $var->price_buy = $valor_sin_formato_price_buy;
        $var->cost_average = $valor_sin_formato_cost_average;
        $var->money = request('money');

        $exento = request('exento');
        if($exento == null){
            $var->exento = false;
        }else{
            $var->exento = true;
        }
        
        $islr = request('islr');
        if($islr == null){
            $var->islr = false;
        }else{
            $var->islr = true;
        }

        if($request->id_account != null ){
            $var->id_account = $request->id_account;
        }

        $var->special_impuesto = $valor_sin_formato_special_impuesto;
        $var->lote= request('lote');
        $var->date_expirate= request('fecha_vencimiento');
        $var->status =  1;

        //Empresa licores
        $var->box               = request('Cajas') ?? 0;
        $var->degree            = $valor_sin_formato_degree;
        $var->bottle            = request('Botellas');
        $var->liter             = $valor_sin_formato_liter;
        $var->capacity          = $valor_sin_formato_capacity;
        //fin Empresa licores
        $var->save();

        $id_product = DB::connection(Auth::user()->database_name)->table('products')
        ->select('products.*')
        ->get()->last();  // consulta el ultimo producto creado para guardarlo en el historial

        
        $global = new GlobalController;

        //Historial del Inventario producto creado
        $date = Carbon::now();
        $date = $date->format('Y-m-d');  
        
        $global->transaction_inv('creado',$id_product->id,'inicio',0,$valor_sin_formato_price_buy,$date,1,1,0,0,0,0,0); // guardando registro en historial

        // fin Historial del Inventario producto creado

        //guardar foto del producto creado----------------------------
        $foto = $global->setCaratula($request->fotop,$id_product->id,$id_product->code_comercial);
         

        if($foto != 'false'){// guardar ruta de foto en el producto creado
           
            Product::on(Auth::user()->database_name)
            ->where('id',$id_product->id)
            ->update(['photo_product' => $foto]);
            
        }

        //fin foto------------------------

        $inventory = new Inventory();
        $inventory->setConnection(Auth::user()->database_name);

        $inventory->product_id = $id_product->id;
        $inventory->id_user = $var->id_user;
        $inventory->code = $var->code_comercial;
        $inventory->amount = 0;
        $inventory->status = 1;
        $inventory->save();


        return redirect('/products')->withSuccess('Registro Exitoso!');
    }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
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
        $company = Company::on(Auth::user()->database_name)->find(1);
    
        $product = Product::on(Auth::user()->database_name)->find($id);
        
        $segments     = Segment::on(Auth::user()->database_name)->orderBY('description','asc')->get();
       
        $subsegments  = Subsegment::on(Auth::user()->database_name)->where('segment_id',$product->segment_id)->orderBY('description','asc')->get();

        $twosubsegments  = TwoSubsegment::on(Auth::user()->database_name)->where('subsegment_id',$product->subsegment_id)->orderBY('description','asc')->get();
     
        $threesubsegments  = ThreeSubsegment::on(Auth::user()->database_name)->where('twosubsegment_id',$product->twosubsegment_id)->orderBY('description','asc')->get();
     
        $unitofmeasures   = UnitOfMeasure::on(Auth::user()->database_name)->orderBY('description','asc')->get();

        $accounts = Account::on(Auth::user()->database_name)->select('id','description')
                                ->where('code_one',1)
                                ->where('code_two', 1)
                                ->where('code_three', 3)
                                ->where('code_four',1)
                                ->where('code_five', '<>',0)
                                ->get();
       
        return view('admin.products.edit',compact('accounts','threesubsegments','twosubsegments','product','segments','subsegments','unitofmeasures','company'));
  
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

    $vars =  Product::on(Auth::user()->database_name)->find($id);

    $vars_status = $vars->status;
    $vars_exento = $vars->exento;
    $vars_islr = $vars->islr;
  
    $data = request()->validate([
        
       
        'segment'         =>'required',
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

    $var = Product::on(Auth::user()->database_name)->findOrFail($id);

    $var->segment_id = request('segment');

    if(request('Subsegment') == 'null'){
       $var->subsegment_id = null;
    }else{
       $var->subsegment_id = request('Subsegment');
    }

    if(request('twoSubsegment') == 'null'){
        $var->twosubsegment_id= null;
    }else{
        $var->twosubsegment_id= request('twoSubsegment');
    }

    if(request('threeSubsegment') == 'null'){
        $var->threesubsegment_id= null;
    }else{
        $var->threesubsegment_id= request('threeSubsegment');
    }
    
    
    $var->unit_of_measure_id = request('unit_of_measure_id');

    $var->code_comercial = request('code_comercial');
    $var->type = request('type');
    $var->description = request('description');

    $valor_sin_formato_price = str_replace(',', '.', str_replace('.', '',request('price')));
    $valor_sin_formato_price_buy = str_replace(',', '.', str_replace('.', '',request('price_buy')));
    $valor_sin_formato_cost_average = str_replace(',', '.', str_replace('.', '',request('cost_average')));
    $valor_sin_formato_special_impuesto = str_replace(',', '.', str_replace('.', '',request('special_impuesto')));
       
    //Empreas licores
    $valor_sin_formato_degree           = trim(str_replace(',', '.', str_replace('.', '',request('Grado') ?? 0)));
    $valor_sin_formato_liter            = trim(str_replace(',', '.', str_replace('.', '',request('Litros') ?? 0)));
    $valor_sin_formato_capacity         = trim(str_replace(',', '.', str_replace('.', '',request('Capacidad') ?? 0)));
    //fin Empreas licores
    

    $var->price = $valor_sin_formato_price;
    $var->price_buy = $valor_sin_formato_price_buy;
    $var->cost_average = $valor_sin_formato_cost_average;
    $fotoname = request('fotoname');

        $global = new GlobalController;
        //guardar foto del producto creado----------------------------
        $foto = $global->setCaratulaup($request->fotop,$var->id,$var->code_comercial,$fotoname);

        if($foto != 'false'){// guardar ruta de foto en el producto creado
           
            Product::on(Auth::user()->database_name)
            ->where('id',$var->id)
            ->update(['photo_product' => $foto]);
            
        }

        //fin foto------------------------

    $var->money = request('money');
    $var->lote= request('lote');
    $var->date_expirate= request('fecha_vencimiento');

    $var->special_impuesto = $valor_sin_formato_special_impuesto;

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

    if($request->id_account != null && ($request->id_account != 'actual')){
        $var->id_account = $request->id_account;
    }
   
    if(request('status') == null){
        $var->status = $vars_status;
    }else{
        $var->status = request('status');
    }

    //Empresa licores
    $var->box               = request('Cajas') ?? 0;
    $var->degree            = $valor_sin_formato_degree;
    $var->bottle            = request('Botellas');
    $var->liter             = $valor_sin_formato_liter;
    $var->capacity          = $valor_sin_formato_capacity;
    //fin Empresa licores

    $var->save();

    return redirect('/products')->withSuccess('Actualizacion Exitosa!');
    }


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy()
   {
        $product = Product::on(Auth::user()->database_name)->find(request('id_product_modal')); 

        if(isset($product)){
            
            Inventory::on(Auth::user()->database_name)
                            ->where('product_id',$product->id)
                            ->update(['status' => 'X']);

            $product->status = 'X';

            $product->save();
    
            return redirect('/products')->withSuccess('Se ha Deshabilitado el Producto Correctamente!!');
        }
   }


}
