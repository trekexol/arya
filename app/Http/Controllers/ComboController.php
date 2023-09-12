<?php

namespace App\Http\Controllers;

use App\Combo;
use App\ComboProduct;
use App\Company;
use App\Inventory;
use App\Product;
use App\UserAccess;

use App\Segment;
use App\Subsegment;
use App\ThreeSubsegment;
use App\TwoSubsegment;
use App\UnitOfMeasure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class ComboController extends Controller
{
    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Combos');
       
 
    }
 
    public function index(request $request)
    {
      
          /* para hacer el submenu "dinamico" */
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

    $combos = Product::on(Auth::user()->database_name)
    ->orderBy('id' ,'desc')->where('status',1)->where('type',"COMBO")->get();
 
 
        return view('admin.combos.index',compact('eliminarmiddleware','actualizarmiddleware','namemodulomiddleware','agregarmiddleware','combos','sistemas'));
    }
 
  
    public function create(request $request)
    {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
         $segments     = Segment::on(Auth::user()->database_name)->orderBY('description','asc')->pluck('description','id')->toArray();
       
         $subsegments  = Subsegment::on(Auth::user()->database_name)->orderBY('description','asc')->get();
      
         $unitofmeasures   = UnitOfMeasure::on(Auth::user()->database_name)->orderBY('description','asc')->get();
 
         return view('admin.combos.create',compact('segments','subsegments','unitofmeasures'));

        }else{

            return redirect('/combos')->withDelete('No Tienes Permiso para Agregar!');
        }
    }

    public function create_assign(request $request,$id_combo)
    {
        
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $combo = Product::on(Auth::user()->database_name)->find($id_combo);
        $global = new GlobalController();
        
        if(isset($combo) && $combo->type == "COMBO"){

            $products = Product::on(Auth::user()->database_name)
            ->orderBy('id' ,'desc')
            ->where('type','=','MATERIAP')
            ->orwhere('type','=','MERCANCIA')
            ->get();
            
            foreach ($products as $product){
                
                $unidad_medida = UnitOfMeasure::on(Auth::user()->database_name)->find($product->unit_of_measure_id);
                $product->unit_of_measure_id = $unidad_medida->description;

                $product->amount = $global->consul_prod_invt($product->id);
                
            }

            $combo_products = ComboProduct::on(Auth::user()->database_name)->where('id_combo',$id_combo)
            ->orderBy('id' ,'desc')
            ->get();
            

            foreach ($combo_products as $productwo){

                $productwo->amount = $global->consul_prod_invt($productwo->id_product);
            }
    

            $company = Company::on(Auth::user()->database_name)->find(1);

            $global = new GlobalController;
            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }
            
            return view('admin.combos.selectproduct',compact('products','id_combo','combo_products','bcv','combo'));
        }else{
            return redirect('combos')->withDanger('Debe seleccionar un Combo!');
        }
    }else{

        return redirect('/combos')->withDelete('No Tienes Permiso para Agregar!');
    }
        
    }

    public function validate_combo_discount($id_quotation){
        $combos = DB::connection(Auth::user()->database_name)->table('inventories')
                        ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                        ->join('products', 'products.id','=','inventories.product_id')
                        ->where('quotation_products.id_quotation','=',$id_quotation)
                        ->where('inventories.amount',0)
                        ->where('quotation_products.status','1')
                        ->where(function ($query){
                            $query->where('products.type','COMBO');
                        })
                        ->select('inventories.id as id_inventory','inventories.code as code','inventories.amount as amount','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                        'quotation_products.amount as amount_quotation')
                        ->get(); 

        $global = new GlobalController();

        if(isset($combos) && count($combos) > 0){
            foreach($combos as $combo){
                $return_value = $global->check_product($id_quotation,$combo->id_inventory,$combo->amount_quotation);

                if($return_value != "exito"){
                    return $return_value;
                }
            }
            return "exito";
        }
        return "exito";
    }



    public function check_exist_combo_in_quotation($id_quotation,$id_product){
        $combos = DB::connection(Auth::user()->database_name)->table('inventories')
                ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                ->join('products', 'products.id','=','inventories.product_id')
                ->where('quotation_products.id_quotation','=',$id_quotation)
                ->where('quotation_products.status','1')
                ->where(function ($query){
                    $query->where('products.type','COMBO');
                })
                ->select('products.id as id_combo','inventories.id as id_inventory','inventories.code as code','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                'quotation_products.amount as amount_quotation')
                ->get(); 

        $total_producto_en_combos = 0;
       //revisa primero todos los combos de esa cotizacion, luego revisa cuales tienen anadido el producto que se busca y va sumando cuantos productos van, de ultimo saca la cuenta a ver el total
        if(isset($combos) && count($combos) > 0){    
            foreach($combos as $combo){
                $combo_searchs = ComboProduct::on(Auth::user()->database_name)->where("id_combo",$combo->id_combo)->get();

                foreach($combo_searchs as $combo_search){
                    if($combo_search->id_product == $id_product){
                        $total_producto_en_combos +=  $combo->amount_quotation * $combo_search->amount_per_product;
                    }
                }
            }
            return $total_producto_en_combos;
        }else{
            return 0;
        }
    }
 
  
    public function store(Request $request)
     {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
         $data = request()->validate([
             
         
             'segment'         =>'required',
             'unit_of_measure_id'         =>'required',
 
             'description'         =>'required',
         
             'price'         =>'required',
             'price_buy'         =>'required',
             'cost_average'         =>'required',
 
             'money'         =>'required',
         
             'special_impuesto'         =>'required',
             
         
         ]);
 
        
         $var = new Product();
         $var->setConnection(Auth::user()->database_name);
 
         $var->segment_id = request('segment');
         $var->subsegment_id= request('Subsegment');
         $var->unit_of_measure_id = request('unit_of_measure_id');
         $var->code_comercial = request('code_comercial');
         $var->type = "COMBO";
         $var->description = request('description');
 
         $var->twosubsegment_id= request('twoSubsegment');
         $var->threesubsegment_id= request('threeSubsegment');
 
         $var->id_user = request('id_user');
 
         $valor_sin_formato_price = request('price');
         $valor_sin_formato_price_buy = request('price_buy');
         $valor_sin_formato_cost_average = request('cost_average');
         $valor_sin_formato_special_impuesto = request('special_impuesto');
         
 
 
         $var->price = $valor_sin_formato_price;
         $var->price_buy = $valor_sin_formato_price_buy;
         $var->cost_average = $valor_sin_formato_cost_average;
         $var->money = request('money');
         $var->photo_product = request('photo_product');
 
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
 
         $var->special_impuesto = $valor_sin_formato_special_impuesto;
         $var->status =  1;
     
         $var->save();
 
         $inventory = new Inventory();
         $inventory->setConnection(Auth::user()->database_name);
 
         $inventory->product_id = $var->id;
         $inventory->id_user = $var->id_user;
         $inventory->code = $var->code_comercial;
         $inventory->amount = 0;
         $inventory->status = 1;
 
         $inventory->save();
 
         return redirect('combos/assign/'.$var->id.'')->withSuccess('Registro del Combo Exitosamente!');
     
        }else{

            return redirect('/combos')->withDelete('No Tienes Permiso para Agregar!');
        }
        }

     public function store_assign(Request $request)
     {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

           try{

            if(isset($request->combo_products) || isset($request->id_products)){
                $array = $request->all();
            
                $amounts = collect();
                
                $count = 0;
                //dd($request);
                foreach ($array as $key => $item) {
                    
                    if(isset($item)){
                        if(substr($key,0, 6) == 'amount'){
                            $collection = collect();
                            $collection->id = substr($key,6);
                            $collection->amount = $item;
                            $amounts->push($collection);
                        }
                    }
                }
                
                //convierte a array
                $id_products = explode(",", $request->id_products);
                
                if(isset($request->combo_products)){
                    
                    $id_combos = explode(",", $request->combo_products);

                    $diferencias = array_diff($id_products,$id_combos);
                    
                    if(empty($diferencias) || (isset($diferencias[0]) && $diferencias[0] == "")){
                        $diferencias = array_diff($id_combos,$id_products);
                    }
                    
                    if(count($diferencias) > 0){
                        foreach($diferencias as $diferencia){
                            $combo_exist = ComboProduct::on(Auth::user()->database_name)->where('id_combo',$request->id_combo)->where('id_product',$diferencia)->first();
                            
                            if(isset($combo_exist)){
                                ComboProduct::on(Auth::user()->database_name)->where('id_combo',$request->id_combo)->where('id_product',$diferencia)->delete();
                            }else{
                                $var = new ComboProduct();
                                $var->setConnection(Auth::user()->database_name);
                                $var->id_combo = $request->id_combo;
                                $var->id_product = $diferencia;

                                foreach($amounts as $amount){
                                    if($amount->id == $var->id_product){
                                        $var->amount_per_product = $amount->amount;
                                    }
                                }
                                if($var->amount_per_product != 0){
                                    $var->save();
                                }
                                
                            }
                        }
                    }
                    //Revisar si todas los montos estan actualizados, sino actualizar
                    foreach($amounts as $amount){

                        $combo_actual = ComboProduct::on(Auth::user()->database_name)->where('id_combo',$request->id_combo)->where('id_product',$amount->id)->first();
                        if(isset($combo_actual)){
                            if($combo_actual->amount_per_product != $amount->amount){
                                ComboProduct::on(Auth::user()->database_name)->where('id_combo',$request->id_combo)
                                ->where('id_product',$amount->id)->update(['amount_per_product' => $amount->amount]);
                            }
                        }
                    }
                }else{
                    
                    foreach($id_products as $id_product){
                        $var = new ComboProduct();
                        $var->setConnection(Auth::user()->database_name);
                        $var->id_combo = $request->id_combo;
                        $var->id_product = $id_product;
                        
                        foreach($amounts as $amount){
                            if($amount->id == $var->id_product){
                                $var->amount_per_product = $amount->amount;
                            }
                        }
                        $var->save();
                    }
                    
                }

            }else{
                return redirect('combos');
            }
            
            $update_prices = request('updatePrices');

            if(isset($update_prices)){
                $price = $request->price;
                $price_buy = $request->price_buy;
            
                $this->updatePrice($request->id_combo,$price,$price_buy);
            }
            
            return redirect('combos')->withSuccess('Registro del Combo Exitosamente!');
        } catch (Exception $e) {

            return redirect('combos/assign/'.$request->id_combo.'')->withDanger('Verifique las cantidades ingresadas ingresados!!');
        }

     } else{

            return redirect('/combos')->withDelete('No Tienes Permiso para Agregar!');
        }
    }

     public function edit(request $request,$id)
     {
        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

          $combo = Product::on(Auth::user()->database_name)->find($id);
          $segments     = Segment::on(Auth::user()->database_name)->orderBY('description','asc')->get();
         
          $subsegments  = Subsegment::on(Auth::user()->database_name)->orderBY('description','asc')->get();

            $twosubsegments  = TwoSubsegment::on(Auth::user()->database_name)->where('subsegment_id',$combo->subsegment_id)->orderBY('description','asc')->get(); 

          
 
            $threesubsegments  = ThreeSubsegment::on(Auth::user()->database_name)->where('twosubsegment_id',$combo->twosubsegment_id)->orderBY('description','asc')->get();


          

    
          $unitofmeasures   = UnitOfMeasure::on(Auth::user()->database_name)->orderBY('description','asc')->get();
  
          
         
          return view('admin.combos.edit',compact('threesubsegments','twosubsegments','combo','segments','subsegments','unitofmeasures'));
    
        } else{

            return redirect('/combos')->withDelete('No Tienes Permiso');
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
            
            
            'segment'             =>'required',
            'unit_of_measure_id'  =>'required',
            'description'         =>'required',
            'price'               =>'required',
            'price_buy'           =>'required',
            'cost_average'        =>'required',
            'money'               =>'required',
            'special_impuesto'    =>'required',
            'status'              =>'required',
            
        ]);
    
        $var = Product::on(Auth::user()->database_name)->findOrFail($id);
    
        $var->segment_id = request('segment');
        $var->subsegment_id= request('Subsegment');
        if(request('twoSubsegment') == 'null' || request('twoSubsegment') == null){
            $var->twosubsegment_id= null;
        }else{
            $var->twosubsegment_id= request('twoSubsegment');
        }
    
        if(request('threeSubsegment') == 'null' || request('threeSubsegment') == null){
            $var->threesubsegment_id= null;
        }else{
            $var->threesubsegment_id= request('threeSubsegment');
        }
        
        
        $var->unit_of_measure_id = request('unit_of_measure_id');
    
        $var->code_comercial = request('code_comercial');
        $var->description = request('description');

        $valor_sin_formato_price = request('price');

        $valor_sin_formato_price_buy = request('price_buy');
        $valor_sin_formato_cost_average = request('cost_average');
        $valor_sin_formato_special_impuesto = request('special_impuesto');
    
        $var->price = $valor_sin_formato_price;
        $var->price_buy = $valor_sin_formato_price_buy;
        $var->cost_average = $valor_sin_formato_cost_average;
        
        $var->photo_product = request('photo_product');
    
        $var->money = request('money');
    
    
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
        
    
        if(request('status') == null){
            $var->status = $vars_status;
        }else{
            $var->status = request('status');
        }
        
        $var->save();
    
        return redirect('/products')->withSuccess('Actualizacion Exitosa!');

    } else{

        return redirect('/combos')->withDelete('No Tienes Permiso!');
    }
    }
  

   
   
    public function updatePrice($id_combo,$price,$price_buy)
    {
        if(isset($price) && ($price > 0) && isset($price_buy) && ($price_buy > 0))
        {
            $combo = Product::on(Auth::user()->database_name)->findOrFail($id_combo);

            $combo->price = $price;

            $combo->price_buy = $price_buy;

            $combo->save();
        }
    }
  
    
     public function destroy(Request $request)
     {
        if(Auth::user()->role_id == '1' || $request->get('eliminarmiddleware') == '1'){

          $product = Product::on(Auth::user()->database_name)->find(request('id_combo_modal')); 
  
          if(isset($product)){
              
              Inventory::on(Auth::user()->database_name)
                              ->where('product_id',$product->id)
                              ->update(['status' => 'X']);
  
              $product->status = 'X';
  
              $product->save();
      
              return redirect('combos')->withSuccess('Se ha Deshabilitado el Combo Correctamente!!');
          }
        } else{

            return redirect('/combos')->withDelete('No Tienes Permiso!');
        }
     }
}
