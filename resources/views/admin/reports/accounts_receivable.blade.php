
  
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
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha</th>
    <th style="text-align: center; width:12%;">Tipo</th>
    <th style="text-align: center; width:5%;">N°</th>
    <th style="text-align: center; width:1%;">Ctrl/Serie</th>
    <th style="text-align: center; width:22%;">Cliente</th>
    <th style="text-align: center;">Vendedor</th>
    <th style="text-align: center;">Total</th>
    <th style="text-align: center;">Abono</th>
    <th style="text-align: center;">Por Cobrar</th>
    <th style="text-align: center;">Status</th>
  </tr> 
  @foreach ($quotations as $quotation)
    <?php 
    

        $amount_bcv = 1;
        $amount_bcv = $quotation->amount_with_iva / $quotation->bcv;
        $diferencia_en_dias = 0;
        $validator_date = '';
        $credit_days = '';

        if(isset($quotation->credit_days)){
            $date_defeated = date("Y-m-d",strtotime($quotation->date_billing."+ $quotation->credit_days days")); 
            
            $currentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $datenow);
            $shippingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date_defeated); 

            $validator_date = $shippingDate->lessThan($currentDate);
            $diferencia_en_dias = $currentDate->diffInDays($shippingDate);
            $credit_days = $quotation->credit_days;

            
            
        }

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
    
    ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_billing ?? $quotation->date_delivery_note ?? $quotation->date_quotation ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $tipo }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice ?? $quotation->number_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->serie ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_client ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_vendor ?? ''}}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_with_iva ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_anticipo ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($por_cobrar, 2, ',', '.') }}</th>
      @if (($diferencia_en_dias >= 0) && ($validator_date))
      <td style="text-align: center; font-weight: normal;" class="text-center font-weight-bold">
          Vencida ({{$diferencia_en_dias}} dias)
      </td>
       @else
      <td style="text-align: center; font-weight: normal;" class="text-center font-weight-bold">
          Pendiente {{$credit_days}} 
      </td>
      @endif
    </tr> 
  @endforeach 

  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th> 
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total_anticipos ?? 0), 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;">{{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
  </tr> 
</table>

</body>
</html>
