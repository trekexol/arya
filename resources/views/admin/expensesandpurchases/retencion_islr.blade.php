
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Retencion ISLR</title>
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
<h3 style="text-align: center;">COMPROBANTE DE RETENCION DE I.S.L.R.</h3>
<h5 style="text-align: center;">Fecha: {{ $datenow }}</h5>
  
<table>
  <tr>
    <th style="font-size: medium; width: 50%; text-align: center; ">PROVEEDOR</th>
    <th style="font-size: medium; width: 50%; text-align: center; ">EMPRESA</th>
  </tr>
  <tr>
    <td style="border-bottom-color: white;">Proveedor: {{ $provider->razon_social ?? '' }}</td>
    <td style="border-bottom-color: white;">Empresa: {{ $company->razon_social ?? ''}}</td>
  </tr>
  <tr>
    <td style="border-bottom-color: white;">Rif: {{ $provider->code_provider ?? '' }}</td>
    <td style="border-bottom-color: white;">Rif: {{ $company->code_rif ?? ''}}</td>
  </tr>
  <tr>
    <td style="border-top-color: rgb(17, 9, 9);">Dirección: {{ $provider->direction ?? '' }}</td>
    <td style="border-top-color: rgb(17, 9, 9);">Dirección: {{ $company->address ?? ''}}</td>
  </tr>
  
</table>
<br><br>

<table>
  <tr>
    <th style="font-size: x-small; width: 10%; text-align: center;">Fecha</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">No Pago</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">No Documento</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">No Ctrl/Serie</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">Monto</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">Cantidad Objeto de Retención</th>
    <th style="font-size: x-small; width: 5%; text-align: center;">Tarifa %</th>
    <th style="font-size: x-small; width: 25%; text-align: center;">Concepto de ISLR</th>
    <th style="font-size: x-small; width: 10%; text-align: center;">Impuesto Retenido menos sustraendo</th>
  </tr>
  <tr>
    <td style="font-size: x-small; text-align: center;">{{ $datenow ?? '' }}</td>
    <td style="font-size: x-small; text-align: center;">{{ $expense->number_islr ?? $expense->id ?? '' }}</td>
    <td style="font-size: x-small; text-align: center;">{{ $expense->invoice ?? ''}}</td>
    <td style="font-size: x-small; text-align: center;">{{ $expense->serie ?? ''}}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format(bcdiv(($expense->amount ?? 0) + ($expense->amount_iva ?? 0), '1', 2), 2, ',', '.')}}</td>
    <td style="font-size: x-small; text-align: right;">{{ number_format(bcdiv($expense->base_imponible?? 0, '1', 2), 2, ',', '.') }}</td>
    <td style="font-size: x-small; text-align: center;">{{ number_format(bcdiv($expense->iva_percentage ?? 0, '1', 2), 2, ',', '.')}}</td>
    @if (isset($expense->islr_concepts['description']))
      <td style="font-size: x-small; text-align: right;">{{ $expense->islr_concepts['description'] ?? ''}} - {{ $expense->islr_concepts['value'] ?? ''}} %</td>   
    @else
      <td style="font-size: x-small; text-align: right;"></td>   
    @endif
   <td style="font-size: x-small; text-align: right;">{{ number_format(bcdiv($expense->retencion_islr ?? 0, '1', 2), 2, ',', '.')}}</td>
  </tr>

</table>

<br><br>
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
