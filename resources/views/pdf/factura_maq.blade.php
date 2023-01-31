<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Factura</title>
<style>

body{
    font-family: "Lucida Console", monospace, sans-serif;
    font-size: 14px;
    color: #222;
    font-weight: normal;
  } 

 table, td, th {
    border: 1px solid white;
    
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
<h4 style="color: black"> FACTURA NRO: {{ str_pad($quotation->number_invoice ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>

 
   
 
<table style="width: 60%;">
  @if (isset($company->franqueo_postal))
  <tr>
    <td style=" width: 20%;">Concesión Postal:</td>
    <td style=" width: 40%;">Nº {{ $company->franqueo_postal ?? ''}}</td>
  </tr>
  @endif
  
  <tr>
    @if (isset($quotation->credit_days))
      <td style="width: 25;">Fecha de Emisión:</td>
      <td style="width: 40%;"> {{ date_format(date_create($quotation->date_billing),"d-m-Y") }} | Dias de Crédito: {{ $quotation->credit_days }}</td>
    @else
      <td style="width: 25%;">Fecha de Emisión:</td>
      <td style="width: 40%;">{{ date_format(date_create($quotation->date_billing),"d-m-Y")}}</td>
    @endif
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <td style="border-right-color: black; border-left-color: black; border-top-color: black;">Nombre / Razón Social: &nbsp;  {{ $quotation->clients['name'] }}</td>
    
   
  </tr>
  <tr>
    <td style="border-right-color: black; border-left-color: black;">Domicilio Fiscal: &nbsp;  {{ $quotation->clients['direction'] }}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <td style="text-align: center; border-color: black; width: 19%;">Teléfono</td>
    <td style="text-align: center; border-color: black;">RIF/CI</td>
    <td style="text-align: center; border-color: black; width: 16%;">N°.Ctrl.Serie</td>
    <td style="text-align: center; border-color: black; width: 19%;">Nota.de.Entrega</td>
    <td style="text-align: center; border-color: black;">Transp./Tipo Entrega</td>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_delivery_note ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <td>Observaciones: &nbsp; {{ $quotation->observation }} </td>
</tr>
  
</table>
  @if (!empty($payment_quotations))
      

      <br>
      <table style="width: 100%;">
        <tr>
          <td style="text-align: center; width: 100%;">Condiciones de Pago</td>
        </tr> 
      </table>

      <table style="width: 100%;">
        <tr>
          <td style="text-align: center; border-color: black; width: 25%;">Tipo de Pago</td>
          <td style="text-align: center; border-color: black;">Cuenta</td>
          <td style="text-align: center; border-color: black; width: 10%;">Referencia</td>
          <td style="text-align: center; border-color: black; width: 4%;">Dias/C</td>
          <td style="text-align: center; border-color: black;">Monto</td>
        </tr>

        @foreach ($payment_quotations as $var)
        <tr>
          <td style="text-align: center; ">{{ $var->payment_type }}</td>
          @if (isset($var->accounts['description']))
            <td style="text-align: center; ">{{ $var->accounts['description'] }}</td>
          @else    
            <td style="text-align: center; "></td>
          @endif
          <td style="text-align: center; ">{{ $var->reference }}</td>
          <td style="text-align: center; ">{{ $var->credit_days }}</td>
          <td style="text-align: center; ">{{ number_format($var->amount , 2, ',', '.')}}</td>
        </tr> 
        @endforeach 
        
      </table>
  @endif
<br>
<table style="width: 100%;">
  <tr>
    <td style="text-align: center; width: 100%;">Productos</td>
  </tr> 
</table>
<table style="width: 100%;">
  <tr>
    <td style="text-align: center; border-color: black;">Código</td>
    <td style="text-align: center; border-color: black; width: 40%;">Descripción</td>
    <td style="text-align: center; border-color: black; width: 5%;">Cant.</td>
    <td style="text-align: center; border-color: black;">P.V.J.</td>
    <td style="text-align: center; border-color: black;">Desc.</td>
    <td style="text-align: center; border-color: black;">Total</td>
  </tr> 
  @foreach ($inventories_quotations as $var)
      <?php
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

      $total_less_percentage = $total_less_percentage / ($bcv ?? 1);
      ?>
    <tr>
      <td style="text-align: center; ">{{ $var->code_comercial }}</td>
      <td style="text-align: center; ">{{ $var->description }}</td>
      <td style="text-align: center; ">{{ $var->amount_quotation }}</td>
      <td style="text-align: center; ">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</td>
      <td style="text-align: center; ">{{ $var->discount }}%</td>
      <td style="text-align: right; ">{{ number_format($total_less_percentage, 2, ',', '.') }}</td>
    </tr> 
  @endforeach 
</table>


<?php
  $iva = ($quotation->base_imponible * $quotation->iva_percentage)/100;

  //$total = $quotation->sub_total_factura + $iva - $quotation->anticipo;

  $total = $quotation->amount_with_iva;

  //$total_petro = ($total - $quotation->anticipo) / $company->rate_petro;

  //$iva = $iva / ($bcv ?? 1);

  $total_coin = $total / ($bcv ?? 1);
?>

<table style="width: 100%;">
  <tr>
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white;">Sub Total</td>
    <td style="text-align: right;  width: 21%;">{{ number_format(bcdiv($quotation->amount / ($bcv ?? 1), '1', 2), 2, ',', '.') }}{{($coin == 'bolivares') ? '' : '$'}}</td>
  </tr> 
  <tr>
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white;">Base Imponible</td>
    <td style="text-align: right;  width: 21%;">{{ number_format(bcdiv($quotation->base_imponible , '1', 2), 2, ',', '.') }}</td>
  </tr>
  <tr>
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</td>
    <td style="text-align: right;  width: 21%;">{{ number_format(bcdiv($iva, '1', 2), 2, ',', '.') }}</td>
  </tr> 
  @if ($quotation->anticipo != 0)
  <tr>
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white;">Anticipo</td>
    <td style="text-align: right;  width: 21%;">{{ number_format(bcdiv($quotation->anticipo , '1', 2), 2, ',', '.') }}</td>
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
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white;">MONTO TOTAL </td>
    <td style="text-align: right;  width: 21%;">{{ number_format(bcdiv($total , '1', 2), 2, ',', '.') }}</td>
  </tr> 
  @if (isset($coin) && ($coin != 'bolivares'))
  <tr>
    <td style="text-align: left;  width: 38%; border-bottom-color: white; border-right-color: white;"> Tasa de cambio a la fecha: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs.</td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white; border-right-color: white;">MONTO TOTAL {{($coin == 'bolivares') ? '' : ' USD'}}</td>
    <td style="text-align: right;  width: 21%; border-bottom-color: white; border-right-color: white;">{{($coin == 'bolivares') ? '' : '$'}}{{ number_format(bcdiv($total_coin , '1', 2), 2, ',', '.') }}</td>
  </tr> 
  @endif
  
  <tr>
    <td style="text-align: left; width: 38%; border-bottom-color: white; border-right-color: white;" ></td>
    <td style="text-align: left;  width: 21%; border-top-color: white; border-right-color: white;"></td>
    <td style="text-align: right;  width: 21%; "></td>
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

</body>
</html>
