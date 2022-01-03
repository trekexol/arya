
  
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
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->code_rif ?? ''}} </h4></th>
    </tr> 
  </table>
  <h4 style="color: black; text-align: center">Ventas</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $datenow ?? '' }} / Fecha desde: {{ $date_begin ?? '' }} Fecha Hasta: {{ $date_end ?? '' }} / Tasa: {{ number_format(($rate ?? 0), 2, ',', '.') }}</h5>
   
 
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Segmento</th>
    <th style="text-align: center; ">Sub Segmento</th>
    <th style="text-align: center; ">Cantidad</th>
    <th style="text-align: center; ">Precio Venta</th>
    <th style="text-align: center; ">Total Precio Venta</th>
    @if (isset($coin) && $coin == 'dolares')
      <th style="text-align: center; ">Total Precio Compra</th>
    @endif
  </tr> 
  <?php
    $total = 0;
    $total_buy = 0;
  ?>
  @foreach ($sales as $sale)
    <?php
        if(isset($coin) && $coin == 'bolivares'){
            $total += $sale->price_sales;
        }else if(isset($coin) && $coin == 'dolares'){
            $total += $sale->price_sales_dolar;
            $total_buy += ($sale->price_buy ?? 0) * $sale->amount_sales;
        }
    ?>

    <tr>
      <td style="text-align: center; ">{{ $sale->code ?? ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{ $sale->description ?? '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ $sale->segment_description ?? ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{ $sale->subsegment_description ?? ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{ $sale->amount_sales ?? ''}}</td>
      @if (isset($coin) && ($coin == 'bolivares'))
        @if ($sale->money == "Bs")
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price ?? 0), 2, ',', '.') }}</td>
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price_sales ?? 0), 2, ',', '.') }}</td>
        @else
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price ?? 0) * ($rate ?? 1), 2, ',', '.') }}</td>
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price_sales ?? 0), 2, ',', '.') }}</td>
        @endif
      @else
        @if ($sale->money == "Bs")
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price ?? 0) / ($rate ?? 1), 2, ',', '.') }}</td>
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price_sales_dolar ?? 0), 2, ',', '.') }}</td>
        @else
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price ?? 0), 2, ',', '.')  }}</td>
          <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price_sales_dolar ?? 0), 2, ',', '.') }}</td>
        @endif
      @endif
      @if (isset($coin) && $coin == 'dolares')
        <td style="text-align: right; font-weight: normal;">{{ number_format(($sale->price_buy ?? 0) * $sale->amount_sales, 2, ',', '.')  }}$</td>
      @endif
    </tr> 
  @endforeach 

  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total, 2, ',', '.') }}</th>
    @if (isset($coin) && $coin == 'dolares')
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_buy, 2, ',', '.') }}</th>
    @endif
  </tr> 
</table>

</body>
</html>
