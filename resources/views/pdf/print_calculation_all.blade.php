

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Calculos de todos los Empleados</title>
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
  .page-break {
      page-break-after: always;
  }
  </style>
</head>

<body>

<?php

    $i = 1;
    $cantidad = count($datos);

    ?>

    @foreach ($datos as $var)

    <table id="top">
        <tr>
          <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="150" height="50" class="d-inline-block align-top" alt="">
          </th>
          <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>    </tr>
      </table>

      <h5 style="text-align: left; color: black">Recibo de pago Nómina: {{ str_pad($nomina->id, 6, "0", STR_PAD_LEFT) }}</h5>
      <h5 style="text-align: left; color: black"> {{ str_pad($nomina->description, 6, "0", STR_PAD_LEFT) }}</h5>


      <table style="text-align: center; width: 100%;">
        <tr>
            <td>Cédula</td>
            <td>Nombre del Empleado</td>
            <td>Cargo</td>

        </tr>
        <tr>
            <td>{{ $var['cedula'] }}</td>
            <td>{{ $var['nombres'] }}</td>
            <td>{{ $var['cargo'] }}</td>

        </tr>

      </table>



 <?php
  $total_asignacion = 0;
  $total_deduccion = 0;


?>
    <br>
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center;">Descripción</th>
        <th style="text-align: center;">Asignación</th>
        <th style="text-align: center;">Deducción</th>

      </tr>

      @foreach($var['datos'] as $data)
      <tr>
        <td>{{ $data['description'] }}</td>

        @if($data['sign'] == 'A')

        @php

        $total_asignacion += $data['monto'];

        @endphp

        <td style="text-align: right;">{{ $data['monto'].' Bs' }}</td>
        <td></td>
        @elseif($data['sign'] == 'D')

        @php

        $total_deduccion += $data['monto'];

        @endphp

        <td></td>
        <td style="text-align: right;">{{ $data['monto'].' Bs' }}</td>
        @endif
      </tr>
      @endforeach

      <tr>
        <td>Total:</td>
        <td style="text-align: right;">{{ $total_asignacion.' Bs' }}</td>
        <td style="text-align: right;">{{ $total_deduccion.' Bs' }}</td>
      </tr>
      <tr>
        <td style="text-align: center;">Total a Pagar:</td>
        <td style="text-align: center;" colspan="2">{{ $total_asignacion - $total_deduccion.' Bs' }}</td>
      </tr>





  </table>

  <br> <br> <br> <br> <br> <br>
  <p style="text-align: center; color: black"><b>__________________________________</b></p>
  <p style="text-align: center; color: black"><b>Firma del Empleado</b></p>

  <?php
   if($i != $cantidad){

    echo "<div class='page-break'></div>";
        $i++;

    }
   ?>
  @endforeach

</body>
</html>
