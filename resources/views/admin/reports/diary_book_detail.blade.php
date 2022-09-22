
  
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
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h3>{{Auth::user()->company->razon_social ?? ''}}  <h3>{{Auth::user()->company->code_rif ?? ''}}</h3> </h3></th>    </tr> 
    </tr> 
  </table>
  <div style="width: 100%; align:center; text-align:center">
  <b><span style="color: black; text-align: center;">LIBRO MAYOR POR CUENTAS</span><br><br>
  <span style="color: black; text-align: center;">Código de Cuenta: {{ $account->code_one ?? ''}}.{{ $account->code_two ?? ''}}.{{ $account->code_three ?? ''}}.{{ $account->code_four ?? ''}}.{{ $account->code_five ?? ''}}</span><br>
  <span style="color: black; text-align: center;">Cuenta: {{ $account->description ?? ''}}</span><br>
  <span style="color: black; text-align: center;">Desde: {{ $date_begin ?? ''}}  -  Hasta {{ $date_end ?? ''}}</span>
  </b>
 </div>
  <?php
   if ($coin == 'bolivares'){
    $moneda = '';
    $monedabs = 'Bs.';   
  } else {
    $moneda = '$';
    $monedabs = '';
   }
  
  ?>
 
  <h5 style="color: black; text-align: right;">Saldo actual a la fecha: {{$moneda}}{{ number_format(bcdiv($saldo,'1',2) ?? 0, 2, ',', '.')}} {{$monedabs}}</h5>
  
  @if (isset($detailvouchers))
      <?php 
      $total_debe = 0;
      $total_haber = 0;
      /*se quito el saldo inicial para que no descuadrara*/

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
            <td style="text-align: right;">{{ number_format(bcdiv($detail->debe,'1',2) ?? 0, 2, ',', '.')}}</td>
            <td style="text-align: right;">{{ number_format(bcdiv($detail->haber,'1',2) ?? 0, 2, ',', '.')}}</td>
            <td style="text-align: right;">{{ number_format(bcdiv($detail->saldo,'1',2) ?? 0, 2, ',', '.')}}</td>
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
      <th style="text-align: right;">{{$moneda}}{{ number_format(bcdiv($total_debe,'1',2) ?? 0, 2, ',', '.')}} {{$monedabs}}</th>
      <th style="text-align: right;">{{$moneda}}{{ number_format(bcdiv($total_haber,'1',2) ?? 0, 2, ',', '.')}} {{$monedabs}}</th>
      <th style="text-align: center; border-color: black;"></th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;">Saldo del Mes</th>
      <th style="text-align: right;">{{$moneda}}{{ number_format(bcdiv($total_debe - $total_haber,'1',2), 2, ',', '.')}} {{$monedabs}}</th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;">Saldo Actual</th>
      <th style="text-align: right;">{{$moneda}}{{ number_format(bcdiv($saldo,'1',2), 2, ',', '.')}} {{$monedabs}}</th>
    </tr>
  </table>
@endif


</body>


</html>
