<?php

namespace App\Imports;

use App\TempMovimientos;


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

                    /*******CONSULTO QUE LA REFERENCIA NO EXISTA EN LA BD ******/
                    
                    $vali   = TempMovimientos::on(Auth::user()->database_name)
                                ->where('banco','Bancamiga')
                                ->where('referencia_bancaria',$row[2])
                                ->where('moneda','bolivares')->first();

                      /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


                    $vali2   = TempMovimientos::on(Auth::user()->database_name)
                                ->where('banco','Bancamiga')
                                ->where('referencia_bancaria',$row[2])
                                ->where('fecha',$arr)
                                ->where('moneda','bolivares')
                                ->where('haber',$row[4])
                                ->where('debe',$row[5])->first();

                    
                    /******si todo esta correcto inserto en BD */
                    if(!$vali AND !$vali2){
                                    $Client = TempMovimientos::Create([
                                
                                        'banco'                   => 'Bancamiga',
                                        'referencia_bancaria'     => $row[2], 
                                        'descripcion'             => $row[3],
                                        'fecha'                   => $arr, 
                                        'haber'                   => $row[4], 
                                        'debe'                    => $row[5], 
                                        'moneda'                    => 'bolivares', 
                                    
                                    ]);
                                    $Client->setConnection(Auth::user()->database_name);
                                
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
                       $haber = trim($row[3], '-');
                       $debe = 0;
                    }else{
                        $debe = $row[3];
                        $haber = 0;
                    }

            /*******CONSULTO QUE LA REFERENCIA NO EXISTA EN LA BD ******/
                    
                      $vali   = TempMovimientos::on(Auth::user()->database_name)
                      ->where('banco','Banco Banesco')
                      ->where('referencia_bancaria',$row[1])
                      ->where('moneda','bolivares')->first();

            /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/
                    $vali2  = TempMovimientos::on(Auth::user()->database_name)
                    ->where('banco','Banco Banesco')
                    ->where('referencia_bancaria',$row[1])
                    ->where('fecha',$arr)
                    ->where('haber',$haber)
                    ->where('debe',$debe)
                    ->where('moneda','bolivares')->first();



                        /******si todo esta correcto inserto en BD */
                        if(!$vali AND !$vali2){
                            $Client = TempMovimientos::Create([

                                'banco'                   => 'Banco Banesco',
                                'referencia_bancaria'     => $row[1], 
                                'descripcion'             => $row[2],
                                'fecha'                   => $arr, 
                                'haber'                   => $haber, 
                                'debe'                    => $debe,
                                'moneda'                  => 'bolivares',  
                                    
                            ]);
                            $Client->setConnection(Auth::user()->database_name);

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
                       $monto =  str_replace(",", ".", $row[7]);
              
                        /**** Verifico si es nota de credito o debito */
                            if($row[5] == 'ND'){
                                $haber = $monto;
                                $debe = 0;
                            }elseif($row[5] == 'NC'){
                                $haber = 0;
                                $debe = $monto;
                            }
                    
                    /****Cambio el formato de la fecha para la BD */
                    $fecha = $row[3];
                    $dias = substr($row[3], 0, 2);
                    $mes = substr($row[3], 2, 2);
                    $años = substr($row[3], 4, 4);
                    $fechacompleta = $años.'-'.$mes.'-'.$dias;
                
                     /*******CONSULTO QUE LA REFERENCIA NO EXISTA EN LA BD ******/
                    
                     $vali   = TempMovimientos::on(Auth::user()->database_name)
                     ->where('banco',$banco)
                     ->where('referencia_bancaria',$row[4])
                     ->where('moneda',$moneda)->first();

           /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


         $vali2   = TempMovimientos::on(Auth::user()->database_name)
                     ->where('banco',$banco)
                     ->where('referencia_bancaria',$row[4])
                     ->where('fecha',$fechacompleta)
                     ->where('haber',$haber)
                     ->where('debe',$debe)
                     ->where('moneda',$moneda)->first();
         /******si todo esta correcto inserto en BD */
        
         if(!$vali AND !$vali2){

            $Client = TempMovimientos::Create([
                    
                'banco'                   => $banco,
                'referencia_bancaria'     => $row[4], 
                'descripcion'             => $row[6],
                'fecha'                   => $fechacompleta, 
                'haber'                   => $haber, 
                'debe'                    => $debe,
                'moneda'                  => $moneda,   
            
            ]);
            
             $Client->setConnection(Auth::user()->database_name);
          
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


       
       elseif($this->banco == 'Banco Banplus'){

  
        foreach($rows as $row){


            
if($i > 1){

            /*******cambio formato de fecha */
 $arr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]));
    
    dd($arr);

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
               $monto =  str_replace(",", ".", $row[7]);
      
                /**** Verifico si es nota de credito o debito */
                    if($row[5] == 'ND'){
                        $haber = $monto;
                        $debe = 0;
                    }elseif($row[5] == 'NC'){
                        $haber = 0;
                        $debe = $monto;
                    }
            
            /****Cambio el formato de la fecha para la BD */
            $fecha = $row[3];
            $dias = substr($row[3], 0, 2);
            $mes = substr($row[3], 2, 2);
            $años = substr($row[3], 4, 4);
            $fechacompleta = $años.'-'.$mes.'-'.$dias;
        
             /*******CONSULTO QUE LA REFERENCIA NO EXISTA EN LA BD ******/
            
             $vali   = TempMovimientos::on(Auth::user()->database_name)
             ->where('banco',$banco)
             ->where('referencia_bancaria',$row[4])
             ->where('moneda',$moneda)->first();

   /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/


 $vali2   = TempMovimientos::on(Auth::user()->database_name)
             ->where('banco',$banco)
             ->where('referencia_bancaria',$row[4])
             ->where('fecha',$fechacompleta)
             ->where('haber',$haber)
             ->where('debe',$debe)
             ->where('moneda',$moneda)->first();
 /******si todo esta correcto inserto en BD */

 if(!$vali AND !$vali2){

    $Client = TempMovimientos::Create([
            
        'banco'                   => $banco,
        'referencia_bancaria'     => $row[4], 
        'descripcion'             => $row[6],
        'fecha'                   => $fechacompleta, 
        'haber'                   => $haber, 
        'debe'                    => $debe,
        'moneda'                  => $moneda,   
    
    ]);
    
     $Client->setConnection(Auth::user()->database_name);
  
    $contador++;
$estatus = TRUE;
$mensaje = 'Archivo BanPlus <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;
}else{
    $contadorerror++;
    $estatus = TRUE;
    $mensaje = 'Archivo BanPlus <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;

}

            
    
     
}else{
    $contadorerror++;
    $estatus = TRUE;
    $mensaje = 'Archivo Mercantil <br> Cargado con Exito: '.$contador.' <br> No Cargados: '.$contadorerror;


}
$i++;
}//// fin  foreach
} ///fin        elseif($this->banco == 3)
       
       
       elseif($this->banco == 'Chase'){


        foreach($rows as $row){

        if($i > 0){

            if(($row[0] == 'CREDIT' OR $row[0] == 'DEBIT') AND is_numeric($row[3])){
                
                $fecha = explode('/',$row[1]);
                $mes = $fecha[0];
                $dia = $fecha[1];
                $año = $fecha[2];
                $fechacompleta = $año.'-'.$mes.'-'.$dia;
               
    
                if($row[0] == 'CREDIT'){
                    $debe = $row[3];
                    $haber = 0;
                }elseif($row[0] == 'DEBIT'){
                   
                    $debe = 0;
                    $haber = trim($row[3], '-');
                }
                     
   
              /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/
   
   
            $vali2   = TempMovimientos::on(Auth::user()->database_name)
                        ->where('banco','Chase')
                        ->where('fecha',$fechacompleta)
                        ->where('haber',$haber)
                        ->where('debe',$debe)
                        ->where('moneda','dolares')->first();
            /******si todo esta correcto inserto en BD */
           
            if(!$vali2){

                $Client = TempMovimientos::Create([
            
                    'banco'                   => 'Chase',
                    'referencia_bancaria'     => NULL, 
                    'descripcion'             => $row[2],
                    'fecha'                   => $fechacompleta, 
                    'haber'                   => $haber, 
                    'debe'                    => $debe,
                    'moneda'                  => 'dolares',  
                
                ]);

                $Client->setConnection(Auth::user()->database_name);

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
                $haber = trim($row[2], '-');
                $debe = 0;
            }else{
                 $debe = $row[2];
                 $haber = 0;
             }

                 /*******CONSULTO QUE LA INFORMACION A CARGAR NO EXISTA EN LA BD ******/
   
   
            $vali2   = TempMovimientos::on(Auth::user()->database_name)
            ->where('banco','BOFA')
            ->where('fecha',$fechacompleta)
            ->where('haber',$haber)
            ->where('debe',$debe)
            ->where('moneda','dolares')->first();
                /******si todo esta correcto inserto en BD */

                    if(!$vali2){
                        $Client = TempMovimientos::Create([
        
                            'banco'                   => 'BOFA',
                            'referencia_bancaria'     => NULL, 
                            'descripcion'             => $row[1],
                            'fecha'                   => $fechacompleta, 
                            'haber'                   => $haber, 
                            'debe'                    => $debe,
                            'moneda'                  => 'dolares',   
                        
                        ]);

                        $Client->setConnection(Auth::user()->database_name);

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

  

        $this->mensaje = $mensaje;
        $this->estatus = $estatus;
    
    }

}