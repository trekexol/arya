
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Resumen de Nómina Detallado</title>
<style>
  body{
    font-size: 9pt;
  }
  table, td, th {
    border: 1px solid black;
  }
  
  table {
    border-collapse: collapse;
    width: 50%;
    /*font-family: Arial, Helvetica, sans-serif;*/
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
  <table id="top">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="93" height="60" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>    </tr> 
  </table>
  <br>
  <span><b>Nómina: {{ $nomina->id }}</b></span>
  <br>
  <span><b>Descripcción: {{ $nomina->description }}</b></span>
  <br>
  <span><b>Fecha desde: {{ \Carbon\Carbon::parse($nomina->date_begin)->format('d-m-Y')}} , Fecha hasta {{ \Carbon\Carbon::parse($nomina->date_end)->format('d-m-Y') }}</b></span>
  <br>
  <br>
  <?php
  global $total_monto_neto_global;
  ?>
<table style="width: 100%;">
  <tbody>
  <tr>
    <th style="text-align: center; width:10%;">CI</th>
    <th style="text-align: center;">Nombres y Apellidos</th>
    <th style="text-align: center; width:1%;">Monto</th>
    <th style="text-align: center; width:1%;">Bono<br>Médico</th>
    <th style="text-align: center; width:1%;">Bono<br>Alimentación</th>
    <th style="text-align: center; width:1%;">Bono<br>Transporte</th>
    <th style="text-align: center; width:1%;">SSO</th>
    <th style="text-align: center; width:1%;">FAOV</th>
    <th style="text-align: center; width:1%;">PIE</th>
    <th style="text-align: center; width:1%;">SSO<br>Patronal</th>
    <th style="text-align: center; width:1%;">FAOV<br>Patronal</th>
    <th style="text-align: center; width:1%;">PIE<br>Patronal</th>
    <th style="text-align: center; width:1%;">Total<br>Deducciones</th>
    <th style="text-align: center; width:1%;">Total Neto {{$total_monto_neto_global}}</th>
    <th style="text-align: center; width:1%;">Total<br>Asignaciones</th>
    <th style="text-align: center; width:1%;">Total<br>General</th>

  
  </tr>

<?php
  $total_asignacion_global = 0;
  $total_bono_medico_global = 0;
  $total_bono_alim_global = 0;
  $total_bono_transporte_global = 0;
  $total_deduccion_sso_global = 0;
  $total_deduccion_faov_global = 0;
  $total_deduccion_pie_global = 0;
  $total_sso_patronal_global = 0;
  $total_faov_patronal_global = 0;
  $total_pie_patronal_global = 0;
  $total_deducciones_global = 0;
  $total_monto_neto_global = 0;
  $total_otras_asignaciones_global = 0;
  $total_total_general_global = 0;
?>

@foreach ($employees as $employee)
<?php
$total_sso_patronal = 0;
$total_faov_patronal = 0;
$total_pie_patronal = 0;

$deducciones = 0;
$monto_neto = 0;
$otras_asignaciones = 0;
$total_general = 0;

$total_sso_patronal = (($employee->asignacion * 12)/52) * $lunes * ($nominabases->sso_company/100);
$total_faov_patronal = $employee->asignacion * ($nominabases->faov_company/100);
$total_pie_patronal =  (($employee->asignacion * 12)/52) * $lunes * ($nominabases->pie_company/100);

$deducciones = $employee->deduccion_sso+$employee->deduccion_faov+$employee->deduccion_pie+$employee->deduccion_ince+$employee->otras_deducciones;
$monto_neto = $employee->total_asignacion_m_deducciones;
$otras_asignaciones = $employee->bono_medico+$employee->bono_alim+$employee->bono_transporte+$employee->otras_asignaciones;
$total_general = $monto_neto+$otras_asignaciones;

?>
  @if ($employee->asignacion > 0)
    <tr>
      <td style="text-align: center;"> {{$employee->id_empleado}}</td>
      <td style="text-align: center;"> {{$employee->nombres}} {{$employee->apellidos}}</td>
      <td style="text-align: right;">{{ number_format($employee->asignacion, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->bono_medico, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->bono_alim, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->bono_transporte, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->deduccion_sso, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->deduccion_faov, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($employee->deduccion_pie, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($total_sso_patronal, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($total_faov_patronal, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($total_pie_patronal, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($deducciones, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($monto_neto, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($otras_asignaciones, 2, ',', '.')}}</td>
      <td style="text-align: right;">{{ number_format($total_general, 2, ',', '.')}}</td>
    </tr>

<?php
$total_asignacion_global += number_format($employee->asignacion, 2, '.', '');
$total_bono_medico_global += number_format($employee->bono_medico, 2, '.', '');
$total_bono_alim_global += number_format($employee->bono_alim, 2, '.', '');
$total_bono_transporte_global += number_format($employee->bono_transporte, 2, '.', '');
$total_deduccion_sso_global += number_format($employee->deduccion_sso, 2, '.', '');
$total_deduccion_faov_global += number_format($employee->deduccion_faov, 2, '.', '');
$total_deduccion_pie_global += number_format($employee->deduccion_pie, 2, '.', '');
$total_sso_patronal_global += number_format($total_sso_patronal, 2, '.', '');
$total_faov_patronal_global += number_format($total_faov_patronal, 2, '.', '');
$total_pie_patronal_global += number_format($total_pie_patronal, 2, '.', '');
$total_deducciones_global += number_format($deducciones, 2, '.', '');
$total_monto_neto_global += number_format($monto_neto, 2, '.', '');
$total_otras_asignaciones_global += number_format($otras_asignaciones, 2, '.', '');
$total_total_general_global += number_format($total_general, 2, '.', '');
?>

  @endif
@endforeach
  </tbody>
  <tfoot>
    <tr>
      <th style="text-align: right;"></th>
      <th style="text-align: center;">Total..</th>
      <th style="text-align: right;">{{ number_format($total_asignacion_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_bono_medico_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_bono_alim_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_bono_transporte_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_deduccion_sso_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_deduccion_faov_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_deduccion_pie_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_sso_patronal_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_faov_patronal_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_pie_patronal_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_deducciones_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_monto_neto_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_otras_asignaciones_global, 2, ',', '.')}}</th>
      <th style="text-align: right;">{{ number_format($total_total_general_global, 2, ',', '.')}}</th>
    </tr>
  </tfoot>

</table>

</body>
</html>
