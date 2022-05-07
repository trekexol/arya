

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
  <h2 style="color: black; text-align: center">Pedidos</h2>
  <br>
  <h2 style="color: black; text-align: center">Fecha de Emisión: {{ date_format(date_create($date_end ?? $datenow ),"d-m-Y") }}</h2>
   
  <?php 
    
    $total_por_pagar = 0;
  ?>
<table style="width: 100%;">
  <thead>
    <tr> 
                
      <th class="text-center">Fecha</th>
      <th class="text-center">N°</th>
      <th class="text-center">Cliente</th>
      <th class="text-center">Vendedor</th>
      <th class="text-center">Transporte</th>
     
  </tr>
  </thead>
  
  <tbody>
      @if (empty($quotations))
      @else  
          @foreach ($quotations as $quotation)
              <tr>
                 <td class="text-center">{{ date_format(date_create($quotation->date_order),"d-m-Y")  }}</td>
                  <td class="text-center">{{ $quotation->number_delivery_note ?? $quotation->id ?? ''}}</td>
                  <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->vendors['name'] ?? ''}} {{ $quotation->vendors['surname'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->transports['placa'] ?? ''}}</td>
                 
              </tr>     
          @endforeach   
      @endif
  </tbody>
</table>
</body>
</html>
