<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Recibo de Condominio</title>
<style>
  table, td, th {
    border: 1px solid black;
  }
  
  table {
    border-collapse: collapse;
    width: 50%;
  }
  
  th {
    
    text-align: left;
  }
  </style>
</head>

<body>
  <table id="top">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="93" height="60" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>    </tr> 
  </table>
  @if ($company->format_header_lines > 0) 
    @for ($i = 0; $i < $company->format_header_lines; $i++)
    <br>
    @endfor
  @endif


  <?php

  $ano = substr(date_format(date_create($quotation->date_delivery_note),"d-m-Y"), 6, 4);
  $mes = substr(date_format(date_create($quotation->date_delivery_note),"d-m-Y"), 3, 2);
  
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

<h4 style="align:center; text-align: center; color: black"> RECIBO DE CONDOMINIO NRO: {{ str_pad($quotation->number_delivery_note, 6, "0", STR_PAD_LEFT)}}</h4>
<h4 style="align:center; text-align: center; color: black"> Mes de  {{ $mes_nombre ?? ''}}</h4>

<table style="width: 60%;">  
  <tr>

      <td style="width: 20%; border-color: white;">Fecha de Emisión:</td>
      <td style="width: 40%; border-color: white;">{{ date_format(date_create($quotation->date_quotation),"d-m-Y")}}</td>

  </tr>
</table>

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 40%;">Propietario</th>
    <th style="text-align: center;">ID Propietario</th>
    <th style="text-align: center;">Apartamento / Local:</th>
    <th style="text-align: center;">Teléfono</th>    
  </tr>
  <tr>
    <td style="text-align: center;">{{ $client->name}}</td>
    <td style="text-align: center;">{{ $client->cedula_rif ?? '' }}</td>
    <td style="text-align: center;">{{ $client->direction ?? ''}}</td>
    <td style="text-align: center;">{{ $client->phone1 ?? '' }}</td>
  </tr>
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; font-size: medium; border-color: white;">Observaciones: &nbsp; {{ $quotation->observation }} </th>
</tr>
</table>
<br>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Total a pagar del Propietario</th>
  </tr> 
</table>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:3%;">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; width:2%;">Cantidad</th>
    <th style="text-align: center; ">Total Bs.</th>
    <th style="text-align: center; ">Total USD</th>
  </tr> 
  <?php
    
    $total_generalbs = 0;
    $total_generalusd = 0;
   ?>
  @foreach ($inventories_quotations as $var)
      <?php
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

      $total_less_percentageusd = $total_less_percentage / ($quotation->bcv ?? 1);

      $total_generalbs += $total_less_percentage;
      $total_generalusd += $total_less_percentage / ($quotation->bcv ?? 1);

      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal; width:2%;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">${{ number_format(bcdiv($total_less_percentageusd,1,'3'), 3, ',', '.') }}</th>
    </tr> 
  @endforeach 


  <tr>
    <th style="text-align: center; border-color: white; font-weight: normal; width:3%;"></th>
    <th style="text-align: center; border-color: white; font-weight: normal; "></th>
    <th style="text-align: center; border-color: white; font-weight: normal; width:2%;"></th>
    <th style="text-align: right; border-color: white;">{{ number_format($total_generalbs, 2, ',', '.') }}</th>
    <th style="text-align: right; border-color: white;">${{ number_format($total_generalusd, 2, ',', '.') }}</th>
  </tr> 


</table>


<table style="width: 100%; margin-top: -22px;">
  <tr>
    <td style="border-color: white;">Tasa de cambio B.C.V: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs. a la fecha {{date_format(date_create($quotation->date_quotation),"d-m-Y")}}</td>
  </tr>
</table>
<br>
<div style="width: 100%; border-color: black; border-width:1px; border-style:solid">
<table style="width: 100%; border-color: black;">

<?php
$nota = "Favor realizar pagos a Nombre de:";
$nota2 ="CONDOMINIO RESIDENCIAS HELENA";
$nota3 ="Rif.: J-31059826-6";
$nota4 ="Banco Banesco Cuenta #01340420874203018846";

?>

    <tr style="border-color: white;"><td style="border-color: white;">{{$nota}}</td></tr>
    <tr style="border-color: white;"><td style="border-color: white;">{{$nota2}}</td></tr>
    <tr style="border-color: white;"><td style="border-color: white;">{{$nota3}}</td></tr>
    <tr style="border-color: white;"><td style="border-color: white;">{{$nota4}}</td></tr>

</table>


</body>
</html>
