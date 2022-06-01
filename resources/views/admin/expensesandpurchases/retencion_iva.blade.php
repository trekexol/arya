
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Retencion Iva</title>
<style>
  table, td, th {
    border: 1px solid black;
  }
  
  table {
    border-collapse: collapse;
    width: 100%;
  }
  
  th {
    
    text-align: left;
  }
  </style>
</head>

<body>
  <br><br><br><br>
<h3 style="text-align: center;">COMPROBANTE DE RETENCIÓN AL IMPUESTO AL VALOR AGREGADO</h3>
<h5 style="">Ley IVA Art. 11: Serán responsables del pago de impuesto en calidad de agente de retención, los compradores o adquirientes de
determindados bienes muebles y los receptores de ciertos servicios, a quienes la administración tribuitaria los designe como tal
</h5>
  
<table>
  <tr>
    <th style="font-size: x-small; width: 40%; border-bottom-color: white;">NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCIÓN</th>
    <th style="font-size: x-small; width: 40%; border-bottom-color: white;">RIF DEL AGENTE DE RETENCIÓN</th>
    <th style="font-size: x-small; width: 20%; border-bottom-color: white;">Nro DE COMPROBANTE</th>
  </tr>
  <tr>
    <td style="font-size: x-small;">{{ $company->razon_social ?? ''}}</td>
    <td style="font-size: x-small;">{{ $company->code_rif ?? ''}}</td>

    
    <td style="font-size: x-small;">{{ $periodo_pago.''.$mes_pago.''.str_pad($expense->number_iva, 8, "0", STR_PAD_LEFT) ?? 'Vista Previa'}}</td>
  </tr>
  
</table>
<br>
<table>
  <tr>
    <th style="font-size: x-small; width: 80%; border-bottom-color: white;">DIRECCIÓN FISCAL DEL AGENTE DE RETENCIÓN</th>
    <th style="font-size: x-small; width: 20%; border-bottom-color: white;">FECHA DE EMISIÓN</th>
  </tr>
  <tr>
    <td style="font-size: x-small;">{{ $company->address ?? ''}}</td>
    <td style="font-size: x-small;">{{ date_format(date_create($pago->created_at),"d-m-Y")?? ''}}</td>
  </tr>
  
</table>
<br>
<table>
  <tr>
    <th style="font-size: x-small; width: 40%; border-bottom-color: white;">NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO</th>
    <th style="font-size: x-small; width: 40%; border-bottom-color: white;">RIF DEL SUJETO RETENIDO</th>
    <th style="font-size: x-small; width: 20%; border-bottom-color: white;">PERIODO FISCAL</th>
  </tr>
  <tr>
    <td style="font-size: x-small;">{{ $provider->razon_social ?? ''}}</td>
    <td style="font-size: x-small;">{{ $provider->code_provider ?? ''}}</td>
    <td style="font-size: x-small;">{{ $periodo_pago ?? ''}}-{{$mes_pago ?? ''}}</td>
  </tr>
  
</table>
<br>
<table>
  <tr>
    <th style="font-size: x-small; width: 80%; border-bottom-color: white;">DIRECCION DEL SUJETO RETENIDO</th>
    <th style="font-size: x-small; width: 20%; border-bottom-color: white;">TELÉFONOS DEL SUJETO RETENIDO</th>
  </tr>
  <tr>
    <td style="font-size: x-small;">{{ $provider->city ?? ''}} , {{ $provider->direction ?? ''}}</td>
    <td style="font-size: x-small;">{{ $provider->phone1 ?? ''}} / {{ $provider->phone2 ?? ''}}</td>
  </tr>
  
</table>

<?php 

  $oper = 1;
  $total = $expense->amount_with_iva;
?>

<br>
<table>
  <tr>
    <th style="font-size: x-small; width: 10%; text-align: center;">Oper</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">Fecha</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">No Factura</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">No Ctrl/Serie</th>
    <th style="font-size: x-small; width: 20%; text-align: center;">Total Compra</th>
    <th style="font-size: x-small; width: 20%; text-align: center;">Compra sin Derecho a Crédito Fiscal</th>
    <th style="font-size: x-small; width: 20%; text-align: center;">Base Imponible</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">Alicuota %</th>
    <th style="font-size: x-small; width: 20%; text-align: center;">Impuesto IVA</th>
    <th style="font-size: x-small; width: 20%; text-align: center;">IVA Retenido</th>
  </tr>
  <tr>
    <td style="font-size: x-small; text-align: center;">{{ $oper }}</td>
    <td style="font-size: x-small; text-align: center;">{{ date_format(date_create($expense->date),"d-m-Y") ?? '' }}</td>
    <td style="font-size: x-small; text-align: center;">{{ $expense->invoice ?? '' }}</td>
    <td style="font-size: x-small; text-align: center;">{{ $expense->serie ?? ''}}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format(bcdiv(($expense->amount ?? 0) + ($expense->amount_iva ?? 0), '1', 2), 2, ',', '.')}}</td>
    <td style="font-size: x-small; text-align: right;"></td> 
    <td style="font-size: x-small; text-align: right;">{{ number_format($expense->base_imponible ?? 0, 2, '.', ',') }}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format($expense->iva_percentage ?? 0, 2, '.', ',')}}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format($expense->amount_iva ?? 0, 2, '.', ',')}}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format($expense->retencion_iva ?? 0, 2, '.', ',')}}</td>
  </tr>

</table>

<br><br><br><br><br><br>
<table style="width: 100%;">
  <tr>
    <th  class="text-left font-weight-normal" style="border-color: #ffffff; width: 50%; text-align: center;">____________________________________</th>
    <th  class="text-left" style="border-color: #ffffff; width: 50%; text-align: center;">_______________________________________________</th>
  </tr>
  <tr>
    <th  style="border-color: #ffffff; width: 50%; text-align: center; font-size: small;">FIRMA BENEFICIARIO</th>
    <th  style="border-color: #ffffff; width: 50%; text-align: center; font-size: small;">FIRMA Y SELLO DEL AGENTE DE RETENCIÓN</th>
  </tr>
</table>

</body>
</html>
