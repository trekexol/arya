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
        @if($resumen != 'SI')
        <th style="text-align: center; width: 9%;">Fecha</th>
        <th style="text-align: center; width: 7%;">Comp.</th>
        <th style="text-align: center; width: 30%;">Descripcion</th>
        <th style="text-align: center; width: 12%;">Referencia</th>
        <th style="text-align: center;">Debe</th>
        <th style="text-align: center;">Haber</th>
        <th style="text-align: center;">Saldo</th>
        @else
        <th style="text-align: center; width: 9%;">Fecha</th>
        <th style="text-align: center; width: 7%;">Comp.</th>
        <th style="text-align: center;">Descripcion</th>
        <th style="text-align: center;">Referencia</th>
        <th style="text-align: center;">Haber</th>
        @endif
        

        @if($resumen != 'SI')
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
    @else


        
            <?php

            $total_debe_d = 0;
            $total_haber_d = 0;

            foreach ($detailvouchers as $detail){
                if ($detail->id_account == $id_account){
                  
                  if((isset($detail->debe)) && ($detail->debe != 0)){
                      $total_debe_d = $detail->debe;
                  }else{
                    
                    if((isset($detail->haber)) && ($detail->haber != 0)){
                      $total_haber_d = $detail->haber;
                    }
                  }
                  $nuevo_array[] = array($detail->date,$detail->id_header,$detail->header_description,$detail->account_counterpart,$detail->reference,$total_debe_d,$total_haber_d,$detail->saldo,$detail->id_account,$detail->id_contrapartida);
                }
            }

              //////////////////NUEVO CODIGO////////////////////////////////////////////////////////////////
              if (!empty($nuevo_array)){
                  for ($q=0;$q<count($nuevo_array);$q++) {
                    for ($k=$q+1; $k<count($nuevo_array);$k++) {
                      if ($nuevo_array[$q][9] == $nuevo_array[$k][9]) {
                          $nuevo_array[$q][5] = $nuevo_array[$q][5]+$nuevo_array[$k][5];
                          $nuevo_array[$q][6] = $nuevo_array[$q][6]+$nuevo_array[$k][6];
                          $nuevo_array[$q][7] = $nuevo_array[$q][7]+$nuevo_array[$k][7];
                          $nuevo_array[$k][0] = 0;
                          $nuevo_array[$k][1] = 0;
                      }

                    }
                  }
              } else {
                  $nuevo_array[] = array(0,0,0,0,0,0,0,0,0,0);
              }

              $total_debe = 0;
              $total_haber = 0;
              $d_saldo = 0;
              $primer_movimiento = true;
              $saldo_d = 0;

              for ($q=0;$q<count($nuevo_array);$q++) {
                         
               // if($nuevo_array[$q][9] != $id_account){


                  if($nuevo_array[$q][1] != 0) {

                       $total_debe += $nuevo_array[$q][5];
                       $total_haber += $nuevo_array[$q][6];

                      /*if($primer_movimiento){

                          $d_saldo = $total_saldo + $nuevo_array[$q][5] - $nuevo_array[$q][6];
                          $saldo_d += $d_saldo;
                          $primer_movimiento = false;

                      }else{
                        
                          $d_saldo = $d_saldo + $nuevo_array[$q][5] - $nuevo_array[$q][6];
                          $saldo_d = $d_saldo;
                      }*/
                      //}
                 /* if($detail->account_counterpart == '' or  $detail->account_counterpart == null){
                    echo "<tr>";
                    echo "<td style='text-align: center;'>".date_format(date_create($detail->date),'d-m-Y')."</td>";
                    echo "<td style='text-align: center;'></td>";
                    echo "<td style='text-align: left;'>Sin Cuenta</td>";
                    echo "<td style='text-align: center;'></td>";
                    echo "<td style='text-align: right;'>".number_format(bcdiv($total_debe,'1',2) ?? 0, 2, ',', '.')."</td>";
                    echo "<td style='text-align: right;'>".number_format(bcdiv($total_haber,'1',2) ?? 0, 2, ',', '.')."</td>";
                    echo "<td style='text-align: right;'>".number_format(bcdiv($total_debe - $total_haber,'1',2) ?? 0, 2, ',', '.')."</td>";

                    echo "</tr>";
                  } else { */
                    echo "<tr>";
                    echo "<td style='text-align: center;'>".$nuevo_array[$q][0]."</td>";
                    echo "<td style='text-align: center;'></td>";
                    echo "<td style='text-align: left;'>".$nuevo_array[$q][3]."</td>";
                    echo "<td style='text-align: center;'></td>";
                    echo "<td style='text-align: right;'>".number_format(bcdiv($nuevo_array[$q][6],'1',2) ?? 0, 2, ',', '.')."</td>";
                   
                    
                    $d_saldo = $nuevo_array[$q][5] - $nuevo_array[$q][6];

                      if($primer_movimiento){
                          $saldo_d = $saldo + $d_saldo;
                          $primer_movimiento = false;
                      }
                    
                    echo "</tr>";                   
                  }
               // }
              }
              ?>
 @endif

 @if($resumen != 'SI')
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
    @else
    <tr>
      <td style="text-align: center;"></td>
      <td style="text-align: center;"></td>
      <td style="text-align: center;">Saldo Inicial</td>
      <td style="text-align: center;"></td>
      <td style="text-align: right;">{{ number_format($saldo_inicial, 2, ',', '.')}}</td>

    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;"></th>
      <th style="text-align: right; border-right-color: black;">{{$moneda}}{{ number_format(bcdiv($total_haber,'1',2) ?? 0, 2, ',', '.')}} {{$monedabs}}</th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;">Saldo del Mes</th>
      <th style="text-align: right; border-right-color: black;">{{$moneda}}{{ number_format(bcdiv($total_debe - $total_haber,'1',2), 2, ',', '.')}} {{$monedabs}}</th>
    </tr>
    <tr>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white;"></th>
      <th style="text-align: center; border-color: white; border-right-color: black;">Saldo Actual</th>
      <th style="text-align: right; border-right-color: black;">{{$moneda}}{{ number_format(bcdiv($saldo,'1',2), 2, ',', '.')}} {{$monedabs}}</th>
    </tr>
    @endif

  </table>


@endif


</body>


</html>
