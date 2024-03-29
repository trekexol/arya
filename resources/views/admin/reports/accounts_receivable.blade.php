

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
  <table>
    <tr>
      @if (Auth::user()->company->foto_company != '')
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h4>{{Auth::user()->company->code_rif ?? ''}}</h4> </h4></th>    </tr>
    </tr>
  </table>
  <h4 style="color: black; text-align: center">CUENTAS POR COBRAR</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>

  <?php

    $total_por_facturar = 0;
    $total_por_cobrar = 0;
    $total_anticipos = 0;
    $total_anticipo = 0;
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:7%;">Fecha</th>
    <th style="text-align: center; width:12%;">Tipo</th>
    <th style="text-align: center; width:5%;">N°</th>
    <th style="text-align: center; width:1%;">Ctrl/Serie</th>
    <th style="text-align: center;">Cliente</th>
    <th style="text-align: center; width:1%;">Vendedor</th>
    <th style="text-align: center;">Total</th>
  
    <th style="text-align: center;">Abono</th>
    @if($type == 'todo' || $type == 'todoa')
    <th style="text-align: center;">Total Anticipos</th>
    @endif
    <th style="text-align: center;">Por Cobrar</th>
    <th style="text-align: center; width:10%;">Status</th>
  </tr>


<?php


if($type == 'todo' || $type == 'todoa') {
 $quotations = $quotations->unique('id_client');
} 

  foreach ($quotations as $quotation){

        $amount_bcv = 1;
        $amount_bcv = $quotation->total_amount_with_iva;
        $diferencia_en_dias = 0;
        $diferencia_en_dias2 = 0;
        $validator_date = '';


        if(isset($quotation->credit_days)){
            $date_defeated = date("Y-m-d",strtotime($quotation->date_billing."+ $quotation->credit_days days"));

            $datenow = date_format(date_create($datenow),"Y-m-d");

            $currentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $datenow);

            $shippingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date_defeated);

            $validator_date = $shippingDate->lessThan($currentDate);
            //$validator_date2 = $shippingDate->lessThan($currentDate2);

            $diferencia_en_dias = $currentDate->diffInDays($shippingDate);

            $diferencia_en_dias2 = $shippingDate->diffInDays($currentDate);


        }


        $quotation->total_amount_with_iva = ($quotation->total_amount_with_iva - $quotation->retencion_iva - $quotation->retencion_islr);
        
        if($type == 'todo' || $type == 'todoa'){
          $por_cobrar = (($quotation->total_amount_with_iva ?? 0) - ($quotation->anticipo_s ?? 0));
          if($por_cobrar < 0){
          $por_cobrar = 0;
          }
        
        } else {
          $por_cobrar = (($quotation->total_amount_with_iva ?? 0) - ($quotation->total_anticipo ?? 0));
          if($por_cobrar < 0){
          $por_cobrar = 0;
          }
        }

    
      $total_por_cobrar += $por_cobrar;

      if($type == 'todo' || $type == 'todoa'){
      $total_anticipo += $quotation->anticipo_s;
      } else {
      $total_anticipo += 0;
      }

      $total_por_facturar += $quotation->total_amount_with_iva;
      $total_anticipos += $quotation->total_anticipo;

      $tipo = '';

      if ($typeinvoice == 'facturas'){
        $tipo = 'Facturas';
      }
      if ($typeinvoice == 'notas'){
        $tipo = 'Notas';
      }
      if ($typeinvoice == 'todo'){
        $tipo = 'Todo';
      }

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

    ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_billing ?? $quotation->date_delivery_note ?? $quotation->date_quotation ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $tipo }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice ?? $quotation->number_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->serie ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_client ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_vendor ?? ''}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->total_amount_with_iva ?? 0), 2, ',', '.') }}</th>
 
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->total_anticipo ?? 0), 2, ',', '.') }}</th>
      
      @if($type == 'todo' || $type == 'todoa')
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->anticipo_s ?? 0), 2, ',', '.') }}</th>
      @endif
      <th style="text-align: right; font-weight: normal;">{{ number_format($por_cobrar, 2, ',', '.') }}</th>
      @if (($diferencia_en_dias >= 0) && ($validator_date))
      <td style="text-align: center; font-weight: normal;" class="text-center font-weight-bold">
        @if ($diferencia_en_dias == 1)
            <span style="color: rgb(201, 9, 9)" >Vencida</span> ({{$diferencia_en_dias}} día)
        @else
        <span style="color: rgb(201, 9, 9)" >Vencida</span> ({{$diferencia_en_dias}} dias)
        @endif
        </td>
       @else
      <td style="text-align: center; font-weight: normal;" class="text-center font-weight-bold">
        @if ($diferencia_en_dias2 == 1)
        <span style="color: rgb(11, 109, 24)" >Vigente</span> ({{$diferencia_en_dias2}} día)
        @else
        <span style="color: rgb(11, 109, 24)" >Vigente</span> ({{$diferencia_en_dias2}} dias)
        @endif
      </td>
      @endif
    </tr>
<?php
}
?>

  <tr>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
    <th style="text-align: center; font-weight: bold; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: bold;">{{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: bold;">{{ number_format(($total_anticipos ?? 0), 2, ',', '.') }}</th>
    @if($type == 'todo' || $type == 'todoa')
    <th style="text-align: right; font-weight: bold;">{{ number_format(($total_anticipo), 2, ',', '.') }}</th>
    @endif
    <th style="text-align: right; font-weight: bold;">{{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: bold; border-color: white;"></th>
  </tr>
</table>

</body>
</html>
