<?php
  use Carbon\Carbon;

  function number_words($valor, $sep, $desc_decimal) {
        $arr = explode(".", $valor);
        $entero = $arr[0];
        if (isset($arr[1])) {
        $decimos = strlen($arr[1]) == 1 ? $arr[1] . '0' : $arr[1];
        }

        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        if (is_array($arr)) {
        $num_word = ($arr[0]>=1000000) ? "{$fmt->format($entero)} de " : "{$fmt->format($entero)} ";
        if (isset($decimos) && $decimos > 0) {
        $num_word .= " $sep {$fmt->format($decimos)} $desc_decimal";
        }
        }
return $num_word;
}

function verMeses($fechaInicio,$fechaFin){



echo $intervalo->y;

}

function mesletras($valor) {
    if($valor == '01'){
        $mes = 'ENERO';
    }if($valor == '02'){
        $mes = 'FEBRERO';
    }if($valor == '03'){
        $mes = 'MARZO';
    }if($valor == '04'){
        $mes = 'ABRIL';
    }if($valor == '05'){
        $mes = 'MAYO';
    }if($valor == '06'){
        $mes = 'JUNIO';
    }if($valor == '07'){
        $mes = 'JULIO';
    }if($valor == '08'){
        $mes = 'AGOSTO';
    }if($valor == '09'){
        $mes = 'SEPTIEMBRE';
    }if($valor == '10'){
        $mes = 'OCTUBRE';
    }if($valor == '11'){
        $mes = 'NOVIMEBRE';
    }if($valor == '12'){
        $mes = 'DICIEMBRE';
    }

    return $mes;
     }




?>
@if($tipo == 'prestacion')

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title></title>
<style>
  table, td, th {
    border: 1px solid black;
    font-size: x-small;
    font-size: 8pt;
  }

  table {
    border-collapse: collapse;
    width: 100%;

  }

  th {

    text-align: left;
  }
  </style>


</head>

<body>
  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr>
  </table>
  <h4 style="color: black; text-align: center">PRESTACIONES</h4>
  <h4 style="color: black; text-align: left">Nombre: {{$employee->nombres.' '.$employee->apellidos}} C.I: {{$employee->id_empleado}}</h4>


<table style="width: 100%;">
  <tr>
<th style="text-align: center; border-right-color: black; border-left-color: black;">MT</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Mes</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Año</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">CDBV</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">VAC</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Sueldo Actual</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Salario Diario</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Alicuota Utili</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Alicuota Vacac</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Salario Integral</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Dias x Mes</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">+Dias</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Prest. Asignadas</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Anticipo</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Prest. Acumulada</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Tasa</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Intereses</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Intereses Acumulados</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Total Prest + Int</th>Nom</th>
</tr>

<?php

    if($employee->amount_utilities == 'Ma'){

        $diasutilidades = 120;

    }else{
        $diasutilidades = 30;
    }
$i = 1;
$o = 1;
$cantidadmeses = 1;
$diasvacaciones = 15;
$diasextras = 0;
$diasvaca = '';
$acumulado = 0;
$interesesacumulado = 0;
?>

@foreach ($datospresta as $datospresta)

<?php

    $sueldodiario = $datospresta->monto/30;
    $cuotautilidad = $sueldodiario*$diasutilidades/360;



    if($o == 24){
        $diasvacaciones = $diasvacaciones + 1;
        $diasextras = $diasextras + 1;
        $os = 1;
    }

    elseif(isset($os) AND $os == 12){
        $diasvacaciones = $diasvacaciones + 1;

        $os = 1;
    }elseif(isset($os)){
        $os++;
    }

    $cuotavaca = $sueldodiario*$diasvacaciones/360;

    $salariointegral = $sueldodiario + $cuotautilidad + $cuotavaca;



    if($cantidadmeses == 3)
    {
      $asig =   $salariointegral * $diasvacaciones;
      $diasvaca = 15;
      $diasextrass = $diasextras;
      $cantidadmeses = 0;
      $ultimodia = 15;
      $acumulado += $asig;
      $interes = $acumulado * $datospresta->tasaaver / 1200;
      $interesesacumulado += $interes;
    }else{
        $diasvaca = '';
        $diasextrass = '';
        $asig = 0;
        $acumulado += $asig;
        $interes = 0;
        $interesesacumulado += $interes;
        $ultimodia = 15;
    }


