

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

  <h4 style="color: black; text-align: center">VENTAS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $datenow ?? '' }} / Fecha desde: {{ $date_begin ?? '' }} Fecha Hasta: {{ $date_end ?? '' }}</h5>


<table style="width: 100%;">

  <tr>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Factura</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Nota</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Codigo</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Descripcion</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Segmento</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Sub Segmento</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Cantidad</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Precio Actual</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Total Venta</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Total Precio Compra</th>
  </tr>
  <?php
    $total = 0;
    $total_buy = 0;
  ?>
   @foreach($sales as $sales)

   <?php
   if (isset($coin) && $coin == 'dolares'){
    $total += $sales->price_sales_dolar;
    $total_buy += ($sales->price_buy ?? 0) * $sales->amount_sales;
   } else {
    $total += ($sales->amount_sales * $sales->price ) * ($rate ?? 1);
    $total_buy += (($sales->price_buy ?? 0) * $sales->amount_sales) * ($rate ?? 1);

   }
?>

   <tr>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ $sales->invoices ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ $sales->notes ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{$sales->code_comercial ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{$sales->description ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{$sales->segment_description ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{$sales->subsegment_description ?? ''}}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{$sales->amount_sales ?? 0}}</td>

    @if (isset($coin) && ($coin == 'bolivares'))

    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ number_format(($sales->price ?? 0) * ($rate ?? 1), 2, ',', '.') }}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ number_format(($sales->amount_sales * $sales->price ?? 0) * ($rate ?? 1), 2, ',', '.') }}</td>

@else

    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ number_format(($sales->price ?? 0), 2, ',', '.')  }}</td>
    <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ number_format(($sales->price_sales_dolar ?? 0), 2, ',', '.') }}</td>

@endif
  <td style="text-align: center; border-right-color: black; border-left-color: black;">{{ number_format(($sales->price_buy ?? 0) * $sales->amount_sales, 2, ',', '.')  }}</td>



</tr>
  @endforeach


  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    @if (isset($coin) && $coin == 'dolares')
    <th style="text-align: right; font-weight: normal;">${{ number_format($total, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">${{ number_format($total_buy, 2, ',', '.') }}</th>
    @else
    <th style="text-align: right; font-weight: normal;">{{ number_format($total * ($rate ?? 1), 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_buy * ($rate ?? 1), 2, ',', '.') }}</th>
    @endif
  </tr>



</table>

</body>
</html>
