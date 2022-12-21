<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Factura</title>
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
<h4 style="color: black"> FACTURA NRO: {{ str_pad($quotation->number_invoice ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>

 
   
 
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
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $quotation->clients['name'] }}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $quotation->clients['direction'] }}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/CI</th>
    <th style="text-align: center;">N° Control / Serie</th>
    <th style="text-align: center;">Nota de Entrega</th>
    <th style="text-align: center;">Transp./Tipo Entrega</th>
   
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
  <?php 
   $lote = '';
   $date_expirate = '';
   $discount = 0;  

      foreach ($inventories_quotations as $var){

          $lote .= $var->lote;
          $date_expirate .= $var->date_expirate;

          $discount += $var->discount;
      }
  
  ?>
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Cantidad</th>
    @if($lote != '')
    <th style="text-align: center; ">Lote</th>
    @endif
    @if($date_expirate != '')
    <th style="text-align: center; ">Fecha Venc</th> 
    @endif
    <th style="text-align: center; ">P.V.J.</th>
    @if($discount > 0)
    <th style="text-align: center; ">Desc</th>
    @endif
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
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
       
      @if ($lote != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->lote }}</th>
      @endif
      
      @if ($date_expirate != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->date_expirate}}</th>
      @endif

      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
      @if($var->discount > 0)
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      @endif
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
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
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->amount / ($bcv ?? 1), '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->base_imponible , '1', 2), 2, ',', '.') }}</th>
  </tr>
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($iva, 2, ',', '.') }}</th>
  </tr> 


  @if (isset($quotation->IGTF_amount) && $quotation->IGTF_amount != 0)
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">IGTF</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->IGTF_amount , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  @if (isset($coin) && ($coin == 'bolivares'))

  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL {{($coin == 'bolivares') ? 'Bs.' : ' USD'}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">{{ number_format(bcdiv($total , '1', 2), 2, ',', '.') }}</th>
  </tr> 

  @endif

  @if (isset($coin) && ($coin != 'bolivares'))
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"> </th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; border-right-color: black; font-size: small;">MONTO TOTAL {{($coin == 'bolivares') ? 'Bs.' : ' USD'}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; border-right-color: black; font-size: small;">{{($coin == 'bolivares') ? '' : '$'}}{{ number_format(bcdiv($total_coin , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;">Tasa de cambio a la fecha: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs.</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL Bs.</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">{{ number_format(bcdiv($total , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
  
  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: black; border-right-color: white;" ></th>
    <th style="text-align: left; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9); border-right-color: black; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; "></th>
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
