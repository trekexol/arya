<?php

namespace App\Imports;

use App\TempMovimientos;
use App\DetailVoucher;
use App\HeaderVoucher;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;




class TempMovimientosImport implements  ToCollection
{


    public $banco;
    public $mensaje;
    public $estatus;

            public function __construct($banco)
            {
                $this->banco = $banco;

            }


    public function collection(Collection $rows)
    {
        $i = 0;
        $contador = 0;
        $contadorerror = 0;


       if($this->banco == 'Bancamiga'){


                foreach($rows as $row){

        if($i > 2){

            /*******VERIFICO QUE TODOS LOS DATOS SON NUMERICOS. PARA PROCEDER */
            if(is_numeric($row[1]) AND is_numeric($row[2]) AND is_numeric($row[4]) AND is_numeric($row[5]))
                {

                    /*********DANDO FORMATO A LA FECHA ****/
                    $arr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]));
                        /*********FIN DANDO FORMATO A LA FECHA ****/




                      /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


                    $vali2   = TempMovimientos::on(Auth::user()->database_name)
                                ->where('banco','BANCAMIGA CUENTA CORRIENTE')
                                ->where('referencia_bancaria',$row[2])
                                ->where('moneda','bolivares')
                                ->where('haber',$row[5])
                                ->where('fecha',$arr)
                                ->where('debe',$row[4])->first();


                    /******si todo esta correcto inserto en BD */
                    if(!$vali2){


                                $user = new TempMovimientos();
                                $user->setConnection(Auth::user()->database_name);
                                $user->banco        = 'BANCAMIGA CUENTA CORRIENTE';
                                $user->referencia_bancaria     = $row[2];
                                $user->descripcion       = $row[3];
                                $user->fecha    = $arr;
                                $user->haber     = $row[5];
                                $user->debe   = $row[4];
                                $user->moneda      = 'bolivares';
                                $user->save();



                                    $contador++;
                                $estatus = TRUE;
                                $mensaje = 'Archivo Bancamiga <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
                            }else{
                                    $contadorerror++;
                                    $estatus = TRUE;
                                    $mensaje = 'Archivo Bancamiga <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

                                }

                }else{
                    $contadorerror++;
                    $estatus = TRUE;
                    $mensaje = 'Archivo Bancamiga <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


                }




                }



