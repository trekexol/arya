

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
 
  <h4 style="color: black; text-align: center">LIBRO DE COMPRAS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $datenow ?? '' }} / Fecha desde: {{ $date_begin ?? '' }} Fecha Hasta: {{ $date_end ?? '' }}</h5>
   
   
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Nº</th>
    <th style="text-align: center; " width="7%">Fecha</th>
    <th style="text-align: center; ">Rif</th>
    <th style="text-align: center; ">Razón Social</th>
    <th style="text-align: center; ">Serie</th>
    <th style="text-align: center; ">Monto</th>
    <th style="text-align: center; ">Base Imponible</th>
    <th style="text-align: center; ">Excento</th>
    <th style="text-align: center; ">Ret.Iva</th>
    <th style="text-align: center; ">Ret.Islr</th>
    <th style="text-align: center; ">IVA</th>
    <th style="text-align: center; ">Total</th>
  </tr> 
  <?php
        $total_base_imponible = 0;
        $c = 0;
        $total_exento = 0;
  ?>
  @foreach ($expenses as $expense)
    <?php
      
      if(isset($coin) && $coin == 'bolivares'){
        $total_base_imponible += $expense->base_imponible;
      }else{
        $total_base_imponible += $expense->base_imponible / ($expense->rate ?? 1);
      }
        
    ?>
    <tr>
      
      <td style="text-align: center; ">{{ $expense->id ?? ''}}</td>
      <td style="text-align: center; ">{{ $expense->date ?? ''}}</td>
      
      <td style="text-align: center; font-weight: normal;">{{ $expense->providers['code_provider'] ?? '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ $expense->providers['razon_social'] ?? '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ $expense->serie ?? ''}}</td>
      @if (isset($coin) && ($coin == 'bolivares'))
        <td style="text-align: right; font-weight: normal;">{{ number_format($expense->amount ?? 0, 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format($expense->base_imponible ?? 0, 2, ',', '.') }}</td>
   
        <td style="text-align: right; font-weight: normal;">{{ number_format($a_total[$c][0] ?? 0, 2, ',', '.') }}</td>

        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->retencion_iva ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->retencion_islr ?? 0), 2, ',', '.') }}</td>
        
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->amount_iva ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->amount_with_iva ?? 0), 2, ',', '.') }}</td>
      @else
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->amount / $expense->rate), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->base_imponible / $expense->rate), 2, ',', '.') }}</td>

        <td style="text-align: right; font-weight: normal;">{{ number_format(($a_total[$c][0] / $expense->rate) * $expense->amount ?? 0, 2, ',', '.') }}</td>

        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->retencion_iva / $expense->rate) ?? 0, 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->retencion_islr / $expense->rate), 2, ',', '.') }}</td>
        
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->amount_iva / $expense->rate), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($expense->amount_with_iva / $expense->rate), 2, ',', '.') }}</td>
      @endif
     
    </tr> 
    <?php
    $total_exento += $a_total[$c][0];
    $c++;

    ?>
  @endforeach 

  
</table>

<h5 style="color: black; text-align: center">Total Exento: {{ number_format(($total_exento ?? 0), 2, ',', '.') }} / Total Compras y Créditos: {{ number_format(($total_base_imponible ?? 0), 2, ',', '.') }} </h5>

</body>
</html>
