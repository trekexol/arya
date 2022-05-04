
  
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
  <h2 style="color: black; text-align: center">Pedidos</h2>
  <br>
  <h2 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h2>
   
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
      <th class="text-center" width="8%"></th>
     
  </tr>
  </thead>
  
  <tbody>
      @if (empty($quotations))
      @else  
          @foreach ($quotations as $quotation)
              <tr>
                 <td class="text-center">{{ $quotation->date_order ?? ''}}</td>
                  <td class="text-center">{{ $quotation->number_delivery_note ?? $quotation->id ?? ''}}</td>
                  <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->vendors['name'] ?? ''}} {{ $quotation->vendors['surname'] ?? ''}}</td>
                  <td class="text-center">{{ $quotation->transports['placa'] ?? ''}}</td>
                  <td class="text-center">
                      <a href="{{ route('quotations.create',[$quotation->id,$quotation->coin])}}" title="Seleccionar"><i class="fa fa-check"></i></a>
                      <a href="{{ route('orders.create_order',[$quotation->id,$quotation->coin])}}" title="Mostrar"><i class="fa fa-file-alt"></i></a>
                      <a href="{{ route('orders.reversar_order',$quotation->id)}}" title="Borrar"><i class="fa fa-trash text-danger"></i></a>
                 </td>
              </tr>     
          @endforeach   
      @endif
  </tbody>
</table>
</body>
</html>
