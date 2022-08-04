
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Orden de Compra</title>
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
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr> 
  </table>
  <br>
  <h4 style="color: black">ORDEN DE COMPRA NRO: {{ str_pad($expense->id, 6, "0", STR_PAD_LEFT)}} / SERIE: {{ $expense->serie }}</h4>

 
<table>
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 20%;">Concesión Postal:</th>
    <th style="font-weight: normal; width: 40%;">Nº {{ $company->franqueo_postal ?? ''}}</th>
  </tr>
  @endif

  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
    <td>{{ $expense->date_delivery_note }}</td>
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $expense->providers['razon_social'] }}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $expense->providers['direction'] }}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/CI</th>
    <th style="text-align: center;">N° Control / Serie</th>
    <th style="text-align: center;">Condición de Pago</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $expense->providers['phone1'] }}</td>
    <td style="text-align: center;">{{ $expense->providers['code_provider'] }}</td>
    <td style="text-align: center;">{{ $expense->serie }}</td>
    <td style="text-align: center;">Nota de Entrega</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; {{ $expense->observation ?? '' }} </th>
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
    <th style="text-align: center; ">Total</th>
  </tr> 
  @foreach ($inventories_expenses as $var)
      <?php
      
      $total_less_percentage = ($var->price * $var->amount_expense);

      $total_less_percentage = $total_less_percentage / ($bcv ?? 1);
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description_expense ?? $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_expense, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php
  $iva = ($expense->base_imponible * $expense->iva_percentage)/100;

  $total = $expense->total_factura + $iva;

  $total_petro = $total / ($bcv ?? 1) / $company->rate_petro;

  $iva = $iva / ($bcv ?? 1);

  $total = $total / ($bcv ?? 1);
?>

<table style="width: 100%;">
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->total_factura / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->base_imponible / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->ventas_exentas / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $expense->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">MONTO TOTAL</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($total, 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white; font-size: small;">MONTO TOTAL Petro</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($total_petro, 6, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); border-right-color: white; font-size: small;"><pre>ORDEN DE COMPRA        </pre></th>
    <th style="text-align: right; font-weight: normal; width: 21%; "></th>
  </tr> 
  
  
  
  
</table>

</body>
</html>
