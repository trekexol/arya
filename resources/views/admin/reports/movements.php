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
  <br>
  <h4 style="color: black; text-align: center">HISTORIAL INVENTARIO</h4>
  <h5 style="color: black; text-align: center">Fecha de Desde: {{date_format(date_create($date_frist),"d-m-Y") ?? ''}}   /   Fecha de Hasta: {{ date_format(date_create($date_end),"d-m-Y")  ?? '' }}</h5>
 <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
  
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha NE</th>
    <th style="text-align: center; width:5%;">NE</th>
    <th style="text-align: center; width:5%;">FAC</th>
    <th style="text-align: center; width:9%;">Fecha FAC</th>
    <th style="text-align: center; width:5%;">Status</th>
    <th style="text-align: center; width:1%;">Ctrl/Serie</th>
    <th style="text-align: center;">Cliente</th>
    <th style="text-align: center;">Vendedor</th>
    <th style="text-align: center;">Total</th>
    <th style="text-align: center;">Abono</th>
    <th style="text-align: center;">Por Cobrar</th>
  </tr> 

 
</table>

</body>
</html>