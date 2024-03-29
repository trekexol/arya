<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\UserAccess;
use App\Sistemas;
use App\Modulo;



class AppServiceProvider extends ServiceProvider
{
    public $conection_logins = "logins";
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //use Illuminate\Support\Facades\Schema; //Schema::defaultStringLength(191);


    View::composer("admin.layouts.dashboard_sidebar_two", Function ($view){

        $user = auth()->user();
        if($user->role_id == 1){ //si es administrador

                $sistemas = Sistemas::on($this->conection_logins)
                ->Where('id_companies','like','%A'.$user->id_company.'A%')
                ->Where('estatus','1')
                ->orderBy('nro_orden','asc')->get();


            $arreglo = array();
            foreach($sistemas as $sistemas){


                $id_sistema = $sistemas->id_sistema;
                $nbsistema = $sistemas->sistema;

                $modulos = Modulo::on($this->conection_logins)
                ->Where('id_sistema',$id_sistema)
                ->Where('estatus','1')
                ->orderBy('nro_orden','asc')
                ->get();

                $arreglom = array();

                foreach($modulos as  $modulos){


                        $ruta = $modulos->ruta;
                        $name = $modulos->name;

                        if($name == 'Balance General'){
                            $name = 'Estado de Situación Financiera';
                        }

                        $idsistema = $modulos->id_sistema;

                        $arreglom[] = ['modulo' => $name, 'ruta' => $ruta, 'icono_modulo' => $modulos->icono_modulo, 'id_sistema' => $idsistema, 'nro_orden' => $modulos->nro_orden];


                }

                $arreglo[] = ['sistema' => $nbsistema, 'idsistema' => $id_sistema,'padre' => $sistemas->padre,'iconosis' => $sistemas->icono_sistema, 'modulo' => $arreglom];

            }
            $view->with('arreglo', $arreglo);

        }//fin del IF


        else{

        $sistemas = UserAccess::on($this->conection_logins)
            ->join('modulos','modulos.id','=','user_access.id_modulo')
            ->join('sistemas','sistemas.id_sistema','=','modulos.id_sistema')
            ->where('user_access.id_user',$user->id)
            ->Where('id_companies','like','%A'.$user->id_company.'A%')
            ->Where('modulos.estatus','1')
            ->Where('sistemas.estatus','1')
            ->select('sistemas.id_sistema','sistemas.sistema','sistemas.icono_sistema','sistemas.padre')
            ->groupBy('sistemas.id_sistema','sistemas.sistema','sistemas.icono_sistema','sistemas.padre')
            ->orderBy('sistemas.nro_orden','asc')
            ->get();

            $arreglo = array();




            foreach($sistemas as $sistemas){


                $id_sistema = $sistemas->id_sistema;
                $nbsistema = $sistemas->sistema;


                $modulos = UserAccess::on($this->conection_logins)
                ->join('modulos','modulos.id','id_modulo')
                ->where('id_user',$user->id)
                ->Where('modulos.id_sistema',$id_sistema)
                ->Where('modulos.estatus','1')
                ->orderBy('nro_orden','asc')
                ->get();


                $arreglom = array();

                foreach($modulos as  $modulos){


                        $ruta = $modulos->ruta;
                        $name = $modulos->name;
                        $idsistema = $modulos->id_sistema;

                        if($name == 'Balance General'){
                            $name = 'Estado de Situación Financiera';
                        }

                        $arreglom[] = ['modulo' => $name, 'ruta' => $ruta, 'icono_modulo' => $modulos->icono_modulo, 'id_sistema' => $idsistema, 'nro_orden' => $modulos->nro_orden];


                }

                $arreglo[] = ['sistema' => $nbsistema, 'idsistema' => $id_sistema,'padre' => $sistemas->padre,'iconosis' => $sistemas->icono_sistema, 'modulo' => $arreglom];

            }



            $view->with('arreglo', $arreglo);

           }




        });
    }
}
