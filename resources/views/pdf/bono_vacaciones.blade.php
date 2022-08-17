
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{asset('vendor/sb-admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Bono Vacaciones</title>
<style>
  body{
    background: white;
  }
  table, td, th {
    border: 1px solid black;
    background: white;
  }
  
  table {
    border-collapse: collapse;
    width: 50%;
  }
  
  th {
    
    text-align: left;
  }
  </style>
</head>

<body>

  <table id="top">
    <tr>
      <th class="small" style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold; font-size: 8pt;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="93" height="60" class="d-inline-block align-top" alt="">
      </th>
      <th class="small" style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold; font-size: 8pt;"><h6>{{Auth::user()->company->razon_social ?? ''}}</h6> </h6>{{Auth::user()->company->code_rif ?? ''}}</h6> </th>    </tr> 
  </table>

  <br>
  <div class="text-center h4">Recibo de Vacaciones</div>

 
   
 
<table style="width: 25%;">
  <tr>
    <th >Fecha: {{ $datenow }}</th>
</table>

<table style="width: 100%;">
  <tr>
    <th style="width: 28%; border-right: none;">Nombre de la Empresa:</th>
    <th style="width: 72%;" class="font-weight-normal">{{ Auth::user()->company->razon_social ?? ''}}</th>
  </tr>
</table>



<table style="width: 100%;">
  <tr>
    <th style="width: 28%; ">Domicilio Fiscal:</th>
    <th style="width: 72%;" class="font-weight-normal">{{ Auth::user()->company->address ?? ''}}</th>
  </tr>
</table>

<table style="width: 100%;">
  <tr>
    <th style="width: 70%; " class="font-weight-normal">Nombre del Trabajador: {{ $employee->nombres }} {{ $employee->apellidos}}</th>
    <th style="width: 30%; " class="text-center">Último Sueldo</th>
  </tr>
  <tr>
    <td class="font-weight-normal">Cargo desempeñado: {{ $employee->professions['description'] }}</td>
    <td class="text-center">{{ number_format($employee->monto_pago, 2, ',', '.')}}</td>
  </tr>  
</table>

<table style="width: 100%;">
  <tr>
    <th class="text-center">Cédula</th>
    <th class="text-center">Fecha de Ingreso</th>
    <th class="text-center">Motivo del Recibo</th>
    <th class="text-center">Fecha de Inicio</th>
    <th class="text-center">Fecha de Fin</th>
  </tr>
  <tr>date_format(date_create($quotationsorigin[0]['date_billing']),"d-m-Y")

    <td class="text-center font-weight-normal">{{ $employee->id_empleado }}</td>
    <td class="text-center font-weight-normal">{{ date_format(date_create($employee->fecha_ingreso),"d-m-Y") }}</td>
    <td class="text-center font-weight-normal">Vacaciones</td>
    <td class="text-center font-weight-normal">{{ date_format(date_create($employee->date_begin),"d-m-Y") }}</td>
    <td class="text-center font-weight-normal">{{ date_format(date_create($employee->date_end),"d-m-Y") }}</td>
  </tr>  
</table>

<?php 
  $total_vacaciones = $employee->days  * $employee->monto_pago / 30;
  $total_bono = $employee->bono  * $employee->monto_pago / 30;
  $total_holidays = $employee->holidays  * $employee->monto_pago / 30;

  $total_asignaciones = $total_vacaciones + $total_bono + $total_holidays;

  //{{sueldo}} * 12 / 52 * {{lunes}} * 0.04
  $total_sso = 0;

  if(isset($employee->mondays)){
    $total_sso =  (($employee->monto_pago * 12)/52) * $employee->mondays * 0.04;
  }

  $total_deducciones = $total_sso + $employee->lph;

  $total_a_pagar = $total_asignaciones - $total_deducciones;
  
?>

<table style="width: 100%;">
  <tr>
    <th class="text-center">Descripción</th>
    <th class="text-center">Total</th>
  </tr>
  
    @if(isset($employee->days))
    <tr>
      <td class="small" style="border-bottom-color: white;">Vacaciones: (Clausula 9 A.C.T. y Art. 190 de la L.O.T.T.T.) {{ $employee->days }} Dia(s)</td>
      <td class="text-center">{{ number_format($total_vacaciones, 2, ',', '.')}}</td>
    </tr>
    @endif
    @if(isset($employee->bono))
    <tr>
      <td class="small"style="border-bottom-color: white;">Bono Vacacional: (Clausula Nº 9 A.C.T. y Art. 192 de la L.O.T.T.T.) {{ $employee->bono }} Dia(s)</td>
      <td class="text-center">{{ number_format($total_bono, 2, ',', '.')}}</td>
    </tr>
    @endif
    @if(isset($employee->holidays))
    <tr>
      <td class="small" >Dias no Habiles, Feriados y Domingos: {{ $employee->holidays }} Dia(s)</td>
      <td class="text-center">{{ number_format($total_holidays, 2, ',', '.')}}</td>
    </tr>
    @endif

    <tr>
      <td class="text-right font-weight-bold" style="border-bottom-color: white;">Total Asignaciones: </td>
      <td class="text-center font-weight-bold">{{ number_format($total_asignaciones, 2, ',', '.')}}</td>
    </tr>

    @if(isset($employee->mondays))
    <tr>
      <td style="border-bottom-color: white;">S.S.O: {{ $employee->mondays }} dias lunes</td>
      <td class="text-center">{{ number_format($total_sso, 2, ',', '.')}}</td>
    </tr>
    @endif
    @if(isset($employee->lph))
    <tr>
      <td style="border-bottom-color: white;">L.P.H:</td>
      <td class="text-center" >{{ number_format($employee->lph, 2, ',', '.')}}</td>
    </tr>
    @endif

    <tr>
      <td class="text-right font-weight-bold" style="border-bottom-color: white;" >Total Deducciones: </td>
      <td class="text-center font-weight-bold">{{ number_format($total_deducciones, 2, ',', '.')}}</td>
    </tr>

    <tr>
      <td class="text-right font-weight-bold" >Total a Pagar: </td>
      <td class="text-center font-weight-bold">{{ number_format($total_a_pagar, 2, ',', '.')}}</td>
    </tr>
  
</table>

<br>
  <div class="small">El suscrito trabajador declara haber recibido de la empresa EMPRESA DEMO C.A. la cantidad {{ number_format($total_a_pagar, 2, ',', '.') }} por concepto de pago de Vacaciones
    correspondientes al periodo {{ date('Y', strtotime($datenow)) }}. Según lo previsto en el Acuerdo Colectivo de Trabajo y la Ley Orgánica del Trabajo,
    Las Trabajadoras y Los Trabajadores.
  </div>

  <br>
  
<div class="small">
  <div class="form-group row col-md-1">
    
    Recibe Conforme: {{ $employee->nombres }} {{ $employee->apellidos }}
  </div>
  <div class="form-group row col-md-1">
    CI: V-11223344
  </div>
  <div class="form-group row col-md-1">
      Firma: ________________________________    
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Fecha:             
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       Hora:
  </div>

  <div class="form-group row col-md-1">
    
    Elaborado Por: 
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    Autorizado Por:
  </div>
  <div class="form-group row col-md-1">
    Firma: ________________________________ 
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    Firma: ________________________________ 
  </div>
  
  

</div>

</body>
</html>
