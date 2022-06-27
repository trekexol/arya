
  
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
  <h5 style="color: black; text-align: center;">{{ $company->razon_social ?? ''}} / Rif: {{ $company->code_rif ?? ''}} / Fecha de Emisión: {{ $datenow }}</h5>
  
  <h4 style="color: black; text-align: center;">LISTADO MOVIMIENTO DE CUENTAS..</h4>
  <h5 style="color: black; text-align: center;">Código de Cuenta: {{ $account->code_one ?? ''}}.{{ $account->code_two ?? ''}}.{{ $account->code_three ?? ''}}.{{ $account->code_four ?? ''}}.{{ $account->code_five ?? ''}}</h5>
  <h5 style="color: black; text-align: center;">Cuenta: {{ $account->description ?? ''}}</h5>
  <h5 style="color: black; text-align: center;">Periodo: {{ $date_begin ?? ''}} hasta {{ $date_end ?? ''}}</h5>
  
 
  <h5 style="color: black; text-align: right;">Saldo actual a la fecha: {{ number_format($saldo ?? 0, 2, ',', '.')}}</h5>
  
  @if (isset($detailvouchers))
      <?php 
      $total_debe = 0;
      $total_haber = 0;
      /*se quito el saldo inicial para que no descuadrara*/
      if(isset($saldo_anterior) && ($saldo_anterior != 0)){
        $saldo_inicial = $saldo_anterior;//($account_historial->balance_previous ?? 0) + ($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0);
      }else{
        $saldo_inicial = ($account_historial->balance_previous ?? 0) + ($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0);
      }
     
      $total_saldo = $saldo_inicial;

     ?>
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center; width: 9%;">Fecha</th>
        <th style="text-align: center; width: 7%;">Comp.</th>
        <th style="text-align: center; width: 30%;">Descripcion</th>
        <th style="text-align: center; width: 12%;">Referencia</th>      
        <th style="text-align: center;">Debe</th>
        <th style="text-align: center;">Haber</th>
        <th style="text-align: center;">Saldo</th>
      </tr>
      
      @foreach ($detailvouchers as $detail)
        @if($detail->id_account == $id_account)
          <?php 
            
              if((isset($detail->debe)) && ($detail->debe != 0)){
                $total_debe += $detail->debe;
              }else if((isset($detail->haber)) && ($detail->haber != 0)){
                $total_haber += $detail->haber;
              }
            
          ?>
          <tr>
            <td style="text-align: center;">{{ date_format(date_create($detail->date),"d-m-Y") ?? ''}}</td>
            <td style="text-align: center;">{{ $detail->id_header ?? ''}}</td>
            <td style="text-align: left;">{{ $detail->header_description ?? ''}} / {{ $detail->account_counterpart ?? '' }}</td>
            <td style="text-align: center;">{{ $detail->reference ?? ''}}</td>
            <td style="text-align: right;">{{ number_format($detail->debe ?? 0, 2, ',', '.')}}</td>
            <td style="text-align: right;">{{ number_format($detail->haber ?? 0, 2, ',', '.')}}</td>
            <td style="text-align: right;">{{ number_format($detail->saldo ?? 0, 2, ',', '.')}}</td>
          </tr>
        @endif
      @endforeach
    

    <tr>
      <td style="text-align: center;"></td>
      <td style="text-align: center;"></td>
      <td style="text-align: center;">Saldo Inicial</td>
      <td style="text-align: center;"></td>
      <td style="text-align: center;"></td>
      <td style="text-align: center;"></td>
      <td style="text-align: right;">{{ number_format($saldo_inicial, 2, ',', '.')}}</td>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;"></th>
      <th style="text-align: right;">{{ number_format($total_debe ?? 0, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_haber ?? 0, 2, ',', '.')}}</th>
      <th style="text-align: center; border-color: black;"></th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;">Saldo del mes</th>
      <th style="text-align: right;">{{ number_format($total_debe - $total_haber, 2, ',', '.')}}</th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: right; border-color: white; border-right-color: black;">Saldo actual a la fecha</th>
      <th style="text-align: right;">{{ number_format($saldo, 2, ',', '.')}}</th>
    </tr>
  </table>
@endif


</body>


</html>
