
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Inventario</title>
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

  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="100%" height="60" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr> 
  </table>
  <h2 style="color: black; text-align: center">Inventario</h2>
  <br>
  <h2 style="color: black; text-align: center">Fecha de Emisión: {{ $datenow }}</h2>
   
 
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Cantidad</th>
    <th style="text-align: center; ">monto</th>
    <th style="text-align: center; ">Iva 16%</th>
    <th style="text-align: center; ">Venta con Iva</th>
    
  </tr> 
  @foreach ($inventories as $var)
      <?php
      $percentage = ($var->price * 16) /100;

      $total = $percentage + $var->price;
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_real, 0, '', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(16, 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


</body>
</html>
