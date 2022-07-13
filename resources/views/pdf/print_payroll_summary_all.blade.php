
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Resumen de Nomina</title>
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


  <br><br><br><br>
  <h5 style="color: black">{{ $nomina->description }}</h5>
  <h5 style="color: black">fecha desde: {{ \Carbon\Carbon::parse($nomina->date_begin)->format('d-m-Y')}} , Fecha hasta {{ \Carbon\Carbon::parse($nomina->date_end)->format('d-m-Y') }}</h5>
 <?php 
 
  $total = 0;
  $total_asignacion = 0;
  $total_deduccion = 0;

  $total_sueldo = 0;
  $total_bono_medico = 0;
  $total_bono_alimentacion = 0;
  $total_sso = 0;
  $total_faov = 0;
  $total_final = 0;
?>

 
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center;">Nombres y Apellidos</th>
        <th style="text-align: center;">Total Sueldo</th>
        <th style="text-align: center;">Bono Médico</th>
        @if ($nomina->type == "Segunda Quincena")
        <th style="text-align: center;">Bono Alimentación</th>
        @endif
        <th style="text-align: center;">SSO</th>
        <th style="text-align: center;">FAOV</th>
        <th style="text-align: center;">Total</th>
      
      </tr>
   
    @for ($i = 0; $i < count($nomina_calculation_asignacion); $i++)
      <?php
        $total_asignacion += $nomina_calculation_asignacion[$i]->total_asignacion ?? 0;
        $total_deduccion += $nomina_calculation_deduccion[$i]->total_deduccion ?? 0;
        $sueldo = ($nomina_calculation_asignacion[$i]->total_asignacion ?? 0) - ($nomina_calculation_deduccion[$i]->total_deduccion ?? 0);
        
        if ($nomina->type == "Segunda Quincena"){
          $bono_medico = (($nomina_calculation_deduccion[$i]->asignacion_general ?? 0) * $bcv) - $sueldo - (45) - $nomina_calculation_faov[$i]->amount - $nomina_calculation_sso[$i]->amount;
          $total = bcdiv(($sueldo), '1', 2) + 45 + bcdiv($bono_medico, '1', 2) + bcdiv($nomina_calculation_faov[$i]->amount, '1', 2) + bcdiv($nomina_calculation_sso[$i]->amount, '1', 2);
        }else{
          $bono_medico = (($nomina_calculation_deduccion[$i]->asignacion_general ?? 0) * $bcv) - $sueldo - $nomina_calculation_faov[$i]->amount - $nomina_calculation_sso[$i]->amount;
          $total = bcdiv(($sueldo), '1', 2) + bcdiv($bono_medico, '1', 2) + bcdiv($nomina_calculation_faov[$i]->amount, '1', 2) + bcdiv($nomina_calculation_sso[$i]->amount, '1', 2);
        }

        $total_sueldo += $sueldo;
        $total_bono_medico += $bono_medico;
        $total_bono_alimentacion += 45;
        $total_sso += $nomina_calculation_sso[$i]->amount;
        $total_faov += $nomina_calculation_faov[$i]->amount;
        $total_final += $total;

      ?>
        <tr>
          <td style="text-align: center;"> {{ $nomina_calculation_asignacion[$i]->nombres }} {{ $nomina_calculation_asignacion[$i]->apellidos ?? '' }}</td>
          <td style="text-align: center;">{{ number_format(bcdiv(($sueldo), '1', 2) , 2, ',', '.')}}</td>
          <td style="text-align: center;">{{ number_format(bcdiv($bono_medico, '1', 2) , 2, ',', '.')}}</td>
          @if ($nomina->type == "Segunda Quincena")
          <td style="text-align: center;">{{ number_format(bcdiv(45, '1', 2) , 2, ',', '.')}}</td>
          @endif
         <td style="text-align: center;">{{ number_format(bcdiv($nomina_calculation_sso[$i]->amount, '1', 2) , 2, ',', '.')}}</td>
         <td style="text-align: center;">{{ number_format(bcdiv($nomina_calculation_faov[$i]->amount, '1', 2) , 2, ',', '.')}}</td>
         <td style="text-align: center;">{{ number_format($total, 2, ',', '.')}}</td>
        </tr>
    @endfor
    
    <tr>
      <td style="text-align: center;"> </td>
      <td style="text-align: center;">{{ number_format(bcdiv($total_sueldo ?? 0, '1', 2) , 2, ',', '.')}}</td>
      <td style="text-align: center;">{{ number_format(bcdiv($total_bono_medico ?? 0, '1', 2) , 2, ',', '.')}}</td>
      @if ($nomina->type == "Segunda Quincena")
      <td style="text-align: center;">{{ number_format(bcdiv($total_bono_alimentacion, '1', 2) , 2, ',', '.')}}</td>
      @endif
      <td style="text-align: center;">{{ number_format(bcdiv($total_sso, '1', 2) , 2, ',', '.')}}</td>
      <td style="text-align: center;">{{ number_format(bcdiv($total_faov, '1', 2) , 2, ',', '.')}}</td>
      <td style="text-align: center;">{{ number_format(bcdiv($total_final ?? 0, '1', 2) , 2, ',', '.')}}</td>
    </tr>
  </table>
<br><br>


</body>
</html>