         $i++;
                }
       }



       elseif($this->banco == 'Banco Banesco'){


                foreach($rows as $row){

        if($i > 0){

            /*******VERIFICO QUE TODOS LOS DATOS SON NUMERICOS. PARA PROCEDER */
            if(is_numeric($row[0]) AND is_numeric($row[1]) AND is_numeric($row[3]))
                {

                    /*******cambio formato de fecha */
                $arr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]));

                /******VERIFICO SI ES NEGATIVO EL MONTO PARA QUITAR EL SIGNO. */
                    if($row[3] < 0 ){
                       $debe = trim($row[3], '-');
                       $haber = 0;
                    }else{
                        $haber = $row[3];
                        $debe = 0;
                    }

            /*******CONSULTO QUE LA REFERENCIA NO EXISTA EN LA BD ******/

                    /*  $vali   = TempMovimientos::on(Auth::user()->database_name)
                      ->where('banco','Banco Banesco')
                      ->where('referencia_bancaria',$row[1])
                      ->where('moneda','bolivares')->first();*/

            /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/
                    $vali2  = TempMovimientos::on(Auth::user()->database_name)
                    ->where('banco','Banco Banesco')
                    ->where('referencia_bancaria',$row[1])
                    ->where('haber',$haber)
                    ->where('debe',$debe)
                    ->where('fecha',$arr)
                    ->where('moneda','bolivares')->first();



                        /******si todo esta correcto inserto en BD */
                        if(!$vali2){

                            $user = new TempMovimientos();
                            $user->setConnection(Auth::user()->database_name);
                            $user->banco        = 'Banco Banesco';
                            $user->referencia_bancaria     = $row[1];
                            $user->descripcion       = $row[2];
                            $user->fecha    = $arr;
                            $user->haber     = $haber;
                            $user->debe   = $debe;
                            $user->moneda      = 'bolivares';
                            $user->save();


                            $contador++;
                        $estatus = TRUE;
                        $mensaje = 'Archivo banesco <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
                        }else{
                            $contadorerror++;
                            $estatus = TRUE;
                            $mensaje = 'Archivo banesco <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

                        }

                    }else{
                        $contadorerror++;
                        $estatus = TRUE;
                        $mensaje = 'Archivo banesco <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


                        }





                }

         $i++;
                } ///fin foreach
       }//fin  elseif($this->banco == 2)


       elseif($this->banco == 'Mercantil'){


                foreach($rows as $row){

       if($row[5] == 'SI' OR $row[5] == 'SF'){

      }elseif(($row[5] == 'ND' OR $row[5] == 'NC') AND is_numeric($row[3]) AND is_numeric($row[4])){


            /******DEFINIENDO EL TIPO DE MONEDA ***/
            if($row[1] == 'USD'){
                $moneda = 'dolares';
                $banco = 'Banco Mercantil Dolares';
            }else{
                $moneda = 'bolivares';
                $banco = 'Banco Mercantil';
            }

                     /**** CAMBIO EL MONTO DE PUNTO A COMA PARA LA BD */
                       $monto =  str_replace(".", "", $row[7]);
                       $monto =  str_replace(",", ".", $monto);

                        /**** Verifico si es nota de credito o debito */
                            if($row[5] == 'ND'){

                                $haber = 0;
                                $debe = $monto;
                            }elseif($row[5] == 'NC'){
                                $haber = $monto;
                                $debe = 0;

                            }

                    /****Cambio el formato de la fecha para la BD */
                    $fecha = $row[3];
                    $dias = substr($row[3], 0, 2);
                    $mes = substr($row[3], 2, 2);
                    $años = substr($row[3], 4, 4);
                    $fechacompleta = $años.'-'.$mes.'-'.$dias;



            $vali2   = TempMovimientos::on(Auth::user()->database_name)
            ->where('banco',$banco)
            ->where('referencia_bancaria',$row[4])
            ->where('haber',$haber)
            ->where('fecha',$fechacompleta)
            ->where('debe',$debe)
            ->where('moneda',$moneda)->first();
            /******si todo esta correcto inserto en BD */

         if(!$vali2){

            $user = new TempMovimientos();
            $user->setConnection(Auth::user()->database_name);
            $user->banco        = $banco;
            $user->referencia_bancaria     = $row[4];
            $user->descripcion       = $row[6];
            $user->fecha    = $fechacompleta;
            $user->haber     = $haber;
            $user->debe   = $debe;
            $user->moneda      = $moneda;
            $user->save();



            $contador++;
        $estatus = TRUE;
        $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
        }else{
            $contadorerror++;
            $estatus = TRUE;
            $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

        }




     }else{
            $contadorerror++;
            $estatus = TRUE;
            $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


        }

}//// fin  foreach
       } ///fin        elseif($this->banco == 3)



       elseif($this->banco == 'Banco Banplus' OR $this->banco == 'Banplus Custodia'){

        if($this->banco == 'Banco Banplus'){
            $moneda = "bolivares";
            $banco = 'Banco Banplus';
        }elseif($this->banco == 'Banplus Custodia'){
            $moneda = "dolares";
            $banco = 'Banplus Custodia';
        }

        foreach($rows as $row){



if($i > 1){


            /*******cambio formato de fecha */
                $fecha = explode('/',$row[0]);
                $dia = $fecha[0];
                $mes = $fecha[1];
                $año = $fecha[2];
                $fechacompleta = $año.'-'.$mes.'-'.$dia;
                $referencia = trim($row[1],"'");
                $descripcion = $row[2];
            if(is_null($row[3])){

                $haber = $row[4].'.'.$row[5];
                $debe = 0;
            }else{

                $debe = $row[3].'.'.$row[4];
                $haber = 0;

            }


      /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


    $vali2   = TempMovimientos::on(Auth::user()->database_name)
                ->where('banco', $banco)
                ->where('referencia_bancaria',$referencia)
                ->where('haber',$haber)
                ->where('debe',$debe)
                ->where('fecha',$fechacompleta)
                ->where('moneda',$moneda)->get();
    /******si todo esta correcto inserto en BD */

    if($vali2->count() == 0){

        $user = new TempMovimientos();
        $user->setConnection(Auth::user()->database_name);
        $user->banco        =  $banco;
        $user->referencia_bancaria     = $referencia;
        $user->descripcion       = $descripcion;
        $user->fecha    = $fechacompleta;
        $user->haber     = $haber;
        $user->debe   = $debe;
        $user->moneda      = $moneda;
        $user->save();


       $contador++;
   $estatus = TRUE;
   $mensaje = 'Archivo Banplus <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
   }else{
       $contadorerror++;
       $estatus = TRUE;
       $mensaje = 'Archivo Banplus <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

   }




}//if($i > 1){


    $i++;

}  //      foreach($rows as $row){

}//     elseif($this->banco == 'Banco Banplus'){

       elseif($this->banco == 'Chase'){


        foreach($rows as $row){

        if($i > 0){

            if(($row[0] == 'CREDIT' OR $row[0] == 'DEBIT') AND is_numeric($row[3])){

                       /*******cambio formato de fecha */
                       $fecha = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]));


               $fecha = explode('-',$fecha);

                $año = $fecha[0];
                $dia = $fecha[1];
                $mes = trim(substr($fecha[2],0,2));
                $fechacompleta = $año.'-'.$mes.'-'.$dia;


                if($row[0] == 'CREDIT'){
                    $haber = $row[3];
                    $debe = 0;
                }elseif($row[0] == 'DEBIT'){

                    $haber = 0;
                    $debe = trim($row[3], '-');
                }


              /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


            $vali2   = TempMovimientos::on(Auth::user()->database_name)
                        ->where('banco','Chase')
                        ->where('haber',$haber)
                        ->where('debe',$debe)
                        ->where('fecha',$fechacompleta)
                        ->where('moneda','dolares')->first();
            /******si todo esta correcto inserto en BD */

            if(!$vali2){

                $user = new TempMovimientos();
                $user->setConnection(Auth::user()->database_name);
                $user->banco        = 'Chase';
                $user->referencia_bancaria     = NULL;
                $user->descripcion       = $row[2];
                $user->fecha    = $fechacompleta;
                $user->haber     = $haber;
                $user->debe   = $debe;
                $user->moneda      = 'dolares';
                $user->save();


            $contador++;
            $estatus = TRUE;
            $mensaje = 'Archivo Chase <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


            }else{

                $contadorerror++;
                $estatus = TRUE;
                $mensaje = 'Archivo Chase <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


            }




            }else{

                $contadorerror++;
                $estatus = TRUE;
                $mensaje = 'Archivo Chase <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


            }









        }///fin if $i

 $i++;
        }//fin foreach
}//fin elseif($this->banco == 4)

