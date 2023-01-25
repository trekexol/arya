<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title></title>
<style>
  table, td, th {
    border: 1px solid black;
    font-size: x-small;
    font-size: 8pt;
  }
  
  table {
    border-collapse: collapse;
    width: 100%;
   
  }
  
  th {
    
    text-align: left;
  }
  </style>
   
  <style>
  .page-break {
      page-break-after: always;
  }
  </style>

</head>

<body>
  <table>
    <tr>
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
    </tr> 
  </table>
  <h4 style="color: black; text-align: center">LIBRO DE VENTAS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $datenow ?? '' }} / Fecha desde: {{ $date_begin ?? '' }} Fecha Hasta: {{ $date_end ?? '' }}</h5>
   

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-left-color: white;border-top-color: white;"></th>

    <th style="text-align: right; border-right-color: white;">Ventas</th>
    <th style="text-align: center; border-right-color: white;">no</th>
    <th style="text-align: left; ">Contribuyentes</th>

    <th style="text-align: right; border-right-color: white;">Ventas</th>
    <th style="text-align: center; border-right-color: white;">a</th>
    <th style="text-align: left; ">Contribuyentes</th>
        
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
    <th style="text-align: center; border-right-color: white; border-left-color: white;border-top-color: white;"></th>
  </tr> 

  <tr>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;"></th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Fecha</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;"></th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;"></th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;"></th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Total Ventas</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Ventas Exoneradas</th>

    <th style="text-align: right; border-right-color: white;">Ventas.Int.</th>
    <th style="text-align: center; border-right-color: white;">o</th>
    <th style="text-align: left; ">Exportaciones</th>

    <th style="text-align: right; border-right-color: white;">Ventas.Int.</th>
    <th style="text-align: center; border-right-color: white;">o</th>
    <th style="text-align: left; ">Exportaciones</th>  
    
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Iva Retenido</th>
    <th style="text-align: center; border-bottom-color: white; border-right-color: black; border-left-color: black; border-top-color: black;">Nº Comp.</th>
  </tr>

  <tr>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Nº</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">de.la.factura</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Cédula./.R.I.F.</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Nombre.o.Razón.Social</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Estatus</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Factura</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Ctrl.Serie</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Nota Debito</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Nota Crédito</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Fac. Afect.</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Ctrl Afect.</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">incluyendo IVA</th>
    <th style="text-align: center; border-right-color: black; border-left-color: black;">Sin Der. a Cred.</th>

    <th style="text-align: center; ">Base Imponible</th>
    <th style="text-align: center; ">% Alic.</th>
    <th style="text-align: center; ">% Impuesto IVA.</th>

    <th style="text-align: center; ">Base Imponible</th>
    <th style="text-align: center; ">% Alic.</th>
    <th style="text-align: center; ">% Impuesto IVA.</th>
        
    <th style="text-align: center; ">por el Comprador</th>
    <th style="text-align: center; ">Retención.</th>
  </tr> 
  <?php
    $total_base_imponible = 0;
    $total_amount = 0;
    $total_amount_exento = 0;
    $total_retencion_iva = 0;
    $total_retencion_islr = 0;
    $total_anticipo = 0;
    $total_amount_iva = 0;
    $total_amount_with_iva = 0;
    $conteo = 1;
  ?>



  @foreach ($quotations as $quotation)
   

  @if($quotation->status == 'X')
    <?php
      $quotation->amount = 0;
      $quotation->base_imponible = 0;
      $quotation->amount_exento = 0;
      $quotation->retencion_iva = 0;
      $quotation->retencion_islr = 0;
      $quotation->bcv = 1;
      $quotation->amount_iva = 0;
      $quotation->amount_with_iva = 0;
     
    ?>
  @endif
  
  <tr>
      <td style="text-align: center; ">{{ $conteo }}</td>
      <td style="text-align: center; ">{{ $quotation->date_billing ?? ''}}</td>
      
      <td style="text-align: center; font-weight: normal;">{{$quotation->clients['type_code']}}{{ $quotation->clients['cedula_rif'] ?? '' }}</td>
      <td style="text-align: center; font-weight: normal;">{{ $quotation->clients['name'] ?? '' }}</td>
      
      @if($quotation->status == 'C' || $quotation->status == 'P')
      <td style="text-align: center; font-weight: normal;">{{'Activa'}}</td>
      @else
        @if($quotation->status == 'X')
        <td style="text-align: center; font-weight: normal;">{{'Reversada'}}</td>
        @else
        <td style="text-align: center; font-weight: normal;">{{'Otro'}}</td>
        @endif
      @endif
      
      <td style="text-align: center; ">{{ $quotation->number_invoice ?? $quotation->id ?? ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{ $quotation->serie ?? ''}}</td>
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--D-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--C-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--FA-->
      <td style="text-align: center; font-weight: normal;">{{''}}</td><!--CA-->

      @if (isset($coin) && ($coin == 'bolivares'))
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_with_iva ?? 0) , 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_exento ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--BIA-->
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--16A -->
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!-- IvaA-->
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->base_imponible ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ $quotation->iva_percentage }}</td> <!--16B -->
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_iva ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->retencion_iva ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td>
        
      @else
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_with_iva / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_exento / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!--BIA-->
        <td style="text-align: right; font-weight: normal;">{{ $quotation->iva_percentage }}</td><!--16A -->
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td><!-- IvaA-->
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->base_imponible / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ $quotation->iva_percentage }}</td><!--16B -->
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_iva / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->retencion_iva / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
        <td style="text-align: right; font-weight: normal;">{{ '' }}</td>
      @endif
     
    </tr>

    <?php

    if($quotation->status == 'C' || $quotation->status == 'P'){


     if (isset($coin) && ($coin == 'bolivares')){
      $total_amount += number_format($quotation->amount,2,'.','');
      $total_base_imponible += number_format($quotation->base_imponible,2,'.','');
      $total_amount_exento += number_format($quotation->amount_exento,2,'.','');
      $total_retencion_iva += number_format($quotation->retencion_iva,2,'.','');
      $total_retencion_islr += number_format($quotation->retencion_islr,2,'.','');
      $total_anticipo += number_format($quotation->anticipo,2,'.','');
      $total_amount_iva += number_format($quotation->amount_iva,2,'.','');
      $total_amount_with_iva += number_format($quotation->amount_with_iva,2,'.','');

     } else {
      $total_amount += number_format(($quotation->amount / $quotation->bcv ?? 0),2,'.',''); 
      $total_base_imponible += number_format(($quotation->base_imponible / $quotation->bcv ?? 0),2,'.',''); 
      $total_amount_exento += number_format(($quotation->amount_exento / $quotation->bcv ?? 0),2,'.','');
      $total_retencion_iva += number_format(($quotation->retencion_iva / $quotation->bcv ?? 0),2,'.',''); 
      $total_retencion_islr += number_format(($quotation->retencion_islr / $quotation->bcv ?? 0),2,'.','');
      $total_amount_iva += number_format(($quotation->amount_iva / $quotation->bcv ?? 0),2,'.',''); 
      $total_amount_with_iva += number_format(($quotation->amount_with_iva / $quotation->bcv ?? 0),2,'.','');
     }


    } else { 

      $total_amount += 0;
      $total_base_imponible += 0;
      $total_amount_exento += 0;
      $total_retencion_iva += 0;
      $total_retencion_islr += 0;
      $total_anticipo += 0;
      $total_amount_iva += 0;
      $total_amount_with_iva += 0;


    }

    $conteo++;
  ?>   
 

  @endforeach 

  <tr>
  
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: right; font-weight: normal; font-style:bold; border-color: white;">{{ number_format($total_amount_with_iva, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal;font-style:bold; border-color: white; border-left: 1px;">{{ number_format($total_amount_exento, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ '' }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ number_format(0, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ number_format($total_base_imponible, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ '' }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ number_format($total_amount_iva, 2, ',', '.') }}</th>
    <th style="text-align: right; font-weight: normal; border-color: white; font-style:bold;">{{ number_format($total_retencion_iva, 2, ',', '.') }}</th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>


  </tr> 
</table>
<div class="page-break"></div>
<table>
  <tr>
    <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
    </th>
    <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>
  </tr> 
</table> 


<?php

$ano = substr($date_end, 6, 4);
$mes = substr($date_end, 3, 2);

if($mes == '01'){
            $nro_mes    = "01";
            $mes_nombre = "ENERO";
        }elseif($mes == '02'){
            $nro_mes    = "02";
            $mes_nombre = "FEBRERO";
        }elseif($mes == '03'){
            $nro_mes    = "03";
            $mes_nombre = "MARZO";
        }elseif($mes == '04'){
            $nro_mes    = "04";
            $mes_nombre = "ABRIL";
        }elseif($mes == '05'){
            $nro_mes    = "05";
            $mes_nombre = "MAYO";
        }elseif($mes == '06'){
            $nro_mes    = "06";
            $mes_nombre = "JUNIO";
        }elseif($mes == '07'){
            $nro_mes    = "07";
            $mes_nombre = "JULIO";
        }elseif($mes == '08'){
            $nro_mes    = "08";
            $mes_nombre = "AGOSTO";
        }elseif($mes == '09'){
            $nro_mes    = "09";
            $mes_nombre = "SEPTIEMBRE";
        }elseif ($mes == '10'){
            $nro_mes    = "10";
            $mes_nombre = "OCTUBRE";
        }elseif ($mes == '11'){
            $nro_mes    = "11";
            $mes_nombre = "NOVIEMBRE";
        }else {
            $nro_mes    = "12";
            $mes_nombre = "DICIEMBRE";
        }

?>




<table ALIGN="right" style="width: 60%;">
  <tr>
    <th style="text-align: center; ">Resumen del periodo {{$mes_nombre}} {{$ano}}  / {{ $date_begin ?? '' }} - {{ $date_end ?? '' }}</th>
    <th style="text-align: center; "></th>
    <th style="text-align: center; ">%</th>
    <th style="text-align: center; "></th>
    <th style="text-align: center; ">Retenciones</th>
  </tr> 
  <tr>
    <td style="text-align: left; font-weight: normal;">Total Ventas Internas No Gravadas</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Notas de Credito  No Gravadas</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Total Ventas Exportación</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal; font-style:bold;">Total Ventas   Internas solo Alicuota General 16%</td>
    <td style="text-align: right; font-weight: normal;">{{number_format($total_base_imponible, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(16, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format($total_amount_iva, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Total Ventas  Internas solo Alicuota General mas Adicional </td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Total Ventas  Internas solo Alicuota Reducida</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal; font-style:bold;">Total de  Ventas y Debitos Fiscales</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format($total_base_imponible, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(16, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format($total_amount_iva, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format($total_retencion_iva, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Ajustes a los Débitos Fiscales de Períodos Anteriores:</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(16, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Notas de Crédito Gravadas Alicuota General 16%</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(16, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal; font-style:bold;">Total Notas de Crédito y Debito Gravadas </td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal;">Total Ajustes Débitos Fiscales de Períodos Anteriores:</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
  <tr>
    <td style="text-align: left; font-weight: normal; font-style:bold;">Total de Ventas y Débitos Fiscales Alicuota General </td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format($total_base_imponible, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(16, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format($total_amount_iva, 2, ',', '.')}}</td>
    <td style="text-align: right; font-weight: normal; font-style:bold;">{{number_format(0, 2, ',', '.')}}</td>
  </tr>
</table>

</body>
</html>
