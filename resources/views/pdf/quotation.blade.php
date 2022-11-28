<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Cotización</title>
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
  <table>
    <tr>
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h4>{{Auth::user()->company->code_rif ?? ''}}</h4> </h4></th>    </tr> 
    </tr> 
  </table>
  @if ($company->format_header_lines > 0) 
    @for ($i = 0; $i < $company->format_header_lines; $i++)
    <br>
    @endfor
  @endif

@if(Auth::user()->company->id != 22)
<h4 style="color: black">Cotización NRO: {{ str_pad( $quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>
@else 
<h4 style="color: black">Cotización NRO: <span style="color:red">{{ str_pad( $quotation->id, 6, "0", STR_PAD_LEFT)}} </span></h4>
@endif

 
   
 
<table style="width: 60%;">
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 20%;">Concesión Postal:</th>
    <th style="font-weight: normal; width: 40%;">Nº {{ $company->franqueo_postal ?? ''}}</th>
  </tr>
  @endif
  
  <tr>
    @if (isset($quotation->credit_days))
      <td style="width: 20%;">Fecha de Emisión:</td>
      <td style="width: 40%;"> {{ date_format(date_create($quotation->date),"d-m-Y") }}</td>
    @else
      <td style="width: 20%;">Fecha de Emisión:</td>
      <td style="width: 40%;">{{ date_format(date_create($quotation->date),"d-m-Y")}}</td>
    @endif
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $quotation->clients['name'] }}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $quotation->clients['direction'] }}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/CI</th>
    <th style="text-align: center;">N° Control / Serie</th>
    <th style="text-align: center;">Nota de Entrega</th>
    <th style="text-align: center;">Transp./Tipo Entrega</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->serie ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->number_delivery_note ?? '' }}</td>
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; {{ $quotation->observation }} </th>
</tr>
  
</table>
 
<br>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Productos</th>
  </tr> 
</table>

<table style="width: 100%;">
  <?php 
  $lote = '';
  $date_expirate = '';
  $discount = 0;  
  $total = 0;
     foreach ($inventories_quotations as $var){

         $lote .= $var->lote;
         $date_expirate .= $var->date_expirate;

         $discount += $var->discount;
     }
 
 ?>
 @if(Auth::user()->company->id != 22)
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    @if( isset($photo) && $photo == 1)
    <th style="text-align: center; ">Foto</th>
    @endif
    <th style="text-align: center; ">Cantidad</th>
    @if($lote != '')
    <th style="text-align: center; ">Lote</th>
    @endif
    @if($date_expirate != '')
    <th style="text-align: center; ">Fecha Venc</th> 
    @endif
    <th style="text-align: center; ">Precio</th>
    @if($discount > 0)
    <th style="text-align: center; ">Desc</th>
    @endif
    <th style="text-align: center; ">Total</th>
  </tr> 
@else
<tr>
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Código</th>
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Descripción</th>
  @if( isset($photo) && $photo == 1)
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Foto</th>
  @endif
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Cantidad</th>
  @if($lote != '')
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Lote</th>
  @endif
  @if($date_expirate != '')
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Fecha Venc</th> 
  @endif
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Precio</th>
  @if($discount > 0)
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Desc</th>
  @endif
  <th style="text-align: center; background-color:#5CAB44; color: #fff">Total</th>
 </tr> 
@endif

  @foreach ($inventories_quotations as $var)
      <?php
      $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

      $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

      $total_less_percentage = $total_less_percentage;

      $total += $total_less_percentage;
      ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      @if( isset($photo) && $photo == 1)
      
          @if(isset($var->photo_product))
          <th style="text-align: center; font-weight: normal;"><img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}"></th>
          @else
          <th style="text-align: center; font-weight: normal;"></th>
          @endif
      @endif
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount_quotation, 0, '', '.') }}</th>
      @if ($lote != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->lote }}</th>
      @endif
      
      @if ($date_expirate != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->date_expirate}}</th>
      @endif
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
      @if($discount > 0)
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      @endif
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php
  $iva = ($quotation->base_imponible * 16)/100;

  $total_all = $total + $iva - ($quotation->anticipo ?? 0);

?>

<table style="width: 100%;">
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($total, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->base_imponible , '1', 2), 2, ',', '.') }}</th>
  </tr>
  @if ($quotation->retencion_iva != 0)
    <tr>
      <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
      <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Retención de Iva</th>
      <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->retencion_iva , '1', 2), 2, ',', '.') }}</th>
    </tr> 
  @endif 
  @if ($quotation->retencion_islr != 0)
    <tr>
      <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
      <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Retención de ISLR</th>
      <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->retencion_islr , '1', 2), 2, ',', '.') }}</th>
    </tr> 
  @endif 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($iva, '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @if ($quotation->anticipo != 0)
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">Anticipo</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($quotation->anticipo , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  <tr>

    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>

    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">MONTO TOTAL {{($coin == 'bolivares') ? 'Bs.' : ' USD'}}</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; ">{{($coin == 'bolivares') ? '' : '$'}}{{ number_format(bcdiv($total_all , '1', 2), 2, ',', '.') }}</th>
  </tr> 

  <tr>
    <th style="text-align: left; width: 38%; border-bottom-color: black; border-right-color: white;" ></th>
    <th style="text-align: left; font-weight: normal; width: 21%; border-top-color: rgb(17, 9, 9); border-right-color: black; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 21%; "></th>
  </tr> 
  
  
</table>
@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal; font-size: small;">
     Nota: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
</body>
</html>