elseif($this->banco == 'BOFA'){



    foreach($rows as $row){

    if($i > 7){

          /*******VERIFICO QUE TODOS LOS DATOS SON NUMERICOS. PARA PROCEDER */
          if(is_numeric($row[2]))
          {

            $fecha = explode('/',$row[0]);
            $mes = $fecha[0];
            $dia = $fecha[1];
            $año = $fecha[2];
            $fechacompleta = $año.'-'.$mes.'-'.$dia;

        /******VERIFICO SI ES NEGATIVO EL MONTO PARA QUITAR EL SIGNO. */
            if($row[2] < 0 ){
                $debe = trim($row[2], '-');
                $haber = 0;
            }else{
                 $haber = $row[2];
                 $debe = 0;
             }

                 /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


            $vali2   = TempMovimientos::on(Auth::user()->database_name)
            ->where('banco','BOFA')
            ->where('haber',$haber)
            ->where('debe',$debe)
            ->where('fecha',$fechacompleta)
            ->where('moneda','dolares')->first();
                /******si todo esta correcto inserto en BD */

                    if(!$vali2){

                        $user = new TempMovimientos();
                        $user->setConnection(Auth::user()->database_name);
                        $user->banco        = 'BOFA';
                        $user->referencia_bancaria     = NULL;
                        $user->descripcion       = $row[1];
                        $user->fecha    = $fechacompleta;
                        $user->haber     = $haber;
                        $user->debe   = $debe;
                        $user->moneda      = 'dolares';
                        $user->save();

                        $contador++;
                        $estatus = TRUE;
                        $mensaje = 'Archivo BOFA <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;



                    }else{
                        $contadorerror++;
                        $estatus = TRUE;
                        $mensaje = 'Archivo BOFA <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

                    }

          }else{

            $contadorerror++;
            $estatus = TRUE;
            $mensaje = 'Archivo BOFA <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

          }



    }

$i++;
    }
}


