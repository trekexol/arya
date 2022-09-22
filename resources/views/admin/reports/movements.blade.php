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
  
 /* th {
    
    text-align: left;
  } */
  </style>
</head>

<body>
  <table>
    <tr>
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h4>{{Auth::user()->company->code_rif ?? ''}}</h4> </h4></th>    </tr> 
    </tr> 
  </table>
  <h4 style="color: black; text-align: center">HISTORIAL INVENTARIO</h4>
 <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
  
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha</th>
    <th style="text-align: center; width:5%;">ID</th>
    <th style="text-align: center; width:7%;">Código.C.</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; width:5%;">Tipo</th>
    <th style="text-align: center; width:7%;">Precio-Venta</th>
    <th style="text-align: center; width:5%;">Cant.</th>
    <th style="text-align: center; width:5%;">Cant.Actual</th>
    <th style="text-align: center; width:5%;">Factura</th>
    <th style="text-align: center; width:5%;">Nota</th>
    <th style="text-align: center; width:5%;">Sucursal</th>
  </tr>
    @foreach ($inventories as $inventory)
        <tr>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->date}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->id_product}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->code_comercial}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->description}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->type}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->price}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->amount}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->amount_real}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->invoice}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->note}}</td>
          <td style="text-align: center; font-weight: normal;">{{ $inventory->branch}}</td>
        </tr>
      @endforeach
  </tbody>
</table>

</body>
</html>