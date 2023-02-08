

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
    dd($nomina_calculation);
    ?>

    @foreach ($nomina_calculation as $var)

    <table id="top">
        <tr>
          <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="93" height="60" class="d-inline-block align-top" alt="">
          </th>
          <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>    </tr>
      </table>
      <h5 style="color: black">ID Empleado: {{ $var->employees['id'] }} , Nombre del Empleado: {{ $var->employees['nombres'] }} {{ $var->employees['apellidos'] }} , Fecha de Ingreso: {{ \Carbon\Carbon::parse($var->employees['fecha_ingreso'])->format('d-m-Y') }}</h5>
      <h5 style="color: black">Número de Nómina: {{ str_pad($nomina->id, 6, "0", STR_PAD_LEFT) }}</h5>

 <?php
  $total_asignacion = 0;
  $total_deduccion = 0;
  $id_employeer = 0;
  $contador_por_pagina = 0;

?>

    <table>
      <tr>
        <th style="text-align: center;">Descripción</th>
        <th style="text-align: center;">Asignación</th>
        <th style="text-align: center;">Deducción</th>

      </tr>


      <tr>

      </tr>








  </table>

  <?php
   if($i == $cantidad){



    }else{
        echo "<div class='page-break'></div>";
        $i++;
    }

   ?>
  @endforeach

</body>
</html>