elseif($this->banco == 'Banco del Tesoro'){

    $nro = 1;

    foreach($rows as $row){

        if($i > 0){


        /****Cambio el formato de la fecha para la BD */
        $data = explode('/',$row[0]);
        $dias = $data[0];
        $mes = $data[1];
        $años = $data[2];
        $fechacompleta = $años.'-'.$mes.'-'.$dias;
        $fechamesa = $años.'-'.$mes;

        $banco = 'Banco del Tesoro';
        $moneda = 'bolivares';


         /**** CAMBIO EL MONTO DE PUNTO A COMA PARA LA BD */
         $monto =  str_replace(".", "", $row[3]);
         $debe =  str_replace(",", ".", $monto);

        /**** CAMBIO EL MONTO DE PUNTO A COMA PARA LA BD */
        $montos =  str_replace(".", "", $row[4]);
        $haber =  str_replace(",", ".", $montos);

        if($row[2] == '000000000'){

            $descripcion = $row[1].' '.$nro;

            $vali   = TempMovimientos::on(Auth::user()->database_name)
            ->where('banco',$banco)
            ->where('referencia_bancaria',$row[2])
            ->where('descripcion', $descripcion)
            ->where('haber',$haber)
            ->where('fecha',$fechacompleta)
            ->where('debe',$debe)
            ->where('moneda',$moneda)->get();

            if($vali->count() == 0){


                $nro++;

            }else{
                $nro++;

            }


        }else{
            $descripcion = $row[1];
        }



        $vali2   = TempMovimientos::on(Auth::user()->database_name)
        ->where('banco',$banco)
        ->where('referencia_bancaria',$row[2])
        ->where('descripcion',$descripcion)
        ->where('haber',$haber)
        ->where('fecha',$fechacompleta)
        ->where('debe',$debe)
        ->where('moneda',$moneda)->first();
        /******si todo esta correcto inserto en BD */


        if(!$vali2){

            $user = new TempMovimientos();
            $user->setConnection(Auth::user()->database_name);
            $user->banco        = $banco;
            $user->referencia_bancaria     = $row[2];
            $user->descripcion       = $descripcion;
            $user->fecha    = $fechacompleta;
            $user->haber     = $haber;
            $user->debe   = $debe;
            $user->moneda      = $moneda;
            $user->save();



            $contador++;
            $estatus = TRUE;
            $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
            }else{
            $contadorerror++;
            $estatus = TRUE;
            $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

            }

        }


$i++;


}//// fin  foreach
} ///fin        elseif($this->banco == 3)





        $this->mensaje = $mensaje;
        $this->estatus = $estatus;

    }

}
