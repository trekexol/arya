

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
  <br>
  <h2 style="color: black; text-align: center">Cotizaciones</h2>
  <br>
  <h2 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h2>
   
  <?php 
    
    $total_por_pagar = 0;
  ?>
<table style="width: 100%;">
  <thead>
  <tr> 
      <th class="text-center">N° de Control/Serie</th>
      <th class="text-center">Cliente</th>
      <th class="text-center">Vendedor</th>
      <th class="text-center">Transp. / Tipo de Entrega</th>
      <th class="text-center">Fecha de Cotización</th>
     
  </tr>
  </thead>
  <tbody>
      @if (empty($quotations))
      @else  
          @foreach ($quotations as $quotation)
              <tr>
                 
                  <td class="text-center">{{ $quotation->serie ?? ''}}</td>
                  <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->vendors['name'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->transports['placa'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->date_quotation ?? ''}}</td>
                             
              </tr>     
          @endforeach   
      @endif
  </tbody>
</table>
</body>
</html>
