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
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ '' }} / Fecha desde: {{ '' }} Fecha Hasta: {{ '' }}</h5>
   

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">MT</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">CPT</th>
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
<th style="text-align: center; border-right-color: black; border-left-color: black;">Taza</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Intereses</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Int.</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Acumulados</th>
<th style="text-align: center; border-right-color: black; border-left-color: black;">Total Prest + Int</th>Nom</th>
</tr> 
  <?php
     //inicio de variables
  ?>
  <!--foreach ($quotations as $quotation)-->

    <tr>
      
      <td style="text-align: center; ">{{ '' }}</td>
      <td style="text-align: center; ">{{ ''}}</td>
      
      <td style="text-align: center; font-weight: normal;">{{''}}{{ '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ '' }}</td>
      <td style="text-align: center; ">{{ ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{''}}</td>
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--D-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--C-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--FA-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--CA-->


        <td style="text-align: right; font-weight: normal;">{{ number_format((0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format((0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--BIA-->
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--16A -->
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!-- IvaA-->
        <td style="text-align: right; font-weight: normal;">{{ number_format((0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--16B -->
        <td style="text-align: right; font-weight: normal;">{{ number_format((0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format((0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td>

    </tr> 
    <?php
      //$conteo++;
    ?>
  <!--endforeach--> 

  <tr  style="display:none;">
  
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-bottom-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal; font-style:bold; border-right-color: black; border-left-color: black;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;font-style:bold;  border-color: black; border-left: 1px;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ '' }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ '' }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
  </tr> 
</table>

<?php
$ano = 0;
$mes = 0;
/*$ano = substr($date_end, 6, 4); //fecha end
$mes = substr($date_end, 3, 2);*/

if($mes == '01'){
            $nro_mes    = "01";
            $mes_nombre = "ENERO";
        }elseif($mes == '02'){
            $nro_mes    = "02";
            $mes_nombre = "FEBRERO";
        }elseif($mes == '03'){
            $nro_mes    = "03";
            $mes_nombre = "MARZO";
        }elseif($mes == '04'){
            $nro_mes    = "04";
            $mes_nombre = "ABRIL";
        }elseif($mes == '05'){
            $nro_mes    = "05";
            $mes_nombre = "MAYO";
        }elseif($mes == '06'){
            $nro_mes    = "06";
            $mes_nombre = "JUNIO";
        }elseif($mes == '07'){
            $nro_mes    = "07";
            $mes_nombre = "JULIO";
        }elseif($mes == '08'){
            $nro_mes    = "08";
            $mes_nombre = "AGOSTO";
        }elseif($mes == '09'){
            $nro_mes    = "09";
            $mes_nombre = "SEPTIEMBRE";
        }elseif ($mes == '10'){
            $nro_mes    = "10";
            $mes_nombre = "OCTUBRE";
        }elseif ($mes == '11'){
            $nro_mes    = "11";
            $mes_nombre = "NOVIEMBRE";
        }else {
            $nro_mes    = "12";
            $mes_nombre = "DICIEMBRE";
        }

?>


</body>
</html>

