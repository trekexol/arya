<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Relación</title>
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
  
  <br><br><br>
  @if ($company->format_header_lines > 0) 
    @for ($i = 0; $i < $company->format_header_lines; $i++)
    <br>
    @endfor
  @endif
<h4 style="color: black"> RELACIÓN DE GASTO NRO: {{ str_pad($quotation->number_invoice ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>

 
   
 
<table style="width: 60%;">
  @if (isset($company->franqueo_postal))
    @if ($company->franqueo_postal > 0)
      <tr>
        <th style="font-weight: normal; width: 20%;">Concesión Postal:</th>
        <th style="font-weight: normal; width: 40%;">Nº {{ $company->franqueo_postal ?? ''}}</th>
      </tr>
    @endif
  @endif
  
  <tr>
    @if (isset($quotation->credit_days))
      <td style="width: 20%;">Fecha de Emisión:</td>
      <td style="width: 40%;"> {{ date_format(date_create($quotation->date_billing),"d-m-Y") }} | Dias de Crédito: {{ $quotation->credit_days }}</td>
    @else
      <td style="width: 20%;">Fecha de Emisión:</td>
      <td style="width: 40%;">{{ date_format(date_create($quotation->date_billing),"d-m-Y")}}</td>
    @endif
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal; font-size: medium;">Condominio: &nbsp;  {{ $client->name }}</th>
    <th style="font-weight: normal; font-size: medium;">RIF/CI: &nbsp; {{ $client->type_code ?? ''}} {{ $client->cedula_rif ?? '' }}</th>
</table>

<table style="width: 100%;">
    <tr>
    <th style="font-weight: normal; font-size: medium;">Dirección: &nbsp;  {{ $client->direction}}</th>
    </tr>
    <tr>
    <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; {{ $quotation->observation }} </th>
   
  </tr>
  
</table>
  @if (!empty($payment_quotations))
      

      <br>
      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; width: 100%;">Condiciones de Pago</th>
        </tr> 
      </table>

      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; ">Tipo de Pago</th>
          <th style="text-align: center; ">Cuenta</th>
          <th style="text-align: center; ">Referencia</th>
          <th style="text-align: center; ">Dias de Credito</th>
          <th style="text-align: center; ">Monto</th>
        </tr>

        @foreach ($payment_quotations as $var)
        <tr>
          <th style="text-align: center; font-weight: normal;">{{ $var->payment_type }}</th>
          @if (isset($var->accounts['description']))
            <th style="text-align: center; font-weight: normal;">{{ $var->accounts['description'] }}</th>
          @else    
            <th style="text-align: center; font-weight: normal;"></th>
          @endif
          <th style="text-align: center; font-weight: normal;">{{ $var->reference }}</th>
          <th style="text-align: center; font-weight: normal;">{{ $var->credit_days }}</th>
          <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount , 2, ',', '.')}}</th>
        </tr> 
        @endforeach 
        
      </table>
  @endif
<br>
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
    <th style="text-align: center; ">P.V.P.</th>
    <th style="text-align: center; ">Desc</th>
    <th style="text-align: center; ">Total</th>
  </tr> 
  <?php
  $total_less_percentage_t = 0;
  $total_coin = 0;
  ?>
  @foreach ($inventories_quotations as $var)
      <?php
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

      $total_less_percentage = $total_less_percentage / ($bcv ?? 1);
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>

          <td style="text-align: right">{{ $var->description}}</td>

      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
    <?php
    $total_less_percentage_t += $total_less_percentage;
    
    ?>

  @endforeach 
</table>


<?php
      //$iva = ($quotationsorigin[0]['base_imponible'] * $quotationsorigin[0]['iva_percentage'])/100;
      $iva = 0;
    
      //$total = $quotationsorigin->sub_total_factura + $iva - $quotationsorigin->anticipo;
    
      //$total = $quotationsorigin[0]['amount_with_iva'];
      $total = $total_less_percentage_t;
    
      //$total_petro = ($total - $quotationsorigin->anticipo) / $company->rate_petro;
    
      //$iva = $iva / ($bcv ?? 1);
    
      $total_coin = $total_less_percentage_t / ($quotation->bcv ?? 1);

?>

<table style="width: 100%;">
  @if ($quotation->anticipo != 0)
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Anticipo</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->anticipo , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL BS</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($total , '1', 2), 2, ',', '.') }}</th>
  </tr> 

  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"> Tasa de cambio a la fecha: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs.</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; border-right-color: black; font-size: small;">MONTO TOTAL USD</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; border-right-color: black; font-size: small;">${{ number_format(bcdiv($total_coin , '1', 2), 2, ',', '.') }}</th>
  </tr> 

  
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: black; border-right-color: white;" ></th>
    <th style="text-align: left; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9); border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; "></th>
  </tr> 
  
  
</table>

</body>
</html>
