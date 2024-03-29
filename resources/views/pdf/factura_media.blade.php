<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Factura</title>
<style>
  table, td, th {
    border: 1px solid black;
    font-size: x-small;
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

  <br><br>
  @if ($company->format_header_lines_med > 0) 
    @for ($i = 0; $i < $company->format_header_lines_med; $i++)
    <br>
    @endfor
  @endif

<table style="width: 100%;">
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 10%;">Concesión Postal:</th>
    <th style="font-weight: normal; width: 10%; border: rgb(17, 9, 9);">Nº {{ $company->franqueo_postal ?? ''}}</th>
    <th style="font-weight: normal; width: 10%; border: rgb(255, 255, 255);"></th>
  </tr>
  @endif
    <tr>
     @if (isset($quotation->credit_days))
      <td style="width: 10%;">Fecha de Emisión:</td>
      <td style="width: 10%;">{{ date_format(date_create($quotation->date_billing),"d-m-Y") }}</td>
    
    @else
      <td style="width: 10%;">Fecha de Emisión:</td>
      <td style="width: 10%;">{{ date_format(date_create($quotation->date_billing),"d-m-Y") }}</td>
    @endif
      <td  style="font-size: 11pt; width: 40%; color: black; font-weight: bold; text-align: right; border-top-color: white; border-right-color: white;">FACTURA NRO: {{ str_pad($quotation->number_invoice ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</td>
     

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
    <th style="text-align: center; " >Teléfono</th>
    <th style="text-align: center; ">RIF/CI</th>
    <th style="text-align: center; ">N° Control / Serie</th>
    <th style="text-align: center; ">N° Pedido</th>
    <th style="text-align: center; ">Nota de Entrega</th>
    <th style="text-align: center; ">Transp./Tipo de Entrega</th>
   
  </tr>
  <tr>
    <td style="text-align: center; ">{{ $quotation->clients['phone1'] }}</td>
    <td style="text-align: center; ">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center; ">{{ $quotation->serie }}</td>
    <td style="text-align: center; ">{{ $quotation->number_pedido }}</td>
    <td style="text-align: center; ">{{ $quotation->number_delivery_note }}</td>
    <td style="text-align: center; ">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; ">Observaciones: &nbsp; {{ $quotation->observation }} </th>
</tr>
  
</table>
  @if (!empty($payment_quotations))
      

      <table class="condicion" style="width: 100%;">
        <tr>
          <th class="condicion" style="text-align: center; width: 100%;">Condiciones de Pago</th>
        </tr> 
      </table>

      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; ">Tipo de Pago</th>
          <th style="text-align: center; ">Cuenta</th>
          <th style="text-align: center; ">Referencia</th>
          @if ($company->id == 1)
          <th style="text-align: center; ">Días de Crédito: {{ $quotation->credit_days }} Días Continuos</th>
          @else
          <th style="text-align: center; ">Días de Crédito {{ $quotation->credit_days }} Días</th>
          @endif 
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
          <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount, 2, ',', '.')}}</th>
        </tr> 
        @endforeach 
        
      </table>
  @endif

<table class="condicion" style="width: 100%;">
  <tr>
    <th class="condicion" style="text-align: center; width: 100%;">Productos</th>
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

      $total_less_percentage = $total_less_percentage / ($bcv ?? 1);
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->amount_quotation }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php
  $monto_m = 0;
  $iva = ($quotation->base_imponible * $quotation->iva_percentage)/100;
  
  //$total = $quotation->sub_total_factura + $iva - $quotation->anticipo;

  //$bcv = bcdiv($bcv ?? 1, '1', 2);

  $total = $quotation->amount_with_iva;

 //$total_petro = ($total - $quotation->anticipo) / $company->rate_petro;

  $total = $total / ($bcv ?? 1);

  $monto_m = $quotation->amount_with_iva;

  if($coin == 'bolivares'){
    
    $total = $total / ($quotation->bcv ?? 1);
  }
 
   $texto_tasa = ' Tasa de cambio a la fecha '.number_format(bcdiv(($quotation->bcv ?? 1), '1', 3), 3, ',', '.').' Bs.';

?>

<table style="width: 100%;">
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{($coin == 'bolivares') ? '' : '$'}}{{ number_format(bcdiv($quotation->amount / ($bcv ?? 1), '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->base_imponible , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($iva, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @if ($quotation->anticipo != 0)
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Anticipo</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->anticipo , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  @if (isset($quotation->IGTF_amount) && $quotation->IGTF_amount != 0)
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">IGTF</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->IGTF_amount , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL BS</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($monto_m, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white; font-weight: normal; padding-left: 5px;" >{{$texto_tasa}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL USD</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">${{ number_format(bcdiv($total, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: black; border-right-color: white;" ></th>
    <th style="text-align: left; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9); border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; "></th>
  </tr> 

  
</table>


@if($company->id == 1)
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal;">
     NOTA: ESTA FACTURA DEBE SER CANCELADA SEGUN LA TASA BCV DEL DÍA DE PAGO. 
    </td>
  </tr>
</table>
@endif

@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal;">
     NOTA 2: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
</body>
</html>
