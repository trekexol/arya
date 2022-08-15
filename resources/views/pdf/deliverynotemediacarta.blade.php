<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Nota de Entrega</title>
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

<body>
  <table id="top">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="120" height="60" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->code_rif ?? ''}} </h4></th>
    </tr> 
  </table>
<div style="margin-top: -15px; margin-top: -15px; color: black;font-size: 9pt;font-weight: bold; text-align: right;">NOTA DE ENTREGA NRO: {{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</div>
<table>
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 40%;">Concesión Postal:</th>
    <th style="font-weight: normal;">Nº {{ $company->franqueo_postal ?? ''}}</th>
   
  </tr>
  @endif
  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
    <td>{{ date_format(date_create($quotation->date_delivery_note),"d-m-Y") }}</td>
    
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
    <th style="text-align: center;">Transporte/Tipo de Entrega</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_pedido ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie }}</td>
    <td style="text-align: center;">Nota de Entrega</td>
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
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
      
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;
      
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(bcdiv($total_less_percentage, '1', 2), 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php

  $base_imponible = $quotation->base_imponible;
  $total_factura = $quotation->amount_with_iva;

  $iva = ($base_imponible * $quotation->iva_percentage)/100;

  $total = $total_factura;

?>

<table style="width: 100%;">
  <!--<tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($quotation->total_factura, 2, ',', '.') }}</th>
  </tr>--> 

  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($base_imponible, 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(($retiene_iva ?? 0), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">MONTO TOTAL</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total, 2, ',', '.') }}</th>
  </tr> 
  
</table>
@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal; font-size: small;">
     Nota: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
<div style="color: black;font-size: 9pt; text-align: center;">Original Cliente</div>
<table>
  <tr>
    <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="120" height="60" class="d-inline-block align-top" alt="">
    </th>
    <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->code_rif ?? ''}} </h4></th>
  </tr> 
</table>
<!-- Repeticion de lo mismo -->
<div style="margin-top: -15px; color: black;font-size: 9pt;font-weight: bold; text-align: right;">NOTA DE ENTREGA NRO: {{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</div>
<table>
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 40%;">Concesión Postal:</th>
    <th style="font-weight: normal;">Nº {{ $company->franqueo_postal ?? ''}}</th>
   
  </tr>
  @endif
  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
     @if (isset($quotation->date_delivery_note))
    <td>{{ date_format(date_create($quotation->date_delivery_note),"d-m-Y")}}</td>
     @else
      <td></td>
    @endif
    
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
    <th style="text-align: center;">Transporte/Tipo de Entrega</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_pedido ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie }}</td>
    <td style="text-align: center;">Nota de Entrega</td>
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
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
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;
    
    ?>
  <tr>
    <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
    <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
    <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
    <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
    <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format(bcdiv($total_less_percentage, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endforeach 
  </table>


  <?php

    $base_imponible = $quotation->base_imponible;
    $total_factura = $quotation->amount_with_iva;

    $iva = ($base_imponible * $quotation->iva_percentage)/100;

    $total = $total_factura;
?>


  <table style="width: 100%;">
  <!--<tr>
  <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
  <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($quotation->total_factura, 2, ',', '.') }}</th>
  </tr>--> 

  <tr>
  <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
  <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($base_imponible, 2, ',', '.') }}</th>
  </tr> 
  <tr>
  <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
  <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(($retiene_iva ?? 0), 2, ',', '.') }}</th>
  </tr> 
  <tr>
  <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
  <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
  </tr> 
  <tr>
  <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">MONTO TOTAL</th>
  <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total, 2, ',', '.') }}</th>
  </tr> 
</table>
@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal; font-size: small;">
     Nota: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
<div style="color: black;font-size: 9pt; text-align: center;">Copia (Sin Derecho a Crédito Fiscal)</div>

<table>
  <tr>
    <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="120" height="60" class="d-inline-block align-top" alt="">
    </th>
    <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->code_rif ?? ''}} </h4></th>
  </tr> 
</table>

<!-- Repeticion de lo mismo -->
<div style="margin-top: -15px; color: black;font-size: 9pt;font-weight: bold; text-align: right;">NOTA DE ENTREGA NRO: {{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</div>
<table>
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 40%;">Concesión Postal:</th>
    <th style="font-weight: normal;">Nº {{ $company->franqueo_postal ?? ''}}</th>
  </tr>
  @endif
  
  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
     @if (isset($quotation->date_delivery_note))
    <td>{{ date_format(date_create($quotation->date_delivery_note),"d-m-Y")}}</td>
     @else
      <td></td>
    @endif
    
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
    <th style="text-align: center;">Transporte/Tipo de Entrega</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_pedido ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie }}</td>
    <td style="text-align: center;">Nota de Entrega</td>
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
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
     
        $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

        $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;
      
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(bcdiv($total_less_percentage, '1', 2), 2, ',', '.') }}</th>
    </tr> 
    @endforeach 
    </table>


    <?php

      $base_imponible = $quotation->base_imponible;
      $total_factura = $quotation->amount_with_iva;

      $iva = ($base_imponible * $quotation->iva_percentage)/100;

      $total = $total_factura;
    ?>

    <table style="width: 100%;">
    <!--<tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($total_factura, 2, ',', '.') }}</th>
    </tr>--> 

    <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($base_imponible, 2, ',', '.') }}</th>
    </tr> 
    <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(($retiene_iva ?? 0), 2, ',', '.') }}</th>
    </tr> 
    <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
    </tr> 
    <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">MONTO TOTAL</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total, 2, ',', '.') }}</th>
    </tr> 

</table>
@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal; font-size: small;">
     Nota: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
<div style="color: black;font-size: 9pt; text-align: center;">Copia Contabilidad (Sin Derecho a Crédito Fiscal)</div>

</body>
</html>
