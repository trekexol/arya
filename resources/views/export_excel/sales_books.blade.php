

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Factura</title>
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
  </style>
</head>

<body>
  
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
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount ?? 0), 2, ',', '.') }}</td>
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
        <td style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount / $quotation->bcv ?? 0), 2, ',', '.') }}</td>
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
        $total_amount +=  $quotation->amount;
        $total_base_imponible += $quotation->base_imponible;
        $total_amount_exento += $quotation->amount_exento;
        $total_retencion_iva += $quotation->retencion_iva;
        $total_retencion_islr += $quotation->retencion_islr;
        $total_anticipo += $quotation->anticipo;
        $total_amount_iva += $quotation->amount_iva;
        $total_amount_with_iva += $quotation->amount_with_iva;

      } else {
        $total_amount += ($quotation->amount / $quotation->bcv ?? 0); 
        $total_amount_exento += ($quotation->amount_exento / $quotation->bcv ?? 0); 
        $total_base_imponible += ($quotation->base_imponible / $quotation->bcv ?? 0); 
        $total_amount_iva += ($quotation->amount_iva / $quotation->bcv ?? 0); 
        $total_retencion_iva += ($quotation->retencion_iva / $quotation->bcv ?? 0); 

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
    <th style="text-align: right; font-weight: normal; font-style:bold; border-color: white;">{{ number_format($total_amount, 2, ',', '.') }}</th>
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

</body>
</html>
