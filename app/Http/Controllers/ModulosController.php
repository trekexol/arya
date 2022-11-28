<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modulo;
use App\UserAccess;
use Illuminate\Support\Facades\View;

class ModulosController extends Controller
{
    //
    public $conection_logins = "logins"; 
   
  
    public function list(Request $request, $id_sistema = null){

        if($request->ajax()){
            try{
                
               $data = explode(',',$id_sistema);
                $idsistema = $data[0];
                $idusuario = $data[1];
       
      
                $modulos = Modulo::on($this->conection_logins)
                ->where('id_sistema',$idsistema)
                ->where('estatus','1')
                ->orderby('name','asc')
                ->select('id','name','agregar','actualizar','eliminar')
                ->get();
        foreach($modulos as $modulos){

            $user_access = UserAccess::on($this->conection_logins)
            ->where('id_user',$idusuario)
            ->where('id_modulo',$modulos->id)
            ->first();

       if(isset($user_access)){
        $arreglom[] = ['id' => $modulos->id,'name' => $modulos->name, 'consulta' => 1, 'agregar' => $modulos->agregar, 'actualizar' => $modulos->actualizar, 'eliminar' => $modulos->eliminar, 
        'agregar2' => $user_access->agregar, 'actualizar2' => $user_access->actualizar, 'eliminar2' => $user_access->eliminar];

       }else{
        $arreglom[] = ['id' => $modulos->id, 'name' => $modulos->name, 'agregar' => $modulos->agregar, 'actualizar' => $modulos->actualizar, 'eliminar' => $modulos->eliminar,
        'agregar2' => 0, 'actualizar2' => 0, 'eliminar2' => 0, 'consulta' => 0];

       }
       
   
        }
           


                return response()->json(View::make('admin.users.tablasmodulos',compact('arreglom','idusuario'))->render());

          

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }


   


        
    }


    public function destroy(Request $request)
    {

        $user_access = UserAccess::on($this->conection_logins)->where('id_user',$request->id_user_modal)
        ->where('id',$request->idacces)->delete();


        
        return redirect('users/createassignmodules/'.$request->id_user_modal)->withDelete('Registro Eliminado Exitoso!');
    }



    public function insert(Request $request)
    {

        if($request->ajax()){
            try{
              
                if(isset($request->iduser) && isset($request->nombremodulo)){
                    
                    
                    if($request->tipopermiso == 'consultar'){
                        $validarmodulo = UserAccess::on($this->conection_logins)
                        ->where('id_user',$request->iduser)
                        ->where('id_modulo',$request->nombremodulo);

                            if($validarmodulo->count() == 0){
                            

                            $var = new UserAccess();
                            $var->setConnection($this->conection_logins);
                            $var->id_user = $request->iduser;
                            $var->id_modulo = $request->nombremodulo;
                            $var->save();

                            
                            return response()->json(true,200);

                            }

                }else{

                    $validarmodulo = UserAccess::on($this->conection_logins)
                    ->where('id_user',$request->iduser)
                    ->where('id_modulo',$request->nombremodulo);

                        if($validarmodulo->count() == 0){
                            
                            $tipo = $request->tipopermiso;

                        $var = new UserAccess();
                        $var->setConnection($this->conection_logins);
                        $var->id_user = $request->iduser;
                        $var->id_modulo = $request->nombremodulo;
                        $var->$tipo  = $request->valor;
                       $var->save();

                        
                        return response()->json(true,200);

                        }else{
                            $tipo = $request->tipopermiso;
                            $validarmodulo = UserAccess::on($this->conection_logins)
                            ->where('id_user',$request->iduser)
                            ->where('id_modulo',$request->nombremodulo)->first();

        
                                $validarmodulo->$tipo  = $request->valor;
                       
                            
                            $validarmodulo->save();

                            return response()->json(true,200);
                        }


                }

                
              
                   
         
                 }else{

                    return response()->json(false,500);
                 }

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
      
       


    
      
    }
    
}
