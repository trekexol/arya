
  
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
  <h4 style="color: black; text-align: center">Reporte Comisión de Vendedores</h4>
  <h6 style="color: black; text-align: center">Facturas Cobradas y No Cobradas</h6>
 
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
  <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
    $total_anticipos = 0;
    
    $total_comision = 0;
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha</th>
    <th style="text-align: center; width:12%;">Tipo</th>
    <th style="text-align: center; width:5%;">N°</th>
    <th style="text-align: center; width:1%;">Ctrl/Serie</th>
    <th style="text-align: center; width:22%;">Cliente</th>
    <th style="text-align: center;">Vendedor</th>
    <th style="text-align: center;">Total Venta</th>
    <th style="text-align: center;">Comisión</th>
    <th style="text-align: center;">Total Comisión</th>
    
  </tr> 
  @foreach ($quotations as $quotation)
    <?php 
    
      if(isset($coin) && $coin != 'bolivares'){

        $quotation->amount_with_iva = ($quotation->amount_with_iva - ($quotation->retencion_iva ?? 0) - ($quotation->retencion_islr ?? 0)) / ($quotation->bcv ?? 1);
        //$quotation->amount_anticipo = ($quotation->amount_anticipo ?? 0) / ($quotation->bcv ?? 1);

        $por_cobrar = (($quotation->amount_with_iva ?? 0) - ($quotation->amount_anticipo ?? 0));
        $total_por_cobrar += $por_cobrar;
        $total_por_facturar += $quotation->amount_with_iva;
      }else{
        $quotation->amount_with_iva = ($quotation->amount_with_iva - $quotation->retencion_iva - $quotation->retencion_islr);
        $por_cobrar = ($quotation->amount_with_iva ?? 0) - ($quotation->amount_anticipo ?? 0);
        $total_por_cobrar += $por_cobrar;
        $total_por_facturar += $quotation->amount_with_iva;
      }
      
      $total_anticipos += $quotation->amount_anticipo;

      $tipo = '';
      if ($quotation->number_delivery_note > 0) {
        $tipo = 'Nota de Entrega';
      }
      if ($quotation->number_invoice > 0){
        $tipo = 'Factura';
      }

      if(isset($quotation->date_billing)){
        $quotation->date_billing = date_format(date_create($quotation->date_billing),"d-m-Y");
      }
      if(isset($quotation->date_delivery_note)){
        $quotation->date_delivery_note = date_format(date_create($quotation->date_delivery_note),"d-m-Y");
      }
      if(isset($quotation->date_quotation)){
        $quotation->date_quotation = date_format(date_create($quotation->date_quotation),"d-m-Y");
      }

      $comision = (($quotation->comision ?? 0) * (bcdiv($quotation->amount ?? 0, '1', 2)))/100;
    
      $total_comision += bcdiv($comision, '1', 2);
    ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_billing ?? $quotation->date_delivery_note ?? $quotation->date_quotation ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $tipo }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice ?? $quotation->number_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->serie ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_client ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_vendor ?? ''}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format(($quotation->comision ?? 0), 0, ',', '.')}}%</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(bcdiv($comision ?? 0, '1', 2), 2, ',', '.') }}</th>
      
    </tr> 
  @endforeach 

  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_comision ?? 0), 2, ',', '.') }}</th> 
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th> 
  </tr> 
</table>

</body>
</html>
