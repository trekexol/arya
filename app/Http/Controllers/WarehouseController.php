<?php

namespace App\Http\Controllers;
use App\UserAccess;
use App\Warehouse;
use App\WarehouseHistories;
use App\Company;
use App\Product;
use App\Account;
use App\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Inventario');
        $this->middleware('valimodulo:Inventario')->only('indexmovements');

    }

   public function index(Request $request)
   {
    $user       =   auth()->user();

    $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos','Almacenes','Transferencia de Almacen'])
                ->select('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->groupby('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->get();

                $agregarmiddleware = $request->get('agregarmiddleware');
                $actualizarmiddleware = $request->get('actualizarmiddleware');
                $eliminarmiddleware = $request->get('eliminarmiddleware');
                $namemodulomiddleware = $request->get('namemodulomiddleware');

       $warehouse = Warehouse::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();
      
       return view('admin.warehouse.index',compact('warehouse','namemodulomiddleware','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }


   public function create(Request $request)
   {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

        $companies     = Company::on(Auth::user()->database_name)->orderBy('razon_social', 'DESC')->get();

       return view('admin.warehouse.create',compact('companies'));

        }else{

            return redirect('/warehouse')->withDelete('No Tienes Permiso para Agregar Almacenes!');
        }    
   }

   public function store(Request $request)
    {
   
    $users = new Warehouse();
    $users->setConnection(Auth::user()->database_name);

    $users->company_id = request('company_id');
    $users->description = request('description');
    $users->direction = request('direction');
    $users->phone = request('phone');
    $users->phone2 = request('phone2');
    $users->person_contact = request('person_contact');
    $users->phone_contact = request('phone_contact');
    $users->observation = request('observation');
  
    $users->status =  request('status');
   
    $users->save();

    return redirect('/warehouse')->withSuccess('Registro Exitoso!');
    }

    public function edit(Request $request, $id)
   {

        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
            $var = Warehouse::on(Auth::user()->database_name)->find($id);
      
            //SIRVE PARA OBTENER EL ID DE SU ESTADO Y DE SU MUNICIPIO, YA QUE NO ESTA GUARDADO EN LA TABLA BRANCH

            $companies = Company::on(Auth::user()->database_name)->get();
          
    
            return view('admin.warehouse.edit',compact('var','companies'));
        }else{

            return redirect('/warehouse')->withDelete('No Tienes Permiso para Editar Almacenes!');

        }
  
   }

   public function update(Request $request, $id)
   {

    $users = Warehouse::on(Auth::user()->database_name)->findOrFail($id);

    $users->company_id = request('company_id');
    $users->description = request('description');
    $users->direction = request('direction');
    $users->phone = request('phone');
    $users->phone2 = request('phone2');
    $users->person_contact = request('person_contact');
    $users->phone_contact = request('phone_contact');
    $users->observation = request('observation');
    $users->status =  request('status');

    $users->save();

    return redirect('/warehouse')->withSuccess('Actualizacion Exitosa!');
    }

    public function movement(request $request,$type = 1,$typet = 1,$branch = 1,$branch_end = 1) {

        $user       =   auth()->user();
        $sistemas = UserAccess::on("logins")
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->whereIn('modulos.name', ['Inventario','Productos y Servicio','Combos'])
                ->select('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->groupby('modulos.name','modulos.ruta','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->get();

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $namemodulomiddleware = $request->get('namemodulomiddleware');

        $global = new GlobalController();


        if ($type == 1) {
            $cond = '!=';
            $valor = null;
        }
        if ($type == 2) {
            $cond = '=';
            $valor = 'MERCANCIA';
        }
        if ($type == 3) {
            $cond = '=';
            $valor = 'MATERIAP';
        }

        $inventories = Product::on(Auth::user()->database_name)
        ->orderBy('id' ,'DESC')
        ->where('status',1)
        ->where('status',1)
        ->where('type',$cond,$valor)
        ->where('type','NOT LIKE','COMBO')
        ->select('id as id_inventory','products.*')
        ->get();


        if($typet == 1 || $typet == 2){
            $inventories = $inventories->filter(function($inventorie) use ($global) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,1,1);
                return $inventorie->amount != 0;
            })->values();

            $origen = Warehouse::on(Auth::user()->database_name)->where('status',1)->where('id',1)->get();

            if($typet == 2){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }


        if($typet == 3 || $typet == 4){
            $inventories = $inventories->filter(function($inventorie) use ($global) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory);
                return $inventorie->amount != 0;
            })->values();

            $origen = Branch::on(Auth::user()->database_name)->where('status',1)->where('id',1)->get();

            if($typet == 4){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }

        
        if($typet == 5 || $typet == 6){

        }

        if (!empty($inventories)){

            $texto_select = '';
            $fin_select = "</select>";

            foreach ($inventories as $var){

                if($typet == 5 || $typet == 6){
                    $var->origen = 'Devolución';
                } else {
                    $var->origen =  $origen[0]['description'];
                }
                
                $texto_select = '';

                foreach($destino as $destin){
                    if($destin->id == 1){
                        $texto_select .= '<option selected value="'.$destin->id.'">'.$destin->description.'</option>';
                    } else {
                        $texto_select .= '<option value="'.$destin->id.'">'.$destin->description.'</option>';
                    }
                }
                $select = "<select class='destino form-control selectdestino' id='selectdestino".$var->id."' name='destino' data-producto='".$var->id."'>";
                $var->destino = $select.' '.$texto_select.' '.$fin_select;
                $var->id_origen = $branch;
                $var->id_destino = $branch_end;

            }
        } else {
            
            foreach ($inventories as $var){
                $var->origen = '';
                $var->destino = '';
                $var->id_origen = 0;
                $var->id_destino = 0;
            }   
        }



        $branches = DB::connection(Auth::user()->database_name)
        ->table('warehouses')
        ->get();

        $company = Company::on(Auth::user()->database_name)->find(1);

        $contrapartidas     = Account::on(Auth::user()->database_name)
                                                        ->orWhere('description', 'LIKE','Bancos')
                                                        ->orWhere('description', 'LIKE','Caja')
                                                        ->orWhere('description', 'LIKE','Cuentas por Pagar Comerciales')
                                                        ->orWhere('description', 'LIKE','Capital Social Suscrito y Pagado')
                                                        ->orWhere('description', 'LIKE','Capital Social Suscripto y No Pagado')
                                                        ->orderBY('description','asc')->pluck('description','id')->toArray();

        return view('admin.warehouse.movement',compact('sistemas','namemodulomiddleware','actualizarmiddleware','inventories','company','type','contrapartidas','branches','branch','typet'));
    
    }


    public function getselect(Request $request, $typet = 1){
        //validar si la peticion es asincrona

       if($typet == 1){

            if($request->ajax()){
                try{

                    $respuesta = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();

                    return response()->json($respuesta,200);

                }catch(Throwable $th){
                    return response()->json(false,500);
                }
            }
       }


       if($typet == 2){
        
            if($request->ajax()){
                try{

                    $respuesta = Branch::on(Auth::user()->database_name)->where('status',1)->get();

                    return response()->json($respuesta,200);

                }catch(Throwable $th){
                    return response()->json(false,500);
                }
            }
       }
    }


    public function refreshtable(Request $request) {
        
        $typet = $request->get('type_transf');
        $type = $request->get('type');
        $branch = 1;
        $branch_end = 1;
        
        $global = new GlobalController();

        if ($type == 1) {
            $cond = '!=';
            $valor = null;
        }
        if ($type == 2) {
            $cond = '=';
            $valor = 'MERCANCIA';
        }
        if ($type == 3) {
            $cond = '=';
            $valor = 'MATERIAP';
        }

        $inventories = Product::on(Auth::user()->database_name)
        ->orderBy('id' ,'DESC')
        ->where('status',1)
        ->where('type',$cond,$valor)
        ->where('type','NOT LIKE','COMBO')
        ->select('id as id_inventory','products.*')
        ->get();


        if($typet == 1 || $typet == 2){
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,1,$branch);
                return $inventorie->amount != 0;
            })->values();
       
            $origen = Warehouse::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();

            if($typet == 2){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }


        if($typet == 3 || $typet == 4){
            
           
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,$branch,null);
                return $inventorie->amount != 0;
            })->values();

            $origen = Branch::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();

            if($typet == 4){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        } 


        if($typet == 5 || $typet == 6){

            foreach ($inventories as $inventorie) {
                $inventorie->amount = "";
            }

            if($typet == 6){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }

        }

        if (!empty($inventories)){

            $texto_select = '';
            $fin_select = "</select>";

            foreach ($inventories as $var){

                if($typet == 5 || $typet == 6){
                    $var->origen = 'Devolución';
                } else {
                    $var->origen =  $origen[0]['description'];
                }
                
                $texto_select = '';

                foreach($destino as $destin){
                    if($destin->id == 1){
                        $texto_select .= '<option selected value="'.$destin->id.'">'.$destin->description.'</option>';
                    } else {
                        $texto_select .= '<option value="'.$destin->id.'">'.$destin->description.'</option>';
                    }
                }
                $select = "<select class='destino form-control selectdestino' id='selectdestino".$var->id."' name='destino' data-producto='".$var->id."'>";
                $var->destino = $select.' '.$texto_select.' '.$fin_select;
                $var->id_origen = $branch;
                $var->id_destino = $branch_end;

            }
        } else {
            
            foreach ($inventories as $var){
                $var->origen = '';
                $var->destino = '';
                $var->id_origen = 0;
                $var->id_destino = 0;
            }   
        }

       return response()->json($inventories);
    }

    public function refresorigen(Request $request) {
       
        $typet = $request->get('type_transf');
        $type = $request->get('type');
        $branch =  $request->get('branch');
        $branch_end = 1;
        
        $global = new GlobalController();

        if ($type == 1) {
            $cond = '!=';
            $valor = null;
        }
        if ($type == 2) {
            $cond = '=';
            $valor = 'MERCANCIA';
        }
        if ($type == 3) {
            $cond = '=';
            $valor = 'MATERIAP';
        }

        $inventories = Product::on(Auth::user()->database_name)
        ->orderBy('id' ,'DESC')
        ->where('status',1)
        ->where('type',$cond,$valor)
        ->where('type','NOT LIKE','COMBO')
        ->select('id as id_inventory','products.*')
        ->get();


        if($typet == 1 || $typet == 2){
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,1,$branch);
                return $inventorie->amount != 0;
            })->values();
       
            $origen = Warehouse::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();
            if($typet == 2){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }


        if($typet == 3 || $typet == 4){
            
           
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,$branch,null);
                return $inventorie->amount != 0;
            })->values();

            $origen = Branch::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();
            if($typet == 4){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }


        if($typet == 5 || $typet == 6){
            //$inventories = array(array(''));

            $inventories = $inventories;

            if($typet == 6){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }

        }

        if (!empty($inventories)){

            $texto_select = '';
            $fin_select = "</select>";

            foreach ($inventories as $var){
                $var->origen =  $origen[0]['description'];
                $texto_select = '';
                foreach($destino as $destin){
                    if($destin->id == 1){
                        $texto_select .= '<option selected value="'.$destin->id.'">'.$destin->description.'</option>';
                    } else {
                        $texto_select .= '<option value="'.$destin->id.'">'.$destin->description.'</option>';
                    }

                }
                $select = "<select class='destino form-control selectdestino' id='selectdestino".$var->id."' name='destino' data-producto='".$var->id."'>";
                $var->destino = $select.' '.$texto_select.' '.$fin_select;
                $var->id_origen = $branch;
                $var->id_destino = $branch_end;
            }
        } else {
            
            foreach ($inventories as $var){
                $var->origen = '';
                $var->destino = '';
                $var->id_origen = 0;
                $var->id_destino = 0;
            }
            
        }


       return response()->json($inventories);
    }
    


    public function refresdestino(Request $request) {
       
        $typet = $request->get('type_transf');
        $type = $request->get('type');
        $branch = $request->get('branch');
        $branch_end = $request->get('branch_end');
        
        $global = new GlobalController();

        if ($type == 1) {
            $cond = '!=';
            $valor = null;
        }
        if ($type == 2) {
            $cond = '=';
            $valor = 'MERCANCIA';
        }
        if ($type == 3) {
            $cond = '=';
            $valor = 'MATERIAP';
        }

        $inventories = Product::on(Auth::user()->database_name)
        ->orderBy('id' ,'DESC')
        ->where('status',1)
        ->where('type',$cond,$valor)
        ->where('type','NOT LIKE','COMBO')
        ->select('id as id_inventory','products.*')
        ->get();


        if($typet == 1 || $typet == 2){
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,1,$branch);
                return $inventorie->amount != 0;
            })->values();
       
            $origen = Warehouse::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();
            if($typet == 2){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }
        }


        if($typet == 3 || $typet == 4){
            
           
            $inventories = $inventories->filter(function($inventorie) use ($global, $branch) {
                $inventorie->amount = $global->consul_prod_invt($inventorie->id_inventory,$branch,null);
                return $inventorie->amount != 0;
            })->values();

            $origen = Branch::on(Auth::user()->database_name)->where('status',1)->where('id',$branch)->get();
            if($typet == 4){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }

        }


        if($typet == 5 || $typet == 6){
            //$inventories = array(array(''));

            $inventories = $inventories;

            if($typet == 6){
                $destino = Branch::on(Auth::user()->database_name)->where('status',1)->get();
            } else {
                $destino = Warehouse::on(Auth::user()->database_name)->where('status',1)->get();
            }

        }

        if (!empty($inventories)){

            $texto_select = '';

            $fin_select = "</select>";

            foreach ($inventories as $var){
                $var->origen =  $origen[0]['description'];
                $texto_select = '';
                foreach($destino as $destin){
                        
                    if ($branch_end == $destin->id){
                        $texto_select .= '<option selected value="'.$destin->id.'">'.$destin->description.'</option>';
                    }else{
                        $texto_select .= '<option value="'.$destin->id.'">'.$destin->description.'</option>';
                    }

                }
                $select = "<select class='destino form-control selectdestino' id='selectdestino".$var->id."' name='destino' data-producto='".$var->id."'>";
                $var->destino = $select.' '.$texto_select.' '.$fin_select;
                $var->id_origen = $branch;
                $var->id_destino = $branch_end;
            }
        } else {
            
            foreach ($inventories as $var){
                $var->origen = '';
                $var->destino = '';
                $var->id_origen = 0;
                $var->id_destino = 0;
            }
            
        }


       return response()->json($inventories);
    }

    public function transferencia(Request $request) {
        
        $origen = $request->get('origen'); 
        $producto = $request->get('producto');
        $selectdestino = $request->get('selectdestino');
        $typet = $request->get('typet');
        $monto = $request->get('monto');
        $amount = 0;
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $global = new GlobalController;
            

       if($typet == 1){
            $global->transaction_inv_almac('almacen','salida',$producto,'-',$monto,0,$date,$origen,$origen,0,0,0,0,null);
            $global->transaction_inv_almac('almacen','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = $global->consul_prod_invt($producto,1,$origen);
        }
        if($typet == 2){
            $global->transaction_inv_almac('almacen','salida',$producto,'-',$monto,0,$date,$origen,$origen,0,0,0,0,null);
            $global->transaction_inv_almac('sucursal','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = $global->consul_prod_invt($producto,1,$origen);
        }
        if($typet == 3){
            $global->transaction_inv_almac('sucursal','salida',$producto,'-',$monto,0,$date,$origen,$origen,0,0,0,0,null);
            $global->transaction_inv_almac('almacen','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = $global->consul_prod_invt($producto,$origen,null);
        }
        if($typet == 4){
            $global->transaction_inv_almac('sucursal','salida',$producto,'-',$monto,0,$date,$origen,$origen,0,0,0,0,null);
            $global->transaction_inv_almac('sucursal','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = $global->consul_prod_invt($producto,$origen,null);
        }  

        if($typet == 5){
            $global->transaction_inv_almac('almacen','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = '';
        }  

        if($typet == 6){
            $global->transaction_inv_almac('sucursal','entrada',$producto,'-',$monto,0,$date,$selectdestino,$selectdestino,0,0,0,0,null);
            $amount = '';
        }  


        return response()->json(['amount' => $amount]);

    }

    public function verificalmacen(Request $request) {
        $id = $request->get('id'); 

        $almacen = WarehouseHistories::on(Auth::user()->database_name)
        ->where('id_warehouse',$id)
        ->first();

        if(empty($almacen)){
            $existe = 'No';
        } else {
            $existe = 'Si';
        }
        
        return response()->json(['existe' => $existe]);

    }

    public function destroy($id)
    {

        $almacen = Warehouse::on(Auth::user()->database_name)->find($id);

        if($almacen) {
            $almacen->delete();
        }
        
        return redirect('/warehouse')->withDanger($almacen->description.' Se ha Eliminado Correctamente!');
  
    }

}

