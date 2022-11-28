<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use App\UserAccess;
use App\Modulo;
class ValidarUsuarioMiddleware
{

    public $conection_logins = "logins"; 

    public function handle($request, Closure $next)
    {


        $user       =   auth()->user();
       


        $users_role =   $user->role_id;

        if ($users_role == 1){
            $nombreRuta = Route::currentRouteName();
            $sistemas = modulo::on($this->conection_logins)
          
            ->where('ruta', 'like', '%'.$nombreRuta.'%')
            ->get();


            $wordCount = $sistemas->count();
       
            if($wordCount > 0){

            $response = $next($request);
           
                return $response;

            }else{
                return redirect('/home')->withSuccess('No Tiene Acceso al Modulo');
            }
        }else{

            $nombreRuta = Route::currentRouteName();

           //dd($nombreRuta);
            $sistemas = UserAccess::on($this->conection_logins)
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.estatus','1')
                ->where('modulos.ruta', 'like', '%'.$nombreRuta.'%')
                ->get();
        
           $wordCount = $sistemas->count();
       
                if($wordCount > 0){

                $response = $next($request);
               
                    return $response;

                }else{
                    return redirect('/home')->withSuccess('No Tiene Acceso al Modulo');
                }

     
                
        }
        

    
        
    }

}