?>


    <tr>
      <td style="text-align: center; ">{{ $i }}</td>
      <td style="text-align: center; font-weight: normal;">{{$datospresta->mes }}</td>
      <td style="text-align: center; font-weight: normal;">{{ $datospresta->año }}</td>
      <td style="text-align: center; font-weight: normal;">{{ ''}}</td>
      <td style="text-align: center; ">{{$diasvacaciones}}</td>
      <td style="text-align: center; font-weight: normal;">{{$datospresta->monto}}</td>
      <td style="text-align: center; font-weight: normal;">{{number_format(($datospresta->monto/30), 2, ',', '.')}}</td><!--D-->
      <td style="text-align: center; font-weight: normal;">{{number_format(($cuotautilidad), 2, ',', '.')}}</td><!--C-->
      <td style="text-align: center; font-weight: normal;">{{number_format(($cuotavaca), 2, ',', '.')}}</td><!--FA-->
      <td style="text-align: center; font-weight: normal;">{{number_format(($salariointegral), 2, ',', '.')}}</td><!--CA-->
        <td style="text-align: center; font-weight: normal;">{{ $diasvaca }}</td>
        <td style="text-align: center; font-weight: normal;">{{ $diasextrass }}</td>
        <td style="text-align: center; font-weight: normal;">{{number_format(($asig), 2, ',', '.')   }}</td><!--BIA-->
        <td style="text-align: center; font-weight: normal;">{{ '' }}</td><!--16A -->
        <td style="text-align: center; font-weight: normal;">{{ number_format(($acumulado), 2, ',', '.')    }}</td><!-- IvaA-->
        <td style="text-align: center; font-weight: normal;">{{ number_format(($datospresta->tasaaver), 2, ',', '.') }}</td>
        <td style="text-align: center; font-weight: normal;">{{ number_format(($interes), 2, ',', '.')}}</td><!--16B -->
        <td style="text-align: center; font-weight: normal;">{{ number_format(($interesesacumulado), 2, ',', '.') }}</td>
        <td style="text-align: center; font-weight: normal;">{{  number_format(($acumulado + $interesesacumulado), 2, ',', '.')  }}</td>

    </tr>

    <?php


        $cantidadmeses++;
        $o++;
        $i++;
    ?>

    @endforeach

  <tr >

    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{$diasvacaciones}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{$datospresta->monto}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{number_format(($datospresta->monto/30), 2, ',', '.')}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{number_format(($cuotautilidad), 2, ',', '.')}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{number_format(($cuotavaca), 2, ',', '.')}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{number_format(($salariointegral), 2, ',', '.')}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{$ultimodia}}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ $diasextrass }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ number_format(($asig), 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ '' }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ number_format(($acumulado), 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ '' }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ '' }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ number_format(($interesesacumulado), 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{ number_format(($acumulado + $interesesacumulado), 2, ',', '.') }}</th>

</tr>
</table>

</body>
</html>




