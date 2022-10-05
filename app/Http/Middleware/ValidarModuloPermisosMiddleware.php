<?php

namespace App\Http\Middleware;

use Closure;
use App\UserAccess;
use App\Modulo;
class ValidarModuloPermisosMiddleware
{
    public $conection_logins = "logins"; 
    public function handle($request, Closure $next, $modulo = null)
    {

        $user       =   auth()->user();
      

        $users_role =   $user->role_id;

        if ($users_role == 1){
           
            
            $sistemas = modulo::on($this->conection_logins)           
            ->where('name', $modulo)
            ->get();


            $wordCount = $sistemas->count();
       
            if($wordCount > 0){

            $response = $next($request);
           
                return $response;

            }else{
                return redirect('/home')->withSuccess('No Tiene Acceso al Modulo');
            }




        }else{
           
            $sistemas = UserAccess::on($this->conection_logins)
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->where('modulos.name', $modulo)
                ->select('modulos.name','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->first();
          
    
           if($sistemas){
                  
                     $request->attributes->set('agregarmiddleware', $sistemas->agregar);
                     $request->attributes->set('actualizarmiddleware', $sistemas->actualizar);
                     $request->attributes->set('eliminarmiddleware', $sistemas->eliminar);
                     $request->attributes->set('namemodulomiddleware', $sistemas->name);
                     $response = $next($request);

                     return $response;
                    
                }else{
                    return redirect('/home')->withDanger('No Tiene Acceso al Modulo');
                }

     
                
        }

        //return $next($request);
    }
}
