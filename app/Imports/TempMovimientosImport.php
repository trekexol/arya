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
        
            public function __construct($banco)
            {
                $this->banco = $banco; // errro en en linea
            }


    public function collection(Collection $rows)
    {
        

       if($this->banco == 1){

        $i = 0;
                foreach($rows as $row){

                if($i > 2){
                  

                    /*********DANDO FORMATO A LA FECHA ****/
                    $arr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]));
                   
                     /*********FIN DANDO FORMATO A LA FECHA ****/
                    $Client = TempMovimientos::Create([
                    
                        'banco'                   => 'bancamiga',
                        'referencia_bancaria'     => $row[2], 
                        'descripcion'             => $row[3],
                        'fecha'                   => $arr, 
                        'haber'                   => $row[4], 
                        'debe'                    => $row[5], 
                    
                    ]);
            
                }

         $i++;
                }
       }


       
       elseif($this->banco == 2){

        $i = 0;
                foreach($rows as $row){

                if($i > 0){

                    $arr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]));


                    if( is_numeric($row[3]) AND ($row[3]<0) ){
                       $haber = trim($row[3], '-');
                       $debe = 0;
                    }else{
                        $debe = $row[3];
                        $haber = 0;
                    }

                    $Client = TempMovimientos::Create([
                    
                        'banco'                   => 'banesco',
                        'referencia_bancaria'     => $row[1], 
                        'descripcion'             => $row[2],
                        'fecha'                   => $arr, 
                        'haber'                   => $haber, 
                        'debe'                    => $debe, 
                    
                    ]);
            
                }

         $i++;
                }
       }


       elseif($this->banco == 3){

  
                foreach($rows as $row){

           
                    if($row[5] == 'ND' OR $row[5] == 'NC'){

                     
                       $monto =  str_replace(",", ".", $row[7]);;
              
                            if($row[5] == 'ND'){
                                $haber = $monto;
                                $debe = 0;
                            }elseif($row[5] == 'NC'){
                                $haber = 0;
                                $debe = $monto;
                            }
                    

                    $fecha = $row[3];
                    $dias = substr($row[3], 0, 2);
                    $mes = substr($row[3], 2, 2);
                    $años = substr($row[3], 4, 4);
                    $fechacompleta = $años.'-'.$mes.'-'.$dias;
                    
             

                    $Client = TempMovimientos::Create([
                    
                        'banco'                   => 'mercantil',
                        'referencia_bancaria'     => $row[4], 
                        'descripcion'             => $row[6],
                        'fecha'                   => $fechacompleta, 
                        'haber'                   => $haber, 
                        'debe'                    => $debe, 
                    
                    ]);
            
             
                }
      
                }
       }  elseif($this->banco == 4){

  
        $i = 0;
        foreach($rows as $row){

        if($i > 0){

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


     

            $Client = TempMovimientos::Create([
            
                'banco'                   => 'chase',
                'referencia_bancaria'     => NULL, 
                'descripcion'             => $row[2],
                'fecha'                   => $fechacompleta, 
                'haber'                   => $haber, 
                'debe'                    => $debe, 
            
            ]);
    
        }

 $i++;
        }
}

elseif($this->banco == 5){

  
    $i = 0;
    foreach($rows as $row){

    if($i > 0){

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


 

        $Client = TempMovimientos::Create([
        
            'banco'                   => 'BOFA',
            'referencia_bancaria'     => NULL, 
            'descripcion'             => $row[2],
            'fecha'                   => $fechacompleta, 
            'haber'                   => $haber, 
            'debe'                    => $debe, 
        
        ]);

    }

$i++;
    }
}

  
        $Client->setConnection(Auth::user()->database_name);

 
    }

}