@elseif($tipo == 'liquidacion')


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{asset('vendor/sb-admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Liquidación</title>
<style>
  body{
    background: white;
  }
  table, td, th {
    border: 1px solid black;
    background: white;
  }

  table {
    border-collapse: collapse;
    width: 100%;
  }

  th {

    text-align: left;
  }
  </style>
</head>

<body>
    <table>
        <tr>
          <th style="text-align: left; font-weight: normal; width: 15%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}"  height="50" class="d-inline-block align-top" alt="">
          </th>
          <th style="text-align: left; font-weight: normal; width: 80%; border-color: white; font-weight: bold;"><h6>{{Auth::user()->company->razon_social ?? ''}}  <h6>{{Auth::user()->company->code_rif ?? ''}}</h6></h6> </th>
        </tr>
      </table>
<div class="small" style="font-size: 12px;">

  <div class="text-center h6">RECIBO DE LIQUIDACIÓN</div>

<div class="small">


    <table style="width: 25%;">
      <tr>
        <th >Fecha: {{ $datenow }}</th>
    </table>

    <table style="width: 100%;">
      <tr>
        <th style="width: 72%; border-right: none;">Nombre de la Empresa: {{ $company->razon_social ?? ''}}</th>
        <th style="width: 28%;" class="font-weight-normal">Rif: {{ $company->code_rif ?? ''}}</th>
      </tr>
    </table>



    <table style="width: 100%;">
      <tr>
        <th style="width: 25%; ">Domicilio Fiscal:</th>
        <th style="width: 75%;" class="font-weight-normal">{{ $company->address ?? ''}}</th>
      </tr>
    </table>

    <table style="width: 100%;">
      <tr>
        <th  class="text-center" style="border-bottom-color: white; width: 25%;">Empleado</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Nombre del Trabajador:</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Cargo</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Cédula</th>
      </tr>
      <tr>
        <td class="text-center font-weight-normal"></td>
        <td class="text-center font-weight-normal">{{ $employee->nombres }} {{ $employee->apellidos}}</td>
        <td class="text-center font-weight-normal">{{ $employee->name }}</td>
        <td class="text-center font-weight-normal">{{ $employee->id_empleado }}</td>
      </tr>
    </table>


    <?php

      if($employee->motivo == 1){
        $motivo = 'Renuncia';
      }elseif($employee->motivo == 2){
        $motivo = 'Despido';
      }else{
        $motivo = 'S/D';
      }
      $montoespecial = 0;
      if ($employee->id_empleado == 'V-18.933.860'){
        $motivo = 'Renuncia';
        $tipo_bono =  'BONIFICACIÓN ÚNICA POR FINALIZACIÓN DE RELACIÓN LABORAL';
        $montoespecial = '2661.89';

      } else {
        $tipo_bono = 'INDEMNIZACION ART.. 92 LOTTT';
      }

      $sueldodiario = number_format($employee->monto_pago / 30, 2, '.', '.');
      $cuotautilidad = $sueldodiario*$diasutilidades/360;
      $cuotavaca = $sueldodiario*$diasvacaciones/360;

      $salariointegral = $sueldodiario + number_format($cuotautilidad, 2, '.', '.') + number_format($cuotavaca, 2, '.', '.');

     $fechaex = explode('-',$employee->fecha_ingreso);
     $fechaexplode = explode('-',$employee->fecha_egreso);


    $feini = new DateTime($fechaex[0].'-'.$fechaex[1]);
    $fefin = new DateTime($fechaexplode[0].'-'.$fechaexplode[1]);



    $diferencia = $feini->diff($fefin);

    $años = $diferencia->format('%Y');
    $mes = $diferencia->format('%M');
    $dias = $diferencia->format('%D');


      if($años > 0){

        $añoservicio = $años;
        $tiempoparautili = $mes;

      }elseif($mes > 5){
        $añoservicio = 1;
        $tiempoparautili = $mes;
      }else{
        $añoservicio = 0;
        $tiempoparautili = $mes;
      }

    $prestaarticulo =  30 * $añoservicio * $salariointegral;
    $prestaarticulo =  number_format($prestaarticulo, 2, ',', '.');
    $prestaarticulo = str_replace(".", "", $prestaarticulo);



    $acumulado =  number_format($acumulado, 2, ',', '.');
    $acumulado = str_replace(".", "", $acumulado);



    $interesesacumulado =  number_format($interesesacumulado, 2, ',', '.');
    $interesesacumulado = str_replace(".", "", $interesesacumulado);


    $pagovacaciones = $employee->monto_pago / 30 * $diasvacaciones;

    $vacapaga = $pagovacaciones * 2;
    $vacapaga =  number_format($vacapaga, 2, ',', '.');
    $vacapaga = str_replace(".", "", $vacapaga);

    $pagovacaciones =  number_format($pagovacaciones, 2, ',', '.');
    $pagovacaciones = str_replace(".", "", $pagovacaciones);


    ?>


    <table style="width: 100%;">
      <tr>
        <th  class="text-center" style="border-bottom-color: #ffffff; width: 25%;">Tiempo de Servicio</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Fecha de Ingreso</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Fecha de Egreso</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Años</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Meses</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Motivo</th>
      </tr>
      <tr>
        <td class="text-center font-weight-normal"></td>
        <td class="text-center font-weight-normal">{{ $employee->fecha_ingreso }}</td>
        <td class="text-center font-weight-normal">{{ $employee->fecha_egreso ?? '' }}</td>
        <td class="text-center font-weight-normal">{{ $años }}</td>
        <td class="text-center font-weight-normal">{{ $mes }}</td>
        <td class="text-center font-weight-normal">{{ $motivo }}</td>
      </tr>
    </table>


    <table style="width: 100%;">
      <tr>
        <th  class="text-center" style="border-bottom-color: #ffffff; width: 25%;">Periodo Actual</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Fecha de último Pago</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Periodo</th>
        <th  class="text-center" style="background: rgb(221, 221, 221)">Mes</th>
      </tr>
      <tr>
        <td class="text-center font-weight-normal"></td>

        <td class="text-center font-weight-normal">{{ $ultimopago_e}}</td>
        <td class="text-center font-weight-normal">{{ \Carbon\Carbon::parse($ultimopago_e)->format('Y') ?? '' }}</td>
        <td class="text-center font-weight-normal">{{ \Carbon\Carbon::parse($ultimopago_e)->format('M') ?? '' }}</td>

      </tr>
    </table>



      <table style="width: 100%;">
        <tr>
          <th  class="text-center" style="border-bottom-color: #ffffff; width: 25%;">Último Salario</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Último Sueldo</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Sueldo Diario</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Alic. Utilidades</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Alic. Vacaciones</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Salario Integral</th>
        </tr>
        <tr>
          <td class="text-center font-weight-normal"></td>
          <td class="text-center font-weight-normal">{{ number_format($employee->monto_pago, 2, '.', '.') }}</td>
          <td class="text-center font-weight-normal">{{ number_format($employee->monto_pago / 30, 2, '.', '.') }}</td>
          <td class="text-center font-weight-normal">{{number_format($cuotautilidad, 2, '.', '.')}}</td>
          <td class="text-center font-weight-normal">{{number_format($cuotavaca, 2, '.', '.')}}</td>
          <td class="text-center font-weight-normal">{{number_format($salariointegral, 2, '.', '.')}}</td>
        </tr>
      </table>




        <table style="width: 100%;">
          <tr>
            <th  class="text-center" style="width: 68%;">Descripción</th>
            <th  class="text-center" style="width: 16%;">Monto</th>
            <th  class="text-center" style="width: 16%;">Total</th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">1 - PRESTACIONES SOCIALES</th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">A - Garantia de Prestaciones Acumuladas</th>

            <th  class="text-center" style="width: 16%;">{{$acumulado}}</th>

            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">B - Prestaciones Sociales LOTTT Art. 142 Literal "C"</th>
            <th  class="text-center" style="width: 16%;">{{$prestaarticulo}}</th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Total Prestaciones Sociales LOTTT Art. 142 Literal "D". Monto mayor entre A y B</td>
            <td  class="text-center" style="width: 16%;"></td>
            <th  class="text-center" style="width: 16%;">
                <?php
                    if(str_replace(",", ".", $acumulado) > str_replace(",", ".", $prestaarticulo)){
                        $totaloot =  $acumulado;

                    }else{
                        $totaloot = $prestaarticulo;
                    }
                ?>

                {{$totaloot}}
            </th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">2 - INTERESES SOBRE PRESTACIONES SOCIALES</th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Intereses Garantia Prestaciones LOTTT 2014. Art. 143</th>
            <th  class="text-center" style="width: 16%;">{{ $interesesacumulado }}</th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Total Intereses Garantia Prestaciones LOTTT 2014. Art. 143</td>
            <td  class="text-center" style="width: 16%;"></td>
            <th  class="text-center" style="width: 16%;">{{ $interesesacumulado }}</th>
          </tr>
        </table>

        <?php

        $total_vacaciones_bonificaciones = $employee->otras_asignaciones;
        ?>


        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">3 - VACACIONES Y BONIFICACIONES</th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Dias de Vacaciones: {{$diasvacaciones}} Dia(s)</th>
            <th  class="text-center" style="width: 16%;">{{$pagovacaciones}}</th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Bono Vacacional: {{$diasvacaciones}} Dia(s)</td>
            <th  class="text-center" style="width: 16%;">{{$pagovacaciones}}</th>
            <td  class="text-center" style="width: 16%;"></td>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Dias Vacaciones Fraccionadas: Dia(s)</td>
            <td  class="text-center" style="width: 16%;"></td>
            <td  class="text-center" style="width: 16%;"></td>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Bono Vacacional Fraccionado: Dia(s) </td>
            <td  class="text-center" style="width: 16%;"></td>
            <td  class="text-center" style="width: 16%;"></td>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Otras Asignaciones:</td>
            <td  class="text-right" style="width: 16%;"></td>
            <td  class="text-center" style="width: 16%;"></td>
          </tr>
          <tr>
            <td  class="text-left" style="width: 68%;">Total Vacaciones y Bonificaciones:</td>
            <td  class="text-right" style="width: 16%;"></td>
            <th  class="text-center" style="width: 16%;">{{ $vacapaga }}</th>
          </tr>
        </table>


        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">4 - UTILIDADES:</th>
          </tr>
        </table>
        <?php
        if($tiempoparautili == 0){
            $tiempoparautili = 1;
            $pagotiempo = 30 / 12 * $tiempoparautili;

        }else{
            $pagotiempo = 30 / 12 * $tiempoparautili;
        }
    ?>
        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Total de Utilidades Fraccionadas a 30 Dias /12 * {{$tiempoparautili}} Mes: {{$pagotiempo}} Dias</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;">

            {{ $totalud = number_format($pagotiempo * $employee->monto_pago / 30, 2, '.', '.')}}
            </th>
          </tr>
        </table>


        <?php

        $total_otras_deducciones = 0;

        if($employee->motivo == 1){

            $totaloot = 0;
        }else{
            $totaloot = str_replace(",", ".", $totaloot);
        }
        /***REMPLACE PARA LA SUMA**/

        $interesesacumulado = str_replace(",", ".", $interesesacumulado);
        $vacapaga = str_replace(",", ".", $vacapaga);
        $totaliquidacion1 = $totaloot +  $interesesacumulado + $vacapaga;
        $totaliquidacion2 = $totaloot;
        $totalapgarto = $totaliquidacion1 + $totaliquidacion2;
        ?>


        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">5 - OTRAS DEDUCIONES:</th>
          </tr>
        </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Seguro Social: {{ $employee->lunes ?? 0 }} lunes</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">F.A.O.V %</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">I.N.C.E.S %</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Anticipo de Prestaciones</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Dias No Laborados * {{ $employee->dias_no_laborados ?? 0 }}</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Otras Deducciones</th>
            <th  class="text-center" style="width: 16%;">{{ number_format($employee->otras_deducciones, 2, ',', '.')}}</th>
            <th  class="text-center" style="width: 16%;"></th>
          </tr>
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Total Otras Deducciones</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;">{{ number_format($total_otras_deducciones, 2, ',', '.')}}</th>
          </tr>
        </table>


        <table style="width: 100%;">
          <tr>
            <th  class="text-left" style="background: rgb(221, 221, 221)">TOTAL LIQUIDACIÓN
            </th>
          </tr>
        </table>

        <table style="width: 100%;">
            <tr>
              <th  class="text-left font-weight-normal" style="width: 68%;">Total Liquidacion</th>
              <th  class="text-center" style="width: 16%;"></th>
              <th  class="text-center" style="width: 16%;">{{ str_replace(".", ",", $totaliquidacion1) }}</th>
            </tr>
          </table>


          <table style="width: 100%;">
            <tr>
              <th  class="text-left" style="background: rgb(221, 221, 221)">{{$tipo_bono}}
              </th>
            </tr>
          </table>

          <table style="width: 100%;">
            <tr>
                <th  class="text-left font-weight-normal" style="width: 68%;">{{$tipo_bono}}</th>
                <th  class="text-center" style="width: 16%;"></th>
                <th  class="text-center" style="width: 16%;">{{  str_replace(".", ",", $montoespecial) }}</th>
              </tr>
              <tr>
                <th  class="text-left font-weight-normal" style="width: 68%;">Total Liquidacion</th>
                <th  class="text-center" style="width: 16%;"></th>
                <th  class="text-center" style="width: 16%;">{{  str_replace(".", ",", $totaliquidacion2) }}</th>
              </tr>
            </table>



        <table style="width: 100%;">
            <tr>
              <th  class="text-left" style="background: rgb(221, 221, 221)">TOTAL LIQUIDACIÓN
              </th>
            </tr>
          </table>

        <table style="width: 100%;">
          <tr>
            <th  class="text-left font-weight-normal" style="width: 68%;">Total a pagar....</th>
            <th  class="text-center" style="width: 16%;"></th>
            <th  class="text-center" style="width: 16%;">{{ str_replace(".", ",", $totalapgarto + $montoespecial) }}</th>
          </tr>
        </table>
        <p>El suscrito trabajador declara haber recibido de la empresa {{ $company->razon_social ?? ''}} la cantidad de Bolivares <b>{{number_words($totalapgarto,"con","centavos")}}</b>
         a su entera satisfacción por concepto de pago completo e indemnizaciones, hasta la fecha de la
          presente liquidación, no teniendo nada que reclamar en relación a salarios e indemnizaciones causadas por el contrato de trabajo que hoy queda
          terminado</p>




      <table style="width: 100%;">
        <tr>
          <th  class="text-left font-weight-normal" style="border-color: #ffffff; width: 50%;">__________________________________</th>
          <th  class="text-left" style="border-color: #ffffff; width: 50%;">__________________________________</th>
        </tr>
        <tr>
          <td  class="text-left font-weight-normal" style="border-color: #ffffff; width: 50%;">Empleado: {{ $employee->nombres }} {{ $employee->apellidos }} C.I : {{ $employee->id_empleado }}</td>
          <td  class="text-left" style="border-color: #ffffff; width: 50%;">Testigo</td>
        </tr>
        <tr>
            <td  class="text-left font-weight-normal" style="border-color: #ffffff; width: 50%;"> C.I : {{ $employee->id_empleado }}</td>
            <td  class="text-left" style="border-color: #ffffff; width: 50%;">C.I :</td>
          </tr>

      </table>

</div>
</div>

</body>
</html>



@elseif($tipo == 'balancecomprobacion')



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>balancecomprobacion</title>
<style>
     table, td, th {
    border: 1px solid black;
    font-size: x-small;
    font-size: 8pt;
  }

  table {
    border-collapse: collapse;
    width: 100%;

  }

  th {

    text-align: left;
  }
  </style>


</head>

<body>
  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr>
  </table>
  <h4 style="color: black; text-align: center">Balance de Comprobacion</h4>
  <h5 style="color: black; text-align: center">Fecha desde: {{ $ini }} Fecha Hasta: {{ $fin }}</h5>

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">CUENTAS</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;"  colspan="2">SUMAS</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;" colspan="2">SALDOS</th>
    </tr>


    <tr>
    <th></th>
    <th style="text-align: center;" >DEBE</th>
    <th style="text-align: center;" >HABER</th>
    <th style="text-align: center;">DEUDOR</th>
    <th style="text-align: center;" >ACREEDOR</th>
    </tr>

    <?php

    $totaldebe = 0;
    $totalhaber = 0;
    $totaldeudor = 0;
    $totalacreedor = 0;
    ?>


       @foreach ($arreglo as $arreglo)

        <?php

        $debe =  str_replace(".", "", $arreglo['Debe']);
        $debe =  str_replace(",", ".", $debe);

        $haber =  str_replace(".", "", $arreglo['Haber']);
        $haber =  str_replace(",", ".", $haber);
        $totaldebe += $debe;
        $totalhaber += $haber;
        ?>


       <tr>
        <td>{{ $arreglo['descripcion'] }}</td>
        <td>{{ $debe }}</td>
        <td>{{ $haber }}</td>

        @if($arreglo['saldoactual'] > 0)

        <?php
            $acree =  str_replace(".", "", $arreglo['saldoactual']);
            $acree =  str_replace(",", ".", $acree);
            $totalacreedor += $acree;
        ?>

        <td></td>
        <td>{{ $acree }}</td>
        @else

        <?php
        $saldodeudor = trim($arreglo['saldoactual'], "-");
        $saldodeudor =  str_replace(".", "", $saldodeudor);
        $saldodeudor =  str_replace(",", ".", $saldodeudor);
        $totaldeudor += $saldodeudor;
        ?>

        <td>{{ $saldodeudor }}</td>
        <td></td>


        @endif

        </tr>


       @endforeach
        <tr>
            <td>TOTAL</td>
            <td>{{ $totaldebe }}</td>
            <td>{{ $totalhaber }}</td>
            <td>{{ $totaldeudor }}</td>
            <td>{{ $totalacreedor }}</td>
        </tr>



</table>




</body>
</html>




@elseif($tipo == 'utilidades')



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>utilidades</title>
<style>
     table, td, th {
    border: 1px solid black;
    font-size: x-small;
    font-size: 8pt;
  }

  table {
    border-collapse: collapse;
    width: 100%;

  }

  th {

    text-align: left;
  }
  </style>


</head>

<body>

    <?php
    $total = 0;
    $i = 0;
    $fecha_actual = date("Y-m-d");

// Fecha de inicio
$fechaInicio = new DateTime($employee->fecha_ingreso);

// Fecha de finalización (puede ser la fecha actual)
$fechaFin = new DateTime($fecha_actual);

// Calcular la diferencia entre las fechas
$diferencia = $fechaInicio->diff($fechaFin);

// Acceder a los componentes de tiempo deseados
//echo "Diferencia de días: " . $diferencia->days . " días<br>";
//echo "Diferencia de meses: " . $diferencia->m . " meses<br>";
//echo "Diferencia de años: " . $diferencia->y . " años<br>";
//echo "Diferencia total en días: " . $diferencia->format('%R%a') . " días<br>";

    if($employee->amount_utilities == 'Ma'){

        $diasutilidades = 120;

        }else{
        $diasutilidades = 30;
        }

    ?>

  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr>
  </table>
  <h4 style="color: black; text-align: center">RECIBO DE UTILIDADES</h4>

  <div class="small" style="font-size: 12px;">

  <div class="small">


      <table style="width: 25%;">
        <tr>
          <th >Fecha: {{ $datenow }}</th>
      </table>

      <table style="width: 100%;">
        <tr>
          <th style="width: 72%; border-right: none;">Nombre de la Empresa: {{ $company->razon_social ?? ''}}</th>
          <th style="width: 28%;" class="font-weight-normal">Rif: {{ $company->code_rif ?? ''}}</th>
        </tr>
      </table>


      <table style="width: 100%;">
        <tr>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Domicilio Fiscal:</th>

        </tr>
        <tr>
          <td class="text-center font-weight-normal">{{ $company->address ?? ''}}</td>

        </tr>
      </table>

      <table style="width: 100%;">
        <tr>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Trabajador:</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Cargo</th>

        </tr>
        <tr>
          <td class="text-center font-weight-normal">{{ $employee->id_empleado }} {{ $employee->nombres }} {{ $employee->apellidos}}</td>
          <td class="text-center font-weight-normal">{{ $employee->name }}</td>

        </tr>
      </table>



      <table style="width: 100%;">
        <tr>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Fecha de Ingreso</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Motivo del Recibo</th>
          <th  class="text-center" style="background: rgb(221, 221, 221)">Dias a Pagar</th>
        </tr>
        <tr>
          <td class="text-center font-weight-normal">{{ $employee->fecha_ingreso }}</td>
          <td class="text-center font-weight-normal">Utilidades</td>
          <td class="text-center font-weight-normal">{{$diasutilidades}}</td>
        </tr>
      </table>



      <br>



          <table style="width: 100%;">
            <tr>
              <th  class="text-center" >Periodo</th>
              <th  class="text-center" >Mes</th>
              <th  class="text-center" >Monto</th>
            </tr>
            @foreach ($datospresta as $datospresta)
            <?php
                $total += $datospresta->monto;
            ?>
            <tr>
                <td  class="text-center" >{{$datospresta->año}}</td>
                <td  class="text-center" >{{mesletras($datospresta->mes)}}</td>
                <td  class="text-center" >{{$datospresta->monto}}</td>
              </tr>
              @php
                $i++;
              @endphp
            @endforeach
            <tr>

                <th  class="text-center" colspan="2" >Acumulado en el año.</th>
                <td  class="text-center"  >{{number_format($total, 2, ',', '.')}}</td>
            </tr>
            <tr>

                <th  class="text-center" colspan="2" >Utilidades.</th>
                <?php $pago = $employee->monto_pago / 12;
                        $pado2 = $pago * $i;
                ?>
                <td  class="text-center"  >{{number_format($pado2, 2, ',', '.')}}</td>
            </tr>
            <tr>

                <th  class="text-center" colspan="2" >Banavih deducción del 1%. </th>
                <td  class="text-center"  >-{{number_format(1 * $pado2 / 100, 2, ',', '.')}}</td>
            </tr>
            <tr>

                <th  class="text-center" colspan="2" >INCES deducción del 0.5%</th>
                <td  class="text-center"  >-{{number_format(0.5 * $pado2 / 100, 2, ',', '.')}}</td>
            </tr>
            <tr>

                <th  class="text-center" colspan="2" >Total Asignacion</th>
                <td  class="text-center"  >{{number_format($pado2 - (1 * $pado2 / 100) -(0.5 * $pado2 / 100), 2, ',', '.')}}</td>
            </tr>
          </table>







  </div>
  </div>




</body>
</html>


@elseif($tipo == 'ARC')



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>ARC</title>
<style>
     table, td, th {
    border: 1px solid black;
    font-size: x-small;
    font-size: 8pt;
  }

  table {
    border-collapse: collapse;
    width: 100%;

  }

  th {

    text-align: left;
  }
  </style>


</head>

<body>
  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr>
  </table>
  <h4 style="color: black; text-align: center">RELACIÓN DE INGRESO ANUAL / ARC</h4>
  <h4 style="color: black; text-align: center">AÑO FISCAL {{$años}}</h4>

<table>

    <tr>
        <th style="text-align: center;" >Empleado</th>
        <th style="text-align: center;" >Cargo</th>
        <th style="text-align: center;" >Fecha de Ingreso</th>
    </tr>

    <tr>
        <th style="text-align: center;" >{{ $employee->id_empleado }} {{ $employee->nombres }} {{ $employee->apellidos}}</th>
        <th style="text-align: center;" >{{ $employee->name }}</th>
        <th style="text-align: center;" >{{ $employee->fecha_ingreso }}</th>
    </tr>
</table>

<br>

    <table>

    <tr>
    <th style="text-align: center;" >Mes</th>
    <th style="text-align: center;" >Remuneración</th>
    <th style="text-align: center;">Remuneración Acumulada</th>
    </tr>

    <?php
    $total = 0;
    ?>


    @foreach ($datospresta as $datospresta)
        <tr>
            <td style="text-align: center;">{{ mesletras($datospresta->mes) }}</td>
            <td style="text-align: center;">{{ $datospresta->monto }} Bs.</td>

        @php
            $total += $datospresta->monto;
        @endphp

            <td style="text-align: center;">{{number_format($total, 2, ',', '.')  }} Bs.</td>
        </tr>
    @endforeach


</table>




</body>
</html>


@endif
