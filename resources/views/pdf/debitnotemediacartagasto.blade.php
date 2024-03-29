<?php

if($DebitNoteExpense->percentage == 0){
    $titulo = 'NOTA DE DEBITO';
    $condicion = 'Nota de debito';
}else{
    $titulo = 'NOTA DE CREDITO';
    $condicion = 'Nota de credito';

}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>{{$titulo}}</title>
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
<div style="margin-top: -15px; margin-top: -15px; color: black;font-size: 9pt;font-weight: bold; text-align: right;">{{$titulo}} NRO: {{ str_pad($DebitNoteExpense->id, 6, "0", STR_PAD_LEFT)}}</div>
<table>

  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
    <td>{{ date_format(date_create($DebitNoteExpense->date),"d-m-Y") }}</td>

  </tr>

</table>
<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal;">Nombre / Razón Social: &nbsp;  {{ $expensesandpurchases->providers['razon_social'] ?? ''}} </th>
  </tr>
</table>
<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal;">Domicilio Fiscal: &nbsp;  {{ $expensesandpurchases->providers['direction'] ?? ''}}</th>
  </tr>
</table>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/C.I</th>
    <th style="text-align: center;">N° Ctrl/Serie</th>
    <th style="text-align: center;">Condición</th>
    <th style="text-align: center;">Factura Afectada</th>

  </tr>
  <tr>
    <td style="text-align: center;">{{ $expensesandpurchases->providers['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $expensesandpurchases->providers['code_provider']}}</td>
    <td style="text-align: center;">{{ $expensesandpurchases->serie }}</td>
    <td style="text-align: center;">{{$condicion}}</td>
    <td style="text-align: center;">{{ $expensesandpurchases->invoice ?? '' }}</td>


  </tr>

</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; ">Observaciones: &nbsp; {{ $DebitNoteExpense->obs ?? ''}} </th>
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
    <th style="text-align: center; ">Precio por unidad</th>
    <th style="text-align: center; ">Total</th>
  </tr>
<?php $totaldebito = 0; ?>
  @foreach ($DebitNoteDetailExpense as $var)
      <?php
      if($coin == "dolares"){

        $monto = $var->price / $bcv;

        $montoporunidad = $monto;
        $monto_perc = $DebitNoteExpense->monto_perc / $bcv;

      }else{
        $monto = $var->price;
        $montoporunidad = $monto;
        $monto_perc = $DebitNoteExpense->monto_perc;
      }


      $percentage = $var->percentage;

    $totaldebito += $monto;

      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->amount }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($montoporunidad, 2, ',', '.')}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($montoporunidad * $var->amount , 2, ',', '.') }}</th>
    </tr>
  @endforeach
</table>


<?php
 if($coin == "dolares"){

  $base_imponible = $DebitNoteExpense->base_imponible / $bcv;

   }else{
    $base_imponible = $DebitNoteExpense->base_imponible;
   }

  $total_factura = $base_imponible;



  $total = $total_factura;

?>

<table style="width: 100%;">
  <!--<tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($DebitNoteExpense->total_factura, 2, ',', '.') }}</th>
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
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">{{$condicion}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{number_format(($monto_perc ?? $totaldebito), 2, ',', '.')  }}</th>
  </tr>
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); ">MONTO TOTAL</th>
    @if($DebitNoteExpense->percentage == 0)
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total + $monto_perc ?? $totaldebito, 2, ',', '.') }}</th>

    @else
    <th style="text-align: right; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9);">{{ number_format($total - $monto_perc ?? $totaldebito, 2, ',', '.') }}</th>
    @endif
</tr>
</table>
@if($DebitNoteExpense->percentage > 0)
<p>Nota : {{ $DebitNoteExpense->notapie  }}</p>
@endif
</body>
@endif

</html>
