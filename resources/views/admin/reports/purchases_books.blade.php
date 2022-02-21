
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title></title>
<style>
  table, td, th {
    border: 1px solid black;
    font-size: x-small;
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
  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr> 
    
  </table>
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
    <th style="text-align: center; ">Exento</th>
    <th style="text-align: center; ">Ret.Iva</th>
    <th style="text-align: center; ">Ret.Islr</th>
    <th style="text-align: center; ">IVA</th>
    <th style="text-align: center; ">Total</th>
  </tr> 
  <?php
        $total_base_imponible = 0;
        $c = 0;
        $total_exento = 0;
        $total_base_imponible = 0;
        $total_amount = 0;
        $total_amount_exento = 0;
        $total_retencion_iva = 0;
        $total_retencion_islr = 0;
        $total_anticipo = 0;
        $total_amount_iva = 0;
        $total_amount_with_iva = 0;
  ?>
  
  @foreach ($expenses as $expense)
    <?php
      
      if(isset($coin) && $coin == 'bolivares'){
        $total_base_imponible += $expense->base_imponible;
        $total_amount += $expense->amount;
        $total_amount_exento += $a_total[$c][0] ?? 0;
        $total_retencion_iva += $expense->retencion_iva;
        $total_retencion_islr += $expense->retencion_islr;
        $total_anticipo += $expense->anticipo;
        $total_amount_iva += $expense->amount_iva;
        $total_amount_with_iva += $expense->amount_with_iva;
      }else{
        $total_base_imponible += $expense->base_imponible / ($expense->rate ?? 1);
        $total_amount += $expense->amount / ($expense->rate ?? 1);
        $total_amount_exento += ($a_total[$c][0] ?? 0) / ($expense->rate ?? 1);
        $total_retencion_iva += $expense->retencion_iva / ($expense->rate ?? 1);
        $total_retencion_islr += $expense->retencion_islr / ($expense->rate ?? 1);
        $total_anticipo += $expense->anticipo / ($expense->rate ?? 1);
        $total_amount_iva += $expense->amount_iva / ($expense->rate ?? 1);
        $total_amount_with_iva += $expense->amount_with_iva / ($expense->rate ?? 1);
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
  
  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_amount, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_base_imponible, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_amount_exento, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_retencion_iva, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_retencion_islr, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_amount_iva, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_amount_with_iva, 2, ',', '.') }}</th>
  </tr> 
</table>

<h5 style="color: black; text-align: center">Total Exento: {{ number_format(($total_exento ?? 0), 2, ',', '.') }} / Total Compras y Créditos: {{ number_format(($total_base_imponible ?? 0), 2, ',', '.') }} </h5>

<table ALIGN="right" style="width: 50%;">
  <tr>
    <th style="text-align: center; ">Resumen Compras del periodo {{ $date_begin ?? '' }} hasta: {{ $date_end ?? '' }}</th>
    <th style="text-align: center; "></th>
    <th style="text-align: center; ">%</th>
    <th style="text-align: center; "></th>
    <th style="text-align: center; ">Retenciones</th>
  </tr> 
  <tr>
    <td style="text-align: right; font-weight: normal;">Compras Internas No Gravadas</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Menos: Notas de Credito del Mes</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras  Internas No Gravadas</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras Exportación</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras  Internas solo Alicuota General</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras  Internas solo Alicuota General</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras  Internas solo Alicuota General mas Adicional</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total Compras  Internas solo Alicuota Reducida</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: normal;">Total compras y Creditos Fiscales</td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
    <td style="text-align: right; font-weight: normal;"></td>
  </tr>
  
</table>
</body>
</html>
