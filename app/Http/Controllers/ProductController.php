<?php

namespace App\Http\Controllers;

use App\Account;
use App\Inventory;
use App\InventoryHistories;
use Carbon\Carbon;
use App\Product;
use App\UserAccess;
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
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Productos y Servicio');

   }

   public function index(request $request,$type = 'todos')
   {
    $user       =   auth()->user();
    $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
                ->select('modulos.name','modulos.ruta')
                ->get();
       
    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');
    $namemodulomiddleware = $request->get('namemodulomiddleware');


       $company = Company::on(Auth::user()->database_name)->find(1);


        if ($type == 'todos') {
        $cond = '!=';
        $valor = null;
        } 

        if ($type == 'MERCANCIA') {
            $cond = '=';
            $valor = $type;
        }   
        if ($type == 'MATERIAP') {
            $cond = '=';
            $valor = $type;
        }
        if ($type == 'COMBO') {
            $cond = '=';
            $valor = $type;
        }   
        if ($type == 'SERVICIO') {
            $cond = '=';
            $valor = $type;
        }  

        $products = Product::on(Auth::user()->database_name)
        ->where('type',$cond,$valor)
        ->where('status','!=','X')
        ->orderBy('status','DESC')
        ->orderBy('id' ,'DESC')->get();

      
       return view('admin.products.index',compact('namemodulomiddleware','sistemas','products','company','type','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }


   public function productprices(request $request,$id)
   {
    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
       $user       =   auth()->user();
       $users_role =   $user->role_id;


       $product_detail        =   Product::on(Auth::user()->database_name)->find($id);
       $products = ProductPrice::on(Auth::user()->database_name)->orderBy('id' ,'ASC')->where("id_product",$product_detail->id)->get();

       if(empty($products)){
           
        return view('admin.index');
       } else {
        return view('admin.products.productprices',compact('agregarmiddleware','actualizarmiddleware','products','product_detail'));
       }

    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }

   }
   public function createprice(request $request,$id)
   {
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

       $user       =   auth()->user();
       $users_role =   $user->role_id;

           $product_detail        =   Product::on(Auth::user()->database_name)->find($id);
  
       return view('admin.products.createprice',compact('product_detail'));
    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
   }

   public function editprice(request $request,$id)
   {
    
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
    $product = ProductPrice::on(Auth::user()->database_name)->find($id);
    $product_detail        =   Product::on(Auth::user()->database_name)->where('id',$product->id_product)->get()->first();

     return view('admin.products.editprice',compact('product','product_detail'));
    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
   }

 
    public function updateproduct($id,Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
        $valor_sin_formato_price            =   trim(str_replace(',', '.', str_replace('.', '',request('Precio')))) ;
        $var = ProductPrice::on(Auth::user()->database_name)->findOrFail($id);
        $var->price = $valor_sin_formato_price;

        $var->save();
        
        $id_producto     = request('id_product');

        return \redirect()->route('products.productprices',$id_producto)->withSuccess('Actualizacion Exitosa!');
    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
       /* //return \redirect()->route('products.productprice',$id)->withSuccess('Actualizacion Exitosa!');;*/
    }


    public function storeprice(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $id     = request('id_product');
          $valor_sin_formato_precio =   trim(str_replace(',', '.', str_replace('.', '',request('Precio'))));

            
            $var = new ProductPrice();
            $var->setConnection(Auth::user()->database_name);
            $var->id_product        = $id;
            $var->price             = $valor_sin_formato_precio;
            $var->status            =  1;
            $var->save();

            return \redirect()->route('products.productprices',$id);
        } else{
            return redirect('/products')->withDanger('No Tiene Permiso!');
        }
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

  
   public function create(request $request)
   {
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
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
    }else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
 
    }

  
   public function store(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

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


        return redirect('/products/index/todos')->withSuccess('Registro Exitoso!');
     } else{
            return redirect('/products/index/todos')->withDanger('No Tiene Permiso!');
        }
    }



   public function edit(request $request,$id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

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
    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
   }


   public function update(Request $request, $id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
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

    return redirect('/products/index/todos')->withSuccess('Actualizacion Exitosa!');

    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
}


   public function destroy(request $request)
   {
    if(Auth::user()->role_id == '1' || $request->get('eliminarmiddleware') == '1'){

        $product = Product::on(Auth::user()->database_name)->find(request('id_product_modal')); 

        if(isset($product)){
            
            Inventory::on(Auth::user()->database_name)
                            ->where('product_id',$product->id)
                            ->update(['status' => 'X']);

            $product->status = 'X';

            $product->save();
    
            return redirect('/products')->withSuccess('Se ha Deshabilitado el Producto Correctamente!!');
        }

    } else{
        return redirect('/products')->withDanger('No Tiene Permiso!');
    }
   }


}
