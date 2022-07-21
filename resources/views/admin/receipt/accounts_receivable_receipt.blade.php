
  
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
  <h4 style="color: black; text-align: center">RECIBOS DE CONDOMINIO</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
  <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
    $total_anticipos = 0;
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha</th>
    <th style="text-align: center; width:3%;">N°</th>
    <th style="text-align: center; width:2%;">ID Propietario</th>
    <th style="text-align: center; width:22%;">Propietario</th>
    <th style="text-align: center; width:2%;">Apartamento</th>
    <th style="text-align: center;">Total</th>
    <th style="text-align: center;">Abono</th>
    @if(Auth::user()->role_id  == '11')
    <th style="text-align: center;">Por Pagar</th>
    @else 
    <th style="text-align: center;">Por Cobrar</th>
    @endif
    <th style="text-align: center; width:10%;">Status</th>
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
      $status = '';


      if ($quotation->status == 'C' & $quotation->verified == 1){
      $status = 'Cobrada';
      } 

      if ($quotation->status == 'C' & $quotation->verified == 0){
      $status = 'Por Verificar';
      } 


      if ($quotation->status == 'P'){
        if(Auth::user()->role_id  == '11'){
        $status = 'Por Pagar';
        } else {
          $status = 'Por Cobrar';  
        }
      }
    ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_billing ?? $quotation->date_delivery_note ?? $quotation->date_quotation ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice ?? $quotation->number_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->cedula_rif ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_client ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->direction ?? ''}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_with_iva ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_anticipo ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($por_cobrar, 2, ',', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $status }}</th>
    </tr> 
  @endforeach 

  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th> 
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_anticipos ?? 0), 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal;">{{ '' }}</th>
  </tr> 
</table>

</body>
</html>
