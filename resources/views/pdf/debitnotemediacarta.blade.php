<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Nota de Débito</title>
<style>
  
  body {
    font-family: Arial, Helvetica, sans-serif;
  }

  table, td, th {
    border: 1px solid black;
    font-size: 9pt;
  }
  
  table {
    border-collapse: collapse;
    width: 50%;
  }
  
  th {  
    text-align: left;
  }

  #top {
    margin-top: -35px;
  }

  </style>
</head>
@if($valor == 1)
<body>
  <table>
    <tr>
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h5>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h5></th>    </tr> 
    </tr> 
  </table>
<div style="margin-top: -15px; margin-top: -15px; color: black;font-size: 9pt;font-weight: bold; text-align: right;">NOTA DE DÉBITO NRO: {{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</div>
<table>

  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
    <td>{{ date_format(date_create($quotation->date),"d-m-Y") }}</td>
    
  </tr>
  
</table>
<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal;">Nombre / Razón Social: &nbsp;  {{ $quotation->clients['name'] ?? ''}} </th>
    <th style="font-weight: normal;">Vendedor: {{ $quotation->vendors['name'] ?? 'No aplica' }} {{ $quotation->vendors['surname'] ?? ''}} </th>
    
  </tr>
</table>
<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal;">Domicilio Fiscal: &nbsp;  {{ $quotation->clients['direction'] ?? ''}}</th>
  </tr>
</table>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/C.I</th>
    <th style="text-align: center;">Pedido</th>
    <th style="text-align: center;">N° Ctrl/Serie</th>
    <th style="text-align: center;">Condición de Pago</th>
    <th style="text-align: center;">Factura Afectada</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_pedido ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie }}</td>
    <td style="text-align: center;">Nota de Débito</td>
    <td style="text-align: center;">{{ $quotation_origin->number_invoice ?? '' }}</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; ">Observaciones: &nbsp; {{ $quotation->observation ?? ''}} </th>
</tr>
  
</table>

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Productos</th>
  </tr> 
</table>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Cantidad</th>
    <th style="text-align: center; ">P.V.J.</th>
    <th style="text-align: center; ">Desc</th>
    <th style="text-align: center; ">Total</th>
  </tr> 

  @foreach ($inventories_quotations as $var)
      <?php
      if($coin == "dolares"){
     
        $monto = $var->price;
       
      }else{
        $monto = $var->price;
      }
      

      $percentage = (($monto * $var->amount) * $var->discount)/100;

      $total_less_percentage = ($monto * $var->amount) - $percentage;
      
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount, 0, '', '.') }}</th>
      
      <th style="text-align: center; font-weight: normal;">{{ number_format($monto, 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php

  $base_imponible = $quotation->base_imponible;
  //$total_factura = $quotation->amount_with_iva;

  $total_factura = $base_imponible;

  if ($base_imponible > 0) {
  $iva = ($base_imponible * $quotation->iva_percentage)/100;
  } else {
  $iva = 0;    
  }

  $total = $total_factura + $iva;

?>

<table style="width: 100%;">
  <!--<tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($quotation->total_factura, 2, ',', '.') }}</th>
  </tr>--> 
  @if($base_imponible > 0)
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($base_imponible, 2, ',', '.') }}</th>
  </tr> 
 @endif
 @if(isset($retiene_iva))
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(($retiene_iva ?? 0), 2, ',', '.') }}</th>
  </tr>
  @endif
  @if($base_imponible > 0)
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
  </tr> 
  @endif
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">Nota de Débito</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);"></th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">MONTO TOTAL</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total, 2, ',', '.') }}</th>
  </tr> 
  
</table>

</body>
@endif
</body>
</html>
