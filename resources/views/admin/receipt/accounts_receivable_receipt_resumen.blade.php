
  
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
  <h4 style="color: black; text-align: center">RESUMEN DE RECIBOS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
  <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
    $total_anticipos = 0;
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:3%;">ID Propietario</th>
    <th style="text-align: center; width:40%;">Propietario</th>
    <th style="text-align: center; width:20%;">Apartamento</th>
    <th style="text-align: center; width:2%;">Recibos</th>
    @if(Auth::user()->role_id  == '11')
    <th style="text-align: center;">Por Pagar</th>
    @else 
    <th style="text-align: center;">Por Cobrar</th>
    @endif

    <th style="text-align: center; width:10%;">Status</th>
  </tr> 

    <?php 
        
        $a_uni2[] = array('','0','',0,0,'','');

        foreach ($quotations as $quotation) {

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


          if ($quotation->status == 'C' ){
          $status = 'Cobrada';
          } 

          if ($quotation->status == 'P'){
            if(Auth::user()->role_id  == '11'){
            $status = 'Por Pagar';
            } else {
              $status = 'Por Cobrar';  
            }
          }

          $a_uni2[] = array($quotation->cedula_rif ?? '',$quotation->name_client ?? '',$quotation->direction ?? '',1,number_format($por_cobrar, 2, '.', ''),$status,$quotation->personcontact ?? '');     

      }

      for ($q=0;$q<count($a_uni2);$q++) {
        for ($k=$q+1; $k<count($a_uni2);$k++) {
           if ($a_uni2[$q][0] == $a_uni2[$k][0]) {
              $a_uni2[$q][3] = $a_uni2[$q][3]+$a_uni2[$k][3];
              $a_uni2[$q][4] = $a_uni2[$q][4]+$a_uni2[$k][4];
              $a_uni2[$k][1] = '0'; 
            }

        }
      } 

      foreach ($a_uni2 as $clave => $fila) {
      $orden[$clave] = $fila[2];
      }

      array_multisort($orden, SORT_ASC, $a_uni2);


    ?>


  @for ($q=0;$q<count($a_uni2);$q++)
     
  @if($a_uni2[$q][1] != '0')
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $a_uni2[$q][0]}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $a_uni2[$q][1]}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $a_uni2[$q][6]}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $a_uni2[$q][3]}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($a_uni2[$q][4], 2, ',', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $a_uni2[$q][5] }}</th>
    </tr> 
   @endif
  @endfor
  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal;">{{ '' }}</th>
  </tr> 
</table>

</body>
</html>
