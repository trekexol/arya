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
  
  <br><br><br>
  @if ($company->format_header_lines > 0) 
    @for ($i = 0; $i < $company->format_header_lines; $i++)
    <br>
    @endfor
  @endif
<h4 style="color: black"> RECIBO DE CONDOMINIO NRO: {{ str_pad($quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>

 
   
 
<table style="width: 60%;">
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 20%;">Concesión Postal:</th>
    <th style="font-weight: normal; width: 40%;">Nº {{ $company->franqueo_postal ?? ''}}</th>
  </tr>
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
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $client->name}}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $client->direction }}
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
    <td style="text-align: center;">{{ $client->phone1 ?? '' }}</td>
    <td style="text-align: center;">{{ $client->type_code ?? ''}} {{ $client->cedula_rif ?? '' }}</td>
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
      <br>
      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; width: 100%;">Relación de Gasto Nº {{ $quotationsorigin[0]['number_invoice'] }} Fecha de Emision: {{ date_format(date_create($quotationsorigin[0]['date_billing']),"d-m-Y") }}</th>
        </tr> 
      </table>

      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; ">Código</th>
          <th style="text-align: center; ">Descripción</th>
          <th style="text-align: center; ">Cantidad</th>
          <th style="text-align: center; ">Lote</th>
          <th style="text-align: center; ">Fecha Venc</th> 
          <th style="text-align: center; ">P.V.J.</th>
          <th style="text-align: center; ">Desc</th>
          <th style="text-align: center; ">Total</th>
        </tr> 
        @foreach ($inventories_quotationso as $varo)
            <?php
            $percentage = (($varo->price * $varo->amount_quotation) * $varo->discount)/100;
      
            $total_less_percentage = ($varo->price * $varo->amount_quotation) - $percentage;
      
            $total_less_percentage = $total_less_percentage / ($bcv ?? 1);
            ?>
          <tr>
            <th style="text-align: center; font-weight: normal;">{{ $varo->code_comercial }}</th>
            <th style="text-align: center; font-weight: normal;">{{ $varo->description }}</th>
            <th style="text-align: center; font-weight: normal;">{{ number_format($varo->amount_quotation, 0, '', '.') }}</th>
      
            <th style="text-align: center; font-weight: normal;">{{ $varo->lote }}</th>
      
       
            <th style="text-align: center; font-weight: normal;">{{ $varo->date_expirate}}</th>
      
      
            <th style="text-align: center; font-weight: normal;">{{ number_format($varo->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
            <th style="text-align: center; font-weight: normal;">{{ $varo->discount }}%</th>
            <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
          </tr> 
        @endforeach 
      </table>

      <?php
      $iva = ($quotationsorigin[0]['base_imponible'] * $quotationsorigin[0]['iva_percentage'])/100;
    
      //$total = $quotationsorigin->sub_total_factura + $iva - $quotationsorigin->anticipo;
    
      $total = $quotationsorigin[0]['amount_with_iva'];
    
      //$total_petro = ($total - $quotationsorigin->anticipo) / $company->rate_petro;
    
      //$iva = $iva / ($bcv ?? 1);
    
      $total_coin = $total / ($bcv ?? 1);
    ?>
    
    <table style="width: 100%;">
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Sub Total</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotationsorigin[0]['amount'] / ($bcv ?? 1), '1', 2), 2, ',', '.') }}{{($coin == 'bolivares') ? '' : '$'}}</th>
      </tr> 
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Base Imponible</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotationsorigin[0]['base_imponible'] , '1', 2), 2, ',', '.') }}</th>
      </tr>
      @if ($quotationsorigin[0]['retencion_iva'] != 0)
        <tr>
          <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
          <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Retención de Iva</th>
          <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotationsorigin[0]['retencion_iva'] , '1', 2), 2, ',', '.') }}</th>
        </tr> 
      @endif 
      @if ($quotationsorigin[0]['retencion_islr'] != 0)
        <tr>
          <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
          <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Retención de ISLR</th>
          <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotationsorigin[0]['retencion_islr'] , '1', 2), 2, ',', '.') }}</th>
        </tr> 
      @endif 
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">I.V.A.{{ $quotationsorigin[0]['iva_percentage'] }}%</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($iva, '1', 2), 2, ',', '.') }}</th>
      </tr> 
      @if ($quotationsorigin[0]['anticipo'] != 0)
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Anticipo</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotationsorigin[0]['anticipo'] , '1', 2), 2, ',', '.') }}</th>
      </tr> 
      @endif
     
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL BS.</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($total , '1', 2), 2, ',', '.') }}</th>
      </tr> 

      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: black; border-right-color: white; font-size: small;">
        <!--<th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: black; border-right-color: white; font-size: small;"> Tasa de cambio B.C.V: {{ ''/*number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.')*/ }} Bs. a la fecha {{''/*date_format(date_create($quotation->date_billing),"d-m-Y")*/}}</th> -->
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: black; font-size: small;">MONTO TOTAL USD</th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: black; font-size: small;">${{ number_format(bcdiv($total/$quotationsorigin[0]['bcv'] , '1', 2), 2, ',', '.') }}</th>
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
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Cantidad</th>
    <th style="text-align: center; ">Alicuota</th> 
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
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $client->aliquot}}%</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price / ($bcv ?? 1), 2, ',', '.')  }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
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
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL BS.</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($total_less_percentage , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-color: white; border-left-color: black; border-bottom-color: black; font-size: small;"> Tasa de cambio B.C.V: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs. a la fecha {{date_format(date_create($quotation->date_billing),"d-m-Y")}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: black; border-right-color: black; font-size: small;">MONTO TOTAL USD</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: black; border-right-color: black; font-size: small;">${{ number_format(bcdiv($total_less_percentage/$quotationsorigin[0]['bcv'] , '1', 2), 2, ',', '.') }}</th>
  </tr>

</table>
<br>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Resumen y Estado de Cuenta</th>
  </tr>
</table>
<table style="width: 100%;">
  
  @if(isset($quotationp))
  <?php
  $total_less_percentagep = 0;
  ?>     
  
  @foreach ($inventories_quotationsp as $varp)
        <?php
        $percentagep = 0;
        $total_less_percentagep = 0;
        $total_less_percentagep = 0;

        $percentagep = (($varp->price * $varp->amount_quotation) * $varp->discount)/100;

        $total_less_percentagepn = $quotation->amount_with_iva;

        $total_less_percentagep += $quotation->amount_with_iva;



        ?>
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-color: white; border-left-color: black; border-bottom-color: black; font-size: small;">Recibo Nº {{ $varp->number_delivery_note }} Fecha {{ date_format(date_create($varp->date_billing),"d-m-Y") }}</th>
        <th style="text-align: right; font-weight: normal; border-top-color: white; border-bottom-color: black;"></th>
        <th style="text-align: right; font-weight: normal; border-top-color: white; border-bottom-color: black;">{{ number_format($total_less_percentagep, 2, ',', '.') }}</th>
      </tr> 
      @endforeach 
  @endif
  <tr style=" border-top-color: white;">
    <th style="text-align: left; font-weight: normal; width: 38%; border-color: white; border-left-color: black; border-bottom-color: black; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; border-top-color: white; border-bottom-color: black;">MONTO TOTAL Bs.</th>
    <th style="text-align: right; font-weight: normal; border-top-color: white;">{{ number_format(bcdiv($total_less_percentagep , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr style=" border-top-color: white;">
    <th style="text-align: left; font-weight: normal; width: 38%; border-color: white; border-left-color: black; border-bottom-color: black; font-size: small;"> Tasa de cambio B.C.V: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs. a la fecha {{date_format(date_create($quotation->date_billing),"d-m-Y")}}</th>
    <th style="text-align: right; font-weight: normal; border-top-color: white; border-bottom-color: black;">MONTO TOTAL USD</th>
    <th style="text-align: right; font-weight: normal; border-top-color: white;">${{ number_format(bcdiv($total_less_percentagep/$quotationsorigin[0]['bcv'] , '1', 2), 2, ',', '.') }}</th>
  </tr> 


</table>
</body>
</html>
